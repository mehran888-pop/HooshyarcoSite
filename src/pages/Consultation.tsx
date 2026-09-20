// درخواست مشاوره رایگان — متصل به گراویتی‌فرم
import { useState } from 'react';
import { Page } from '../components/shared/Overlay';
import { Button, Field, Input, Select, Textarea, Badge } from '../components/shared/ui';
import { useToast } from '../components/shared/Overlay';
import Icon from '../components/shared/Icon';
import { GF, SITE } from '../config/site';
import { isValidMobile, faDate } from '../lib/format';
import { gfService } from '../services/gravityforms';
import { baleService, smsService } from '../services/integrations';

export function Consultation() {
  const toast = useToast();
  const [form, setForm] = useState({ name: '', mobile: '', service: 'هوش مصنوعی', date: '', time: '۱۰:۰۰', desc: '' });
  const [loading, setLoading] = useState(false);
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));

  // ۷ روز آینده برای انتخاب تاریخ جلسه
  const dates = Array.from({ length: 7 }).map((_, i) => {
    const d = new Date(); d.setDate(d.getDate() + i + 1);
    return { iso: d.toISOString().slice(0, 10), label: faDate(d) };
  });

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    if (form.name.trim().length < 2) return toast('نام خود را وارد کنید.', 'error');
    if (!isValidMobile(form.mobile)) return toast('شماره موبایل معتبر وارد کنید.', 'error');
    setLoading(true);
    try {
      await gfService.submit(GF.forms.consultation, { ...form, source: 'consultation' }, 'consultation');
      await baleService.notify('📞 درخواست مشاوره جدید', `${form.name} برای «${form.service}» درخواست مشاوره داد.`);
      await smsService.send('melipayamak', form.mobile, `${form.name} عزیز، درخواست مشاوره شما ثبت شد؛ کارشناسان ما به‌زودی تماس می‌گیرند.`);
      toast('درخواست مشاوره شما ثبت شد. به‌زودی تماس می‌گیریم ☎️');
      setForm({ name: '', mobile: '', service: 'هوش مصنوعی', date: '', time: '۱۰:۰۰', desc: '' });
    } catch {
      toast('خطا در ثبت درخواست.', 'error');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="rocket" size={14} /> مشاوره رایگان</span>
          <h1>درخواست <span className="grad-text">مشاوره رایگان</span></h1>
          <p className="muted">کارشناسان ما در اولین فرصت با شما تماس می‌گیرند.</p>
        </div>
      </section>

      <section className="section" style={{ paddingTop: 30 }}>
        <div className="container" style={{ maxWidth: 900 }}>
          <div className="consult-grid">
            <form className="card" style={{ padding: 30 }} onSubmit={submit}>
              <div className="grid-2">
                <Field label="نام و نام خانوادگی *"><Input value={form.name} onChange={(e) => set('name', e.target.value)} placeholder="نام شما" /></Field>
                <Field label="شماره موبایل *"><Input value={form.mobile} onChange={(e) => set('mobile', e.target.value)} placeholder="۰۹۱۲..." inputMode="numeric" /></Field>
              </div>
              <Field label="خدمت موردنظر"><Select value={form.service} onChange={(e) => set('service', e.target.value)}>
                <option>هوش مصنوعی</option><option>طراحی سایت</option><option>فروشگاه اینترنتی</option><option>نرم‌افزار اختصاصی</option><option>CRM و اتوماسیون</option><option>برندینگ</option><option>سئو و مارکتینگ</option>
              </Select></Field>
              <div className="grid-2">
                <Field label="تاریخ تماس ترجیحی">
                  <Select value={form.date} onChange={(e) => set('date', e.target.value)}>
                    <option value="">هر زمان</option>
                    {dates.map((d) => <option key={d.iso} value={d.label}>{d.label}</option>)}
                  </Select>
                </Field>
                <Field label="ساعت تماس">
                  <Select value={form.time} onChange={(e) => set('time', e.target.value)}>
                    {['۹:۰۰', '۱۰:۰۰', '۱۱:۰۰', '۱۲:۰۰', '۱۳:۰۰', '۱۵:۰۰', '۱۶:۰۰', '۱۷:۰۰'].map((t) => <option key={t}>{t}</option>)}
                  </Select>
                </Field>
              </div>
              <Field label="توضیح کوتاه پروژه"><Textarea value={form.desc} onChange={(e) => set('desc', e.target.value)} placeholder="کمی درباره نیاز خود توضیح دهید..." /></Field>
              <Button type="submit" variant="primary" block size="lg" disabled={loading} icon={loading ? 'loader' : 'rocket'}>{loading ? 'در حال ثبت...' : 'ثبت درخواست مشاوره'}</Button>
            </form>

            <div className="consult-side">
              <div className="card mb-2">
                <h3 className="mb-2" style={{ fontSize: 17 }}>چرا مشاوره رایگان؟</h3>
                <ul className="svc-features">
                  {['بررسی نیاز کسب‌وکار شما', 'ارائه راهکار و برآورد هزینه', 'بدون هیچ‌گونه تعهد', 'امکان جلسه حضوری یا آنلاین'].map((f) => <li key={f}><Icon name="check-circle" size={16} style={{ color: 'var(--teal)' }} />{f}</li>)}
                </ul>
              </div>
              <div className="card">
                <h3 className="mb-2" style={{ fontSize: 17 }}>راه‌های ارتباط سریع</h3>
                <div className="flex col gap-2">
                  <a className="btn btn-ghost" href={`tel:${SITE.mobileEn}`}><Icon name="phone" size={16} /> {SITE.mobile}</a>
                  <a className="btn btn-ghost" href={`https://t.me/+${SITE.mobileEn}`} target="_blank" rel="noreferrer"><Icon name="send-h" size={16} /> تلگرام</a>
                  <a className="btn btn-ghost" href={`https://wa.me/${SITE.mobileEn}`} target="_blank" rel="noreferrer"><Icon name="chat-circle" size={16} /> واتس‌اپ</a>
                </div>
                <div className="mt-2"><Badge tone="teal" icon="clock">پاسخ‌گویی در کمتر از ۲ ساعت کاری</Badge></div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </Page>
  );
}
