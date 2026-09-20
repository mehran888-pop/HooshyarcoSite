// مودال، تاست و سوییچ صفحه
import { createContext, useCallback, useContext, useEffect, useState, type ReactNode } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import Icon from './Icon';
import { uid } from '../../lib/format';

/* ===== مودال ===== */
export function Modal({ open, onClose, title, children, width = 500 }: {
  open: boolean; onClose: () => void; title?: string; children: ReactNode; width?: number;
}) {
  useEffect(() => {
    const h = (e: KeyboardEvent) => e.key === 'Escape' && onClose();
    if (open) window.addEventListener('keydown', h);
    return () => window.removeEventListener('keydown', h);
  }, [open, onClose]);

  return (
    <AnimatePresence>
      {open && (
        <motion.div className="modal-overlay" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onMouseDown={onClose}>
          <motion.div
            className="modal-panel"
            style={{ maxWidth: width }}
            initial={{ opacity: 0, y: 24, scale: .97 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 24, scale: .97 }}
            onMouseDown={(e) => e.stopPropagation()}
          >
            <div className="modal-head">
              <h3>{title}</h3>
              <button className="icon-btn" onClick={onClose} aria-label="بستن"><Icon name="x" size={18} /></button>
            </div>
            <div className="modal-body">{children}</div>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

/* ===== تاست ===== */
type ToastType = 'success' | 'error' | 'info';
interface Toast { id: string; type: ToastType; message: string }
type ToastFn = (message: string, type?: ToastType) => void;

const ToastCtx = createContext<ToastFn>(() => {});
export const useToast = () => useContext(ToastCtx);
export { type ToastFn };

const toastTone: Record<ToastType, { icon: string; color: string }> = {
  success: { icon: 'check-circle', color: 'var(--success)' },
  error: { icon: 'alert-circle', color: 'var(--danger)' },
  info: { icon: 'info', color: 'var(--info)' },
};

export function ToastProvider({ children }: { children: ReactNode }) {
  const [toasts, setToasts] = useState<Toast[]>([]);
  const push = useCallback<ToastFn>((message, type = 'success') => {
    const id = uid('t');
    setToasts((t) => [...t, { id, message, type }]);
    setTimeout(() => setToasts((t) => t.filter((x) => x.id !== id)), 3800);
  }, []);
  return (
    <ToastCtx.Provider value={push}>
      {children}
      <div className="toast-wrap">
        <AnimatePresence>
          {toasts.map((t) => (
            <motion.div key={t.id} className="toast" initial={{ opacity: 0, x: 40 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: 40 }}>
              <Icon name={toastTone[t.type].icon} size={20} style={{ color: toastTone[t.type].color }} />
              <span className="small">{t.message}</span>
            </motion.div>
          ))}
        </AnimatePresence>
      </div>
    </ToastCtx.Provider>
  );
}

/* ===== سوییچ صفحه با انیمیشن ===== */
export function Page({ children, delay = 0 }: { children: ReactNode; delay?: number }) {
  return (
    <motion.main initial={{ opacity: 0, y: 14 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: .45, delay, ease: [0.22, 1, 0.36, 1] }}>
      {children}
    </motion.main>
  );
}
