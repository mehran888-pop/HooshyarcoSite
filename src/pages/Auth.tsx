// ورود / ثبت‌نام با شماره موبایل و کد یک‌بارمصرف (OTP)
import { useEffect, useState } from 'react';
import { useNavigate, useSearchParams, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { GuestOnly } from '../components/layout/Scroll';
import { Button, Field, Input } from '../components/shared/ui';
import { useToast } from '../components/shared/Overlay';
import Icon from '../components/shared/Icon';
import { authService } from '../services/wordpress';
import { smsService } from '../services/integrations';
import { useAuth } from '../store';
import { isValidMobile, faNum } from '../lib/format';

export default function Auth() {
  return (
    <GuestOnly>
      <div className="auth-wrap">
        <div className="auth-brand">
          <div className="auth-logo">ه</div>
          <h1>هوش‌یار پاری‌نگر</h1>
          <p className="muted">ورود و ثبت‌نام با شماره موبایل</p>
          <ul className="auth-benefits">
            <li><Icon name="lock" size={16} /> ورود امن بدون نیاز به رمز عبور</li>
            <li><Icon name="dashboard" size={16} /> دسترسی به پنل کاربری و فاکتورها</li>
            <li><Icon name="ticket" size={16} /> تیکت پشتیبانی و چت آنلاین</li>
          </ul>
        </div>
        <AuthCard />
      </div>
    </GuestOnly>
  );
}

function AuthCard() {
  const [sp] = useSearchParams();
  const mode = sp.get('mode') === 'register' ? 'register' : 'login';
  const [step, setStep] = useState<'mobile' | 'otp' | 'register'>('mobile');
  const [name, setName] = useState('');
  const [mobile, setMobile] = useState('');
  const [otp, setOtp] = useState('');
  const [code, setCode] = useState('');
  const [isStaff, setIsStaff] = useState(false);
  const [loading, setLoading] = useState(false);
  const [timer, setTimer] = useState(0);
  const [attempts, setAttempts] = useState(0);
  const [locked, setLocked] = useState(false);
  const { login } = useAuth();
  const toast = useToast();
  const navigate = useNavigate();

  useEffect(() => {
    if (timer <= 0) return;
    const t = setTimeout(() => setTimer((s) => s - 1), 1000);
    return () => clearTimeout(t);
  }, [timer]);

  async function sendCode() {
    if (!isValidMobile(mobile)) return toast('شماره موبایل معتبر وارد کنید.', 'error');
    if (locked) return;
    setLoading(true);
    try {
      await authService.sendOtp(mobile);
      await smsService.send('melipayamak', mobile, `کد ورود به هوش‌یار: ۱۲۳۴۵`);
      setStep('otp');
      setTimer(120);
      toast('کد تأیید ارسال شد.');
    } finally {
      setLoading(false);
    }
  }

  async function verify() {
    if (code.trim().length < 4) return toast('کد تأیید را وارد کنید.', 'error');
    setLoading(true);
    try {
      const session = await authService.verifyOtp(mobile, code, isStaff);
      login(session);
      toast(`خوش آمدید، ${session.user.name} 👋`);
      navigate(session.isStaff ? '/admin' : '/panel');
    } catch (e: any) {
      const n = attempts + 1;
      setAttempts(n);
      if (n >= 5) { setLocked(true); toast('به دلیل تلاش‌های ناموفق، ورود برای ۵ دقیقه قفل شد (جلوگیری از اسپم).', 'error'); }
      else toast(e?.message || 'کد تأیید نادرست است.', 'error');
    } finally {
      setLoading(false);
    }
  }

  async function doRegister() {
    if (name.trim().length < 2) return toast('نام خود را وارد کنید.', 'error');
    setLoading(true);
    try {
      await authService.register(mobile, name, '');
      const session = await authService.verifyOtp(mobile, '12345', false);
      login(session);
      toast('ثبت‌نام شما با موفقیت انجام شد 🎉');
      navigate('/panel');
    } catch (e: any) {
      toast(e?.message || 'خطا در ثبت‌نام.', 'error');
    } finally {
      setLoading(false);
    }
  }

  return (
    <motion.div className="auth-card" initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
      {step === 'mobile' && (
        <>
          <h2 className="mb-2" style={{ fontSize: 21 }}>{mode === 'register' ? 'ثبت‌نام / ورود' : 'ورود یا ثبت‌نام'}</h2>
          <p className="muted small mb-3">شماره موبایل خود را وارد کنید؛ اگر حساب ندارید، به صورت خودکار ساخته می‌شود.</p>
          <Field label="شماره موبایل">
            <Input value={mobile} onChange={(e) => setMobile(e.target.value)} placeholder="۰۹۱۲۳۴۵۶۷۸۹" inputMode="numeric" dir="ltr" style={{ textAlign: 'left' }} />
          </Field>
          {mode === 'register' && (
            <Field label="نام و نام خانوادگی">
              <Input value={name} onChange={(e) => setName(e.target.value)} placeholder="نام شما" />
            </Field>
          )}
          <label className="check-line">
            <input type="checkbox" checked={isStaff} onChange={(e) => setIsStaff(e.target.checked)} />
            <span className="small">ورود به عنوان مدیر / پشتیبان</span>
          </label>
          <Button variant="primary" block size="lg" disabled={loading || locked} onClick={sendCode} icon={loading ? 'loader' : 'send'} style={{ marginTop: 18 }}>
            {loading ? 'در حال ارسال...' : 'دریافت کد تأیید'}
          </Button>
          {locked && <p className="small" style={{ color: 'var(--danger)', marginTop: 10 }}><Icon name="lock" size={13} /> ورود موقتاً قفل شد.</p>}
          <div className="auth-hint"><Icon name="shield" size={14} /> ورود امن با OTP؛ کد دمو: <b className="mono" dir="ltr">12345</b></div>
        </>
      )}

      {step === 'otp' && (
        <>
          <h2 className="mb-2" style={{ fontSize: 21 }}>کد تأیید را وارد کنید</h2>
          <p className="muted small mb-3">کد ۵ رقمی ارسال‌شده به <b className="mono">{faNum(mobile)}</b> را وارد نمایید.</p>
          <Field label="کد تأیید">
            <Input value={code} onChange={(e) => setCode(e.target.value.replace(/\D/g, '').slice(0, 5))} placeholder="• • • • •" inputMode="numeric" dir="ltr" style={{ textAlign: 'center', letterSpacing: 8, fontWeight: 800, fontSize: 20 }} />
          </Field>
          <Button variant="primary" block size="lg" disabled={loading || locked} onClick={verify} icon={loading ? 'loader' : 'check'}>
            {loading ? 'در حال بررسی...' : 'تأیید و ورود'}
          </Button>
          <div className="flex items-center justify-between mt-2">
            <button className="small muted" onClick={() => setStep('mobile')}>ویرایش شماره</button>
            <button className="small" style={{ color: 'var(--gold)' }} disabled={timer > 0} onClick={sendCode}>
              {timer > 0 ? `ارسال مجدد (${faNum(Math.floor(timer / 60))}:${faNum(String(timer % 60).padStart(2, '0'))})` : 'ارسال مجدد کد'}
            </button>
          </div>
          {attempts > 2 && <p className="small" style={{ color: 'var(--danger)', marginTop: 8 }}>تلاش‌های نادرست: {faNum(attempts)} از ۵</p>}
        </>
      )}

      {step === 'register' && (
        <>
          <h2 className="mb-2" style={{ fontSize: 21 }}>ثبت‌نام</h2>
          <p className="muted small mb-3">حساب شما با شماره {faNum(mobile)} ساخته می‌شود.</p>
          <Field label="نام و نام خانوادگی"><Input value={name} onChange={(e) => setName(e.target.value)} /></Field>
          <Button variant="primary" block size="lg" disabled={loading} onClick={doRegister} icon={loading ? 'loader' : 'user-plus'}>
            {loading ? '...' : 'ساخت حساب و ورود'}
          </Button>
        </>
      )}

      <div className="text-center mt-3">
        <Link to="/consultation" className="small" style={{ color: 'var(--teal)' }}>سوالی دارید؟ درخواست مشاوره دهید</Link>
      </div>
    </motion.div>
  );
}
