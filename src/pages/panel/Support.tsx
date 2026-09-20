// تیکت پشتیبانی پنل کاربری
import { useState } from 'react';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell } from '../../components/panel/PanelShell';
import { Badge, Button, Field, Input, Select, Textarea, Empty } from '../../components/shared/ui';
import { Modal } from '../../components/shared/Overlay';
import { useToast } from '../../components/shared/Overlay';
import Icon from '../../components/shared/Icon';
import { useTickets } from '../../store';
import { faDateTime, uid } from '../../lib/format';
import type { Ticket, TicketMessage, TicketPriority } from '../../lib/types';

const statusTone: Record<Ticket['status'], any> = { open: 'gold', answered: 'teal', pending: 'warning', closed: 'muted' };
const statusLabel: Record<Ticket['status'], string> = { open: 'باز', answered: 'پاسخ داده شد', pending: 'در انتظار', closed: 'بسته شده' };
const prioLabel: Record<TicketPriority, string> = { low: 'کم', medium: 'متوسط', high: 'زیاد', critical: 'بحرانی' };

export default function SupportTickets() {
  const tickets = useTickets((s) => s.tickets);
  const add = useTickets((s) => s.add);
  const toast = useToast();
  const [openModal, setOpenModal] = useState(false);
  const [selected, setSelected] = useState<Ticket | null>(null);
  const [form, setForm] = useState({ subject: '', department: 'فنی', priority: 'medium' as TicketPriority, message: '' });

  function submit(e: React.FormEvent) {
    e.preventDefault();
    if (form.subject.trim().length < 3) return toast('موضوع تیکت را وارد کنید.', 'error');
    const t = add({
      subject: form.subject, department: form.department, status: 'open', priority: form.priority,
      messages: [{ id: uid('m'), author: 'customer', authorName: 'شما', text: form.message || form.subject, date: new Date().toISOString() }],
    });
    toast(`تیکت ${t.id} ثبت شد. پشتیبان ما به‌زودی پاسخ می‌دهد.`);
    setOpenModal(false);
    setForm({ subject: '', department: 'فنی', priority: 'medium', message: '' });
    setSelected(t);
  }

  return (
    <RequireAuth>
      <PanelShell title="تیکت پشتیبانی" subtitle="درخواست پشتیبانی ثبت کنید و پاسخ‌ها را دنبال کنید" nav={nav}>
        <div className="flex justify-between items-center mb-3" style={{ justifyContent: 'space-between' }}>
          <Button variant="primary" icon="plus" onClick={() => setOpenModal(true)}>ثبت تیکت جدید</Button>
        </div>

        {tickets.length === 0 ? (
          <Empty icon="ticket" title="تیکتی ثبت نشده" text="برای شروع، اولین تیکت پشتیبانی خود را ثبت کنید." />
        ) : (
          <div className="card ticket-list" style={{ padding: 0 }}>
            {tickets.map((t) => (
              <button key={t.id} className="ticket-row" onClick={() => setSelected(t)}>
                <span className="ticket-id mono small muted">#{t.id}</span>
                <span style={{ flex: 1, textAlign: 'right' }}>
                  <b className="small">{t.subject}</b>
                  <div className="small muted">{t.department} • {faDateTime(t.createdAt)}</div>
                </span>
                <Badge tone={prioTone(t.priority)}>{prioLabel[t.priority]}</Badge>
                <Badge tone={statusTone[t.status]}>{statusLabel[t.status]}</Badge>
                <Icon name="chevron-down" size={16} style={{ transform: 'rotate(-90deg)', color: 'var(--muted)' }} />
              </button>
            ))}
          </div>
        )}

        <TicketChatModal ticket={selected} onClose={() => setSelected(null)} />

        <Modal open={openModal} onClose={() => setOpenModal(false)} title="ثبت تیکت جدید" width={560}>
          <form onSubmit={submit}>
            <Field label="موضوع *"><Input value={form.subject} onChange={(e) => setForm({ ...form, subject: e.target.value })} placeholder="خلاصه مشکل یا درخواست" /></Field>
            <div className="grid-2">
              <Field label="بخش"><Select value={form.department} onChange={(e) => setForm({ ...form, department: e.target.value })}>
                <option>فنی</option><option>مالی و فاکتور</option><option>فروش</option><option>آموزش</option>
              </Select></Field>
              <Field label="اولویت"><Select value={form.priority} onChange={(e) => setForm({ ...form, priority: e.target.value as TicketPriority })}>
                {(['low', 'medium', 'high', 'critical'] as TicketPriority[]).map((p) => <option key={p} value={p}>{prioLabel[p]}</option>)}
              </Select></Field>
            </div>
            <Field label="توضیحات"><Textarea value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })} placeholder="شرح کامل مشکل..." /></Field>
            <div className="flex gap-2">
              <Button type="submit" variant="primary" icon="send" block>ثبت تیکت</Button>
            </div>
          </form>
        </Modal>
      </PanelShell>
    </RequireAuth>
  );
}

