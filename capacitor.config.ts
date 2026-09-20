import type { CapacitorConfig } from '@capacitor/cli';

// پیکربندی Capacitor — برای خروجی گرفتن اپلیکیشن اختصاصی اندروید/iOS
// مراحل:
//   1) npm install @capacitor/android @capacitor/ios
//   2) npm run build       (خروجی در dist/)
//   3) npx cap add android (یا ios)
//   4) npx cap sync
//   5) npx cap open android → ساخت APK در Android Studio
const config: CapacitorConfig = {
  appId: 'dev.hooshyar.parinegar',
  appName: 'هوش‌یار پاری‌نگر',
  webDir: 'dist',
  server: {
    androidScheme: 'https',
  },
  plugins: {
    // پوش‌نوتیفیکیشن و درگاه‌ها در نسخه بومی از طریق همین پلاگین‌ها متصل می‌شوند
    SplashScreen: {
      launchShowDuration: 800,
      backgroundColor: '#080c1a',
      showSpinner: false,
    },
  },
};

export default config;
