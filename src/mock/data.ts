// داده‌های نمونه (Demo) — وقتی وردپرس متصل نیست از این‌ها استفاده می‌شود.
import type {
  ActivityItem, CrmContact, Deal, Exam, Faq, Invoice, JobPosition, Notification,
  Order, Product, Project, Service, SupportPlan, TeamMember, Testimonial, Ticket,
} from '../lib/types';

export const services: Service[] = [
  {
    id: 'svc-1', slug: 'ai-consulting', title: 'مشاوره و پیاده‌سازی هوش مصنوعی',
    excerpt: 'طراحی و استقرار مدل‌های هوش مصنوعی، چت‌بات، تحلیل داده و اتوماسیون هوشمند برای کسب‌وکار شما.',
    description: 'از تحلیل داده و یادگیری ماشین تا ساخت دستیار هوشمند اختصاصی. ما مسیر هوشمندسازی سازمان شما را از صفر تا صد طراحی و پیاده‌سازی می‌کنیم؛ شامل مدل‌های پردازش زبان طبیعی، پیش‌بینی فروش، تشخیص تصویر و چت‌بات‌های سازمانی.',
    icon: 'brain',
    priceFrom: 25_000_000,
    category: 'هوش مصنوعی',
    popular: true,
    features: ['مشاوره و نقشه راه هوشمندسازی', 'ساخت چت‌بات و دستیار صوتی', 'مدل‌های یادگیری ماشین و بینایی ماشین', 'تحلیل داده و داشبورد مدیریتی', 'اتوماسیون فرایندهای سازمانی (RPA)'],
  },
  {
    id: 'svc-2', slug: 'web-development', title: 'طراحی سایت و فروشگاه اینترنتی',
    excerpt: 'سایت شرکتی، فروشگاهی و لندینگ با بالاترین کیفیت، سئو و سرعت — متصل به وردپرس و ووکامرس.',
    description: 'طراحی و توسعه وب‌سایت‌های مدرن و واکنش‌گرا با تمرکز بر سرعت، امنیت و سئو. قالب‌های اختصاصی وردپرس، فروشگاه‌های ووکامرس و سامانه‌های تحت وب سازمانی.',
    icon: 'globe',
    priceFrom: 15_000_000,
    category: 'طراحی سایت',
    popular: true,
    features: ['طراحی UI/UX اختصاصی', 'فروشگاه ووکامرس و درگاه پرداخت', 'سئو تکنیکال و سرعت بالا', 'قالب اختصاصی وردپرس', 'پشتیبانی و نگهداری'],
  },
  {
    id: 'svc-3', slug: 'custom-software', title: 'توسعه نرم‌افزار و اپلیکیشن اختصاصی',
    excerpt: 'طراحی و توسعه وب‌اپلیکیشن و اپلیکیشن موبایل اختصاصی (اندروید/iOS) متناسب با فرایندهای سازمان شما.',
    description: 'ساخت نرم‌افزارهای سفارشی تحت وب و موبایل؛ از CRM و اتوماسیون داخلی تا اپلیکیشن مشتریان. معماری مقیاس‌پذیر و امن با فناوری‌های روز.',
    icon: 'code',
    priceFrom: 40_000_000,
    category: 'نرم‌افزار',
    features: ['وب‌اپلیکیشن و PWA نصب‌شونده', 'اپلیکیشن اندروید و iOS', 'رابط API و یکپارچه‌سازی', 'پنل مدیریت پیشرفته', 'اتوماسیون و گردش کار داخلی'],
  },
  {
    id: 'svc-4', slug: 'crm-automation', title: 'CRM و اتوماسیون فروش و پشتیبانی',
    excerpt: 'سامانه مدیریت ارتباط با مشتری، قیف فروش و پیگیری خودکار مشتری — همراه با تیکت و چت آنلاین.',
    description: 'سامانه کامل CRM با قیف فروش، نمره‌دهی سرنخ، یادآوری و پیگیری خودکار، یکپارچه با پیامک، بله، واتس‌اپ و چت آنلاین. همراه با سامانه تیکت پشتیبانی سازمانی.',
    icon: 'users',
    priceFrom: 20_000_000,
    category: 'اتوماسیون',
    features: ['مدیریت سرنخ و قیف فروش', 'پیگیری خودکار و یادآور هوشمند', 'تیکت پشتیبانی و چت آنلاین', 'گزارش‌های فروش و تحلیل', 'یکپارچه با پیامک و پیام‌رسان‌ها'],
  },
  {
    id: 'svc-5', slug: 'branding', title: 'برندینگ و هویت بصری',
    excerpt: 'طراحی لوگو، هویت بصری، موشن‌گرافیک و محتوای تبلیغاتی که برند شما را متمایز می‌کند.',
    description: 'از طراحی لوگو و هویت بصری کامل تا موشن‌گرافیک، تیزر و کاتالوگ. هویتی منسجم که در ذهن مخاطب می‌ماند.',
    icon: 'palette',
    priceFrom: 8_000_000,
    category: 'برندینگ',
    features: ['طراحی لوگو و هویت بصری', 'موشن‌گرافیک و تیزر', 'کاتالوگ و اقلام چاپی', 'راهنمای برند', 'طراحی پست‌های شبکه‌های اجتماعی'],
  },
  {
    id: 'svc-6', slug: 'seo-marketing', title: 'سئو و دیجیتال مارکتینگ',
    excerpt: 'افزایش بازدید و فروش با سئو، تبلیغات گوگل، کمپین پیامکی و استراتژی محتوا.',
    description: 'رتبه گرفتن در نتایج گوگل، کمپین‌های تبلیغاتی و اتوماسیون بازاریابی؛ با گزارش‌دهی شفاف و قابل اندازه‌گیری.',
    icon: 'trending',
    priceFrom: 10_000_000,
    category: 'دیجیتال مارکتینگ',
    features: ['سئو و بهینه‌سازی محتوا', 'تبلیغات کلیکی گوگل', 'کمپین پیامکی و ایمیل', 'استراتژی محتوا', 'آنالیز و گزارش دوره‌ای'],
  },
];

