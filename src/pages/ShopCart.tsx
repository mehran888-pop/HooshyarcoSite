// سبد خرید و پرداخت
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Page } from '../components/shared/Overlay';
import { Button, Empty, Field, Input } from '../components/shared/ui';
import { useToast } from '../components/shared/Overlay';
import Icon from '../components/shared/Icon';
import { useCart, cartTotal, cartItemsCount, useAuth, useNotifications } from '../store';
import { gatewayList, pay } from '../services/payment';
import { orderService } from '../services/woocommerce';
import { baleService, smsService } from '../services/integrations';
import { price, faNum, faDateTime } from '../lib/format';

export function Cart() {
  const { items, setQty, remove, clear } = useCart();
  const total = cartTotal(items);
  const toast = useToast();
  if (items.length === 0) {
    return (
      <Page>
        <div className="container" style={{ paddingTop: 60 }}>
          <Empty icon="cart" title="سبد خرید شما خالی است" text="هنوز محصولی به سبد اضافه نکرده‌اید." action={<Button as="link" to="/products" variant="primary" icon="cart">رفتن به فروشگاه</Button>} />
        </div>
      </Page>
    );
  }
  return (
    <Page>
      <div className="container" style={{ paddingTop: 40, maxWidth: 960 }}>
        <h1 style={{ fontSize: 24, marginBottom: 20 }}>سبد خرید</h1>
        <div className="cart-list">
          {items.map(({ product, qty }) => (
            <div key={product.id} className="cart-row">
              <div className={`cart-thumb theme-${Number(product.id) % 4}`}><Icon name="package" size={26} /></div>
              <div style={{ flex: 1 }}>
                <b style={{ fontSize: 14.5 }}>{product.name}</b>
                <div className="small muted">{price(product.price)}</div>
              </div>
              <div className="qty-stepper">
                <button onClick={() => setQty(product.id, qty - 1)}><Icon name="minus" size={14} /></button>
                <span className="mono">{faNum(qty)}</span>
                <button onClick={() => setQty(product.id, qty + 1)}><Icon name="plus" size={14} /></button>
              </div>
              <b className="small nowrap">{price(product.price * qty)}</b>
              <button className="icon-btn" onClick={() => { remove(product.id); toast('از سبد حذف شد', 'info'); }} aria-label="حذف"><Icon name="trash" size={16} /></button>
            </div>
          ))}
        </div>
        <div className="cart-footer">
          <Button variant="danger" size="sm" icon="trash" onClick={() => { clear(); toast('سبد خالی شد', 'info'); }}>خالی کردن سبد</Button>
          <div className="cart-summary-card">
            <span className="small muted">جمع کل ({faNum(cartItemsCount(items))} کالا)</span>
            <span className="grad-gold-text" style={{ fontSize: 24, fontWeight: 900 }}>{price(total)}</span>
            <Link to="/checkout" className="btn btn-primary btn-block mt-2"><Icon name="credit" size={17} /> ادامه و پرداخت</Link>
          </div>
        </div>
      </div>
    </Page>
  );
}

