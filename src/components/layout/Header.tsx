// هدر اصلی — منو از تنظیمات NAV_LINKS (معادل فهرست وردپرس) خوانده می‌شود
import { useEffect, useState } from 'react';
import { Link, NavLink, useLocation, useNavigate } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import Icon from '../shared/Icon';
import { Avatar } from '../shared/ui';
import { NAV_LINKS, SITE } from '../../config/site';
import { useAuth, useCart, cartItemsCount, useNotifications } from '../../store';
import { faNum } from '../../lib/format';

function Logo({ onClick }: { onClick?: () => void }) {
  return (
    <Link to="/" className="logo" onClick={onClick}>
      <span className="logo-mark">ه</span>
      <span className="logo-text">
        <b>هوش‌یار پاری‌نگر</b>
        <i>هوش مصنوعی • نرم‌افزار • طراحی سایت</i>
      </span>
    </Link>
  );
}

export default function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const { session } = useAuth();
  const items = useCart((s) => s.items);
  const unreadNtf = useNotifications((s) => s.items.filter((n) => !n.read).length);
  const navigate = useNavigate();
  const loc = useLocation();

  useEffect(() => {
    const h = () => setScrolled(window.scrollY > 20);
    h();
    window.addEventListener('scroll', h);
    return () => window.removeEventListener('scroll', h);
  }, []);

  useEffect(() => setMobileOpen(false), [loc.pathname]);

  const goPanel = () => navigate(session?.isStaff ? '/admin' : '/panel');

  return (
    <>
      <header className={`site-header ${scrolled ? 'scrolled' : ''}`}>
        <div className="container-wide header-inner">
          <Logo />
          <nav className="nav-desktop">
            {NAV_LINKS.map((l) => (
              <NavLink key={l.to} to={l.to} end={l.to === '/'} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}>
                {l.label}
              </NavLink>
            ))}
          </nav>
          <div className="header-actions">
            <button className="icon-btn head-bell" title="اعلان‌ها" onClick={() => navigate(session ? (session.isStaff ? '/admin/notifications' : '/panel/notifications') : '/auth')}>
              <Icon name="bell" size={19} />
              {unreadNtf > 0 && <span className="dot-gold">{faNum(unreadNtf)}</span>}
            </button>
            <Link to="/cart" className="icon-btn head-cart" title="سبد خرید">
              <Icon name="cart" size={19} />
              {items.length > 0 && <span className="dot-teal">{faNum(cartItemsCount(items))}</span>}
            </Link>
            {session ? (
              <button className="user-chip" onClick={goPanel} title={session.user.name}>
                <Avatar name={session.user.name} src={session.user.avatar} size={36} />
              </button>
            ) : (
              <>
                <button className="btn btn-ghost btn-sm login-btn-desktop" onClick={() => navigate('/auth')}>
                  <Icon name="user" size={16} /> ورود / ثبت‌نام
                </button>
                <button className="btn btn-primary btn-sm" onClick={() => navigate('/consultation')}>درخواست مشاوره</button>
              </>
            )}
            <button className="icon-btn nav-toggle" onClick={() => setMobileOpen((v) => !v)} aria-label="منو">
              <Icon name={mobileOpen ? 'x' : 'menu'} size={22} />
            </button>
          </div>
        </div>
      </header>

      {/* فوتر موبایلی (تب‌بار اپ‌شکل) */}
      <nav className="mobile-tabbar">
        <Tab to="/" icon="home" label="خانه" end />
        <Tab to="/services" icon="layers" label="خدمات" />
        <Tab to="/products" icon="cart" label="فروشگاه" />
        <Tab to="/consultation" icon="chat-circle" label="مشاوره" />
        <Tab to={session ? (session.isStaff ? '/admin' : '/panel') : '/auth'} icon="user" label={session ? 'پنل' : 'ورود'} />
      </nav>

      {/* منوی موبایل تمام‌صفحه */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div className="mobile-menu" initial={{ opacity: 0, y: -12 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -12 }}>
            <div className="mobile-menu-links">
              <Link to="/" className="mb-logo">هوش‌یار پاری‌نگر</Link>
              {NAV_LINKS.map((l) => (
                <Link key={l.to} to={l.to} className="mb-link">{l.label}</Link>
              ))}
              {session ? (
                <>
                  <button className="btn btn-primary" onClick={goPanel}><Icon name="dashboard" size={17} /> ورود به پنل کاربری</button>
                  <button className="btn btn-ghost" onClick={() => { navigate('/panel/logout'); }}><Icon name="logout" size={17} /> خروج</button>
                </>
              ) : (
                <>
                  <button className="btn btn-primary" onClick={() => navigate('/auth')}><Icon name="user" size={17} /> ورود / ثبت‌نام با موبایل</button>
                  <button className="btn btn-ghost" onClick={() => navigate('/consultation')}>درخواست مشاوره</button>
                </>
              )}
            </div>
            <div className="mb-contact">
              <span className="small muted">{SITE.mobile} — {SITE.email}</span>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}

function Tab({ to, icon, label, end }: { to: string; icon: string; label: string; end?: boolean }) {
  return (
    <NavLink to={to} end={end} className={({ isActive }) => `tab-item ${isActive ? 'active' : ''}`}>
      <Icon name={icon} size={20} />
      <span>{label}</span>
    </NavLink>
  );
}