export const products: Product[] = [
  {
    id: 1, name: 'قالب سازمانی هوش‌یار (وردپرس + React)', slug: 'hooshyar-theme',
    sku: 'HYP-TH-001', price: 4_900_000, regularPrice: 6_500_000,
    image: '', category: 'قالب وردپرس', rating: 4.8, reviews: 34, stock: 99, sale: true,
    shortDescription: 'قالب اختصاصی شرکت هوش‌یار پاری‌نگر؛ متصل به وردپرس، ووکامرس، گراویتی‌فرم و پنل کاربری.',
    description: 'قالب کامل و آماده نصب شامل پنل کاربری، فروشگاه، سیستم تیکت، درگاه‌های پرداخت ایرانی و اتصال پیامکی.',
    attributes: [{ label: 'سازگاری', value: 'وردپرس ۶+' }, { label: 'نصب', value: 'همراه آموزش' }, { label: 'پشتیبانی', value: '۶ ماهه' }],
  },
  {
    id: 2, name: 'پنل CRM ابری هوش‌یار', slug: 'hooshyar-crm',
    sku: 'HYP-CRM-001', price: 2_900_000, regularPrice: 3_900_000,
    image: '', category: 'نرم‌افزار', rating: 4.9, reviews: 51, stock: 999999, sale: true,
    shortDescription: 'سامانه CRM ابری با قیف فروش، پیگیری خودکار و یکپارچگی پیامک و بله.',
    description: 'نصب و راه‌اندازی سامانه CRM اختصاصی روی سرور شما با اتصال به درگاه‌ها و سامانه پیامکی.',
    attributes: [{ label: 'کاربر', value: 'نامحدود' }, { label: 'استقرار', value: 'سرور اختصاصی' }],
  },
  {
    id: 3, name: 'چت‌بات هوشمند فارسی', slug: 'hooshyar-chatbot',
    sku: 'HYP-AI-001', price: 3_500_000, regularPrice: 4_500_000,
    image: '', category: 'هوش مصنوعی', rating: 4.7, reviews: 22, stock: 999999, sale: true,
    shortDescription: 'چت‌بات پاسخگوی خودکار مبتنی بر داده‌های کسب‌وکار شما، قابل اتصال به سایت و بله.',
    description: 'ربات پاسخگو با یادگیری از محتوای شما، پشتیبانی خودکار و اتصال به اپراتور انسانی.',
    attributes: [{ label: 'زبان', value: 'فارسی' }, { label: 'آموزش', value: 'با داده شما' }],
  },
  {
    id: 4, name: 'بسته سئو پایه (۳ ماهه)', slug: 'seo-starter',
    sku: 'HYP-SEO-001', price: 4_000_000, regularPrice: 5_000_000,
    image: '', category: 'دیجیتال مارکتینگ', rating: 4.6, reviews: 18, stock: 999999,
    shortDescription: 'بهینه‌سازی فنی و محتوایی سایت برای رتبه‌گیری در گوگل به مدت ۳ ماه.',
    description: 'شامل آنالیز کامل، بهینه‌سازی سرعت، سئو داخلی و گزارش ماهانه رتبه کلمات کلیدی.',
    attributes: [{ label: 'مدت', value: '۳ ماه' }, { label: 'گزارش', value: 'ماهانه' }],
  },
  {
    id: 5, name: 'پشتیبانی فنی سالانه سایت', slug: 'support-yearly',
    sku: 'HYP-SUP-001', price: 6_000_000, regularPrice: 7_500_000,
    image: '', category: 'پشتیبانی', rating: 4.9, reviews: 40, stock: 999999, sale: true,
    shortDescription: 'پشتیبانی فنی، امنیت، پشتیبان‌گیری و بروزرسانی مستمر به مدت یک سال.',
    description: 'پایش روزانه، رفع اشکال، بروزرسانی هسته و افزونه‌ها، پشتیبان‌گیری خودکار و گواهینامه SSL.',
    attributes: [{ label: 'مدت', value: '۱۲ ماه' }, { label: 'پشتیبان‌گیری', value: 'روزانه' }],
  },
  {
    id: 6, name: 'پلن پشتیبانی پیشرفته + تیکت', slug: 'support-pro',
    sku: 'HYP-SUP-002', price: 1_500_000, regularPrice: 2_000_000,
    image: '', category: 'پشتیبانی', rating: 4.8, reviews: 29, stock: 999999,
    shortDescription: 'پشتیبانی پیشرفته با تیکت نامحدود و پاسخ‌گویی اولویت‌دار.',
    description: 'دسترسی به تیکت پشتیبانی نامحدود، چت آنلاین و پاسخ‌گویی اولویت‌دار در ساعات اداری.',
    attributes: [{ label: 'تیکت', value: 'نامحدود' }, { label: 'اولویت', value: 'بالا' }],
  },
  {
    id: 7, name: 'طراحی لوگو اختصاصی', slug: 'logo-design',
    sku: 'HYP-BR-001', price: 2_000_000, regularPrice: 2_800_000,
    image: '', category: 'برندینگ', rating: 4.7, reviews: 15, stock: 999999,
    shortDescription: 'طراحی لوگو اختصاصی با ۳ کانسپت اولیه و تحویل فایل‌های کامل.',
    description: 'طراحی لوگو منحصربه‌فرد با ارائه ۳ ایده اولیه، چند مرحله اصلاح و تحویل فرمت‌های کامل.',
    attributes: [{ label: 'کانسپت', value: '۳ طرح' }, { label: 'تحویل', value: 'روز ۷' }],
  },
  {
    id: 8, name: 'اپلیکیشن اندروید اختصاصی (شروع)', slug: 'android-app',
    sku: 'HYP-APP-001', price: 35_000_000, regularPrice: 42_000_000,
    image: '', category: 'نرم‌افزار', rating: 5, reviews: 12, stock: 999999, sale: true,
    shortDescription: 'طراحی و توسعه اپلیکیشن اندروید اختصاصی از صفر تا انتشار در مارکت.',
    description: 'توسعه اپلیکیشن بومی اندروید با پنل مدیریت، اعلان و انتشار در کافه‌بازار و مایکت.',
    attributes: [{ label: 'پلتفرم', value: 'Android' }, { label: 'انتشار', value: 'کافه‌بازار' }],
  },
];

