// ابزارهای قالب‌بندی فارسی — اعداد، تاریخ شمسی، قیمت و ولیدیشن موبایل

const FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';

/** تبدیل ارقام انگلیسی به فارسی */
export function faNum(input: number | string): string {
  return String(input).replace(/\d/g, (d) => FA_DIGITS[Number(d)]);
}

/** تبدیل ارقام فارسی به انگلیسی */
export function enNum(input: number | string): string {
  return String(input)
    .replace(/[۰-۹]/g, (d) => String(FA_DIGITS.indexOf(d)))
    .replace(/[٠-٩]/g, (d) => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));
}

/** جداکننده هزارگان + ارقام فارسی */
export function faSep(value: number | string): string {
  const n = Number(enNum(value));
  if (Number.isNaN(n)) return String(value);
  const fixed = Number.isInteger(n) ? n.toLocaleString('en-US') : n.toLocaleString('en-US', { maximumFractionDigits: 2 });
  return faNum(fixed);
}

/** قیمت با واحد تومان */
export function price(value: number | string): string {
  return `${faSep(value)} تومان`;
}

/** تاریخ شمسی از Date */
export function faDate(date: Date | string | number): string {
  try {
    const d = date instanceof Date ? date : new Date(date);
    return new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
      year: 'numeric', month: 'long', day: 'numeric',
    }).format(d);
  } catch {
    return faNum(String(date));
  }
}

/** تاریخ + ساعت شمسی */
export function faDateTime(date: Date | string | number): string {
  const d = date instanceof Date ? date : new Date(date);
  const day = faDate(d);
  const time = new Intl.DateTimeFormat('fa-IR', { hour: '2-digit', minute: '2-digit' }).format(d);
  return `${day} — ${time}`;
}

/** ولیدیشن شماره موبایل ایران (۰۹xxxxxxxxx) */
export function isValidMobile(input: string): boolean {
  return /^09\d{9}$/.test(enNum(String(input).trim()));
}

/** نرمال‌سازی موبایل برای ارسال (۰۹ → 98...) */
export function normalizeMobile(input: string): string {
  let m = enNum(String(input).trim());
  if (m.startsWith('0098')) m = m.slice(4);
  else if (m.startsWith('98')) m = m.slice(2);
  else if (m.startsWith('0')) m = m.slice(1);
  return `+98${m}`;
}

/** زمان نسبی فارسی (مثلاً «۵ دقیقه پیش») */
export function timeAgo(date: Date | string | number): string {
  const d = date instanceof Date ? date : new Date(date);
  const diff = (Date.now() - d.getTime()) / 1000;
  if (diff < 60) return 'هم‌اکنون';
  if (diff < 3600) return `${faNum(Math.floor(diff / 60))} دقیقه پیش`;
  if (diff < 86400) return `${faNum(Math.floor(diff / 3600))} ساعت پیش`;
  if (diff < 86400 * 30) return `${faNum(Math.floor(diff / 86400))} روز پیش`;
  return faDate(d);
}

/** کد کوتاه یکتا */
export function uid(prefix = 'id'): string {
  return `${prefix}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 7)}`;
}

/** مخفی‌سازی بخشی از شماره موبایل */
export function maskMobile(mobile: string): string {
  const m = enNum(mobile);
  if (m.length < 7) return mobile;
  return faNum(`${m.slice(0, 4)}***${m.slice(-3)}`);
}

/** دانلود فایل از رشته/بافر (برای فاکتور، خروجی و...) */
export function downloadBlob(content: string, filename: string, mime = 'text/plain;charset=utf-8') {
  const blob = new Blob(['\uFEFF' + content], { type: mime });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

/** مدت زمان خواندن متن (تقریبی) */
export function readTime(text: string): string {
  const words = text.trim().split(/\s+/).length;
  return `${faNum(Math.max(1, Math.ceil(words / 180)))} دقیقه`;
}
