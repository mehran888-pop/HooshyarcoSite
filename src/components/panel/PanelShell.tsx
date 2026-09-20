// پوسته پنل کاربری — سایدبار دسکتاپ + تب‌بار موبایل
import { Link, NavLink, useNavigate } from 'react-router-dom';
import Icon from '../shared/Icon';
import { Avatar } from '../shared/ui';
import { useAuth, useCart, cartItemsCount, useChat } from '../../store';
import { faNum } from '../../lib/format';

export interface PanelNavItem { to: string; end?: boolean; icon: string; label: string; badge?: number }

export function PanelShell({ title, subtitle, nav, children, accent = 'gold', notifPath = '/panel/notifications' }: {
  title: string; subtitle?: string; nav: PanelNavItem[]; children: React.ReactNode; accent?: 'gold' | 'teal'; notifPath?: string;
}) {
  const { session, logout } = useAuth();
  const items = useCart((s) => s.items);
  const { open, toggle } = useChat();
  const navigate = useNavigate();
  const cartCount = cartItemsCount(items);

  return (
    <div className="panel-layout">
      <aside className="panel-sidebar glass">
        <Link to="/" className="panel-brand">
          <span className="logo-mark">ه</span>
          <span><b>هوش‌یار</b><i className="small muted">پنل کاربری</i></span>
        </Link>

        <div className="panel-user">
          <Avatar name={session?.user.name ?? '؟'} src={session?.user.avatar} size={44} />
          <div>
            <b className="small">{session?.user.name}</b>
            <div className="small muted mono ltr" style={{ fontSize: 11 }}>{session?.user.mobile}</div>
          </div>
        </div>

        <nav className="panel-nav">
          {nav.map((n) => (
            <NavLink key={n.to} to={n.to} end={n.end} className={({ isActive }) => `panel-nav-item ${isActive ? 'active' : ''}`}>
              <Icon name={n.icon} size={19} />
              <span>{n.label}</span>
              {n.badge ? <span className={`nav-badge ${accent === 'teal' ? 'b-teal' : 'b-gold'}`}>{faNum(n.badge)}</span> : null}
            </NavLink>
          ))}
        </nav>

        <div className="panel-side-actions">
          <button className="side-btn" onClick={toggle}><Icon name="chat" size={17} /> چت آنلاین {!open && <span className="pulse-dot" />}</button>
          <Link to="/cart" className="side-btn"><Icon name="cart" size={17} /> سبد خرید {cartCount > 0 && <span className="small" style={{ color: 'var(--teal)' }}>({faNum(cartCount)})</span>}</Link>
          <button className="side-btn danger" onClick={() => { logout(); navigate('/'); }}><Icon name="logout" size={17} /> خروج از حساب</button>
        </div>
      </aside>

      {/* تب‌بار موبایل */}
      <nav className="panel-mobile-tabs">
        {nav.slice(0, 5).map((n) => (
          <NavLink key={n.to} to={n.to} end={n.end} className={({ isActive }) => `panel-mtab ${isActive ? 'active' : ''}`}>
            <Icon name={n.icon} size={20} />
            <span>{n.label}</span>
          </NavLink>
        ))}
      </nav>

      <main className="panel-main">
        <header className="panel-topbar">
          <div>
            <h1 style={{ fontSize: 20 }}>{title}</h1>
            {subtitle && <span className="small muted">{subtitle}</span>}
          </div>
          <div className="flex items-center gap-2">
            <Link to={notifPath} className="icon-btn"><Icon name="bell" size={18} /></Link>
          </div>
        </header>
        <div className="panel-content fade-up">{children}</div>
      </main>
    </div>
  );
}

export function StatCard({ icon, label, value, tone = 'gold', sub, onClick }: {
  icon: string; label: string; value: React.ReactNode; tone?: 'gold' | 'teal' | 'violet' | 'danger'; sub?: string; onClick?: () => void;
}) {
  const tones: Record<string, string> = {
    gold: 'var(--gold-soft)', teal: 'var(--teal-soft)', violet: 'var(--violet-soft)', danger: 'var(--danger-soft)',
  };
  const colors: Record<string, string> = { gold: 'var(--gold)', teal: 'var(--teal)', violet: '#c4b5fd', danger: 'var(--danger)' };
  return (
    <button className="card stat-card" onClick={onClick} style={{ textAlign: 'right', cursor: onClick ? 'pointer' : 'default' }}>
      <span className="stat-ic" style={{ background: tones[tone], color: colors[tone] }}><Icon name={icon} size={22} /></span>
      <span className="small muted">{label}</span>
      <b style={{ fontSize: 22, lineHeight: 1.4 }}>{value}</b>
      {sub && <span className="small muted">{sub}</span>}
    </button>
  );
}