export const projects: Project[] = [
  {
    id: 'p1', title: 'سامانه CRM و ارتباط با مشتری «بازارلی»', client: 'بازارلی',
    category: 'نرم‌افزار', year: '۱۴۰۳', link: '#',
    description: 'طراحی و پیاده‌سازی CRM ابری با قیف فروش، پیگیری خودکار و یکپارچگی پیامک و بله برای یک مجموعه فروش کالای دیجیتال.',
    img: '', tags: ['React', 'Node', 'PostgreSQL', 'RabbitMQ'],
  },
  {
    id: 'p2', title: 'فروشگاه اینترنتی لوازم خانگی', client: 'خانه‌کala',
    category: 'فروشگاه', year: '۱۴۰۳', link: '#',
    description: 'فروشگاه ووکامرس هدلس با اتصال به زرین‌پال و آیدی‌پی، اپلیکیشن PWA و پنل مشتری اختصاصی.',
    img: '', tags: ['React', 'WooCommerce', 'PWA', 'ZarinPal'],
  },
  {
    id: 'p3', title: 'پلتفرم آزمون آنلاین و استخدام', client: 'کوشا',
    category: 'آموزش', year: '۱۴۰۳', link: '#',
    description: 'سامانه آزمون آنلاین با نظارت تصویری، تصحیح خودکار و یکپارچه با سیستم استخدام؛ همراه اپلیکیشن موبایل.',
    img: '', tags: ['React', 'WebRTC', 'AI Scoring'],
  },
  {
    id: 'p4', title: 'دستیار هوشمند بانکی', client: 'بانک تجارت‌نگر',
    category: 'هوش مصنوعی', year: '۱۴۰۲', link: '#',
    description: 'چت‌بات فارسی مبتنی بر مدل زبانی برای پاسخ‌گویی به ۲۰ هزار پرسش در روز و انتقال به اپراتور.',
    img: '', tags: ['NLP', 'LLM', 'Redis'],
  },
  {
    id: 'p5', title: 'وب‌سایت و رزرو آنلاین کلینیک', client: 'کلینیک لبخند',
    category: 'طراحی سایت', year: '۱۴۰۲', link: '#',
    description: 'سایت معرفی و رزرو نوبت آنلاین با یادآوری پیامکی خودکار و اتصال به تقویم پزشکان.',
    img: '', tags: ['Next.js', 'SMS', 'Calendar'],
  },
  {
    id: 'p6', title: 'اپلیکیشن فروشگاهی میوه و تره‌بار', client: 'تره‌بار آنلاین',
    category: 'اپلیکیشن', year: '۱۴۰۲', link: '#',
    description: 'اپلیکیشن اندروید و iOS سفارش آنلاین با پرداخت در محل و درگاه، همراه داشبورد فروشنده.',
    img: '', tags: ['Flutter', 'Payment', 'Map'],
  },
];

