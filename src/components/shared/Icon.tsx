import {
  Brain, Globe, Code, Users, Palette, TrendingUp, Sparkles, Rocket, ShieldCheck, Headphones,
  Bot, MessageSquare, CreditCard, Bell, Ticket, Package, FileText, Wallet, GraduationCap,
  Video, PlayCircle, Menu, X, ChevronDown, ChevronLeft, ChevronRight, Phone, Mail, MapPin,
  Instagram, Linkedin, Github, Send, SendHorizonal, Star, Check, CheckCircle2, AlertTriangle,
  AlertCircle, Info, Clock, Search, ShoppingCart, User, UserPlus, LogOut, LayoutDashboard,
  Settings, Plus, Minus, Trash2, Eye, Download, ExternalLink, Filter, ListChecks, BarChart3,
  LineChart, PieChart, Target, Zap, Award, Briefcase, Home, BookOpen, GraduationCap as Grad,
  ClipboardCheck, Timer, Play, RefreshCw, Loader2, Paperclip, Smile, MoreHorizontal, Heart,
  Calendar, Layers, Server, Smartphone, Database, Cloud, Lock, LifeBuoy, TrendingDown,
  CircleDollarSign, Receipt, Printer, ScanLine, MessageCircle, WalletCards, Banknote,
  type LucideIcon,
} from 'lucide-react';

const map: Record<string, LucideIcon> = {
  brain: Brain, globe: Globe, code: Code, users: Users, palette: Palette, 'trending': TrendingUp,
  sparkles: Sparkles, rocket: Rocket, shield: ShieldCheck, headphones: Headphones, bot: Bot,
  chat: MessageSquare, 'chat-circle': MessageCircle, credit: CreditCard, bell: Bell, ticket: Ticket,
  package: Package, file: FileText, wallet: Wallet, grad: GraduationCap, video: Video,
  'play-circle': PlayCircle, menu: Menu, x: X, 'chevron-down': ChevronDown, 'chevron-left': ChevronLeft,
  'chevron-right': ChevronRight, phone: Phone, mail: Mail, pin: MapPin, instagram: Instagram,
  linkedin: Linkedin, github: Github, send: Send, 'send-h': SendHorizonal, star: Star, check: Check,
  'check-circle': CheckCircle2, alert: AlertTriangle, 'alert-circle': AlertCircle, info: Info,
  clock: Clock, search: Search, cart: ShoppingCart, user: User, 'user-plus': UserPlus, logout: LogOut,
  dashboard: LayoutDashboard, settings: Settings, plus: Plus, minus: Minus, trash: Trash2, eye: Eye,
  download: Download, external: ExternalLink, filter: Filter, list: ListChecks, bar: BarChart3,
  line: LineChart, pie: PieChart, target: Target, zap: Zap, award: Award, briefcase: Briefcase,
  home: Home, book: BookOpen, clip: ClipboardCheck, timer: Timer, play: Play, refresh: RefreshCw,
  loader: Loader2, attach: Paperclip, smile: Smile, more: MoreHorizontal, heart: Heart,
  calendar: Calendar, layers: Layers, server: Server, phone2: Smartphone, database: Database,
  cloud: Cloud, lock: Lock, lifebuoy: LifeBuoy, trenddown: TrendingDown, money: CircleDollarSign,
  receipt: Receipt, printer: Printer, scan: ScanLine, cards: WalletCards, banknote: Banknote,
  author: User,
};

export default function Icon({ name, size = 20, className = '', strokeWidth = 2, style }: {
  name: string; size?: number; className?: string; strokeWidth?: number; style?: React.CSSProperties;
}) {
  const Cmp = map[name] || Sparkles;
  return <Cmp size={size} className={className} strokeWidth={strokeWidth} style={style} />;
}