function prioTone(p: TicketPriority): any { return { low: 'muted', medium: 'info', high: 'warning', critical: 'danger' }[p]; }

function TicketChatModal({ ticket, onClose }: { ticket: Ticket | null; onClose: () => void }) {
  const reply = useTickets((s) => s.reply);
  const updateStatus = useTickets((s) => s.updateStatus);
  const toast = useToast();
  const [text, setText] = useState('');
  if (!ticket) return null;

  function send() {
    if (!text.trim()) return;
    const msg: TicketMessage = { id: uid('m'), author: 'customer', authorName: 'شما', text, date: new Date().toISOString() };
    reply(ticket!.id, msg);
    setText('');
    toast('پاسخ شما ثبت شد.');
    // شبیه‌سازی پاسخ پشتیبان
    setTimeout(() => {
      reply(ticket!.id, { id: uid('m'), author: 'support', authorName: 'پشتیبانی هوش‌یار', text: 'ممنون از پیگیری شما 🙏 همکار ما در اسرع وقت بررسی می‌کند.', date: new Date().toISOString() });
      updateStatus(ticket!.id, 'answered');
    }, 1800);
  }

  return (
    <Modal open={!!ticket} onClose={onClose} title={`تیکت #${ticket.id}`} width={620}>
      <div className="flex items-center gap-2 mb-3 wrap">
        <Badge tone={statusTone[ticket.status]}>{statusLabel[ticket.status]}</Badge>
        <Badge tone="muted">{ticket.department}</Badge>
        <span className="small muted">{ticket.subject}</span>
      </div>
      <div className="ticket-thread">
        {ticket.messages.map((m) => (
          <div key={m.id} className={`thread-msg ${m.author === 'customer' ? 'me' : m.author === 'system' ? 'sys' : 'support'}`}>
            <div className="thread-bubble">
              <span className="chat-name small">{m.authorName}</span>
              <p>{m.text}</p>
              <span className="chat-time" dir="ltr">{faDateTime(m.date).split('—')[1]}</span>
            </div>
          </div>
        ))}
      </div>
      <div className="flex gap-2 mt-3">
        <Input value={text} onChange={(e) => setText(e.target.value)} placeholder="پاسخ خود را بنویسید..." onKeyDown={(e) => e.key === 'Enter' && send()} />
        <Button variant="primary" icon="send" onClick={send}>ارسال</Button>
      </div>
      {ticket.status === 'open' && (
        <Button variant="ghost" size="sm" style={{ marginTop: 8 }} onClick={() => { updateStatus(ticket.id, 'closed'); toast('تیکت بسته شد.', 'info'); onClose(); }}>بستن تیکت</Button>
      )}
    </Modal>
  );
}

const nav = [
  { to: '/panel', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/panel/support', icon: 'ticket', label: 'تیکت پشتیبانی' },
  { to: '/panel/plans', icon: 'shield', label: 'تمدید پشتیبانی' },
  { to: '/panel/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/panel/orders', icon: 'cart', label: 'سفارش‌های من' },
  { to: '/panel/notifications', icon: 'bell', label: 'اعلان‌ها' },
];