export const team: TeamMember[] = [
  { id: 't1', name: 'مهدی رضایی', role: 'مدیرعامل و بنیان‌گذار', bio: 'کارشناس ارشد هوش مصنوعی با ۱۲ سال تجربه در مدیریت محصولات دیجیتال و رهبری تیم‌های فنی.', avatar: '', skills: ['استراتژی', 'هوش مصنوعی', 'مدیریت محصول'], socials: { l: '#', i: '#', t: '#' } },
  { id: 't2', name: 'سارا محمدی', role: 'مدیر فنی', bio: 'توسعه‌دهنده فول‌استک با تمرکز بر معماری مقیاس‌پذیر، میکروسرویس و زیرساخت ابری.', avatar: '', skills: ['React', 'Node', 'Cloud'], socials: { g: '#', l: '#' } },
  { id: 't3', name: 'علی کریمی', role: 'کارشناس ارشد هوش مصنوعی', bio: 'متخصص یادگیری ماشین و پردازش زبان طبیعی؛ طراح چت‌بات‌ها و مدل‌های پیش‌بینی.', avatar: '', skills: ['Python', 'NLP', 'ML'], socials: { g: '#', t: '#' } },
  { id: 't4', name: 'نگار حسینی', role: 'طراح UI/UX', bio: 'طراح رابط و تجربه کاربری با نگاه جزئی‌نگر به برند و رضایت کاربر نهایی.', avatar: '', skills: ['Figma', 'Design System', 'Motion'], socials: { i: '#', l: '#' } },
  { id: 't5', name: 'رضا قاسمی', role: 'مدیر دیجیتال مارکتینگ', bio: 'متخصص سئو و تبلیغات آنلاین با تمرکز بر رشد ارگانیک و نرخ تبدیل.', avatar: '', skills: ['SEO', 'Ads', 'Analytics'], socials: { t: '#', l: '#' } },
  { id: 't6', name: 'الهام نادری', role: 'مدیر پشتیبانی و موفقیت مشتری', bio: 'مسئول تجربه مشتری، پشتیبانی سازمانی و ارتقای رضایت کاربران.', avatar: '', skills: ['پشتیبانی', 'CRM', 'آموزش'], socials: { t: '#', i: '#' } },
];

