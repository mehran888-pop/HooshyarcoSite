// پنل مدیریت — تیکت‌ها، مشتریان، فاکتورها و اعلان‌ها
import { useState } from 'react';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell } from '../../components/panel/PanelShell';
import { Badge, Button, Empty, Field, Input, Textarea } from '../../components/shared/ui';
import { Modal } from '../../components/shared/Overlay';
import { useToast } from '../../components/shared/Overlay';
import Icon from '../../components/shared/Icon';
import { contacts, invoices, tickets } from '../../mock/data';
import { useTickets } from '../../store';
import { faNum, price, faDateTime, timeAgo } from '../../lib/format';
import type { Ticket } from '../../lib/types';

const NAV = [
  { to: '/admin', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/admin/crm', icon: 'users', label: 'CRM' },
  { to: '/admin/support', icon: 'ticket', label: 'تیکت‌ها' },
  { to: '/admin/customers', icon: 'briefcase', label: 'مشتریان' },
  { to: '/admin/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/admin/notifications', icon: 'bell', label: 'اعلان‌ها' },
];

const statusTone: Record<string, any> = { open: 'gold', answered: 'teal', pending: 'warning', closed: 'muted' };
const statusLabel: Record<string, string> = { open: 'باز', answered: 'پاسخ داده شد', pending: 'در انتظار', closed: 'بسته شده' };

export function AdminSupport() {
  const all = useTickets((s) => s.tickets);
  const reply = useTickets((s) => s.reply);
  const updateStatus = useTickets((s) => s.updateStatus);
  const toast = useToast();
  const [selected, setSelected] = useState<Ticket | null>(null);
  const [filter, setFilter] = useState('all');

  const filtered = filter === 'all' ? all : all.filter((t) => t.status === filter);
  const active = filtered.length ? filtered.find((t) => t.id === selected?.id) ? selected : filtered[0] : null;

  const openCount = all.filter((t) => t.status === 'open' || t.status === 'pending').length;

  return (
    <RequireAuth staff>
      <PanelShell title="مدیریت تیکت‌های پشتیبانی" subtitle={`${faNum(openCount)} تیکت نیازمند رسیدگی`} nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="admin-ticket-layout">
          <div className="card" style={{ padding: 0, overflow: 'hidden' }}>
            <div className="filter-row" style={{ padding: 12 }}>
              {[['all', 'همه'], ['open', 'باز'], ['pending', 'در انتظار'], ['answered', 'پاسخ‌داده‌شده'], ['closed', 'بسته']].map(([k, l]) => (
                <button key={k} className={`filter-chip ${filter === k ? 'active' : ''}`} style={{ fontSize: 12, padding: '6px 13px' }} onClick={() => setFilter(k)}>{l}</button>
              ))}
            </div>
            {filtered.length === 0 ? <Empty icon="ticket" title="تیکتی در این وضعیت نیست" /> : filtered.map((t) => (
              <button key={t.id} className={`ticket-row ${selected?.id === t.id ? 'active' : ''}`} onClick={() => setSelected(t)}>
                <span className="mono small muted">#{t.id}</span>
                <span style={{ flex: 1, textAlign: 'right' }}>
                  <b className="small">{t.subject}</b>
                  <div className="small muted">{t.department} • {timeAgo(t.createdAt)}</div>
                </span>
                <Badge tone={statusTone[t.status]}>{statusLabel[t.status]}</Badge>
              </button>
            ))}
          </div>

          {active && (
            <div className="card ticket-detail">
              <div className="flex items-center justify-between mb-3 wrap gap-2">
                <div><b>تیکت #{active.id}</b><div className="small muted">{active.subject}</div></div>
                <div className="flex gap-1">
                  <Button size="sm" variant="ghost" onClick={() => { updateStatus(active.id, 'closed'); toast('تیکت بسته شد.', 'info'); }}>بستن</Button>
                  <Button size="sm" variant="teal" onClick={() => { updateStatus(active.id, 'answered'); toast('وضعیت به «پاسخ داده شد» تغییر کرد.'); }}>پاسخ‌داده‌شده</Button>
                </div>
              </div>
              <div className="ticket-thread">
                {active.messages.map((m) => (
                  <div key={m.id} className={`thread-msg ${m.author === 'customer' ? 'me' : 'support'}`}>
                    <div className="thread-bubble">
                      <span className="chat-name small">{m.authorName}</span>
                      <p>{m.text}</p>
                      <span className="chat-time" dir="ltr">{faDateTime(m.date).split('—')[1]}</span>
                    </div>
                  </div>
                ))}
              </div>
              <ReplyBox onSubmit={(text) => {
                reply(active.id, { id: `m-${Date.now()}`, author: 'support', authorName: 'پشتیبانی هوش‌یار', text, date: new Date().toISOString() });
                updateStatus(active.id, 'answered');
                toast('پاسخ شما ارسال شد.');
              }} />
            </div>
          )}
        </div>
      </PanelShell>
    </RequireAuth>
  );
}

function ReplyBox({ onSubmit }: { onSubmit: (text: string) => void }) {
  const [text, setText] = useState('');
  return (
    <div className="flex gap-2 mt-3">
      <Input value={text} onChange={(e) => setText(e.target.value)} placeholder="پاسخ به مشتری..." onKeyDown={(e) => e.key === 'Enter' && text.trim() && (onSubmit(text), setText(''))} />
      <Button variant="primary" icon="send" disabled={!text.trim()} onClick={() => { onSubmit(text); setText(''); }}>ارسال</Button>
    </div>
  );
}

export function AdminCustomers() {
  const toast = useToast();
  return (
    <RequireAuth staff>
      <PanelShell title="مشتریان" subtitle="سوابق، قراردادها و وضعیت پشتیبانی مشتریان" nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="grid-3">
          {contacts.map((c) => (
            <div key={c.id} className="card customer-card">
              <div className="flex items-center gap-3 mb-2">
                <span className="stat-ic" style={{ background: 'var(--gold-soft)', color: 'var(--gold)' }}><Icon name="briefcase" size={18} /></span>
                <div><b>{c.name}</b><div className="small muted">{c.company}</div></div>
              </div>
              <div className="flex gap-1 mb-2">{c.tags.map((t) => <span key={t} className="tech-chip">{t}</span>)}</div>
              <p className="small muted mb-1"><Icon name="phone" size={12} /> <span className="mono ltr">{c.mobile}</span></p>
              <p className="small muted mb-2"><Icon name="clock" size={12} /> آخرین فعالیت: {timeAgo(c.lastActivity)}</p>
              <div className="flex gap-1">
                <Button size="sm" variant="teal" icon="send" onClick={() => toast('پیامک پیگیری ارسال شد.')}>پیگیری خودکار</Button>
                <Button size="sm" variant="ghost" icon="receipt" onClick={() => toast('فاکتور جدید برای مشتری ساخته شد.')}>فاکتور</Button>
              </div>
            </div>
          ))}
        </div>
      </PanelShell>
    </RequireAuth>
  );
}

export function AdminInvoices() {
  const toast = useToast();
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState({ number: `INV-${new Date().getFullYear()}-${faNum(6)}`, customer: '', title: '', qty: '1', price: '' });

  return (
    <RequireAuth staff>
      <PanelShell title="فاکتورها و حسابداری" subtitle="مدیریت مالی کوچک فروشگاهی" nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="flex justify-between items-center mb-3">
          <span className="small muted">{faNum(invoices.length)} فاکتور</span>
          <Button variant="primary" size="sm" icon="plus" onClick={() => setOpen(true)}>ساخت فاکتور جدید</Button>
        </div>
        <div className="card" style={{ padding: 0, overflowX: 'auto' }}>
          <table className="table">
            <thead><tr><th>شماره</th><th>مشتری</th><th>اقلام</th><th>مبلغ</th><th>وضعیت</th><th>درگاه</th></tr></thead>
            <tbody>
              {invoices.map((inv) => (
                <tr key={inv.id}>
                  <td className="mono small" dir="ltr">{inv.number}</td>
                  <td className="small">{inv.customerName}</td>
                  <td className="small muted">{faNum(inv.items.length)} قلم</td>
                  <td className="small nowrap"><b>{price(inv.items.reduce((a, i) => a + i.qty * i.unitPrice, 0))}</b></td>
                  <td><Badge tone={inv.status === 'paid' ? 'success' : inv.status === 'overdue' ? 'danger' : inv.status === 'sent' ? 'info' : 'muted'}>{inv.status === 'paid' ? 'پرداخت شده' : inv.status === 'sent' ? 'ارسال شده' : inv.status === 'overdue' ? 'سررسید گذشته' : 'پیش‌نویس'}</Badge></td>
                  <td className="small muted">{inv.gateway ?? '—'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <Modal open={open} onClose={() => setOpen(false)} title="ساخت فاکتور جدید" width={540}>
          <Field label="شماره فاکتور"><Input value={form.number} onChange={(e) => setForm({ ...form, number: e.target.value })} dir="ltr" /></Field>
          <Field label="مشتری"><Input value={form.customer} onChange={(e) => setForm({ ...form, customer: e.target.value })} placeholder="نام مشتری" /></Field>
          <div className="grid-3">
            <Field label="شرح قلم"><Input value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} /></Field>
            <Field label="تعداد"><Input value={form.qty} onChange={(e) => setForm({ ...form, qty: e.target.value })} inputMode="numeric" /></Field>
            <Field label="قیمت (تومان)"><Input value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} inputMode="numeric" /></Field>
          </div>
          <Button variant="primary" block icon="plus" onClick={() => { setOpen(false); toast("فاکتور جدید ثبت شد (نسخه نمایشی)"); }}>ثبت فاکتور</Button>
        </Modal>
      </PanelShell>
    </RequireAuth>
  );
}

export function AdminNotifications() {
  return (
    <RequireAuth staff>
      <PanelShell title="مرکز اطلاع‌رسانی" subtitle="ارسال هشدار لحظه‌ای به مدیران از طریق ربات بله و پیامک" nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="grid-3">
          {[
            { icon: 'send-h', t: 'ربات بله', d: 'ارسال اطلاع‌رسانی لحظه‌ای رویدادها به مدیر', tone: 'teal' },
            { icon: 'chat', t: 'پیامک', d: 'ملی‌پیامک، sms.ir و ippanel.co', tone: 'gold' },
            { icon: 'bell', t: 'رویدادها', d: 'ثبت و نمایش لحظه‌ای رخدادهای سامانه', tone: 'violet' },
          ].map((c) => (
            <div key={c.t} className="card text-center">
              <div className="feature-icon"><Icon name={c.icon} size={26} /></div>
              <h3 style={{ fontSize: 16 }}>{c.t}</h3>
              <p className="muted small">{c.d}</p>
            </div>
          ))}
        </div>
        <div className="card mt-3">
          <h3 className="mb-3" style={{ fontSize: 16 }}>📜 رویدادهای اخیر</h3>
          <div className="timeline">
            {['ثبت سرنخ جدید از چت آنلاین', 'پرداخت موفق فاکتور INV-1402-001', 'ثبت تیکت پشتیبانی با اولویت بالا', 'تمدید قرارداد پشتیبانی'].map((ev, i) => (
              <div key={i} className="tl-item">
                <span className={`tl-dot ${['t-violet', 't-gold', 't-teal', 't-gold'][i]}`}><Icon name={['user', 'payment', 'ticket', 'refresh'][i]} size={13} /></span>
                <div><p className="small">{ev}</p><span className="small muted" style={{ fontSize: 11 }}>{faNum(5 - i)} دقیقه پیش</span></div>
              </div>
            ))}
          </div>
        </div>
      </PanelShell>
    </RequireAuth>
  );
}
