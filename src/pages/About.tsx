// صفحات پروژه‌ها، تیم و درباره ما
import { useEffect, useState } from 'react';
import { Page } from '../components/shared/Overlay';
import { SectionHead } from '../components/shared/ui';
import { ProjectCard, TeamCard, CTASection } from '../components/shared/Sections';
import Icon from '../components/shared/Icon';
import { contentService } from '../services/wordpress';
import { SITE } from '../config/site';
import type { Project, TeamMember } from '../lib/types';

export function Projects() {
  const [projects, setProjects] = useState<Project[]>([]);
  useEffect(() => { contentService.getProjects().then(setProjects).catch(() => {}); }, []);
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="briefcase" size={14} /> نمونه‌کارها</span>
          <h1>پروژه‌های <span className="grad-text">موفق ما</span></h1>
          <p className="muted">هر پروژه، یک چالش واقعی و یک نتیجه قابل اندازه‌گیری.</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 40 }}>
        <div className="container">
          <div className="grid-3">{projects.map((p, i) => <ProjectCard key={p.id} p={p} i={i} />)}</div>
        </div>
      </section>
      <CTASection />
    </Page>
  );
}

export function Team() {
  const [team, setTeam] = useState<TeamMember[]>([]);
  useEffect(() => { contentService.getTeam().then(setTeam).catch(() => {}); }, []);
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="users" size={14} /> تیم ما</span>
          <h1>قهرمانان <span className="grad-text">هوش‌یار</span></h1>
          <p className="muted">تیمی از متخصصان عاشق فناوری و حل مسئله.</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 40 }}>
        <div className="container">
          <div className="grid-3">{team.map((m, i) => <TeamCard key={m.id} m={m} i={i} />)}</div>
        </div>
      </section>
      <CTASection />
    </Page>
  );
}

export function About() {
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="sparkles" size={14} /> درباره ما</span>
          <h1>داستان <span className="grad-text">{SITE.name}</span></h1>
          <p className="muted" style={{ maxWidth: 640, margin: '0 auto' }}>{SITE.description}</p>
        </div>
      </section>

      <section className="section">
        <div className="container" style={{ maxWidth: 1000 }}>
          <div className="about-grid">
            {[
              { icon: 'target', t: 'ماموریت ما', d: 'شتاب‌دهی به رشد کسب‌وکارهای ایرانی با فناوری‌های هوشمند و راهکارهای دیجیتال باکیفیت جهانی.' },
              { icon: 'eye', t: 'چشم‌انداز', d: 'تبدیل شدن به پیشروترین شرکت هوش مصنوعی و توسعه نرم‌افزار در منطقه، با تمرکز بر نوآوری و رضایت مشتری.' },
              { icon: 'heart', t: 'ارزش‌های ما', d: 'صداقت، کیفیت، مشتری‌مداری و یادگیری مستمر. ما به نتیجه وعده‌داده‌شده متعهدیم.' },
            ].map((b) => (
              <div key={b.t} className="card card-hover text-center">
                <div className="feature-icon"><Icon name={b.icon} size={26} /></div>
                <h3>{b.t}</h3>
                <p className="muted small">{b.d}</p>
              </div>
            ))}
          </div>

          <div className="numbers-strip mt-4">
            {[
              { n: '+۸', l: 'سال تجربه' },
              { n: '+۲۵۰', l: 'پروژه تحویل‌شده' },
              { n: '+۱۸۰', l: 'مشتری فعال' },
              { n: '+۳۵', l: 'متخصص تمام‌وقت' },
            ].map((s) => (
              <div key={s.l} className="num-item"><b className="grad-text">{s.n}</b><span className="small muted">{s.l}</span></div>
            ))}
          </div>
        </div>
      </section>
      <CTASection title="با ما همکار شوید" desc="فرصت‌های شغلی ما را ببینید یا به عنوان مشتری شروع کنید." dual />
    </Page>
  );
}
