import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.damkarkabbekasi.dispatch',
  appName: 'DAMKAR DISPATCH',
  webDir: 'public',
  server: {
    androidScheme: 'https',
    url: 'https://damkarkabbekasi.my.id',
    allowNavigation: [
      'damkarkabbekasi.my.id'
    ],
    cleartext: true
  }
};

export default config;
