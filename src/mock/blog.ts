// مقالات و آموزش‌های نمونه
export interface BlogPost {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  content: string;
  category: 'article' | 'tutorial';
  tags: string[];
  author: string;
  date: string;
  readTime: string;
  hasVideo: boolean;
  videoUrl?: string;
  thumbnailTheme: string;
}

const now = Date.now();
const daysAgo = (n: number) => new Date(now - n * 86400_000).toISOString();

export const posts: BlogPost[] = [
  {
    id: 'b1', slug: 'what-is-ai-for-business', title: 'هوش مصنوعی چگونه کسب‌وکار شما را متحول می‌کند؟',
    excerpt: 'مروری بر کاربردهای عملی هوش مصنوعی در فروش، پشتیبانی و تصمیم‌گیری؛ به زبان ساده برای مدیران.',
    content: 'هوش مصنوعی دیگر یک فناوری آینده نیست؛ امروز ابزاری عملی برای رشد کسب‌وکارهاست. از پیش‌بینی فروش و شخصی‌سازی تجربه مشتری تا اتوماسیون وظایف تکراری، هوش مصنوعی می‌تواند بهره‌وری تیم شما را چند برابر کند. در این مقاله با سه سناریوی کاربردی و نحوه شروع هوشمندسازی کسب‌وکارتان آشنا می‌شوید.\n\nنخستین گام، شناخت نقاط درد سازمان است؛ یعنی جایی که بیشترین زمان یا خطای انسانی صرف می‌شود. سپس با یک پروژه کوچک و قابل اندازه‌گیری شروع کنید و نتایج را ارزیابی نمایید. تیم هوش‌یار در تمام این مسیر همراه شماست.',
    category: 'article', tags: ['هوش مصنوعی', 'مدیریت'], author: 'مهدی رضایی', date: daysAgo(3), readTime: '۶ دقیقه', hasVideo: false, thumbnailTheme: 'violet',
  },
  {
    id: 'b2', slug: 'woocommerce-speed-optimization', title: 'آموزش: افزایش سرعت فروشگاه ووکامرس',
    excerpt: 'گام‌به‌گام سرعت فروشگاه وردپرسی خود را بهبود دهید و نرخ تبدیل را بالا ببرید.',
    content: 'سرعت فروشگاه مهم‌ترین عامل تجربه کاربری و رتبه گوگل است. در این آموزش یاد می‌گیرید کش را بهینه کنید، تصاویر را فشرده‌سازی نمایید و از CDN استفاده کنید. همچنین با ابزارهای اندازه‌گیری سرعت و نحوه تفسیر نتایج آشنا می‌شوید.',
    category: 'tutorial', tags: ['ووکامرس', 'وردپرس', 'سرعت'], author: 'سارا محمدی', date: daysAgo(6), readTime: '۱۰ دقیقه', hasVideo: true, videoUrl: 'https://www.w3schools.com/html/mov_bbb.mp4', thumbnailTheme: 'teal',
  },
  {
    id: 'b3', slug: 'crm-best-practices', title: '۵ اشتباه رایج در استفاده از CRM',
    excerpt: 'اگر CRM شما کارایی لازم را ندارد، شاید یکی از این اشتباهات را مرتکب می‌شوید.',
    content: 'بسیاری از سازمان‌ها CRM می‌خرند اما نتیجه نمی‌گیرند. دلیل اصلی، نبود فرایند مشخص و عدم یکپارچگی با سایر ابزارهاست. در این مقاله پنج اشتباه رایج و راه‌حل هر یک را بررسی می‌کنیم.',
    category: 'article', tags: ['CRM', 'فروش'], author: 'الهام نادری', date: daysAgo(10), readTime: '۵ دقیقه', hasVideo: false, thumbnailTheme: 'gold',
  },
  {
    id: 'b4', slug: 'pwa-vs-native', title: 'آموزش ویدیویی: وب‌اپلیکیشن یا اپلیکیشن بومی؟',
    excerpt: 'کدام برای کسب‌وکار شما مناسب‌تر است؟ مقایسه کامل PWA و اپ بومی با مثال عملی.',
    content: 'انتخاب بین PWA و اپلیکیشن بومی یکی از تصمیمات کلیدی است. در این ویدیو تفاوت‌ها، هزینه‌ها و موارد استفاده هر کدام را با مثال‌های واقعی بررسی می‌کنیم و به شما کمک می‌کنیم تصمیم درست بگیرید.',
    category: 'tutorial', tags: ['موبایل', 'PWA'], author: 'علی کریمی', date: daysAgo(14), readTime: '۸ دقیقه', hasVideo: true, videoUrl: 'https://www.w3schools.com/html/mov_bbb.mp4', thumbnailTheme: 'violet',
  },
  {
    id: 'b5', slug: 'seo-2024-trends', title: 'روندهای سئو که باید بشناسید',
    excerpt: 'از هوش مصنوعی گوگل تا سئوی مبتنی بر نیت کاربر؛ چطور در نتایج بمانید.',
    content: 'الگوریتم‌های گوگل هر روز هوشمندتر می‌شوند. تمرکز باید از کلمه کلیدی به نیت کاربر منتقل شود. محتوای عمیق، تجربه کاربری عالی و اعتبار دامنه، سه ستون سئو در آینده هستند.',
    category: 'article', tags: ['سئو', 'محتوا'], author: 'رضا قاسمی', date: daysAgo(20), readTime: '۷ دقیقه', hasVideo: false, thumbnailTheme: 'teal',
  },
  {
    id: 'b6', slug: 'online-exam-platform', title: 'آموزش: راه‌اندازی آزمون آنلاین با نظارت تصویری',
    excerpt: 'از ساخت سوال تا تصحیح خودکار؛ آموزش کامل سامانه آزمون آنلاین هوش‌یار.',
    content: 'در این آموزش یاد می‌گیرید آزمون استخدامی بسازید، سوال‌های چهارگزینه‌ای و تشریحی اضافه کنید، نظارت تصویری را فعال نمایید و نتایج را به صورت خودکار ارزیابی کنید.',
    category: 'tutorial', tags: ['آزمون', 'استخدام'], author: 'علی کریمی', date: daysAgo(26), readTime: '۱۲ دقیقه', hasVideo: true, videoUrl: 'https://www.w3schools.com/html/mov_bbb.mp4', thumbnailTheme: 'gold',
  },
];
