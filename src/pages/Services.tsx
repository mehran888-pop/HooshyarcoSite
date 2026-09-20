// صفحه خدمات و جزئیات خدمت
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Page } from '../components/shared/Overlay';
import { SectionHead } from '../components/shared/ui';
import { ServiceCard, CTASection } from '../components/shared/Sections';
import Icon from '../components/shared/Icon';
import { contentService } from '../services/wordpress';
import { price, faNum } from '../lib/format';
import type { Service } from '../lib/types';

export function Services() {
  const [services, setServices] = useState<Service[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('همه');

  useEffect(() => {
    contentService.getServices().then((s) => { setServices(s); setLoading(false); }).catch(() => setLoading(false));
  }, []);

  const cats = ['همه', ...Array.from(new Set(services.map((s) => s.category)))];
  const filtered = filter === 'همه' ? services : services.filter((s) => s.category === filter);

  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="sparkles" size={14} /> خدمات ما</span>
          <h1>خدمات <span className="grad-text">هوش‌یار پاری‌نگر</span></h1>
          <p className="muted">راهکارهای کامل دیجیتال برای رشد و هوشمندسازی کسب‌وکار شما.</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 40 }}>
        <div className="container">
          <div className="filter-row">
            {cats.map((c) => (
              <button key={c} className={`filter-chip ${filter === c ? 'active' : ''}`} onClick={() => setFilter(c)}>{c}</button>
            ))}
          </div>
          {loading ? (
            <div className="text-center" style={{ padding: 60 }}><Icon name="loader" size={30} className="spin" /></div>
          ) : (
            <div className="grid-3 mt-3">
              {filtered.map((s, i) => <ServiceCard key={s.id} s={s} i={i} />)}
            </div>
          )}
        </div>
      </section>
      <CTASection />
    </Page>
  );
}

export function ServiceDetail() {
  const { slug } = useParams();
  const [service, setService] = useState<Service | undefined>();

  useEffect(() => {
    contentService.getServices().then((s) => setService(s.find((x) => x.slug === slug)));
  }, [slug]);

  if (!service) return (
    <Page><div className="container text-center" style={{ padding: 120 }}><Icon name="loader" size={30} className="spin" /></div></Page>
  );

  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow">{service.category}</span>
          <h1>{service.title}</h1>
          <p className="muted" style={{ maxWidth: 680, margin: '0 auto' }}>{service.description}</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 40 }}>
        <div className="container" style={{ maxWidth: 900 }}>
          <div className="svc-detail-grid">
            <div className="card">
              <div className="svc-icon"><Icon name={service.icon} size={30} /></div>
              <h2 className="mb-2" style={{ fontSize: 20 }}>چه چیزی دریافت می‌کنید؟</h2>
              <ul className="svc-features">
                {service.features.map((f) => <li key={f}><Icon name="check-circle" size={17} style={{ color: 'var(--teal)' }} />{f}</li>)}
              </ul>
            </div>
            <div className="card price-side">
              <span className="small muted">شروع قیمت از</span>
              <div className="grad-gold-text" style={{ fontSize: 34, fontWeight: 900, margin: '6px 0' }}>{price(service.priceFrom ?? 0)}</div>
              <ul className="svc-features mb-3">
                <li><Icon name="check" size={14} style={{ color: 'var(--teal)' }} /> مشاوره اولیه رایگان</li>
                <li><Icon name="check" size={14} style={{ color: 'var(--teal)' }} /> ضمانت کیفیت</li>
                <li><Icon name="check" size={14} style={{ color: 'var(--teal)' }} /> پشتیبانی پس از تحویل</li>
              </ul>
              <Link to="/consultation" className="btn btn-primary btn-block"><Icon name="rocket" size={17} /> درخواست مشاوره</Link>
              <Link to="/cart" className="btn btn-ghost btn-block mt-1">مشاهده تعرفه‌ها</Link>
            </div>
          </div>

          <div className="process-steps mt-4">
            {['مشاوره و نیازسنجی', 'طراحی و پیشنهاد', 'توسعه و اجرا', 'تحویل و پشتیبانی'].map((step, i) => (
              <div key={step} className="step">
                <span className="step-num grad-text">{faNum(i + 1)}</span>
                <span className="small">{step}</span>
              </div>
            ))}
          </div>
        </div>
      </section>
      <CTASection />
    </Page>
  );
}
