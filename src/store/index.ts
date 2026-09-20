// استور سراسری با Zustand
import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import type { AuthSession, CartItem, ChatMessage, Notification, Product } from '../lib/types';
import { notifications as seedNotifications, chatSeed } from '../mock/data';
import { uid } from '../lib/format';

/** ===== احراز هویت ===== */
interface AuthState {
  session: AuthSession | null;
  /** کد OTP که در حالت دمو همیشه ۱۲۳۴۵ است */
  pendingMobile: string | null;
  login: (session: AuthSession) => void;
  logout: () => void;
  setPendingMobile: (mobile: string | null) => void;
}

const DEMO_USERS: Record<string, { name: string; role: AuthSession['user']['role']; company?: string }> = {
  '09123456789': { name: 'مهدی رضایی', role: 'admin' },
  '09127778899': { name: 'کامران عزیزی', role: 'support' },
};

export const useAuth = create<AuthState>()(
  persist(
    (set) => ({
      session: null,
      pendingMobile: null,
      login: (session) => set({ session, pendingMobile: null }),
      logout: () => set({ session: null }),
      setPendingMobile: (pendingMobile) => set({ pendingMobile }),
    }),
    { name: 'hooshyar.auth' },
  ),
);

/** کاربر دمو بر اساس شماره موبایل */
export function demoUserFor(mobile: string) {
  const found = DEMO_USERS[String(mobile).trim()];
  if (found) return found;
  return { name: 'کاربر هوش‌یار', role: 'customer' as const };
}

/** ===== سبد خرید ===== */
interface CartState {
  items: CartItem[];
  add: (product: Product, qty?: number) => void;
  remove: (productId: number | string) => void;
  setQty: (productId: number | string, qty: number) => void;
  clear: () => void;
}

export const useCart = create<CartState>()(
  persist(
    (set) => ({
      items: [],
      add: (product, qty = 1) =>
        set((s) => {
          const existing = s.items.find((i) => i.product.id === product.id);
          if (existing) {
            return { items: s.items.map((i) => (i.product.id === product.id ? { ...i, qty: i.qty + qty } : i)) };
          }
          return { items: [...s.items, { product, qty }] };
        }),
      remove: (productId) => set((s) => ({ items: s.items.filter((i) => i.product.id !== productId) })),
      setQty: (productId, qty) =>
        set((s) => ({ items: s.items.map((i) => (i.product.id === productId ? { ...i, qty: Math.max(1, qty) } : i)) })),
      clear: () => set({ items: [] }),
    }),
    { name: 'hooshyar.cart' },
  ),
);

export const cartItemsCount = (items: CartItem[]) => items.reduce((a, i) => a + i.qty, 0);
export const cartTotal = (items: CartItem[]) => items.reduce((a, i) => a + i.qty * i.product.price, 0);

/** ===== اعلان‌ها ===== */
interface NotificationState {
  items: Notification[];
  unread: () => number;
  push: (n: Omit<Notification, 'id' | 'date' | 'read'>) => void;
  markRead: (id: string) => void;
  markAllRead: () => void;
}

export const useNotifications = create<NotificationState>()(
  persist(
    (set, get) => ({
      items: seedNotifications,
      unread: () => get().items.filter((n) => !n.read).length,
      push: (n) =>
        set((s) => ({
          items: [{ ...n, id: uid('ntf'), date: new Date().toISOString(), read: false }, ...s.items],
        })),
      markRead: (id) => set((s) => ({ items: s.items.map((n) => (n.id === id ? { ...n, read: true } : n)) })),
      markAllRead: () => set((s) => ({ items: s.items.map((n) => ({ ...n, read: true })) })),
    }),
    { name: 'hooshyar.notifications' },
  ),
);

/** ===== چت آنلاین ===== */
interface ChatState {
  open: boolean;
  messages: ChatMessage[];
  unread: number;
  typing: boolean;
  toggle: () => void;
  send: (text: string) => void;
  markSeen: () => void;
}

