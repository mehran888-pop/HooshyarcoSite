// استخدام و آزمون آنلاین (با نظارت تصویری و تصحیح خودکار)
import { useEffect, useMemo, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Page } from '../components/shared/Overlay';
import { Badge, Button, Empty, Toggle } from '../components/shared/ui';
import { useToast } from '../components/shared/Overlay';
import Icon from '../components/shared/Icon';
import { positions as demoPositions, exam as demoExam } from '../mock/data';
import { useAuth } from '../store';
import { faNum, uid } from '../lib/format';
import type { Exam, JobPosition } from '../lib/types';

const typeLabel: Record<string, string> = { fulltime: 'تمام‌وقت', parttime: 'پاره‌وقت', remote: 'دورکاری', contract: 'قراردادی' };

export function Careers() {
  const active = demoPositions.filter((p) => p.active);
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="briefcase" size={14} /> فرصت‌های شغلی</span>
          <h1>به خانواده <span className="grad-text">هوش‌یار</span> بپیوندید</h1>
          <p className="muted">فرایند استخدام شفاف و آنلاین؛ آزمون استخدامی را در همین سایت انجام دهید.</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 30 }}>
        <div className="container" style={{ maxWidth: 900 }}>
          <div className="flex col gap-3">
            {active.map((j) => (
              <div key={j.id} className="card job-card">
                <div>
                  <div className="flex items-center gap-2 wrap">
                    <h3 style={{ fontSize: 17 }}>{j.title}</h3>
                    <Badge tone="teal">{j.department}</Badge>
                    <Badge tone="muted">{typeLabel[j.type]}</Badge>
                  </div>
                  <p className="small muted mt-1"><Icon name="pin" size={13} /> {j.location} • <Icon name="wallet" size={13} /> {j.salary}</p>
                  <p className="muted small mt-2">{j.description}</p>
                </div>
                <div className="flex items-center gap-2">
                  <Button as="link" to={`/careers/${j.id}`} variant="primary" size="sm">جزئیات و آزمون</Button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </Page>
  );
}

export function JobDetail() {
  const { id } = useParams();
  const session = useAuth((s) => s.session);
  const [agreed, setAgreed] = useState(false);
  const [started, setStarted] = useState(false);
  const job = demoPositions.find((j) => j.id === id);
  const navigate = useNavigate();

  if (!job) return <Page><div className="container text-center" style={{ padding: 100 }}><Empty title="موقعیت یافت نشد" /></div></Page>;

  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow">{job.department}</span>
          <h1>{job.title}</h1>
          <p className="muted">{job.location} • {typeLabel[job.type]} • {job.salary}</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 20 }}>
        <div className="container" style={{ maxWidth: 800 }}>
          <div className="card mb-3">
            <h3 className="mb-2" style={{ fontSize: 17 }}>شرح موقعیت</h3>
            <p className="muted small" style={{ lineHeight: 2.1 }}>{job.description}</p>
            <h3 className="mb-2 mt-3" style={{ fontSize: 17 }}>الزامات</h3>
            <ul className="svc-features">
              {job.requirements.map((r) => <li key={r}><Icon name="check" size={15} style={{ color: 'var(--teal)' }} />{r}</li>)}
            </ul>
          </div>
          <div className="card exam-intro">
            <div className="feature-icon"><Icon name="clip" size={26} /></div>
            <div style={{ flex: 1 }}>
              <h3 style={{ fontSize: 17 }}>آزمون آنلاین استخدام</h3>
              <p className="muted small">آزمون شامل {faNum(demoExam.questions.length)} سوال با تصحیح خودکار و نظارت تصویری (وب‌کم). مدت آزمون {faNum(demoExam.durationMinutes)} دقیقه و حد نصاب قبولی {faNum(demoExam.passScore)} از {faNum(demoExam.totalPoints)} است.</p>
              <div className="flex items-center gap-2 mt-2 wrap">
                <Badge tone="violet" icon="video">نظارت تصویری</Badge>
                <Badge tone="gold" icon="zap">تصحیح خودکار</Badge>
                <Badge tone="teal" icon="timer">زمان‌بندی شده</Badge>
              </div>
            </div>
          </div>

          {!session ? (
            <div className="card text-center mt-3" style={{ padding: 26 }}>
              <p className="mb-3">برای شروع آزمون، ابتدا با شماره موبایل وارد شوید.</p>
              <Button as="link" to={`/auth?redirect=/careers/${job.id}`} variant="primary" icon="user">ورود / ثبت‌نام</Button>
            </div>
          ) : !agreed ? (
            <div className="card mt-3">
              <p className="small muted mb-2">قبل از شروع، موارد زیر را تأیید کنید:</p>
              <label className="check-line"><input type="checkbox" checked={agreed} onChange={(e) => setAgreed(e.target.checked)} /><span className="small">با قوانین آزمون موافقم و رضایت به ضبط تصویر در طول آزمون دارم.</span></label>
              <Button variant="primary" block style={{ marginTop: 14 }} disabled={!agreed} onClick={() => setAgreed(true)} icon="check">تأیید و ادامه</Button>
            </div>
          ) : (
            <div className="card mt-3 text-center">
              <p className="small muted mb-2">آماده‌اید؟ پس از شروع، تایمر آزمون فعال می‌شود و قابل توقف نیست.</p>
              <Button variant="primary" block onClick={() => navigate(`/careers/${job.id}/exam`)} icon="play">شروع آزمون</Button>
            </div>
          )}
        </div>
      </section>
    </Page>
  );
}

