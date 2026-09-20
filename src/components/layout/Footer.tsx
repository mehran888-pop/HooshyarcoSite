// فوتر — اطلاعات تماس، لینک‌ها و خبرنامه (متصل به وردپرس/گراویتی‌فرم)
import { Link } from 'react-router-dom';
import Icon from '../shared/Icon';
import { Input, Button } from '../shared/ui';
import { SITE, NAV_LINKS } from '../../config/site';
import { faNum } from '../../lib/format';
import { useState } from 'react';

export default function Footer() {
  const [email, setEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  return (
    <footer className="site-footer" id="footer">
      <div className="footer-cta">
        <div className="container-wide cta-box">
          <div>
            <h2 className="grad-text">پروژه بعدی شما را با هم می‌سازیم 🚀</h2>
            <p className="muted">همین حالا مشاوره رایگان بگیرید و کسب‌وکارتان را هوشمند کنید.</p>
          </div>
          <div className="cta-actions">
            <Button as="link" to="/consultation" variant="primary" size="lg" icon="rocket">درخواست مشاوره رایگان</Button>
            <Button as="link" to="/products" variant="ghost" size="lg">مشاهده خدمات و محصولات</Button>
          </div>
        </div>
      </div>

      <div className="container-wide footer-grid">
        <div className="f-col">
          <div className="f-logo">
            <span className="logo-mark">ه</span>
            <div>
              <b>{SITE.name}</b>
              <i className="small muted">{SITE.tagline}</i>
            </div>
          </div>
          <p className="f-desc">{SITE.description}</p>
          <div className="f-socials">
            {[
              { icon: 'instagram', href: SITE.socials.instagram },
              { icon: 'linkedin', href: SITE.socials.linkedin },
              { icon: 'github', href: SITE.socials.github },
              { icon: 'send-h', href: SITE.socials.telegram },
            ].map((s) => (
              <a key={s.icon} href={s.href} className="social-btn" aria-label={s.icon} target="_blank" rel="noreferrer"><Icon name={s.icon} size={18} /></a>
            ))}
          </div>
        </div>

        <div className="f-col">
          <h4>دسترسی سریع</h4>
          <ul>
            {NAV_LINKS.slice(0, 6).map((l) => <li key={l.to}><Link to={l.to}>{l.label}</Link></li>)}
          </ul>
        </div>

        <div className="f-col">
          <h4>خدمات</h4>
          <ul>
            <li><Link to="/services">هوش مصنوعی</Link></li>
            <li><Link to="/services">طراحی سایت و فروشگاه</Link></li>
            <li><Link to="/services">نرم‌افزار و اپلیکیشن</Link></li>
            <li><Link to="/services">CRM و اتوماسیون</Link></li>
            <li><Link to="/careers">استخدام و آزمون</Link></li>
          </ul>
        </div>

        <div className="f-col">
          <h4>تماس با ما</h4>
          <ul className="f-contact">
            <li><Icon name="pin" size={16} /><span className="small">{SITE.address}</span></li>
            <li><Icon name="phone" size={16} /><span className="small ltr">{SITE.mobile}</span></li>
            <li><Icon name="mail" size={16} /><span className="small ltr">{SITE.email}</span></li>
          </ul>
          <div className="f-newsletter mt-2">
            <p className="small" style={{ fontWeight: 600 }}>عضویت در خبرنامه</p>
            {subscribed ? (
              <span className="badge badge-teal"><Icon name="check" size={13} /> عضویت شما ثبت شد</span>
            ) : (
              <form className="inline-flex gap-2" onSubmit={(e) => { e.preventDefault(); if (email) setSubscribed(true); }}>
                <Input type="email" placeholder="ایمیل شما" value={email} onChange={(e) => setEmail(e.target.value)} style={{ height: 42 }} />
                <Button type="submit" variant="teal" size="sm" icon="send" style={{ height: 42 }} />
              </form>
            )}
          </div>
        </div>
      </div>

      <div className="footer-bottom">
        <div className="container-wide flex items-center justify-between wrap gap-2">
          <span className="small muted">© {faNum(new Date().getFullYear())} {SITE.name} — تمامی حقوق محفوظ است.</span>
          <div className="flex gap-3 small muted">
            <span className="inline-flex items-center gap-1"><Icon name="shield" size={14} style={{ color: 'var(--teal)' }} /> امنیت SSL</span>
            <span className="inline-flex items-center gap-1"><Icon name="receipt" size={14} style={{ color: 'var(--gold)' }} /> درگاه رسمی پرداخت</span>
            <span className="inline-flex items-center gap-1"><Icon name="headphones" size={14} style={{ color: 'var(--violet)' }} /> پشتیبانی ۷/۲۴</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
