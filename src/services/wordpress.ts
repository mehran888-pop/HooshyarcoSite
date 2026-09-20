// سرویس اتصال به وردپرس (Headless) با fallback به داده‌های دمو
import { wpHttp, isLive } from './http';
import { demoUserFor } from '../store';
import { posts as demoPosts } from '../mock/blog';
import { faqs as demoFaqs, services as demoServices, team as demoTeam, projects as demoProjects, testimonials as demoTestimonials } from '../mock/data';
import type { AuthSession, Faq, Project, Service, TeamMember, Testimonial, User } from '../lib/types';
import type { BlogPost } from '../mock/blog';

// ===== لاگین با موبایل + OTP (JWT) =====
export const authService = {
  /** ارسال کد OTP — در حالت واقعی از مسیر سفارشی وردپرس یا افزونه JWT+OTP */
  async sendOtp(mobile: string): Promise<{ success: boolean; message: string }> {
    if (!isLive()) {
      await new Promise((r) => setTimeout(r, 900));
      return { success: true, message: 'کد تأیید ارسال شد (حالت دمو: کد ۱۲۳۴۵)' };
    }
    const { data } = await wpHttp.post('/wp-json/hooshyar/v1/auth/otp', { mobile });
    return { success: true, message: data.message || 'کد تأیید ارسال شد' };
  },

  /** تأیید OTP → دریافت توکن JWT */
  async verifyOtp(mobile: string, code: string, isStaff = false): Promise<AuthSession> {
    if (!isLive()) {
      await new Promise((r) => setTimeout(r, 900));
      if (String(code).trim() !== '12345') throw new Error('کد تأیید نادرست است (کد دمو: ۱۲۳۴۵)');
      const base = demoUserFor(mobile);
      const user: User = { id: 1, name: base.name, mobile, role: base.role, verified: true, company: base.company };
      return { token: 'demo-jwt-token', user, isStaff: isStaff || base.role !== 'customer' };
    }
    const { data } = await wpHttp.post('/wp-json/jwt-auth/v1/token', { username: mobile, password: code });
    return { token: data.token, user: { id: data.user?.id ?? mobile, name: data.user?.nicename ?? mobile, mobile, role: 'customer' }, isStaff };
  },

  /** ثبت‌نام با موبایل */
  async register(mobile: string, name: string, email: string): Promise<{ success: boolean; message: string }> {
    if (!isLive()) {
      await new Promise((r) => setTimeout(r, 900));
      return { success: true, message: 'ثبت‌نام انجام شد. خوش آمدید!' };
    }
    const { data } = await wpHttp.post('/wp-json/hooshyar/v1/auth/register', { mobile, name, email });
    return { success: true, message: data.message || 'ثبت‌نام انجام شد' };
  },
};

// ===== محتوا =====
export const contentService = {
  async getPosts(): Promise<BlogPost[]> {
    if (!isLive()) return demoPosts;
    const { data } = await wpHttp.get('/wp-json/wp/v2/posts?_embed');
    return data.map((p: any) => ({
      id: String(p.id), slug: p.slug, title: p.title?.rendered ?? '',
      excerpt: p.excerpt?.rendered?.replace(/<[^>]+>/g, '') ?? '', content: p.content?.rendered ?? '',
      category: 'article', tags: [], author: 'هوش‌یار', date: p.date, readTime: '۵ دقیقه',
      hasVideo: false, thumbnailTheme: 'teal',
    }));
  },
  async getPost(slug: string): Promise<BlogPost | undefined> {
    if (!isLive()) return demoPosts.find((p) => p.slug === slug);
    const { data } = await wpHttp.get(`/wp-json/wp/v2/posts?slug=${slug}&_embed`);
    return data?.[0] as BlogPost | undefined;
  },
  async getServices(): Promise<Service[]> { return isLive() ? (await wpHttp.get('/wp-json/hooshyar/v1/services')).data : demoServices; },
  async getTeam(): Promise<TeamMember[]> { return isLive() ? (await wpHttp.get('/wp-json/hooshyar/v1/team')).data : demoTeam; },
  async getProjects(): Promise<Project[]> { return isLive() ? (await wpHttp.get('/wp-json/hooshyar/v1/projects')).data : demoProjects; },
  async getTestimonials(): Promise<Testimonial[]> { return isLive() ? (await wpHttp.get('/wp-json/hooshyar/v1/testimonials')).data : demoTestimonials; },
  async getFaqs(): Promise<Faq[]> { return isLive() ? (await wpHttp.get('/wp-json/hooshyar/v1/faqs')).data : demoFaqs; },
};
