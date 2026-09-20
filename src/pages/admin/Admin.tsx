// پنل مدیریت — داشبورد + CRM + تیکت + فاکتور + مشتریان + اعلان‌های لحظه‌ای
import { useMemo, useState } from 'react';
import { RequireAuth } from '../../components/layout/Scroll';
import { PanelShell, StatCard, type PanelNavItem } from '../../components/panel/PanelShell';
import { Badge, Button, Empty } from '../../components/shared/ui';
import { useToast } from '../../components/shared/Overlay';
import Icon from '../../components/shared/Icon';
import { contacts, deals, invoices, tickets, activities } from '../../mock/data';
import { useNotifications } from '../../store';
import { faNum, price, timeAgo, faDateTime } from '../../lib/format';
import { baleService } from '../../services/integrations';
import type { Invoice } from '../../lib/types';

const NAV: PanelNavItem[] = [
  { to: '/admin', end: true, icon: 'dashboard', label: 'داشبورد' },
  { to: '/admin/crm', icon: 'users', label: 'CRM' },
  { to: '/admin/support', icon: 'ticket', label: 'تیکت‌ها' },
  { to: '/admin/customers', icon: 'briefcase', label: 'مشتریان' },
  { to: '/admin/invoices', icon: 'receipt', label: 'فاکتورها' },
  { to: '/admin/notifications', icon: 'bell', label: 'اعلان‌ها' },
];

const stageLabel: Record<string, string> = { new: 'جدید', qualified: 'واجد شرایط', proposal: 'پیشنهاد', negotiation: 'مذاکره', won: 'برنده', lost: 'از دست رفته' };
const stageTone: Record<string, any> = { new: 'info', qualified: 'violet', proposal: 'gold', negotiation: 'warning', won: 'success', lost: 'danger' };

export default function AdminDashboard() {
  const pushNtf = useNotifications((s) => s.push);
  const toast = useToast();
  const totalPipeline = useMemo(() => deals.filter((d) => d.stage !== 'lost').reduce((a, d) => a + d.value, 0), []);
  const wonTotal = deals.filter((d) => d.stage === 'won').reduce((a, d) => a + d.value, 0);
  const openTickets = tickets.filter((t) => t.status === 'open' || t.status === 'pending').length;
  const activeLeads = contacts.filter((c) => c.status === 'lead').length;

  function notifyManager() {
    baleService.notify('🚨 رویداد مهم', 'داشبورد مدیریت بررسی شد؛ اطلاع‌رسانی لحظه‌ای فعال است.');
    pushNtf({ title: 'اطلاع‌رسانی لحظه‌ای', body: 'رویداد مدیریتی به مدیر از طریق بله ارسال شد.', type: 'system' });
    toast('هشدار لحظه‌ای برای مدیر در بله ارسال شد 📢');
  }

  return (
    <RequireAuth staff>
      <PanelShell title="داشبورد مدیریت" subtitle="نمای کلی شرکت هوش‌یار پاری‌نگر" nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="grid-4">
          <StatCard icon="users" label="سرنخ فعال" value={faNum(activeLeads)} tone="violet" />
          <StatCard icon="ticket" label="تیکت باز" value={faNum(openTickets)} tone={openTickets ? 'danger' : 'teal'} />
          <StatCard icon="line" label="ارزش قیف فروش" value={price(totalPipeline)} tone="gold" />
          <StatCard icon="award" label="فروش برنده‌شده" value={price(wonTotal)} tone="teal" />
        </div>

        <div className="dash-grid mt-4">
          <div className="card">
            <div className="flex items-center justify-between mb-3">
              <h3 style={{ fontSize: 16 }}>📈 قیف فروش CRM</h3>
              <Button variant="ghost" size="sm" onClick={notifyManager} icon="send">اطلاع‌رسانی لحظه‌ای به بله</Button>
            </div>
            {Object.entries(stageLabel).filter(([s]) => s !== 'lost').map(([stage, label]) => {
              const arr = deals.filter((d) => d.stage === stage);
              const sum = arr.reduce((a, d) => a + d.value, 0);
              return (
                <div key={stage} className="pipe-row">
                  <span className="small nowrap" style={{ width: 90 }}>{label}</span>
                  <div className="pipe-bar"><div className={`pipe-fill f-${stage}`} style={{ width: `${Math.max(6, (sum / totalPipeline) * 100)}%` }} /></div>
                  <span className="small nowrap" style={{ width: 90, textAlign: 'left' }}>{faNum(arr.length)} فرصت</span>
                </div>
              );
            })}
          </div>

          <div className="card">
            <h3 className="mb-3" style={{ fontSize: 16 }}>🎯 فرصت‌های فروش اخیر</h3>
            {deals.slice(0, 4).map((d) => (
              <div key={d.id} className="flex items-center gap-2 deal-row">
                <span style={{ flex: 1 }}><b className="small">{d.title}</b><div className="small muted">{d.owner}</div></span>
                <Badge tone={stageTone[d.stage]}>{stageLabel[d.stage]}</Badge>
                <span className="small nowrap grad-gold-text" style={{ fontWeight: 800 }}>{price(d.value)}</span>
              </div>
            ))}
          </div>

          <div className="card">
            <h3 className="mb-3" style={{ fontSize: 16 }}>🔔 رویدادهای لحظه‌ای</h3>
            <div className="timeline">
              {activities.slice(0, 4).map((a) => (
                <div key={a.id} className="tl-item">
                  <span className={`tl-dot t-${a.tone ?? 'muted'}`}><Icon name={a.icon} size={13} /></span>
                  <div><p className="small">{a.text}</p><span className="small muted" style={{ fontSize: 11 }}>{timeAgo(a.date)}</span></div>
                </div>
              ))}
            </div>
            <div className="live-indicator mt-2"><span className="pulse-dot" /> اطلاع‌رسانی لحظه‌ای به مدیر فعال است</div>
          </div>

          <div className="card">
            <h3 className="mb-3" style={{ fontSize: 16 }}>💰 فاکتورهای اخیر</h3>
            {invoices.slice(0, 4).map((inv) => (
              <div key={inv.id} className="flex items-center gap-2 deal-row">
                <span className="mono small muted" dir="ltr">{inv.number}</span>
                <span style={{ flex: 1 }} className="small">{inv.customerName}</span>
                <span className="small nowrap">{price(inv.items.reduce((a, i) => a + i.qty * i.unitPrice, 0))}</span>
                <Badge tone={inv.status === 'paid' ? 'success' : inv.status === 'overdue' ? 'danger' : 'muted'}>{inv.status === 'paid' ? 'پرداخت شد' : 'باز'}</Badge>
              </div>
            ))}
          </div>
        </div>
      </PanelShell>
    </RequireAuth>
  );
}

