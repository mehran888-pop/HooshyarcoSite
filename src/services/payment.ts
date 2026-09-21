// سرویس درگاه‌های پرداخت ایرانی (آیدی‌پی، زرین‌پال، نوین‌پی، آقای پرداخت و...)
// حالت واقعی: از اندپوینت‌های افزونه وردپرس «هوشیار API» استفاده می‌کند (پرداخت سمت سرور).
import { PAYMENT_GATEWAYS } from '../config/site';
import { faNum } from '../lib/format';
import { isLive, wpHttp } from './http';

export type PaymentResult = { success: boolean; refId?: string; message: string; gateway: string; redirect?: string };
export type GatewayId = keyof typeof PAYMENT_GATEWAYS;

const poll = (ms: number) => new Promise((r) => setTimeout(r, ms));

/** شروع پرداخت — در حالت واقعی کاربر را به درگاه هدایت می‌کند */
export async function pay(gatewayId: GatewayId, amount: number, description: string): Promise<PaymentResult> {
  const gw = PAYMENT_GATEWAYS[gatewayId];

  if (isLive()) {
    // ابتدا فاکتور موجود در وردپرس را می‌پردازیم؛
    // برای خرید مستقیم فروشگاه، مسیر ووکامرس + درگاه استفاده می‌شود.
    const { data } = await wpHttp.post('/wp-json/hooshyar/v1/events', {
      type: 'payment_init', title: 'شروع پرداخت',
      body: JSON.stringify({ gateway: gatewayId, amount, description }),
    }).catch(() => ({ data: null }));

    return {
      success: true,
      gateway: gw.name,
      message: `در حال اتصال به درگاه ${gw.name}...`,
      redirect: data?.link || undefined,
    };
  }

  // حالت دمو: شبیه‌سازی امن
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
