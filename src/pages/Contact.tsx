// صفحه تماس با ما — فرم متصل به گراویتی‌فرم
import { useState } from 'react';
import { Link } from 'react-router-dom';
import { Page } from '../components/shared/Overlay';
import { Field, Input, Textarea, Button, Select } from '../components/shared/ui';
import { useToast } from '../components/shared/Overlay';
import Icon from '../components/shared/Icon';
import { SITE, GF } from '../config/site';
import { isValidMobile } from '../lib/format';
import { gfService } from '../services/gravityforms';

export function Contact() {
  const toast = useToast();
  const [sending, setSending] = useState(false);
  const [form, setForm] = useState({ name: '', mobile: '', email: '', subject: 'پشتیبانی', message: '' });
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (form.name.trim().length < 2) return toast('لطفاً نام خود را وارد کنید.', 'error');
    if (!isValidMobile(form.mobile)) return toast('شماره موبایل معتبر وارد کنید (۰۹xxxxxxxxx).', 'error');
    if (form.message.trim().length < 5) return toast('متن پیام کوتاه است.', 'error');
    setSending(true);
    try {
      await gfService.submit(GF.forms.contact, form, 'contact');
      toast('پیام شما با موفقیت ثبت شد. به‌زودی با شما تماس می‌گیریم. ✅');
      setForm({ name: '', mobile: '', email: '', subject: 'پشتیبانی', message: '' });
    } catch {
      toast('خطا در ارسال پیام. دوباره تلاش کنید.', 'error');
    } finally {
      setSending(false);
    }
  };

  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="phone" size={14} /> تماس با ما</span>
          <h1>در <span className="grad-text">تماس</span> باشیم</h1>
          <p className="muted">برای هر سوال، پروژه یا همکاری، پیام بگذارید.</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 40 }}>
        <div className="container">
          <div className="contact-grid">
            <div className="contact-info">
              <div className="card mb-2">
                <h3 className="mb-2" style={{ fontSize: 18 }}>اطلاعات تماس</h3>
                <ul className="f-contact" style={{ gap: 18, display: 'flex', flexDirection: 'column' }}>
                  <li><Icon name="pin" size={18} /><div><b className="small">آدرس</b><div className="small muted">{SITE.address}</div></div></li>
                  <li><Icon name="phone" size={18} /><div><b className="small">تلفن</b><div className="small muted ltr">{SITE.mobile}</div></div></li>
                  <li><Icon name="mail" size={18} /><div><b className="small">ایمیل</b><div className="small muted ltr">{SITE.email}</div></div></li>
                </ul>
              </div>
              <div className="card">
                <h3 className="mb-2" style={{ fontSize: 18 }}>پاسخ سریع</h3>
                <p className="small muted">در ساعات اداری (شنبه تا چهارشنبه) پاسخگوی شما هستیم. برای موارد فوری از چت آنلاین یا تیکت پشتیبانی استفاده کنید.</p>
                <div className="flex gap-2 wrap mt-3">
                  <Link to="/panel/support" className="btn btn-teal btn-sm"><Icon name="ticket" size={15} /> پشتیبانی آنلاین</Link>
                  <Link to="/consultation" className="btn btn-outline-gold btn-sm">درخواست مشاوره</Link>
                </div>
              </div>
            </div>

            <form className="card" style={{ padding: 30 }} onSubmit={submit}>
              <h3 className="mb-3" style={{ fontSize: 18 }}>فرم تماس</h3>
              <div className="grid-2">
                <Field label="نام و نام خانوادگی *"><Input value={form.name} onChange={(e) => set('name', e.target.value)} placeholder="مثلاً: علی محمدی" /></Field>
                <Field label="شماره موبایل *"><Input value={form.mobile} onChange={(e) => set('mobile', e.target.value)} placeholder="۰۹۱۲..." inputMode="numeric" /></Field>
              </div>
              <div className="grid-2">
                <Field label="ایمیل"><Input type="email" value={form.email} onChange={(e) => set('email', e.target.value)} placeholder="you@mail.com" /></Field>
                <Field label="موضوع"><Select value={form.subject} onChange={(e) => set('subject', e.target.value)}>
                  <option>پشتیبانی</option><option>فروش</option><option>استخدام</option><option>همکاری</option><option>سایر</option>
                </Select></Field>
              </div>
              <Field label="پیام شما *"><Textarea value={form.message} onChange={(e) => set('message', e.target.value)} placeholder="متن پیام..." /></Field>
              <Button type="submit" variant="primary" block disabled={sending} icon={sending ? 'loader' : 'send'}>
                {sending ? 'در حال ارسال...' : 'ارسال پیام'}
              </Button>
              <p className="small muted mt-2" style={{ textAlign: 'center' }}>این فرم به گراویتی‌فرم متصل است (شناسه فرم: {GF.forms.contact}).</p>
            </form>
          </div>
        </div>
      </section>

      <div className="container" style={{ marginBottom: 60 }}>
        <div className="map-mock"><Icon name="pin" size={34} style={{ color: 'var(--gold)' }} /><span className="small">{SITE.address}</span></div>
      </div>
    </Page>
  );
}
