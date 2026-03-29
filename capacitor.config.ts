import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.damkarkabbekasi.dispatch',
  appName: 'DAMKAR DISPATCH',
  webDir: 'public',
  server: {
    androidScheme: 'https',
    url: 'https://dispatch.damkarkabbekasi.go.id',
    allowNavigation: [
      'dispatch.damkarkabbekasi.go.id'
    ],
    cleartext: true
  }
};

export default config;
