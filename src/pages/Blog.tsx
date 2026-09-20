// مقالات و آموزش‌های ویدیویی (متصل به وردپرس)
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Page } from '../components/shared/Overlay';
import { Badge, Button, Empty } from '../components/shared/ui';
import Icon from '../components/shared/Icon';
import { contentService } from '../services/wordpress';
import { faDate, readTime } from '../lib/format';
import type { BlogPost } from '../mock/blog';

const themes = ['violet', 'teal', 'gold'];

export function Blog() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);
  const [tab, setTab] = useState<'all' | 'article' | 'tutorial'>('all');
  useEffect(() => { contentService.getPosts().then((p) => { setPosts(p); setLoading(false); }).catch(() => setLoading(false)); }, []);
  const filtered = tab === 'all' ? posts : posts.filter((p) => p.category === tab);
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="book" size={14} /> بلاگ</span>
          <h1>مقالات و <span className="grad-text">آموزش‌ها</span></h1>
          <p className="muted">دانش روز فناوری، به زبان ساده.</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 30 }}>
        <div className="container">
          <div className="filter-row">
            {[{ id: 'all', l: 'همه' }, { id: 'article', l: '📄 مقالات' }, { id: 'tutorial', l: '🎬 آموزش‌ها' }].map((t) => (
              <button key={t.id} className={`filter-chip ${tab === t.id ? 'active' : ''}`} onClick={() => setTab(t.id as any)}>{t.l}</button>
            ))}
          </div>
          {loading ? <div className="text-center" style={{ padding: 60 }}><Icon name="loader" size={30} className="spin" /></div> : filtered.length === 0 ? <Empty title="مطلبی یافت نشد" /> : (
            <div className="grid-3 mt-3">
              {filtered.map((p, i) => (
                <Link to={`/blog/${p.slug}`} key={p.id} className="card card-hover blog-card">
                  <div className={`blog-thumb theme-${themes.indexOf(p.thumbnailTheme)}`}>
                    <Icon name={p.hasVideo ? 'play-circle' : 'book'} size={38} />
                    {p.hasVideo && <span className="play-badge"><Icon name="play" size={16} /></span>}
                  </div>
                  <div className="blog-body">
                    <div className="flex items-center gap-2">
                      <Badge tone={p.category === 'tutorial' ? 'violet' : 'teal'}>{p.category === 'tutorial' ? 'آموزش' : 'مقاله'}</Badge>
                      <span className="small muted">{faDate(p.date)}</span>
                    </div>
                    <h3>{p.title}</h3>
                    <p className="muted small">{p.excerpt}</p>
                    <div className="small muted flex items-center gap-2"><Icon name="author" size={13} /> {p.author} • {p.readTime}</div>
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      </section>
    </Page>
  );
}

export function BlogPost() {
  const { slug } = useParams();
  const [post, setPost] = useState<BlogPost | undefined>();

  useEffect(() => {
    contentService.getPosts().then((p) => setPost(p.find((x) => x.slug === slug)));
  }, [slug]);

  if (!post) return <Page><div className="container text-center" style={{ padding: 120 }}><Icon name="loader" size={30} className="spin" /></div></Page>;

  return (
    <Page>
      <div className="container" style={{ maxWidth: 800, paddingTop: 40 }}>
        <div className="breadcrumb small muted mb-3"><Link to="/blog">بلاگ</Link> <Icon name="chevron-down" size={13} style={{ transform: 'rotate(-90deg)' }} /> {post.title.slice(0, 30)}…</div>
        <span className="eyebrow">{post.category === 'tutorial' ? 'آموزش' : 'مقاله'}</span>
        <h1 style={{ fontSize: 28, margin: '14px 0 10px', lineHeight: 1.7 }}>{post.title}</h1>
        <div className="flex items-center gap-3 small muted mb-3">
          <span className="inline-flex items-center gap-1"><Icon name="check-circle" size={14} /> {post.author}</span>
          <span className="inline-flex items-center gap-1"><Icon name="calendar" size={14} /> {faDate(post.date)}</span>
          <span className="inline-flex items-center gap-1"><Icon name="clock" size={14} /> {readTime(post.content)} مطالعه</span>
        </div>
        <div className={`blog-hero theme-${themes.indexOf(post.thumbnailTheme)}`}><Icon name={post.hasVideo ? 'play-circle' : 'sparkles'} size={60} /></div>
        {post.hasVideo && post.videoUrl && (
          <div className="card mb-3 mt-3" style={{ padding: 14 }}>
            <h3 className="mb-2" style={{ fontSize: 16 }}>🎬 ویدیوی آموزشی</h3>
            <video className="q-video" controls src={post.videoUrl} style={{ margin: 0 }} />
          </div>
        )}
        <article className="post-content">
          {post.content.split('\n\n').map((par, i) => <p key={i}>{par}</p>)}
        </article>
        <div className="flex wrap gap-2 mt-3">
          {post.tags.map((t) => <span key={t} className="tech-chip">{t}</span>)}
        </div>
        <div className="card mt-4" style={{ display: 'flex', alignItems: 'center', gap: 16, flexWrap: 'wrap' }}>
          <Icon name="chat-circle" size={32} style={{ color: 'var(--gold)' }} />
          <div style={{ flex: 1 }}>
            <b style={{ fontSize: 15 }}>سوالی درباره این موضوع دارید؟</b>
            <div className="small muted">تیم ما آماده پاسخگویی و مشاوره است.</div>
          </div>
          <Button as="link" to="/consultation" variant="primary" size="sm">درخواست مشاوره</Button>
        </div>
      </div>
    </Page>
  );
}
