// تنظیمات برند و پیکربندی قالب
// مقدارهای واقعی وردپرس/سرویس‌ها را می‌توانید اینجا (یا از طریق متغیرهای محیطی) ست کنید.

export const SITE = {
  name: 'هوش‌یار پاری‌نگر',
  nameEn: 'Hooshyar Parinegar',
  tagline: 'شرکت هوش مصنوعی، نرم‌افزار و طراحی سایت',
  slogan: 'هوش مصنوعی در خدمت رشد کسب‌وکار شما',
  description:
    'هوش‌یار پاری‌نگر با بهره‌گیری از هوش مصنوعی، طراحی سایت و توسعه نرم‌افزار اختصاصی، کسب‌وکار شما را دیجیتال و هوشمند می‌کند؛ از فروشگاه آنلاین و CRM تا اتوماسیون، اپلیکیشن موبایل و پشتیبانی سازمانی.',
  mobile: '۰۹۱۲ ۳۴۵ ۶۷۸۹',
  mobileEn: '09123456789',
  email: 'hello@hooshyar.dev',
  address: 'تهران، خیابان ولیعصر، برج فناوری، طبقه ۱۲',
  socials: { instagram: '#', telegram: '#', linkedin: '#', github: '#', whatsapp: '#' },
};

/** آدرس پایه وردپرس (Headless). اگر خالی باشد حالت دمو/Mock فعال است. */
export const APP_CONFIG = {
  /** وردپرس مرکزی (مدیریت محتوای فعلی) */
  wpBaseUrl: import.meta.env.VITE_WP_URL || '',
  /** سایت دوم وردپرس (اختیاری، برای جداسازی محتوا) */
  wpBaseUrl2: import.meta.env.VITE_WP_URL_2 || '',
  /** ووکامرس (اگر جدا از وردپرس اصلی است) */
  woocommercePath: '/wp-json/wc/v3',
  /** حالت دمو: وقتی وردپرس متصل نیست از داده نمونه استفاده شود */
  demoMode: import.meta.env.VITE_DEMO_MODE !== 'false',
  consumerKey: import.meta.env.VITE_WC_CONSUMER_KEY || '',
  consumerSecret: import.meta.env.VITE_WC_CONSUMER_SECRET || '',
};

/** درگاه‌های پرداخت ایرانی */
export const PAYMENT_GATEWAYS = {
  idpay: { id: 'idpay', name: 'آیدی‌پی (IDPay)', color: '#00b0f0' },
  zarinpal: { id: 'zarinpal', name: 'زرین‌پال', color: '#f5a623' },
  novinpay: { id: 'novinpay', name: 'نوین‌پی', color: '#7c3aed' },
  aqayepardakht: { id: 'aqayepardakht', name: 'آقای پرداخت', color: '#e11d48' },
  payping: { id: 'payping', name: 'پی‌پینگ', color: '#2563eb' },
  payir: { id: 'payir', name: 'پی (pay.ir)', color: '#16a34a' },
  melat: { id: 'melat', name: 'به‌پرداخت ملت', color: '#e11d48' },
  saman: { id: 'saman', name: 'سامان کیش', color: '#0e7490' },
  zibal: { id: 'zibal', name: 'زیبال', color: '#b45309' },
  poolam: { id: 'poolam', name: 'پولام', color: '#db2777' },
} as const;

/** سامانه‌های پیامکی */
export const SMS_PROVIDERS = {
  melipayamak: { id: 'melipayamak', name: 'ملی‌پیامک', base: 'https://rest.payamak-panel.com/api/SendSMS/' },
  smsir: { id: 'smsir', name: 'sms.ir', base: 'https://api.sms.ir/v1/send/' },
  ippanel: { id: 'ippanel', name: 'ایپ‌پنل (ippanel.co)', base: 'https://api2.ippanel.com/api/v1/' },
} as const;

/** ربات اطلاع‌رسانی بله */
export const BALE = {
  api: import.meta.env.VITE_BALE_API || '',
  token: import.meta.env.VITE_BALE_TOKEN || '',
  channel: import.meta.env.VITE_BALE_CHANNEL || '',
};

/** گراویتی‌فرم */
export const GF = {
  /** شناسه‌های فرم برای اتصال: فرم استخدام، مشاوره، تماس و... */
  forms: {
    employment: import.meta.env.VITE_GF_EMPLOYMENT || 1,
    consultation: import.meta.env.VITE_GF_CONSULTATION || 2,
    contact: import.meta.env.VITE_GF_CONTACT || 3,
  },
};

export const NAV_LINKS = [
  { to: '/', label: 'خانه' },
  { to: '/services', label: 'خدمات' },
  { to: '/projects', label: 'پروژه‌ها' },
  { to: '/products', label: 'فروشگاه' },
  { to: '/team', label: 'تیم ما' },
  { to: '/blog', label: 'مقالات و آموزش' },
  { to: '/careers', label: 'استخدام' },
  { to: '/about', label: 'درباره ما' },
  { to: '/contact', label: 'تماس با ما' },
];
