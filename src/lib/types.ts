// تایپ‌های سراسری قالب

export type Role = 'admin' | 'support' | 'customer';

export interface User {
  id: number | string;
  name: string;
  email?: string;
  mobile: string;
  role: Role;
  avatar?: string;
  company?: string;
  /** وضعیت احراز هویت */
  verified?: boolean;
}

export interface AuthSession {
  token: string;
  user: User;
  /** آیا کاربر از پنل مدیر/پشتیبان وارد می‌شود */
  isStaff?: boolean;
}

export type ServiceCategory = string;

export interface Service {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  description: string;
  icon: string;
  features: string[];
  priceFrom?: number;
  category: ServiceCategory;
  popular?: boolean;
}

export interface TeamMember {
  id: string;
  name: string;
  role: string;
  bio: string;
  avatar: string;
  skills: string[];
  socials: { l?: string; i?: string; t?: string; g?: string };
}

export interface Project {
  id: string;
  title: string;
  client: string;
  category: string;
  description: string;
  img: string;
  tags: string[];
  year: string;
  link?: string;
}

export interface Testimonial {
  id: string;
  name: string;
  role: string;
  company: string;
  text: string;
  rating: number;
  avatar: string;
}

export interface Faq {
  q: string;
  a: string;
}

export interface Product {
  id: number | string;
  name: string;
  slug: string;
  sku: string;
  price: number;
  regularPrice?: number;
  image: string;
  category: string;
  shortDescription: string;
  description: string;
  stock: number;
  rating: number;
  reviews: number;
  attributes: { label: string; value: string }[];
  sale?: boolean;
}

export interface CartItem {
  product: Product;
  qty: number;
}

export interface Order {
  id: number | string;
  number: string;
  date: string;
  status: 'pending' | 'processing' | 'completed' | 'cancelled' | 'refunded';
  total: number;
  items: { name: string; qty: number; price: number }[];
  paymentMethod?: string;
  refId?: string;
}

export type TicketStatus = 'open' | 'answered' | 'pending' | 'closed';
export type TicketPriority = 'low' | 'medium' | 'high' | 'critical';

export interface TicketMessage {
  id: string;
  author: 'customer' | 'support' | 'system';
  authorName: string;
  text: string;
  date: string;
}

export interface Ticket {
  id: string;
  subject: string;
  department: string;
  status: TicketStatus;
  priority: TicketPriority;
  createdAt: string;
  messages: TicketMessage[];
}

export type CrmStatus = 'lead' | 'opportunity' | 'customer' | 'lost' | 'qualified';

export interface CrmContact {
  id: string;
  name: string;
  mobile: string;
  company?: string;
  email?: string;
  tags: string[];
  status: CrmStatus;
  assignedTo: string;
  lastActivity: string;
  score: number;
  notes: string[];
  source?: string;
}

export interface Deal {
  id: string;
  title: string;
  contactId: string;
  value: number;
  stage: 'new' | 'qualified' | 'proposal' | 'negotiation' | 'won' | 'lost';
  probability: number;
  expectedClose: string;
  owner: string;
}

export type InvoiceStatus = 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';

export interface Invoice {
  id: string;
  number: string;
  customerId: string;
  customerName: string;
  items: { title: string; qty: number; unitPrice: number }[];
  status: InvoiceStatus;
  createdAt: string;
  dueDate: string;
  paidAt?: string;
  refId?: string;
  gateway?: string;
}

export interface JobPosition {
  id: string;
  title: string;
  department: string;
  location: string;
  type: 'fulltime' | 'parttime' | 'remote' | 'contract';
  salary?: string;
  description: string;
  requirements: string[];
  active: boolean;
}

export interface ExamQuestion {
  id: string;
  type: 'choice' | 'multi' | 'text' | 'video';
  text: string;
  options?: string[];
  correct?: number[] | null;
  points: number;
  videoUrl?: string;
  timeLimit?: number;
}

export interface Exam {
  id: string;
  title: string;
  positionId: string;
  durationMinutes: number;
  totalPoints: number;
  passScore: number;
  questions: ExamQuestion[];
  shuffled: boolean;
  proctored: boolean;
}

export interface SupportPlan {
  id: string;
  name: string;
  durationLabel: string;
  durationMonths: number;
  price: number;
  features: string[];
  popular?: boolean;
}

export interface Notification {
  id: string;
  title: string;
  body: string;
  date: string;
  read: boolean;
  type: 'support' | 'order' | 'system' | 'finance' | 'exam';
}

export interface ChatMessage {
  id: string;
  from: 'me' | 'them';
  name: string;
  text: string;
  date: string;
  status?: 'sending' | 'sent' | 'read' | 'failed';
}

export interface ActivityItem {
  id: string;
  icon: string;
  text: string;
  date: string;
  tone?: 'gold' | 'teal' | 'violet' | 'danger' | 'muted';
}