export function AdminCrm() {
  const toast = useToast();
  return (
    <RequireAuth staff>
      <PanelShell title="مدیریت ارتباط با مشتری (CRM)" subtitle="سرنخ‌ها، مشتریان و فرصت‌های فروش" nav={NAV} accent="teal" notifPath="/admin/notifications">
        <div className="grid-2 mb-3">
          <div className="card" style={{ padding: 0, overflowX: 'auto' }}>
            <h3 style={{ fontSize: 16, padding: "16px 16px 0" }}>👥 سرنخ‌ها و مشتریان</h3>
            <table className="table">
              <thead><tr><th>نام</th><th>موبایل</th><th>شرکت</th><th>وضعیت</th><th>امتیاز</th><th>مسئول</th><th>آخرین فعالیت</th></tr></thead>
              <tbody>
                {contacts.map((c) => (
                  <tr key={c.id}>
                    <td className="small"><b>{c.name}</b>{c.tags.includes('VIP') && <Badge tone="gold" icon="star">VIP</Badge>}</td>
                    <td className="small mono ltr">{c.mobile}</td>
                    <td className="small muted">{c.company}</td>
                    <td><Badge tone={c.status === 'customer' ? 'success' : c.status === 'lead' ? 'info' : c.status === 'opportunity' ? 'gold' : 'violet'}>{c.status === 'customer' ? 'مشتری' : c.status === 'lead' ? 'سرنخ' : c.status === 'opportunity' ? 'فرصت' : 'واجد شرایط'}</Badge></td>
                    <td className="small"><b className="grad-gold-text">{faNum(c.score)}</b></td>
                    <td className="small muted">{c.assignedTo}</td>
                    <td className="small muted nowrap">{timeAgo(c.lastActivity)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div className="flex col gap-2">
            {contacts.slice(0, 5).map((c) => (
              <div key={c.id} className="card contact-mini">
                <div style={{ flex: 1 }}>
                  <b className="small">{c.name}</b>
                  <div className="small muted">{c.company} • {c.source}</div>
                  <div className="flex gap-1 mt-1">{c.tags.map((t) => <span key={t} className="tech-chip">{t}</span>)}</div>
                </div>
                <div style={{ textAlign: 'left' }}>
                  <div className="small muted mb-1">امتیاز: <b className="grad-gold-text">{faNum(c.score)}</b></div>
                  <Button size="sm" variant="teal" icon="send" onClick={() => toast(`پیامک پیگیری برای ${c.name} ارسال شد.`)}>پیگیری خودکار</Button>
                </div>
              </div>
            ))}
            <div className="card">
              <h4 className="mb-2 small">🤖 اتوماسیون فعال</h4>
              {['پیگیری خودکار سرنخ‌های جدید', 'یادآور تمدید قراردادها', 'امتیازدهی خودکار مشتریان', 'اطلاع‌رسانی لحظه‌ای به مدیر'].map((a) => (
                <div key={a} className="flex items-center gap-2 small" style={{ padding: '6px 0' }}><Icon name="check-circle" size={14} style={{ color: 'var(--teal)' }} />{a}</div>
              ))}
            </div>
          </div>
        </div>
      </PanelShell>
    </RequireAuth>
  );
}