export const useChat = create<ChatState>()(
  (set, get) => ({
    open: false,
    messages: chatSeed.map((c) => ({ ...c, id: uid('ch'), from: 'them' as const, status: 'read' as const })),
    unread: 1,
    typing: false,
    toggle: () => set((s) => ({ open: !s.open, unread: s.open ? s.unread : 0 })),
    markSeen: () => set({ unread: 0 }),
    send: (text) => {
      const msg: ChatMessage = { id: uid('ch'), from: 'me', name: 'شما', text, date: new Date().toISOString(), status: 'sent' };
      set((s) => ({ messages: [...s.messages, msg], typing: true }));
      // شبیه‌سازی پاسخ خودکار پشتیبان
      setTimeout(() => {
        const reply: ChatMessage = {
          id: uid('ch'), from: 'them', name: 'پشتیبان هوش‌یار',
          text: 'ممنون از پیام شما 🙏 همکاران ما به‌زودی پاسخ می‌دهند. (این پاسخ آزمایشی است؛ با اتصال به وردپرس، پاسخ واقعی پشتیبانان نمایش داده می‌شود)',
          date: new Date().toISOString(), status: 'read',
        };
        set((s) => ({
          typing: false,
          messages: [...s.messages, reply],
          unread: s.open ? 0 : s.unread + 1,
        }));
      }, 1400);
    },
  }),
);

/** ===== تیکت‌های پشتیبانی ===== */
import { tickets as seedTickets, invoices as seedInvoices } from '../mock/data';
import type { Invoice, Ticket, TicketMessage } from '../lib/types';

interface TicketState {
  tickets: Ticket[];
  add: (t: Omit<Ticket, 'id' | 'createdAt' | 'messages'> & { messages?: TicketMessage[] }) => Ticket;
  reply: (ticketId: string, msg: TicketMessage) => void;
  updateStatus: (ticketId: string, status: Ticket['status']) => void;
  renewCount: () => number;
}

export const useTickets = create<TicketState>()(
  persist(
    (set, get) => ({
      tickets: seedTickets,
      add: (t) => {
        const ticket: Ticket = {
          ...t, id: uid('tk'), createdAt: new Date().toISOString(),
          messages: t.messages ?? [{
            id: uid('m'), author: 'customer', authorName: 'کاربر', text: t.subject, date: new Date().toISOString(),
          }],
        };
        set((s) => ({ tickets: [ticket, ...s.tickets] }));
        return ticket;
      },
      reply: (ticketId, msg) => set((s) => ({ tickets: s.tickets.map((t) => (t.id === ticketId ? { ...t, messages: [...t.messages, msg], status: t.status === 'open' ? 'pending' : t.status } : t)) })),
      updateStatus: (ticketId, status) => set((s) => ({ tickets: s.tickets.map((t) => (t.id === ticketId ? { ...t, status } : t)) })),
      renewCount: () => get().tickets.filter((t) => t.status === 'open' || t.status === 'pending').length,
    }),
    { name: 'hooshyar.tickets' },
  ),
);

/** ===== فاکتورها ===== */
interface InvoiceState {
  invoices: Invoice[];
  add: (i: Omit<Invoice, 'id'>) => void;
  pay: (id: string, refId: string, gateway: string) => void;
  totalPaid: () => number;
  totalDue: () => number;
}

export const useInvoices = create<InvoiceState>()(
  persist(
    (set, get) => ({
      invoices: seedInvoices,
      add: (i) => set((s) => ({ invoices: [{ ...i, id: uid('inv') }, ...s.invoices] })),
      pay: (id, refId, gateway) => set((s) => ({ invoices: s.invoices.map((inv) => inv.id === id ? { ...inv, status: 'paid', paidAt: new Date().toISOString(), refId, gateway } : inv) })),
      totalPaid: () => get().invoices.filter((i) => i.status === 'paid').reduce((a, i) => a + i.items.reduce((x, it) => x + it.qty * it.unitPrice, 0), 0),
      totalDue: () => get().invoices.filter((i) => i.status === 'sent' || i.status === 'overdue').reduce((a, i) => a + i.items.reduce((x, it) => x + it.qty * it.unitPrice, 0), 0),
    }),
    { name: 'hooshyar.invoices' },
  ),
);