export const testimonials: Testimonial[] = [
  { id: 'r1', name: 'حمید تهرانی', role: 'مدیرعامل', company: 'بازارلی', text: 'سیستم CRM هوش‌یار فروش ما را در ۶ ماه ۴۰٪ افزایش داد. پیگیری خودکار مشتری واقعاً کار می‌کند.', rating: 5, avatar: '' },
  { id: 'r2', name: 'مریم شریفی', role: 'بنیان‌گذار', company: 'فروشگاه خانه‌کala', text: 'فروشگاه ما با سرعت عالی بالا آمد و درگاه‌ها بدون کوچک‌ترین مشکل کار می‌کنند. پشتیبانی فوق‌العاده است.', rating: 5, avatar: '' },
  { id: 'r3', name: 'کامران عزیزی', role: 'مدیر آیتی', company: 'کلینیک لبخند', text: 'سیستم رزرو و یادآوری پیامکی، نو‌شوهای ما را به شدت کاهش داد. تیم حرفه‌ای و پاسخگو.', rating: 4, avatar: '' },
  { id: 'r4', name: 'لیلا مرادی', role: 'مدیر منابع انسانی', company: 'کوشا', text: 'پلتفرم آزمون آنلاین با نظارت تصویری، فرایند استخدام ما را چند برابر سریع‌تر کرد.', rating: 5, avatar: '' },
];

export const faqs: Faq[] = [
  { q: 'چقدر طول می‌کشد تا پروژه من آماده شود؟', a: 'بسته به نوع پروژه؛ طراحی سایت معمولاً ۲ تا ۴ هفته، نرم‌افزار اختصاصی ۱ تا ۳ ماه و پروژه‌های هوش مصنوعی ۱ تا ۶ ماه زمان می‌برد. برنامه زمان‌بندی دقیق در جلسه مشاوره مشخص می‌شود.' },
  { q: 'آیا بعد از تحویل هم پشتیبانی دارید؟', a: 'بله، همه پروژه‌ها شامل دوره پشتیبانی رایگان هستند و پس از آن می‌توانید از پلن‌های پشتیبانی ماهانه یا سالانه با تیکت و چت آنلاین استفاده کنید.' },
  { q: 'درگاه پرداخت چه گزینه‌هایی دارد؟', a: 'زرین‌پال، آیدی‌پی، نوین‌پی، آقای پرداخت، پی‌پینگ و سایر درگاه‌های معتبر ایرانی. هر دو حالت اشتراکی و اختصاصی پشتیبانی می‌شود.' },
  { q: 'آیا امکان اتصال به سامانه پیامکی هم هست؟', a: 'بله؛ ملی‌پیامک، sms.ir و ippanel.co همگی پشتیبانی می‌شوند و سناریوهای اطلاع‌رسانی خودکار قابل تنظیم است.' },
  { q: 'وب‌اپلیکیشن موبایل یعنی چه؟', a: 'سایت شما به صورت PWA قابل نصب روی گوشی ارائه می‌شود و در صورت نیاز نسخه بومی اندروید/iOS با Capacitor یا فلاتر ساخته می‌شود.' },
];

export const plans: SupportPlan[] = [
  { id: 'pl1', name: 'پلن پایه', durationLabel: '۳ ماه', durationMonths: 3, price: 1_800_000, features: ['پشتیبانی تلفنی در ساعات اداری', '۱۲ تیکت در ماه', 'پشتیبان‌گیری ماهانه', 'بروزرسانی امنیتی'] },
  { id: 'pl2', name: 'پلن حرفه‌ای', durationLabel: '۶ ماه', durationMonths: 6, price: 3_200_000, features: ['پشتیبانی اولویت‌دار', 'تیکت نامحدود', 'پشتیبان‌گیری هفتگی', 'پایش امنیت و آپ‌تایم', 'گزارش ماهانه'], popular: true },
  { id: 'pl3', name: 'پلن سازمانی', durationLabel: '۱۲ ماه', durationMonths: 12, price: 5_500_000, features: ['پشتیبانی ۲۴/۷', 'چت آنلاین اختصاصی', 'پشتیبان‌گیری روزانه', 'مدیر اختصاصی حساب', 'پایش لحظه‌ای و SLA'] },
];

