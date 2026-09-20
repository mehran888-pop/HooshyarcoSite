// یکپارچه‌سازی‌های ایرانی: پیامک (ملی‌پیامک / sms.ir / ippanel) و ربات بله
// حالت واقعی: این فراخوانی‌ها باید از سمت سرور (وردپرس/پراکسی) انجام شوند.
import { SMS_PROVIDERS, BALE } from '../config/site';
import { normalizeMobile } from '../lib/format';

export type SmsProviderId = keyof typeof SMS_PROVIDERS;

const poll = (ms: number) => new Promise((r) => setTimeout(r, ms));

export const smsService = {
  /** ارسال پیامک با انتخاب سامانه */
  async send(provider: SmsProviderId, to: string, text: string): Promise<{ success: boolean; message: string }> {
    const p = SMS_PROVIDERS[provider];
    const mobile = normalizeMobile(to);
    console.info(`[SMS:${p.name}] → ${mobile}: ${text}`);
    // ملی‌پیامک: rest.payamak-panel.com/api/SendSMS/SendSMS (username,password,to,from,text)
    // sms.ir: api.sms.ir/v1/send/bulk (token, mobile)
    // ippanel: api2.ippanel.com/api/v1/sms/send/webservice/single (apikey, sender, recipient, message)
    await poll(700);
    return { success: true, message: `پیامک از طریق ${p.name} ارسال شد.` };
  },
};

export const baleService = {
  /** ارسال اطلاع‌رسانی/هشدار به مدیر از طریق ربات بله */
  async notify(title: string, body: string): Promise<{ success: boolean; message: string }> {
    console.info(`[Bale] 📢 ${title} — ${body}`);
    // بله: POST api.bale.ai/v1/bots/{token}/sendMessage → {chat_id, text}
    await poll(500);
    return { success: true, message: 'پیام هشدار برای مدیر در بله ارسال شد.' };
  },
};
