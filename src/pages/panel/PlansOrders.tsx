// تمدید پشتیبانی، سفارش‌ها، اعلان‌ها و خروج از پنل
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell } from '../../components/panel/PanelShell';
import { Badge, Button, Empty } from '../../components/shared/ui';
import { useToast } from '../../components/shared/Overlay';
import Icon from '../../components/shared/Icon';
import { plans } from '../../mock/data';
import { useNotifications, useAuth } from '../../store';
import { price, faNum, timeAgo } from '../../lib/format';
import { pay } from '../../services/payment';
import { baleService, smsService } from '../../services/integrations';

const NAV = [
  { to: '/panel', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/panel/support', icon: 'ticket', label: 'تیکت پشتیبانی' },
  { to: '/panel/plans', icon: 'shield', label: 'تمدید پشتیبانی' },
  { to: '/panel/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/panel/orders', icon: 'cart', label: 'سفارش‌های من' },
  { to: '/panel/notifications', icon: 'bell', label: 'اعلان‌ها' },
];

export function Plans() {
  const toast = useToast();
  const pushNtf = useNotifications((s) => s.push);
  const [busy, setBusy] = useState<string | null>(null);

  async function renew(planId: string) {
    const plan = plans.find((p) => p.id === planId)!;
    setBusy(planId);
    try {
      const ref = await pay('zarinpal', plan.price, `تمدید پشتیبانی ${plan.name}`);
      await baleService.notify('🔁 تمدید پشتیبانی', `مشتری پلن ${plan.name} را تمدید کرد.`);
      await smsService.send('melipayamak', '09123456789', `تمدید پشتیبانی ${plan.name} با موفقیت انجام شد.`);
      pushNtf({ title: 'تمدید پشتیبانی', body: `پلن ${plan.name} تا ${plan.durationLabel} تمدید شد.`, type: 'support' });
      toast(`پلن ${plan.name} با موفقیت تمدید شد ✅ کد پیگیری: ${ref.refId}`);
    } finally {
      setBusy(null);
    }
  }

  return (
    <RequireAuth>
      <PanelShell title="تمدید پشتیبانی" subtitle="پلن مناسب خود را انتخاب و تمدید کنید" nav={NAV}>
        <div className="grid-3 mt-2">
          {plans.map((p) => (
            <motion.div key={p.id} className={`card plan-card ${p.popular ? 'popular' : ''}`} whileHover={{ y: -5 }}>
              {p.popular && <span className="popular-flag"><Icon name="star" size={12} /> پیشنهاد ما</span>}
              <Icon name={p.id === 'pl1' ? 'shield' : p.id === 'pl2' ? 'zap' : 'award'} size={30} style={{ color: 'var(--gold)' }} />
              <h3 style={{ fontSize: 18 }}>{p.name}</h3>
              <span className="badge badge-teal">{p.durationLabel}</span>
              <div className="grad-gold-text" style={{ fontSize: 26, fontWeight: 900, margin: '10px 0' }}>{price(p.price)}</div>
              <ul className="svc-features mb-3">
                {p.features.map((f) => <li key={f}><Icon name="check" size={14} style={{ color: 'var(--teal)' }} />{f}</li>)}
              </ul>
              <Button variant={p.popular ? 'primary' : 'outline-gold'} block disabled={busy === p.id} onClick={() => renew(p.id)} icon={busy === p.id ? 'loader' : 'refresh'}>
                {busy === p.id ? 'در حال پرداخت...' : 'تمدید با پرداخت آنلاین'}
              </Button>
            </motion.div>
          ))}
        </div>
      </PanelShell>
    </RequireAuth>
  );
}

export function Orders() {
  return (
    <RequireAuth>
      <PanelShell title="سفارش‌های من" subtitle="تاریخچه سفارش‌های فروشگاه" nav={NAV}>
        <Empty
          icon="cart"
          title="هنوز سفارش ثبت نشده"
          text="پس از اولین خرید از فروشگاه، سفارش‌های شما اینجا نمایش داده می‌شود (در نسخه متصل به ووکامرس با API خوانده می‌شود)."
          action={<Link to="/products" className="btn btn-primary btn-sm"><Icon name="cart" size={15} /> رفتن به فروشگاه</Link>}
        />
      </PanelShell>
    </RequireAuth>
  );
}

export function Notifications() {
  const items = useNotifications((s) => s.items);
  const markRead = useNotifications((s) => s.markRead);
  const markAll = useNotifications((s) => s.markAllRead);
  const toneMap: Record<string, any> = { support: 'teal', order: 'gold', system: 'violet', finance: 'teal', exam: 'info' };
  return (
    <RequireAuth>
      <PanelShell title="اعلان‌ها" subtitle="رویدادهای حساب کاربری شما" nav={NAV}>
        <div className="flex justify-between items-center mb-3">
          <span className="small muted">{faNum(items.length)} اعلان</span>
          <Button variant="ghost" size="sm" onClick={markAll} icon="check">خواندن همه</Button>
        </div>
        {items.length === 0 ? <Empty icon="bell" title="اعلانی ندارید" /> : (
          <div className="flex col gap-2">
            {items.map((n) => (
              <button key={n.id} className={`ntf-item ${n.read ? 'read' : ''}`} onClick={() => markRead(n.id)}>
                <span className="stat-ic"><Icon name={n.type === 'finance' ? 'wallet' : n.type === 'support' ? 'ticket' : n.type === 'exam' ? 'clip' : 'bell'} size={18} /></span>
                <span style={{ flex: 1, textAlign: 'right' }}>
                  <b className="small">{n.title}</b>
                  <div className="small muted">{n.body}</div>
                </span>
                <span className="small muted nowrap">{timeAgo(n.date)}</span>
                {!n.read && <span className="pulse-dot" />}
              </button>
            ))}
          </div>
        )}
      </PanelShell>
    </RequireAuth>
  );
}

export function Logout() {
  const navigate = useNavigate();
  const logout = useAuth((s) => s.logout);
  const toast = useToast();
  logout();
  toast('با موفقیت از حساب خارج شدید.', 'info');
  navigate('/');
  return null;
}