export const positions: JobPosition[] = [
  {
    id: 'j1', title: 'توسعه‌دهنده فرانت‌اند (React)', department: 'فنی', location: 'تهران / دورکاری',
    type: 'fulltime', salary: 'حقوق رقابتی', active: true,
    description: 'همکاری در توسعه پنل‌های کاربری و وب‌اپ‌های اختصاصی با React و TypeScript.',
    requirements: ['تسلط به React و TypeScript', 'آشنایی با سیستم طراحی و UI', 'حداقل ۲ سال سابقه', 'روحیه کار تیمی'],
  },
  {
    id: 'j2', title: 'کارشناس هوش مصنوعی', department: 'هوش مصنوعی', location: 'تهران', type: 'fulltime', salary: 'حقوق رقابتی', active: true,
    description: 'طراحی و آموزش مدل‌های یادگیری ماشین و چت‌بات‌های فارسی.',
    requirements: ['تسلط به Python و ML', 'تجربه NLP و مدل‌های زبانی', 'آشنایی با ابزارهای MLOps'],
  },
  {
    id: 'j3', title: 'طراح UI/UX', department: 'طراحی', location: 'دورکاری', type: 'remote', active: true,
    description: 'طراحی رابط کاربری محصولات دیجیتال و سیستم‌های طراحی.',
    requirements: ['تسلط به Figma', 'نمونه‌کار قوی', 'آشنایی با اصول طراحی واکنش‌گرا'],
  },
  {
    id: 'j4', title: 'کارشناس پشتیبانی و موفقیت مشتری', department: 'پشتیبانی', location: 'تهران', type: 'fulltime', active: true,
    description: 'پاسخ‌گویی به مشتریان از طریق تیکت و چت و مدیریت رضایت مشتری.',
    requirements: ['روابط عمومی قوی', 'تسلط به نرم‌افزارهای مدیریت تیکت', 'آشنایی با مفاهیم وب'],
  },
  {
    id: 'j5', title: 'کارشناس سئو', department: 'مارکتینگ', location: 'دورکاری', type: 'parttime', active: false,
    description: 'بهینه‌سازی و رتبه‌گیری سایت‌های مشتریان در گوگل.',
    requirements: ['تسلط به سئو داخلی و خارجی', 'تجربه ابزارهای آنالیز', 'مهارت تولید محتوای سئو شده'],
  },
];

const now = Date.now();
const mins = (n: number) => new Date(now - n * 60_000).toISOString();

export const tickets: Ticket[] = [
  {
    id: 'tk-1001', subject: 'مشکل در اتصال درگاه زرین‌پال', department: 'فنی', status: 'open', priority: 'high', createdAt: mins(120),
    messages: [
      { id: 'm1', author: 'customer', authorName: 'حمید تهرانی', text: 'سلام، موقع پرداخت خطای verify می‌گیرم. کد مرچنت رو مجدد چک کردم ولی مشکل باقیه.', date: mins(120) },
      { id: 'm2', author: 'support', authorName: 'پشتیبانی هوش‌یار', text: 'سلام و وقت بخیر، لطفاً کد پیگیری تراکنش ناموفق را از داشبورد زرین‌پال ارسال کنید تا بررسی کنم.', date: mins(100) },
    ],
  },
  {
    id: 'tk-1002', subject: 'درخواست آموزش کار با پنل CRM', department: 'آموزش', status: 'answered', priority: 'medium', createdAt: mins(60 * 25),
    messages: [
      { id: 'm1', author: 'customer', authorName: 'مریم شریفی', text: 'می‌خواستم یک جلسه آموزشی آنلاین برای تیم فروش هماهنگ کنم.', date: mins(60 * 25) },
      { id: 'm2', author: 'support', authorName: 'پشتیبانی هوش‌یار', text: 'سلام، روز سه‌شنبه ساعت ۱۰ صبح مناسب است؟ لینک جلسه را یک ساعت قبل ارسال می‌کنم.', date: mins(60 * 24) },
    ],
  },
  {
    id: 'tk-1003', subject: 'تمدید پشتیبانی سالانه', department: 'فروش', status: 'pending', priority: 'low', createdAt: mins(60 * 70),
    messages: [
      { id: 'm1', author: 'customer', authorName: 'کامران عزیزی', text: 'سلام، پلن پشتیبانی ما رو به مدت یک سال دیگه تمدید کنید.', date: mins(60 * 70) },
    ],
  },
];

