// داشبورد پنل کاربری (مشتری)
import { Link } from 'react-router-dom';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell, StatCard, type PanelNavItem } from '../../components/panel/PanelShell';
import { Badge, Button } from '../../components/shared/ui';
import Icon from '../../components/shared/Icon';
import { useAuth, useTickets, useInvoices, useNotifications, useCart, cartTotal } from '../../store';
import { faNum, price, timeAgo } from '../../lib/format';
import { plans } from '../../mock/data';
import { activities } from '../../mock/data';

const nav: PanelNavItem[] = [
  { to: '/panel', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/panel/support', icon: 'ticket', label: 'تیکت پشتیبانی' },
  { to: '/panel/plans', icon: 'shield', label: 'تمدید پشتیبانی' },
  { to: '/panel/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/panel/orders', icon: 'cart', label: 'سفارش‌های من' },
  { to: '/panel/notifications', icon: 'bell', label: 'اعلان‌ها' },
];

function Dashboard() {
  const { session } = useAuth();
  const tickets = useTickets((s) => s.tickets);
  const invoices = useInvoices((s) => s.invoices);
  const totalPaid = useInvoices((s) => s.totalPaid());
  const notifications = useNotifications((s) => s.items);
  const openTickets = tickets.filter((t) => t.status === 'open' || t.status === 'pending').length;
  const dueInvoices = invoices.filter((i) => i.status === 'sent' || i.status === 'overdue');
  const { items } = useCart();

  // پلن فعال کاربر
  const activePlan = plans[0];
  const planStart = new Date(Date.now() - 20 * 86400_000);
  const planEnd = new Date(planStart.getTime() + activePlan.durationMonths * 30 * 86400_000);
  const progress = ((Date.now() - planStart.getTime()) / (planEnd.getTime() - planStart.getTime())) * 100;

  return (
    <RequireAuth>
      <PanelShell
        title={`سلام، ${session?.user.name ?? 'کاربر'} 👋`}
        subtitle="نمای کلی حساب کاربری و خدمات شما"
        nav={nav}
      >
        <div className="grid-4">
          <StatCard icon="ticket" label="تیکت‌های باز" value={faNum(openTickets)} tone={openTickets ? 'gold' : 'teal'} />
          <StatCard icon="receipt" label="فاکتور در انتظار" value={faNum(dueInvoices.length)} tone={dueInvoices.length ? 'danger' : 'teal'} />
          <StatCard icon="wallet" label="مجموع پرداخت‌شده" value={price(totalPaid)} tone="teal" />
          <StatCard icon="bell" label="اعلان نخوانده" value={faNum(notifications.filter((n) => !n.read).length)} tone="violet" />
        </div>

        <div className="dash-grid mt-4">
          {/* پلن پشتیبانی */}
          <div className="card">
            <div className="flex items-center justify-between mb-2">
              <h3 style={{ fontSize: 16 }}>💠 پلن پشتیبانی فعال</h3>
              <Badge tone="gold">{activePlan.name}</Badge>
            </div>
            <div className="plan-progress-wrap">
              <div className="flex items-center justify-between small muted mb-2">
                <span>شروع: {new Intl.DateTimeFormat('fa-IR').format(planStart)}</span>
                <span>پایان: {new Intl.DateTimeFormat('fa-IR').format(planEnd)}</span>
              </div>
              <div className="plan-progress"><div style={{ width: `${Math.min(100, progress)}%` }} /></div>
              <p className="small muted mt-2">سرویس شما تا {faNum(Math.max(1, Math.round(activePlan.durationMonths * 30 * (1 - progress / 100))))} روز دیگر فعال است.</p>
            </div>
            <Link to="/panel/plans" className="btn btn-outline-gold btn-sm mt-2"><Icon name="refresh" size={15} /> تمدید پشتیبانی</Link>
          </div>

          {/* فعالیت‌های اخیر */}
          <div className="card">
            <h3 className="mb-3" style={{ fontSize: 16 }}>🕓 فعالیت‌های اخیر</h3>
            <div className="timeline">
              {activities.map((a) => (
                <div key={a.id} className="tl-item">
                  <span className={`tl-dot t-${a.tone ?? 'muted'}`}><Icon name={a.icon} size={13} /></span>
                  <div><p className="small">{a.text}</p><span className="small muted" style={{ fontSize: 11 }}>{timeAgo(a.date)}</span></div>
                </div>
              ))}
            </div>
          </div>

          {/* دسترسی سریع */}
          <div className="card">
            <h3 className="mb-3" style={{ fontSize: 16 }}>⚡ دسترسی سریع</h3>
            <div className="quick-grid">
              <Link to="/panel/support" className="quick-item"><Icon name="ticket" size={18} /><span className="small">تیکت جدید</span></Link>
              <Link to="/panel/invoices" className="quick-item"><Icon name="receipt" size={18} /><span className="small">پرداخت فاکتور</span></Link>
              <Link to="/panel/invoices" className="quick-item"><Icon name="download" size={18} /><span className="small">دانلود فاکتور</span></Link>
              <Link to="/consultation" className="quick-item"><Icon name="rocket" size={18} /><span className="small">مشاوره</span></Link>
              <Link to="/products" className="quick-item"><Icon name="cart" size={18} /><span className="small">فروشگاه</span></Link>
              <Link to="/careers" className="quick-item"><Icon name="clip" size={18} /><span className="small">آزمون استخدام</span></Link>
            </div>
          </div>

          {/* وضعیت سبد */}
          <div className="card">
            <h3 className="mb-2" style={{ fontSize: 16 }}>🛒 سبد خرید</h3>
            {items.length === 0 ? (
              <p className="small muted">سبد شما خالی است.</p>
            ) : (
              <p className="small mb-2">{faNum(items.length)} کالا — <b className="grad-gold-text">{price(cartTotal(items))}</b></p>
            )}
            <Link to="/cart" className="btn btn-teal btn-sm"><Icon name="cart" size={15} /> مشاهده سبد</Link>
          </div>
        </div>
      </PanelShell>
    </RequireAuth>
  );
}

export default Dashboard;
