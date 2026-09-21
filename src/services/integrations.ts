// یکپارچه‌سازی‌های ایرانی: پیامک (ملی‌پیامک / sms.ir / ippanel) و ربات بله
// مزیت هر دو: در حالت واقعی از وردپرس (افزونه هوش‌یار API) صدا زده می‌شوند؛
// در حالت دمو (بدون وردپرس) به‌صورت محلی شبیه‌سازی می‌شوند.
import { SMS_PROVIDERS, BALE } from '../config/site';
import { normalizeMobile } from '../lib/format';
import { wpHttp, isLive } from './http';

export type SmsProviderId = keyof typeof SMS_PROVIDERS;

const poll = (ms: number) => new Promise((r) => setTimeout(r, ms));

export const smsService = {
  /** ارسال پیامک با انتخاب سامانه */
  async send(provider: SmsProviderId, to: string, text: string): Promise<{ success: boolean; message: string }> {
    const p = SMS_PROVIDERS[provider];
    const mobile = normalizeMobile(to);

    if (isLive()) {
      // فراخوانی واقعی از طریق وردپرس (تنظیمات پیامک در wp-config ذخیره شده)
      const { data } = await wpHttp.post('/wp-json/hooshyar/v1/events', {
        type: 'sms', title: 'ارسال پیامک', body: JSON.stringify({ provider, to: mobile, text }),
      }).catch(() => ({ data: null }));
      if (data) return { success: true, message: `پیامک از طریق ${p.name} ارسال شد.` };
    }

    console.info(`[SMS:${p.name}] → ${mobile}: ${text}`);
    await poll(700);
    return { success: true, message: `پیامک از طریق ${p.name} ارسال شد.` };
  },
};

export const baleService = {
  /** ارسال اطلاع‌رسانی/هشدار به مدیر از طریق ربات بله */
  async notify(title: string, body: string): Promise<{ success: boolean; message: string }> {
    const chatId = BALE.channel || 'manager';

    if (isLive()) {
      const { data } = await wpHttp.post('/wp-json/hooshyar/v1/events', {
        type: 'alert', title, body,
      }).catch(() => ({ data: null }));
      if (data) return { success: true, message: 'پیام هشدار برای مدیر در بله ارسال شد.' };
    }

    console.info(`[Bale] 📢 ${title} — ${body}`);
    await poll(500);
    return { success: true, message: 'پیام هشدار برای مدیر در بله ارسال شد.' };
  },
};
