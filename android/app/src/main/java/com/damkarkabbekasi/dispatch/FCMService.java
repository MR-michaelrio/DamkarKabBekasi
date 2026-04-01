package com.damkarkabbekasi.dispatch;

import android.media.AudioAttributes;
import android.media.MediaPlayer;
import android.util.Log;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import java.io.IOException;
import java.util.Map;

public class FCMService extends FirebaseMessagingService {
    private static final String TAG = "FCMService";
    private MediaPlayer mediaPlayer;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);
        Log.d(TAG, "From: " + remoteMessage.getFrom());

        // Check if message contains data payload
        if (remoteMessage.getData().size() > 0) {
            Map<String, String> data = remoteMessage.getData();
            String ttsUrl = data.get("tts_url");

            if (ttsUrl != null && !ttsUrl.isEmpty()) {
                Log.d(TAG, "TTS URL found: " + ttsUrl);
                playAudio(ttsUrl);
            }
        }
    }

    private void playAudio(String url) {
        if (mediaPlayer != null) {
            mediaPlayer.release();
        }

        mediaPlayer = new MediaPlayer();
        try {
            mediaPlayer.setAudioAttributes(
                new AudioAttributes.Builder()
                    .setContentType(AudioAttributes.CONTENT_TYPE_SPEECH)
                    .setUsage(AudioAttributes.USAGE_ALARM) // Use ALARM to ensure it plays even in doze/quiet modes
                    .build()
            );
            mediaPlayer.setDataSource(url);
            mediaPlayer.prepareAsync();
            mediaPlayer.setOnPreparedListener(new MediaPlayer.OnPreparedListener() {
                @Override
                public void onPrepared(MediaPlayer mp) {
                    mp.start();
                }
            });
            mediaPlayer.setOnCompletionListener(new MediaPlayer.OnCompletionListener() {
                @Override
                public void onCompletion(MediaPlayer mp) {
                    mp.release();
                    mediaPlayer = null;
                }
            });
            mediaPlayer.setOnErrorListener(new MediaPlayer.OnErrorListener() {
                @Override
                public boolean onError(MediaPlayer mp, int what, int extra) {
                    Log.e(TAG, "MediaPlayer error: " + what + ", " + extra);
                    mp.release();
                    mediaPlayer = null;
                    return true;
                }
            });

        } catch (IOException e) {
            Log.e(TAG, "Error playing audio", e);
        }
    }

    @Override
    public void onDestroy() {
        if (mediaPlayer != null) {
            mediaPlayer.release();
            mediaPlayer = null;
        }
        super.onDestroy();
    }
}