export const contacts: CrmContact[] = [
  { id: 'c1', name: 'حمید تهرانی', mobile: '09121112233', company: 'بازارلی', tags: ['مشتری', 'VIP'], status: 'customer', assignedTo: 'الهام نادری', lastActivity: mins(180), score: 92, notes: ['قرارداد پشتیبانی سالانه دارد', 'علاقه‌مند به ماژول جدید هوش مصنوعی'], source: 'وب‌سایت' },
  { id: 'c2', name: 'مریم شریفی', mobile: '09353334455', company: 'خانه‌کala', tags: ['مشتری'], status: 'customer', assignedTo: 'نگار حسینی', lastActivity: mins(60 * 6), score: 85, notes: ['فروشگاه فعال'], source: 'اینستاگرام' },
  { id: 'c3', name: 'کامران عزیزی', mobile: '09127778899', company: 'کلینیک لبخند', tags: ['مشتری', 'تمدید'], status: 'opportunity', assignedTo: 'الهام نادری', lastActivity: mins(60 * 30), score: 74, notes: ['در حال مذاکره برای تمدید'], source: 'تلفنی' },
  { id: 'c4', name: 'لیلا مرادی', mobile: '09011112233', company: 'کوشا', tags: ['سرنخ'], status: 'lead', assignedTo: 'رضا قاسمی', lastActivity: mins(60 * 50), score: 48, notes: ['درخواست دموی پلتفرم آزمون'], source: 'فرم استخدام' },
  { id: 'c5', name: 'سینا توکلی', mobile: '09123338877', company: 'فروشگاه آنلاین نو', tags: ['سرنخ', 'فروشگاه'], status: 'lead', assignedTo: 'رضا قاسمی', lastActivity: mins(60 * 90), score: 35, notes: ['استعلام قیمت فروشگاه'], source: 'چت آنلاین' },
  { id: 'c6', name: 'نازنین صادقی', mobile: '09357770011', company: 'استارتاپ ایده‌نو', tags: [], status: 'qualified', assignedTo: 'مهدی رضایی', lastActivity: mins(60 * 120), score: 61, notes: [], source: 'رویداد' },
];

export const deals: Deal[] = [
  { id: 'd1', title: 'قرارداد پشتیبانی سالانه بازارلی', contactId: 'c1', value: 5_500_000, stage: 'negotiation', probability: 70, expectedClose: mins(-60 * 24 * 6), owner: 'الهام نادری' },
  { id: 'd2', title: 'طراحی فروشگاه نو', contactId: 'c5', value: 18_000_000, stage: 'proposal', probability: 45, expectedClose: mins(-60 * 24 * 10), owner: 'رضا قاسمی' },
  { id: 'd3', title: 'پلتفرم آزمون کوشا (فاز ۲)', contactId: 'c4', value: 40_000_000, stage: 'qualified', probability: 55, expectedClose: mins(-60 * 24 * 14), owner: 'مهدی رضایی' },
  { id: 'd4', title: 'تمدید هاست و نگهداری کلینیک لبخند', contactId: 'c3', value: 3_200_000, stage: 'proposal', probability: 60, expectedClose: mins(-60 * 24 * 3), owner: 'الهام نادری' },
  { id: 'd5', title: 'ساخت اپلیکیشن استارتاپ ایده‌نو', contactId: 'c6', value: 55_000_000, stage: 'new', probability: 20, expectedClose: mins(-60 * 24 * 20), owner: 'مهدی رضایی' },
];

export const invoices: Invoice[] = [
  {
    id: 'inv-1', number: 'INV-1402-001', customerId: 'c1', customerName: 'حمید تهرانی - بازارلی',
    items: [{ title: 'پشتیبانی فنی سالانه', qty: 1, unitPrice: 5_500_000 }],
    status: 'paid', createdAt: mins(60 * 24 * 5), dueDate: mins(-60 * 24 * 5), paidAt: mins(60 * 24 * 4), refId: 'IDP-88213', gateway: 'idpay',
  },
  {
    id: 'inv-2', number: 'INV-1403-002', customerId: 'c2', customerName: 'مریم شریفی - خانه‌کala',
    items: [{ title: 'بسته سئو پایه (۳ ماه)', qty: 1, unitPrice: 4_000_000 }, { title: 'طراحی لندینگ', qty: 1, unitPrice: 3_000_000 }],
    status: 'sent', createdAt: mins(60 * 24 * 2), dueDate: mins(-60 * 24 * 8),
  },
  {
    id: 'inv-3', number: 'INV-1403-003', customerId: 'c4', customerName: 'لیلا مرادی - کوشا',
    items: [{ title: 'استقرار پلتفرم آزمون', qty: 1, unitPrice: 25_000_000 }],
    status: 'draft', createdAt: mins(60 * 6), dueDate: mins(-60 * 24 * 15),
  },
  {
    id: 'inv-4', number: 'INV-1403-004', customerId: 'c3', customerName: 'کامران عزیزی - کلینیک لبخند',
    items: [{ title: 'پلن پشتیبانی حرفه‌ای ۶ ماه', qty: 1, unitPrice: 3_200_000 }],
    status: 'overdue', createdAt: mins(60 * 24 * 12), dueDate: mins(60 * 24 * 4),
  },
  {
    id: 'inv-5', number: 'INV-1403-005', customerId: 'c5', customerName: 'سینا توکلی - فروشگاه نو',
    items: [{ title: 'پیش‌پرداخت طراحی فروشگاه', qty: 1, unitPrice: 9_000_000 }],
    status: 'sent', createdAt: mins(60 * 24), dueDate: mins(-60 * 24 * 5),
  },
];

