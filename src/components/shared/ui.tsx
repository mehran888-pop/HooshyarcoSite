// کامپوننت‌های UI پایه
import { Link } from 'react-router-dom';
import type { ReactNode, ButtonHTMLAttributes, InputHTMLAttributes, TextareaHTMLAttributes, SelectHTMLAttributes } from 'react';
import Icon from './Icon';

type Variant = 'primary' | 'teal' | 'violet' | 'ghost' | 'outline-gold' | 'danger' | 'dark';

export function Button({ as, to, variant = 'primary', size, block, icon, children, className = '', ...rest }: {
  as?: 'link';
  to?: string;
  variant?: Variant;
  size?: 'sm' | 'lg';
  block?: boolean;
  icon?: string;
  children?: ReactNode;
  className?: string;
} & ButtonHTMLAttributes<HTMLButtonElement>) {
  const cls = ['btn', `btn-${variant}`, size ? `btn-${size}` : '', block ? 'btn-block' : '', className].filter(Boolean).join(' ');
  const content = (
    <>
      {icon && <Icon name={icon} size={size === 'sm' ? 16 : 19} />}
      {children}
    </>
  );
  if (as === 'link' && to) return <Link to={to} className={cls}>{content}</Link>;
  return <button className={cls} {...rest}>{content}</button>;
}

export function Field({ label, hint, error, children }: { label?: string; hint?: string; error?: string; children: ReactNode }) {
  return (
    <div className="field">
      {label && <label>{label}</label>}
      {children}
      {hint && !error && <span className="small muted">{hint}</span>}
      {error && <span className="small" style={{ color: 'var(--danger)' }}>{error}</span>}
    </div>
  );
}

export function Input(props: InputHTMLAttributes<HTMLInputElement>) {
  return <input className="input" {...props} />;
}
export function Textarea(props: TextareaHTMLAttributes<HTMLTextAreaElement>) {
  return <textarea className="input" {...props} />;
}
export function Select(props: SelectHTMLAttributes<HTMLSelectElement>) {
  return <select className="input" {...props} />;
}

export function Badge({ tone = 'muted', icon, children }: { tone?: 'gold' | 'teal' | 'violet' | 'danger' | 'success' | 'info' | 'warning' | 'muted'; icon?: string; children: ReactNode }) {
  return <span className={`badge badge-${tone}`}>{icon && <Icon name={icon} size={13} />}{children}</span>;
}

const palette = ['linear-gradient(135deg,#f6c453,#d99a2b)', 'linear-gradient(135deg,#2dd4bf,#0d9488)', 'linear-gradient(135deg,#8b5cf6,#6d28d9)', 'linear-gradient(135deg,#60a5fa,#2563eb)', 'linear-gradient(135deg,#f472b6,#db2777)', 'linear-gradient(135deg,#34d399,#059669)'];

export function Avatar({ name, src, size = 40, className = '' }: { name: string; src?: string; size?: number; className?: string }) {
  const bg = palette[(name?.charCodeAt(0) ?? 0) % palette.length];
  const initials = (name || '؟').trim().split(/\s+/).slice(0, 2).map((w) => w[0]).join(' ');
  if (src) return <img src={src} alt={name} className={`avatar ${className}`} style={{ width: size, height: size }} />;
  return (
    <span className={`avatar ${className}`} style={{ width: size, height: size, background: bg, display: 'grid', placeItems: 'center', fontSize: size * 0.4, fontWeight: 800, color: '#fff' }}>
      {initials}
    </span>
  );
}

export function Rating({ value, size = 16 }: { value: number; size?: number }) {
  return (
    <span className="inline-flex items-center gap-1" dir="ltr">
      {[1, 2, 3, 4, 5].map((i) => (
        <Icon key={i} name="star" size={size} className={i <= Math.round(value) ? 'text-gold' : ''} style={{ color: i <= Math.round(value) ? 'var(--gold)' : 'var(--surface-3)', fill: i <= Math.round(value) ? 'var(--gold)' : 'none' }} />
      ))}
    </span>
  );
}

export function Empty({ icon = 'package', title, text, action }: { icon?: string; title: string; text?: string; action?: ReactNode }) {
  return (
    <div className="text-center" style={{ padding: '48px 20px' }}>
      <div style={{ width: 72, height: 72, margin: '0 auto 18px', borderRadius: 24, background: 'var(--surface-2)', display: 'grid', placeItems: 'center', border: '1px solid var(--border)' }}>
        <Icon name={icon} size={32} style={{ color: 'var(--muted)' }} />
      </div>
      <h4 style={{ marginBottom: 6 }}>{title}</h4>
      {text && <p className="muted small mb-3">{text}</p>}
      {action}
    </div>
  );
}

export function SectionHead({ eyebrow, title, desc, grad }: { eyebrow?: string; title: ReactNode; desc?: string; grad?: boolean }) {
  return (
    <div className="section-head">
      {eyebrow && <span className="eyebrow"><Icon name="sparkles" size={14} /> {eyebrow}</span>}
      <h2 className={grad ? 'grad-text' : ''}>{title}</h2>
      {desc && <p>{desc}</p>}
      <div className="accent-line" style={{ marginTop: 18 }} />
    </div>
  );
}

export function Spinner({ size = 22, className = '' }: { size?: number; className?: string }) {
  return <Icon name="loader" size={size} className={`spin ${className}`} style={{ color: 'var(--muted)' }} />;
}

export function Toggle({ checked, onChange, label }: { checked: boolean; onChange: (v: boolean) => void; label?: string }) {
  return (
    <button type="button" onClick={() => onChange(!checked)} className="inline-flex items-center gap-2" style={{ cursor: 'pointer' }}>
      <span style={{ width: 42, height: 24, borderRadius: 99, background: checked ? 'var(--grad-teal)' : 'var(--surface-3)', position: 'relative', transition: '.2s', border: '1px solid var(--border-strong)' }}>
        <span style={{ position: 'absolute', top: 2, right: checked ? 20 : 2, width: 18, height: 18, borderRadius: '50%', background: '#fff', transition: '.2s' }} />
      </span>
      {label && <span className="small">{label}</span>}
    </button>
  );
}
