// صفحه اصلی
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Page } from '../components/shared/Overlay';
import { SectionHead } from '../components/shared/ui';
import { FaqList, ProjectCard, ServiceCard, TestimonialCard } from '../components/shared/Sections';
import Icon from '../components/shared/Icon';
import { contentService } from '../services/wordpress';
import { SITE } from '../config/site';
import { faNum } from '../lib/format';
import type { Faq, Project, Service, Testimonial } from '../lib/types';

const heroAnim = {
  hidden: { opacity: 0, y: 26 },
  show: (i: number) => ({ opacity: 1, y: 0, transition: { delay: i * 0.1, duration: .6, ease: [0.22, 1, 0.36, 1] as const } }),
};

export default function Home() {
  const [services, setServices] = useState<Service[]>([]);
  const [projects, setProjects] = useState<Project[]>([]);
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [faqs, setFaqs] = useState<Faq[]>([]);

  useEffect(() => {
    Promise.all([contentService.getServices(), contentService.getProjects(), contentService.getTestimonials(), contentService.getFaqs()])
      .then(([s, p, t, f]) => { setServices(s); setProjects(p); setTestimonials(t); setFaqs(f); })
      .catch(() => {});
  }, []);

  return (
    <Page>
      {/* ===== Hero ===== */}
      <section className="hero">
        <div className="hero-bg" />
        <div className="container hero-inner">
          <motion.div className="hero-content" initial="hidden" animate="show">
            <motion.span className="eyebrow" custom={0} variants={heroAnim}><span className="pulse-dot" /> {SITE.nameEn}</motion.span>
            <motion.h1 custom={1} variants={heroAnim}>
              هوش مصنوعی در خدمت <span className="grad-text">رشد کسب‌وکار</span> شما
            </motion.h1>
            <motion.p className="hero-sub" custom={2} variants={heroAnim}>{SITE.description}</motion.p>
            <motion.div className="hero-actions" custom={3} variants={heroAnim}>
              <Link to="/consultation" className="btn btn-primary btn-lg"><Icon name="rocket" size={19} /> درخواست مشاوره رایگان</Link>
              <Link to="/services" className="btn btn-ghost btn-lg"><Icon name="layers" size={19} /> مشاهده خدمات</Link>
            </motion.div>
            <motion.div className="hero-stats" custom={4} variants={heroAnim}>
              {[
                { v: '+۲۵۰', l: 'پروژه موفق' },
                { v: '+۱۸۰', l: 'مشتری فعال' },
                { v: '۹۹٪', l: 'رضایت مشتری' },
                { v: '۷/۲۴', l: 'پشتیبانی' },
              ].map((s) => (
                <div key={s.l} className="stat">
                  <b className="grad-text">{s.v}</b>
                  <span className="small muted">{s.l}</span>
                </div>
              ))}
            </motion.div>
          </motion.div>

          <motion.div className="hero-visual" initial={{ opacity: 0, scale: .9 }} animate={{ opacity: 1, scale: 1 }} transition={{ duration: .7, delay: .3 }}>
            <div className="orbit orbit-1" />
            <div className="orbit orbit-2" />
            <div className="brain-core">
              <Icon name="brain" size={64} />
            </div>
            {['bot', 'code', 'cart', 'grad', 'shield', 'chat'].map((ic, i) => (
              <div key={ic} className={`float-chip fc-${i + 1}`}><Icon name={ic} size={20} /></div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* ===== ویژگی‌ها ===== */}
      <section className="section">
        <div className="container">
          <SectionHead eyebrow="چرا هوش‌یار؟" title={<>فناوری کامل برای <span className="grad-text">کسب‌وکار شما</span></>} desc="از طراحی سایت و فروشگاه تا هوش مصنوعی و اتوماسیون؛ همه نیازهای دیجیتال شما در یک تیم." />
          <div className="grid-4">
            {[
              { icon: 'brain', t: 'هوش مصنوعی', d: 'چت‌بات، تحلیل داده و اتوماسیون هوشمند' },
              { icon: 'globe', t: 'طراحی سایت', d: 'سایت و فروشگاه مدرن و سریع' },
              { icon: 'code', t: 'نرم‌افزار اختصاصی', d: 'وب‌اپ و اپلیکیشن موبایل سفارشی' },
              { icon: 'shield', t: 'امنیت و پشتیبانی', d: 'حفاظت ۷/۲۴ و جلوگیری از اسپم' },
            ].map((f) => (
              <div key={f.t} className="card card-hover text-center feature-card">
                <div className="feature-icon"><Icon name={f.icon} size={28} /></div>
                <h3 style={{ fontSize: 16 }}>{f.t}</h3>
                <p className="muted small">{f.d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== خدمات ===== */}
      <section className="section" id="services">
        <div className="container">
          <SectionHead eyebrow="خدمات ما" title={<>راهکارهای <span className="grad-text">یکپارچه</span></>} desc="خدمات کامل دیجیتال، از ایده تا استقرار و پشتیبانی بلندمدت." />
          <div className="grid-3">
            {(services.length ? services : []).slice(0, 6).map((s, i) => <ServiceCard key={s.id} s={s} i={i} />)}
          </div>
          <div className="text-center mt-4">
            <Link to="/services" className="btn btn-outline-gold">مشاهده همه خدمات <Icon name="chevron-down" size={16} /></Link>
          </div>
        </div>
      </section>

      {/* ===== پروژه‌ها ===== */}
      <section className="section" id="projects">
        <div className="container">
          <SectionHead eyebrow="نمونه‌کارها" title={<>پروژه‌هایی که به آن‌ها <span className="grad-text">افتخار می‌کنیم</span></>} desc="گزیده‌ای از پروژه‌های موفق در حوزه‌های مختلف." />
          <div className="grid-3">
            {projects.slice(0, 6).map((p, i) => <ProjectCard key={p.id} p={p} i={i} />)}
          </div>
          <div className="text-center mt-4">
            <Link to="/projects" className="btn btn-outline-gold">همه پروژه‌ها</Link>
          </div>
        </div>
      </section>

      {/* ===== بنر پنل/PWA ===== */}
      <section className="section-sm">
        <div className="container">
          <div className="panel-banner">
            <div className="panel-banner-text">
              <h2 className="grad-text">پنل کاربری و اپلیکیشن اختصاصی</h2>
              <p className="muted">با شماره موبایل وارد شوید؛ پنل مشتری، فاکتور آنلاین، تیکت پشتیبانی، CRM و آزمون استخدام — همه جا در دسترس شما. همین قالب را روی گوشی خود نصب کنید!</p>
              <div className="flex gap-2 wrap mt-3">
                <Link to="/auth" className="btn btn-teal"><Icon name="user" size={17} /> ورود / ثبت‌نام</Link>
                <Link to="/careers" className="btn btn-ghost"><Icon name="clip" size={17} /> آزمون استخدامی</Link>
              </div>
            </div>
            <div className="phone-mockup">
              <div className="phone-screen">
                <div className="phone-app-icon">ه</div>
                <div className="phone-title">هوش‌یار پاری‌نگر</div>
                <div className="phone-links">
                  <span className="phone-row"><Icon name="dashboard" size={14} /> داشبورد</span>
                  <span className="phone-row"><Icon name="ticket" size={14} /> تیکت پشتیبانی</span>
                  <span className="phone-row"><Icon name="receipt" size={14} /> فاکتورها</span>
                  <span className="phone-row"><Icon name="cart" size={14} /> فروشگاه</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* ===== نظرات مشتریان ===== */}
      <section className="section" id="testimonials">
        <div className="container">
          <SectionHead eyebrow="نظرات مشتریان" title={<>اعتمادی که <span className="grad-text">ساخته‌ایم</span></>} />
          <div className="grid-2" style={{ alignItems: 'stretch' }}>
            {testimonials.map((t, i) => <TestimonialCard key={t.id} t={t} i={i} />)}
          </div>
          <div className="trust-bar mt-4">
            {['+۲۵۰ پروژه', '+۱۸۰ مشتری', '۹۹٪ رضایت', '۸ سال تجربه'].map((t, i) => (
              <div key={i} className="trust-item"><Icon name={['award', 'users', 'heart', 'clock'][i]} size={18} style={{ color: 'var(--gold)' }} /> <b>{t}</b></div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== سوالات ===== */}
      <section className="section" id="faq">
        <div className="container" style={{ maxWidth: 860 }}>
          <SectionHead eyebrow="سوالات متداول" title={<>پاسخ <span className="grad-text">پرسش‌های شما</span></>} />
          <FaqList faqs={faqs} />
        </div>
      </section>

      {/* ===== CTA ===== */}
      <section className="section-sm" style={{ paddingTop: 0 }}>
        <div className="container">
          <div className="cta-mini">
            <div>
              <h2 className="grad-text">همین امروز شروع کنید</h2>
              <p className="muted">مشاوره رایگان + تخفیف ویژه استقرار کامل پلتفرم.</p>
            </div>
            <div className="flex gap-2 wrap">
              <Link to="/consultation" className="btn btn-primary btn-lg"><Icon name="rocket" size={18} /> درخواست مشاوره</Link>
              <Link to="/contact" className="btn btn-ghost btn-lg">تماس با ما</Link>
            </div>
          </div>
        </div>
      </section>
    </Page>
  );
}
