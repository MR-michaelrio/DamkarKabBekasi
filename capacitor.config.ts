import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.gmci.dispatch',
  appName: 'GMCI DISPATCH',
  webDir: 'public',
  server: {
    // androidScheme: 'https',
    // In production, this should be your actual live URL
    url: 'http://127.0.0.1:8000', // IP khusus agar Emulator bisa akses Localhost Laptop

    cleartext: true
  }
};

export default config;