export function Checkout() {
  const { items, clear } = useCart();
  const session = useAuth((s) => s.session);
  const pushNtf = useNotifications((s) => s.push);
  const toast = useToast();
  const navigate = useNavigate();
  const [gateway, setGateway] = useState<string>('zarinpal');
  const [form, setForm] = useState({ name: session?.user.name ?? '', mobile: session?.user.mobile ?? '', email: '' });
  const [busy, setBusy] = useState(false);
  const [result, setResult] = useState<{ success: boolean; refId?: string; message: string } | null>(null);
  const total = cartTotal(items);
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));

  if (items.length === 0 && !result) {
    return (
      <Page>
        <div className="container" style={{ paddingTop: 60 }}>
          <Empty icon="cart" title="سبد خرید خالی است" action={<Button as="link" to="/products" variant="primary">رفتن به فروشگاه</Button>} />
        </div>
      </Page>
    );
  }

  async function doPay() {
    if (form.name.trim().length < 2 || !/^09\d{9}$/.test(String(form.mobile).replace(/[۰-۹]/g, (d) => String('۰۱۲۳۴۵۶۷۸۹'.indexOf(d))))) {
      toast('نام و شماره موبایل معتبر وارد کنید.', 'error');
      return;
    }
    setBusy(true);
    try {
      const gid = gateway as any;
      const ref = await pay(gid, total, 'سفارش از فروشگاه هوش‌یار');
      const order = await orderService.create(items.map((i) => ({ name: i.product.name, qty: i.qty, price: i.product.price })), total, gateway);
      // اطلاع‌رسانی خودکار به مدیر (بله) + پیامک تأیید به مشتری
      baleService.notify('🛒 سفارش جدید', `سفارش به مبلغ ${price(total)} ثبت و پرداخت شد.`);
      smsService.send('melipayamak', form.mobile, `پرداخت شما با موفقیت انجام شد. شماره سفارش: ${order.number}`);
      pushNtf({ title: 'سفارش ثبت شد', body: `سفارش ${order.number} با موفقیت پرداخت شد.`, type: 'order' });
      setResult({ success: true, refId: ref.refId, message: ref.message });
      clear();
    } finally {
      setBusy(false);
    }
  }

  return (
    <Page>
      <div className="container" style={{ paddingTop: 40, maxWidth: 900 }}>
        <h1 style={{ fontSize: 24, marginBottom: 20 }}>تسویه حساب</h1>
        {result ? (
          <div className="card text-center" style={{ padding: 44 }}>
            <div style={{ width: 74, height: 74, borderRadius: 24, background: 'var(--success-soft)', display: 'grid', placeItems: 'center', margin: '0 auto 16px' }}>
              <Icon name="check-circle" size={40} style={{ color: 'var(--success)' }} />
            </div>
            <h2 style={{ fontSize: 22 }}>پرداخت با موفقیت انجام شد 🎉</h2>
            <p className="muted mt-2">{result.message}</p>
            {result.refId && <div className="flex items-center gap-2 justify-center mt-2"><span className="badge badge-info mono" dir="ltr">{result.refId}</span></div>}
            <p className="small muted mt-2">رسید و جزئیات سفارش به پنل کاربری و پیامک شما ارسال شد.</p>
            <div className="flex gap-2 justify-center mt-3 wrap">
              <Button variant="primary" icon="receipt" onClick={() => navigate('/panel/invoices')}>مشاهده فاکتورها</Button>
              <Button variant="ghost" onClick={() => navigate('/panel')}>رفتن به پنل</Button>
            </div>
          </div>
        ) : (
          <div className="checkout-grid">
            <div className="card">
              <h3 className="mb-3" style={{ fontSize: 17 }}>اطلاعات خریدار</h3>
              <Field label="نام و نام خانوادگی *"><Input value={form.name} onChange={(e) => set('name', e.target.value)} /></Field>
              <Field label="شماره موبایل *"><Input value={form.mobile} onChange={(e) => set('mobile', e.target.value)} inputMode="numeric" /></Field>
              <Field label="ایمیل (اختیاری)"><Input type="email" value={form.email} onChange={(e) => set('email', e.target.value)} /></Field>
            </div>
            <div>
              <div className="card mb-2">
                <h3 className="mb-2" style={{ fontSize: 17 }}>انتخاب درگاه پرداخت</h3>
                <div className="gateway-list">
                  {gatewayList.map((g) => (
                    <button key={g.id} className={`gateway-item ${gateway === g.id ? 'active' : ''}`} onClick={() => setGateway(g.id)}>
                      <span className="gateway-logo" style={{ background: g.color }}>{g.name[0]}</span>
                      <span className="small" style={{ fontWeight: 700 }}>{g.name}</span>
                      {gateway === g.id && <Icon name="check-circle" size={17} style={{ color: 'var(--teal)', marginRight: 'auto' }} />}
                    </button>
                  ))}
                </div>
              </div>
              <div className="card cart-summary-card">
                <div className="flex items-center justify-between"><span className="small muted">مبلغ قابل پرداخت</span><span className="grad-gold-text" style={{ fontSize: 24, fontWeight: 900 }}>{price(total)}</span></div>
                <Button variant="primary" block size="lg" disabled={busy} onClick={doPay} style={{ marginTop: 14 }} icon={busy ? 'loader' : 'credit'}>
                  {busy ? 'در حال اتصال به درگاه...' : 'پرداخت و ثبت سفارش'}
                </Button>
                <p className="small muted mt-2 text-center"><Icon name="lock" size={13} /> اطلاعات شما محرمانه و امن است.</p>
              </div>
            </div>
          </div>
        )}
      </div>
    </Page>
  );
}