export const notifications: Notification[] = [
  { id: 'n1', title: 'تیکت جدید ثبت شد', body: 'تیکت «مشکل در اتصال درگاه» از حمید تهرانی ثبت شد.', date: mins(120), read: false, type: 'support' },
  { id: 'n2', title: 'پرداخت موفق', body: 'فاکتور INV-1402-001 با موفقیت از طریق آیدی‌پی پرداخت شد.', date: mins(60 * 24 * 4), read: false, type: 'finance' },
  { id: 'n3', title: 'سرنخ جدید در CRM', body: 'سرنخ «سینا توکلی» از چت آنلاین ثبت شد.', date: mins(60 * 90), read: true, type: 'system' },
  { id: 'n4', title: 'آزمون استخدامی', body: 'آزمون «توسعه‌دهنده فرانت‌اند» نمره قبولی ۷۵ دارد.', date: mins(60 * 30), read: true, type: 'exam' },
];

export const activities: ActivityItem[] = [
  { id: 'a1', icon: 'ticket', text: 'تیکت جدید از حمید تهرانی ثبت شد', date: mins(120), tone: 'teal' },
  { id: 'a2', icon: 'payment', text: 'پرداخت فاکتور INV-1402-001 تأیید شد', date: mins(60 * 24 * 4), tone: 'gold' },
  { id: 'a3', icon: 'user', text: 'سرنخ جدید: سینا توکلی', date: mins(60 * 90), tone: 'violet' },
  { id: 'a4', icon: 'bell', text: 'یادآور تمدید قرارداد کلینیک لبخند', date: mins(60 * 100), tone: 'muted' },
];

export const exam: Exam = {
  id: 'ex-1', title: 'آزمون استخدامی توسعه‌دهنده فرانت‌اند', positionId: 'j1', durationMinutes: 30,
  totalPoints: 20, passScore: 14, shuffled: true, proctored: true,
  questions: [
    { id: 'q1', type: 'choice', text: 'کدام هوک React برای اجرای اثرات جانبی استفاده می‌شود؟', options: ['useState', 'useEffect', 'useRef', 'useMemo'], correct: [1], points: 4 },
    { id: 'q2', type: 'choice', text: 'خروجی `typeof null` در جاوااسکریپت چیست؟', options: ['null', 'undefined', 'object', 'string'], correct: [2], points: 4 },
    { id: 'q3', type: 'multi', text: 'کدام‌یک جزو مبانی سئو فنی است؟', options: ['سرعت بارگذاری', 'متا تگ‌ها', 'رنگ لوگو', 'داده ساختاریافته'], correct: [0, 1, 3], points: 4 },
    { id: 'q4', type: 'text', text: 'مفهوم Virtual DOM را در دو خط توضیح دهید.', correct: null, points: 4 },
    { id: 'q5', type: 'video', text: 'در این ویدیو پس از تماشا، روش مدیریت state در مقیاس بزرگ را توضیح دهید.', videoUrl: 'https://www.w3schools.com/html/mov_bbb.mp4', timeLimit: 60, points: 4 },
  ],
};

export const chatSeed: { name: string; text: string; date: string }[] = [
  { name: 'پشتیبان هوش‌یار', text: 'سلام 👋 به هوش‌یار پاری‌نگر خوش آمدید. چطور می‌توانم کمکتان کنم؟', date: mins(2) },
];
