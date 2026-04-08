package com.damkarkabbekasi.dispatch;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.AudioAttributes;
import android.media.MediaPlayer;
import android.os.Build;
import android.util.Log;
import android.net.Uri;
import androidx.core.app.NotificationCompat;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import android.os.PowerManager;
import java.io.IOException;
import java.util.Map;

public class FCMService extends FirebaseMessagingService {
    private static final String TAG = "FCMService";
    private static final String CHANNEL_ID = "damkar-emergency";
    private MediaPlayer mediaPlayer;
    private PowerManager.WakeLock wakeLock;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);
        Log.d(TAG, "FCM Message received! From: " + remoteMessage.getFrom());
        Log.d(TAG, "Message data size: " + remoteMessage.getData().size());
        Log.d(TAG, "Has notification: " + (remoteMessage.getNotification() != null));

        Map<String, String> data = remoteMessage.getData();
        Log.d(TAG, "Message data: " + data);

        // Check if this is a patient request message
        if (data.size() > 0 && data.containsKey("title")) {
            String title = data.get("title");
            String body = data.get("body");
            String ttsUrl = data.get("tts_url");

            Log.d(TAG, "Processing patient request - Title: " + title + ", TTS URL: " + ttsUrl);

            // Play Emergency Sound Immediately
            playEmergencySound();

            // Show Manual Notification
            if (title != null && body != null) {
                showNotification(title, body);
            }

            // Play TTS Audio (after emergency sound and notification)
            if (ttsUrl != null && !ttsUrl.isEmpty()) {
                playAudio(ttsUrl);
            }
        } else {
            Log.d(TAG, "Ignoring non-patient-request message");
        }
    }

    private void playEmergencySound() {
        Log.d(TAG, "playEmergencySound: Playing emergency.mp3 immediately");

        try {
            MediaPlayer emergencyPlayer = MediaPlayer.create(this, R.raw.emergency);
            if (emergencyPlayer != null) {
                emergencyPlayer.setAudioAttributes(
                    new AudioAttributes.Builder()
                        .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                        .setUsage(AudioAttributes.USAGE_NOTIFICATION_EVENT)
                        .build()
                );
                emergencyPlayer.setVolume(1.0f, 1.0f);
                emergencyPlayer.setOnCompletionListener(MediaPlayer::release);
                emergencyPlayer.start();
                Log.d(TAG, "playEmergencySound: Emergency sound started");
            } else {
                Log.e(TAG, "playEmergencySound: Failed to create MediaPlayer for emergency sound");
            }
        } catch (Exception e) {
            Log.e(TAG, "playEmergencySound: Error playing emergency sound", e);
        }
    }

    private void showNotification(String title, String body) {
        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        Uri soundUri = Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.emergency);

        // Create Channel for Android O+
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(CHANNEL_ID, "Emergency Notifications", NotificationManager.IMPORTANCE_HIGH);
            channel.setDescription("Emergency alerts for Damkar");
            
            AudioAttributes audioAttributes = new AudioAttributes.Builder()
                    .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                    .setUsage(AudioAttributes.USAGE_NOTIFICATION_EVENT)
                    .build();
            channel.setSound(soundUri, audioAttributes);
            
            notificationManager.createNotificationChannel(channel);
        }

        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent pendingIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE);

        NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this, CHANNEL_ID)
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentTitle(title)
                .setContentText(body)
                .setAutoCancel(true)
                .setSound(soundUri)
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setCategory(NotificationCompat.CATEGORY_ALARM)
                .setContentIntent(pendingIntent);

        notificationManager.notify(0, notificationBuilder.build());
    }

    private void playAudio(String url) {
        Log.d(TAG, "playAudio: Requested URL -> " + url);

        // Terminate any existing player first
        if (mediaPlayer != null) {
            try {
                mediaPlayer.stop();
                mediaPlayer.release();
            } catch (Exception e) {}
            mediaPlayer = null;
        }

        // Use PowerManager to keep the CPU awake during the 3s delay & playback
        PowerManager pm = (PowerManager) getSystemService(Context.POWER_SERVICE);
        wakeLock = pm.newWakeLock(PowerManager.PARTIAL_WAKE_LOCK, "Damkar:TTSWakeLock");
        wakeLock.acquire(15 * 1000); // Max 15 seconds

        try {
            Log.d(TAG, "playAudio: Waiting 3 seconds for notification sound...");
            Thread.sleep(3000); // 3 seconds synchronous wait on the FCM worker thread

            mediaPlayer = new MediaPlayer();
            mediaPlayer.setAudioAttributes(
                new AudioAttributes.Builder()
                    .setContentType(AudioAttributes.CONTENT_TYPE_SPEECH)
                    .setUsage(AudioAttributes.USAGE_MEDIA)
                    .build()
            );
            
            Log.d(TAG, "playAudio: Setting Data Source -> " + url);
            mediaPlayer.setDataSource(url);
            mediaPlayer.setVolume(1.0f, 1.0f);

            mediaPlayer.setOnCompletionListener(mp -> {
                Log.d(TAG, "playAudio: Playback completed.");
                mp.release();
                mediaPlayer = null;
                if (wakeLock != null && wakeLock.isHeld()) {
                    wakeLock.release();
                }
            });

            mediaPlayer.setOnErrorListener((mp, what, extra) -> {
                Log.e(TAG, "playAudio error. What: " + what + ", Extra: " + extra + ", URL: " + url);
                mp.release();
                mediaPlayer = null;
                if (wakeLock != null && wakeLock.isHeld()) {
                    wakeLock.release();
                }
                return true;
            });

            // Using synchronous prepare() to block the FCM thread until audio is loaded.
            // This prevents the Service from being killed prematurely.
            Log.d(TAG, "playAudio: Preparing player (Synchronous)...");
            mediaPlayer.prepare();
            Log.d(TAG, "playAudio: MediaPlayer prepared. Starting playback.");
            mediaPlayer.start();

        } catch (InterruptedException e) {
            Log.e(TAG, "playAudio: Interrupted delay", e);
            if (wakeLock != null && wakeLock.isHeld()) wakeLock.release();
        } catch (IOException e) {
            Log.e(TAG, "playAudio: IOException during setup/prepare", e);
            if (wakeLock != null && wakeLock.isHeld()) wakeLock.release();
        } catch (Exception e) {
            Log.e(TAG, "playAudio: Unexpected error", e);
            if (wakeLock != null && wakeLock.isHeld()) wakeLock.release();
        }
    }

    @Override
    public void onDestroy() {
        if (mediaPlayer != null) {
            mediaPlayer.release();
            mediaPlayer = null;
        }
        if (wakeLock != null && wakeLock.isHeld()) {
            wakeLock.release();
        }
        super.onDestroy();
    }
}
