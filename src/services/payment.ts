// سرویس درگاه‌های پرداخت ایرانی (آیدی‌پی، زرین‌پال، نوین‌پی، آقای پرداخت و...)
// حالت واقعی: فراخوانی API درگاه از سمت سرور الزامی است؛ این ماژول ساختار و رابط استاندارد را پیاده می‌کند.
import { PAYMENT_GATEWAYS } from '../config/site';
import { faNum } from '../lib/format';

export type PaymentResult = { success: boolean; refId?: string; message: string; gateway: string };
export type GatewayId = keyof typeof PAYMENT_GATEWAYS;

const poll = (ms: number) => new Promise((r) => setTimeout(r, ms));

/** هدایت به درگاه پرداخت و انتظار برای نتیجه (شبیه‌سازی امن برای دمو) */
export async function pay(gatewayId: GatewayId, amount: number, description: string): Promise<PaymentResult> {
  const gw = PAYMENT_GATEWAYS[gatewayId];
  // در نسخه واقعی: درخواست ساخت تراکنش به API درگاه
  // IDPay: POST https://api.idpay.ir/v1.1/payment
  // ZarinPal: POST https://payment.zarinpal.com/pg/v4/payment/request.json
  await poll(1600);
  return {
    success: true,
    refId: `${gw.id.toUpperCase()}-${Math.floor(100000 + Math.random() * 900000)}`,
    gateway: gw.name,
    message: `پرداخت ${faNum(amount)} تومان با موفقیت از طریق ${gw.name} انجام شد.`,
  };
}

/** تأیید تراکنش (در نسخه واقعی پس از بازگشت از درگاه) */
export async function verify(gatewayId: GatewayId, refId: string): Promise<PaymentResult> {
  await poll(800);
  return { success: true, refId, gateway: PAYMENT_GATEWAYS[gatewayId].name, message: 'تراکنش تأیید شد.' };
}

export const gatewayList = Object.values(PAYMENT_GATEWAYS);
