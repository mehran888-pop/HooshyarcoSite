// فاکتورها، سفارش‌ها، تمدید پشتیبانی و اعلان‌های پنل کاربری
import { useState } from 'react';
import { Link } from 'react-router-dom';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell, StatCard } from '../../components/panel/PanelShell';
import { Badge, Button, Empty } from '../../components/shared/ui';
import { Modal } from '../../components/shared/Overlay';
import { useToast } from '../../components/shared/Overlay';
import Icon from '../../components/shared/Icon';
import { useInvoices, useNotifications, useTickets, useAuth } from '../../store';
import { faNum, faDateTime, price, downloadBlob } from '../../lib/format';
import { gatewayList, pay } from '../../services/payment';
import { baleService, smsService } from '../../services/integrations';
import { plans } from '../../mock/data';
import type { Invoice } from '../../lib/types';

const NAV = [
  { to: '/panel', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/panel/support', icon: 'ticket', label: 'تیکت پشتیبانی' },
  { to: '/panel/plans', icon: 'shield', label: 'تمدید پشتیبانی' },
  { to: '/panel/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/panel/orders', icon: 'cart', label: 'سفارش‌های من' },
  { to: '/panel/notifications', icon: 'bell', label: 'اعلان‌ها' },
];

const invTone: Record<Invoice['status'], any> = { draft: 'muted', sent: 'info', paid: 'success', overdue: 'danger', cancelled: 'muted' };
const invLabel: Record<Invoice['status'], string> = { draft: 'پیش‌نویس', sent: 'در انتظار پرداخت', paid: 'پرداخت شده', overdue: 'سررسید گذشته', cancelled: 'لغو شده' };

export function Invoices() {
  const invoices = useInvoices((s) => s.invoices);
  const paid = useInvoices((s) => s.totalPaid());
  const due = useInvoices((s) => s.totalDue());
  const [selected, setSelected] = useState<Invoice | null>(null);

  return (
    <RequireAuth>
      <PanelShell title="فاکتورها" subtitle="مشاهده، دانلود و پرداخت آنلاین فاکتورها" nav={NAV}>
        <div className="grid-3 mb-3">
          <StatCard icon="wallet" label="جمع پرداخت‌شده" value={price(paid)} tone="teal" />
          <StatCard icon="alert-circle" label="مانده قابل پرداخت" value={price(due)} tone="danger" />
          <StatCard icon="receipt" label="تعداد فاکتورها" value={faNum(invoices.length)} tone="gold" />
        </div>

        {invoices.length === 0 ? (
          <Empty icon="receipt" title="فاکتوری ثبت نشده" />
        ) : (
          <div className="card" style={{ padding: 0, overflowX: 'auto' }}>
            <table className="table">
              <thead><tr><th>شماره</th><th>مشتری</th><th>مبلغ</th><th>وضعیت</th><th>تاریخ</th><th>عملیات</th></tr></thead>
              <tbody>
                {invoices.map((inv) => (
                  <tr key={inv.id}>
                    <td className="mono small" dir="ltr">{inv.number}</td>
                    <td className="small">{inv.customerName}</td>
                    <td className="small nowrap"><b>{price(inv.items.reduce((a, i) => a + i.qty * i.unitPrice, 0))}</b></td>
                    <td><Badge tone={invTone[inv.status]}>{invLabel[inv.status]}</Badge></td>
                    <td className="small muted nowrap">{faDateTime(inv.createdAt)}</td>
                    <td>
                      <div className="flex gap-1">
                        <button className="icon-btn" onClick={() => setSelected(inv)} title="مشاهده"><Icon name="eye" size={15} /></button>
                        <button className="icon-btn" onClick={() => downloadInvoice(inv)} title="دانلود"><Icon name="download" size={15} /></button>
                        {(inv.status === 'sent' || inv.status === 'overdue') && (
                          <Link to={`/panel/invoices/pay/${inv.id}`} className="btn btn-teal btn-sm">پرداخت</Link>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        <InvoiceModal invoice={selected} onClose={() => setSelected(null)} />
      </PanelShell>
    </RequireAuth>
  );
}

function InvoiceModal({ invoice, onClose }: { invoice: Invoice | null; onClose: () => void }) {
  if (!invoice) return null;
  const total = invoice.items.reduce((a, i) => a + i.qty * i.unitPrice, 0);
  return (
    <Modal open={!!invoice} onClose={onClose} title={`فاکتور ${invoice.number}`} width={640}>
      <div className="invoice-preview">
        <div className="inv-head">
          <div className="flex items-center gap-2"><span className="logo-mark" style={{ width: 40, height: 40, fontSize: 18 }}>ه</span><b>هوش‌یار پاری‌نگر</b></div>
          <Badge tone={invTone[invoice.status]}>{invLabel[invoice.status]}</Badge>
        </div>
        <div className="grid-2 small muted mt-2">
          <span>صادر برای: <b style={{ color: 'var(--text)' }}>{invoice.customerName}</b></span>
          <span>تاریخ صدور: {faDateTime(invoice.createdAt)}</span>
          <span>سررسید: {faDateTime(invoice.dueDate)}</span>
          {invoice.refId && <span>کد پیگیری: <span className="mono ltr">{invoice.refId}</span></span>}
        </div>
        <table className="table mt-3">
          <thead><tr><th>شرح</th><th>تعداد</th><th>قیمت واحد</th><th>جمع</th></tr></thead>
          <tbody>
            {invoice.items.map((i, k) => (
              <tr key={k}><td className="small">{i.title}</td><td className="mono">{faNum(i.qty)}</td><td className="small">{price(i.unitPrice)}</td><td className="small nowrap">{price(i.qty * i.unitPrice)}</td></tr>
            ))}
          </tbody>
        </table>
        <div className="inv-total">مبلغ قابل پرداخت: <b className="grad-gold-text" style={{ fontSize: 22 }}>{price(total)}</b></div>
      </div>
    </Modal>
  );
}

export function PayInvoicePage() {
  const toast = useToast();
  const payInv = useInvoices((s) => s.pay);
  const pushNtf = useNotifications((s) => s.push);
  const session = useAuth((s) => s.session);
  const [gateway, setGateway] = useState('zarinpal');
  const [busy, setBusy] = useState(false);
  const [result, setResult] = useState<{ refId: string } | null>(null);
  // نسخه نمایشی: اولین فاکتور «در انتظار پرداخت» را انتخاب می‌کند (در نسخه واقعی از آیدی مسیر /pay/:id می‌آید)
  const all = useInvoices((s) => s.invoices);
  const target = all.find((i) => i.status === 'sent' || i.status === 'overdue') ?? all[0];
  const total = target ? target.items.reduce((a, i) => a + i.qty * i.unitPrice, 0) : 0;

  async function doPay() {
    if (!target) return;
    setBusy(true);
    try {
      const ref = await pay(gateway as any, total, `پرداخت فاکتور ${target.number}`);
      payInv(target.id, ref.refId!, gateway);
      await baleService.notify('💳 پرداخت فاکتور', `فاکتور ${target.number} پرداخت شد.`);
      await smsService.send('melipayamak', session?.user.mobile ?? '', `فاکتور ${target.number} با موفقیت پرداخت شد.`);
      pushNtf({ title: 'فاکتور پرداخت شد', body: `پرداخت فاکتور ${target.number} تأیید شد.`, type: 'finance' });
      setResult({ refId: ref.refId! });
      toast('پرداخت فاکتور با موفقیت انجام شد ✅');
    } finally {
      setBusy(false);
    }
  }

  return (
    <RequireAuth>
      <PanelShell title="پرداخت فاکتور" subtitle="پرداخت امن آنلاین" nav={NAV}>
        {result ? (
          <div className="card text-center" style={{ padding: 40, maxWidth: 560, margin: '0 auto' }}>
            <div style={{ width: 70, height: 70, borderRadius: 22, background: 'var(--success-soft)', display: 'grid', placeItems: 'center', margin: '0 auto 14px' }}>
              <Icon name="check-circle" size={38} style={{ color: 'var(--success)' }} />
            </div>
            <h2 style={{ fontSize: 20 }}>پرداخت موفق 🎉</h2>
            <p className="muted small mt-2">کد پیگیری: <span className="mono ltr badge badge-info">{result.refId}</span></p>
            <Link to="/panel/invoices" className="btn btn-primary mt-3">بازگشت به فاکتورها</Link>
          </div>
        ) : !target ? (
          <Empty icon="receipt" title="فاکتور در انتظار پرداختی یافت نشد" action={<Link to="/panel/invoices" className="btn btn-primary btn-sm">بازگشت</Link>} />
        ) : (
          <div className="card" style={{ maxWidth: 640, margin: '0 auto', padding: 30 }}>
            <div className="flex items-center justify-between mb-3">
              <span className="small muted">فاکتور <b className="mono" dir="ltr">{target.number}</b></span>
              <span className="grad-gold-text" style={{ fontSize: 26, fontWeight: 900 }}>{price(total)}</span>
            </div>
            <h3 className="mb-2" style={{ fontSize: 16 }}>انتخاب درگاه پرداخت</h3>
            <div className="gateway-list">
              {gatewayList.map((g) => (
                <button key={g.id} className={`gateway-item ${gateway === g.id ? 'active' : ''}`} onClick={() => setGateway(g.id)}>
                  <span className="gateway-logo" style={{ background: g.color }}>{g.name[0]}</span>
                  <span className="small" style={{ fontWeight: 700 }}>{g.name}</span>
                  {gateway === g.id && <Icon name="check-circle" size={17} style={{ color: 'var(--teal)', marginRight: 'auto' }} />}
                </button>
              ))}
            </div>
            <Button variant="primary" block size="lg" disabled={busy} onClick={doPay} style={{ marginTop: 18 }} icon={busy ? 'loader' : 'credit'}>
              {busy ? 'در حال اتصال به درگاه...' : `پرداخت ${price(total)}`}
            </Button>
          </div>
        )}
      </PanelShell>
    </RequireAuth>
  );
}

function downloadInvoice(inv: Invoice) {
  const total = inv.items.reduce((a, i) => a + i.qty * i.unitPrice, 0);
  const rows = inv.items.map((i) => `${i.title}\t${i.qty}\t${i.unitPrice}\t${i.qty * i.unitPrice}`).join('\n');
  const txt = `هوش‌یار پاری‌نگر\nفاکتور: ${inv.number}\nمشتری: ${inv.customerName}\nصدور: ${inv.createdAt}\n\n${rows}\n\nمبلغ کل: ${total} تومان`;
  downloadBlob(txt, `${inv.number}.txt`);
}
