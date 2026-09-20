// اسکرول به بالا + کامپوننت محافظ مسیر
import { useEffect, useState, type ReactNode } from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import Icon from '../shared/Icon';
import { useAuth } from '../../store';

export function ScrollToTop() {
  const { pathname } = useLocation();
  useEffect(() => { window.scrollTo({ top: 0, behavior: 'instant' as ScrollBehavior }); }, [pathname]);
  return null;
}

export function ScrollTopButton() {
  const [show, setShow] = useState(false);
  useEffect(() => {
    const h = () => setShow(window.scrollY > 500);
    window.addEventListener('scroll', h);
    return () => window.removeEventListener('scroll', h);
  }, []);
  return (
    <AnimatePresence>
      {show && (
        <motion.button className="scroll-top" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })} aria-label="بالا">
          <Icon name="chevron-right" size={20} style={{ transform: 'rotate(90deg)' }} />
        </motion.button>
      )}
    </AnimatePresence>
  );
}

/** محافظ مسیرهای نیازمند ورود */
export function RequireAuth({ children, staff = false }: { children: ReactNode; staff?: boolean }) {
  const session = useAuth((s) => s.session);
  if (!session) return <Navigate to="/auth" replace />;
  if (staff && !session.isStaff) return <Navigate to="/panel" replace />;
  return <>{children}</>;
}

/** اگر کاربر وارد شده، صفحه ورود را رد کند */
export function GuestOnly({ children }: { children: ReactNode }) {
  const session = useAuth((s) => s.session);
  if (session) return <Navigate to={session.isStaff ? '/admin' : '/panel'} replace />;
  return <>{children}</>;
}