export function ExamPage() {
  const { id } = useParams();
  const exam: Exam = demoExam;
  const navigate = useNavigate();
  const toast = useToast();
  const [idx, setIdx] = useState(0);
  const [answers, setAnswers] = useState<Record<string, number[] | string>>({});
  const [timeLeft, setTimeLeft] = useState(exam.durationMinutes * 60);
  const [finished, setFinished] = useState(false);
  const [camOn, setCamOn] = useState(false);

  useEffect(() => {
    const t = setInterval(() => setTimeLeft((s) => Math.max(0, s - 1)), 1000);
    return () => clearInterval(t);
  }, []);

  useEffect(() => { if (timeLeft === 0 && !finished) submitExam(); /* eslint-disable-next-line */ }, [timeLeft]);

  const q = exam.questions[idx];
  const min = Math.floor(timeLeft / 60), sec = timeLeft % 60;

  const score = useMemo(() => {
    let s = 0;
    for (const question of exam.questions) {
      const ans = answers[question.id];
      if (question.type === 'choice' && Array.isArray(ans) && question.correct && ans[0] === question.correct[0]) s += question.points;
      if (question.type === 'multi' && Array.isArray(ans) && question.correct) {
        const correct = [...question.correct].sort().join(','), given = [...ans].sort().join(',');
        if (correct === given) s += question.points;
      }
      if (question.type === 'text' && typeof ans === 'string' && ans.trim().length > 3) s += Math.max(1, Math.floor(question.points * 0.7)); // تصحیح خودکار ساده
    }
    return s;
  }, [answers, exam.questions]);

  function submitExam() {
    setFinished(true);
    const passed = score >= exam.passScore;
    toast(`${passed ? '🎉 قبول شدید!' : 'متأسفانه حد نصاب کسب نشد.'} نمره شما: ${faNum(score)} از ${faNum(exam.totalPoints)}`, passed ? 'success' : 'error');
  }

  if (finished) {
    const passed = score >= exam.passScore;
    return (
      <Page>
        <div className="container" style={{ maxWidth: 640, paddingTop: 60 }}>
          <div className="card text-center" style={{ padding: 40 }}>
            <Icon name={passed ? 'award' : 'alert-circle'} size={54} style={{ color: passed ? 'var(--gold)' : 'var(--danger)' }} />
            <h2 style={{ fontSize: 22, marginTop: 12 }}>{passed ? 'تبریک! شما قبول شدید 🎉' : 'نمره شما کمتر از حد نصاب بود'}</h2>
            <div className="grad-text" style={{ fontSize: 44, fontWeight: 900, margin: '14px 0' }}>{faNum(score)} <span className="small" style={{ fontSize: 18 }}>از {faNum(exam.totalPoints)}</span></div>
            <p className="muted small">حد نصاب قبولی: {faNum(exam.passScore)}</p>
            <div className="flex gap-2 justify-center mt-3 wrap">
              {passed && <Button variant="primary" onClick={() => toast('نتیجه شما برای تیم منابع انسانی ارسال شد. به‌زودی تماس می‌گیریم.')}>ثبت نهایی نتیجه</Button>}
              <Button variant="ghost" onClick={() => navigate('/careers')}>بازگشت به فرصت‌ها</Button>
            </div>
          </div>
        </div>
      </Page>
    );
  }

  return (
    <Page>
      <div className="container" style={{ maxWidth: 860, paddingTop: 30 }}>
        <div className="exam-top glass">
          <div>
            <b>{exam.title}</b>
            <div className="small muted">سوال {faNum(idx + 1)} از {faNum(exam.questions.length)}</div>
          </div>
          <div className={`exam-timer ${timeLeft < 120 ? 'danger' : ''}`}><Icon name="timer" size={18} /> {faNum(min)}:{faNum(String(sec).padStart(2, '0'))}</div>
          <Toggle checked={camOn} onChange={setCamOn} label="نظارت تصویری" />
        </div>

        <div className="cam-strip">
          <div className={`cam-view ${camOn ? 'live' : ''}`}>
            {camOn ? <><span className="pulse-dot" /> <span className="small muted">وب‌کم در حال ضبط است (شبیه‌سازی)</span></> : <><Icon name="video" size={22} /> <span className="small muted">نظارت تصویری فعال نیست</span></>}
          </div>
          <div className="exam-progress"><div style={{ width: `${((idx + 1) / exam.questions.length) * 100}%` }} /></div>
        </div>

        <motion.div key={q.id} className="card exam-q" initial={{ opacity: 0, x: 24 }} animate={{ opacity: 1, x: 0 }}>
          <Badge tone={q.type === 'choice' ? 'teal' : q.type === 'multi' ? 'violet' : q.type === 'video' ? 'gold' : 'muted'}>
            {q.type === 'choice' ? 'چهارگزینه‌ای' : q.type === 'multi' ? 'چند انتخابی' : q.type === 'video' ? 'آزمون ویدیویی' : 'تشریحی'}
          </Badge>
          <h3 className="mt-2" style={{ fontSize: 17 }}>{q.text}</h3>
          <span className="small muted">امتیاز: {faNum(q.points)}</span>

          {q.type === 'video' && q.videoUrl && (
            <video className="q-video" controls src={q.videoUrl} onEnded={() => toast('ویدیو تمام شد؛ حالا پاسخ دهید.' ,'info')} />
          )}

          {q.type === 'choice' && q.options?.map((o, i) => (
            <button key={i} className={`opt ${(answers[q.id] as number[])?.[0] === i ? 'active' : ''}`} onClick={() => setAnswers((a) => ({ ...a, [q.id]: [i] }))}>
              <span className="opt-mark">{faNum(i + 1)}</span> {o}
            </button>
          ))}
          {q.type === 'multi' && q.options?.map((o, i) => {
            const cur = (answers[q.id] as number[]) ?? [];
            const on = cur.includes(i);
            return (
              <button key={i} className={`opt ${on ? 'active' : ''}`} onClick={() => setAnswers((a) => ({ ...a, [q.id]: on ? cur.filter((x) => x !== i) : [...cur, i] }))}>
                <span className="opt-mark">{on ? '✓' : faNum(i + 1)}</span> {o}
              </button>
            );
          })}
          {q.type === 'text' && (
            <textarea className="input mt-2" rows={4} value={(answers[q.id] as string) ?? ''} onChange={(e) => setAnswers((a) => ({ ...a, [q.id]: e.target.value }))} placeholder="پاسخ خود را بنویسید..." />
          )}
        </motion.div>

        <div className="flex items-center justify-between mt-3">
          <Button variant="ghost" disabled={idx === 0} onClick={() => setIdx((i) => i - 1)} icon="chevron-right">قبلی</Button>
          {idx < exam.questions.length - 1 ? (
            <Button variant="primary" onClick={() => setIdx((i) => i + 1)}>بعدی <Icon name="chevron-down" size={16} style={{ transform: 'rotate(-90deg)' }} /></Button>
          ) : (
            <Button variant="teal" onClick={submitExam} icon="check-circle">ثبت و پایان آزمون</Button>
          )}
        </div>
      </div>
    </Page>
  );
}
