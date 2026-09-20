// کامپوننت‌های بخش‌های عمومی (سرویس، پروژه، تیم، نظر مشتری)
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import type { Project, Service, TeamMember, Testimonial } from '../../lib/types';
import Icon from '../shared/Icon';
import { Avatar, Badge, Button, Rating } from '../shared/ui';
import { price } from '../../lib/format';

const itemAnim = { hidden: { opacity: 0, y: 24 }, show: (i: number) => ({ opacity: 1, y: 0, transition: { delay: i * 0.07, duration: .5 } }) };

export function ServiceCard({ s, i }: { s: Service; i: number }) {
  return (
    <motion.div className={`card card-hover service-card ${s.popular ? 'popular' : ''}`} custom={i} initial="hidden" whileInView="show" viewport={{ once: true, margin: '-40px' }} variants={itemAnim}>
      {s.popular && <span className="popular-flag"><Icon name="zap" size={12} /> پرطرفدار</span>}
      <div className="svc-icon"><Icon name={s.icon} size={30} /></div>
      <h3>{s.title}</h3>
      <p className="muted small" style={{ flex: 1 }}>{s.excerpt}</p>
      <ul className="svc-features">
        {s.features.slice(0, 3).map((f) => <li key={f}><Icon name="check" size={14} style={{ color: 'var(--teal)' }} />{f}</li>)}
      </ul>
      <div className="flex items-center justify-between mt-2">
        <span className="small muted">شروع از <b className="grad-gold-text">{price(s.priceFrom ?? 0)}</b></span>
        <Link to={`/services/${s.slug}`} className="btn btn-outline-gold btn-sm">جزئیات</Link>
      </div>
    </motion.div>
  );
}

export function ProjectCard({ p, i }: { p: Project; i: number }) {
  return (
    <motion.div className="project-card" custom={i} initial="hidden" whileInView="show" viewport={{ once: true, margin: '-40px' }} variants={itemAnim}>
      <div className={`project-thumb theme-${i % 4}`}>
        <Icon name={['code', 'cart', 'grad', 'brain'][i % 4]} size={40} />
        <span className="badge badge-dark project-year">{p.year}</span>
      </div>
      <div className="project-body">
        <div className="flex items-center justify-between gap-2">
          <Badge tone="teal">{p.category}</Badge>
          <span className="small muted">مشتری: {p.client}</span>
        </div>
        <h3>{p.title}</h3>
        <p className="muted small">{p.description}</p>
        <div className="flex wrap gap-1 mt-2">
          {p.tags.map((t) => <span key={t} className="tech-chip" dir="ltr">{t}</span>)}
        </div>
      </div>
    </motion.div>
  );
}

export function TeamCard({ m, i }: { m: TeamMember; i: number }) {
  return (
    <motion.div className="card card-hover team-card text-center" custom={i} initial="hidden" whileInView="show" viewport={{ once: true, margin: '-40px' }} variants={itemAnim}>
      <Avatar name={m.name} src={m.avatar} size={88} className="ring-gold" />
      <h3 style={{ fontSize: 17 }}>{m.name}</h3>
      <span className="badge badge-gold mt-1">{m.role}</span>
      <p className="muted small mt-2">{m.bio}</p>
      <div className="flex wrap gap-1 items-center justify-center mt-2">
        {m.skills.map((s) => <span key={s} className="tech-chip">{s}</span>)}
      </div>
      <div className="flex gap-2 items-center justify-center mt-3">
        {m.socials.l && <a className="social-btn" href={m.socials.l}><Icon name="linkedin" size={15} /></a>}
        {m.socials.t && <a className="social-btn" href={m.socials.t}><Icon name="send-h" size={15} /></a>}
        {m.socials.i && <a className="social-btn" href={m.socials.i}><Icon name="instagram" size={15} /></a>}
        {m.socials.g && <a className="social-btn" href={m.socials.g}><Icon name="github" size={15} /></a>}
      </div>
    </motion.div>
  );
}

export function TestimonialCard({ t, i }: { t: Testimonial; i: number }) {
  return (
    <motion.div className="card testimonial-card" custom={i} initial="hidden" whileInView="show" viewport={{ once: true, margin: '-40px' }} variants={itemAnim}>
      <div className="flex items-center justify-between">
        <Rating value={t.rating} />
        <Icon name="chat-circle" size={26} style={{ color: 'rgba(246,196,83,.35)' }} />
      </div>
      <p className="testimonial-text">“{t.text}”</p>
      <div className="flex items-center gap-3 mt-3">
        <Avatar name={t.name} src={t.avatar} size={46} />
        <div>
          <b style={{ fontSize: 14.5 }}>{t.name}</b>
          <div className="small muted">{t.role} — {t.company}</div>
        </div>
      </div>
    </motion.div>
  );
}

export function FaqList({ faqs }: { faqs: { q: string; a: string }[] }) {
  return (
    <div className="faq-list">
      {faqs.map((f, i) => (
        <details key={i} className="faq-item">
          <summary>
            <span>{f.q}</span>
            <Icon name="chevron-down" size={18} className="faq-chev" />
          </summary>
          <p className="muted small">{f.a}</p>
        </details>
      ))}
    </div>
  );
}

export function CTASection({ title = 'آماده شروع پروژه هستید؟', desc = 'همین حالا مشاوره رایگان بگیرید.', dual = false }: { title?: string; desc?: string; dual?: boolean }) {
  return (
    <section className="section-sm">
      <div className="container">
        <div className="cta-mini">
          <div>
            <h2 className="grad-text">{title}</h2>
            <p className="muted">{desc}</p>
          </div>
          <div className="flex gap-2 wrap">
            <Button as="link" to="/consultation" variant="primary" icon="rocket">درخواست مشاوره</Button>
            {dual && <Button as="link" to="/contact" variant="ghost">تماس با ما</Button>}
          </div>
        </div>
      </div>
    </section>
  );
}
