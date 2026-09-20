// ساختار اصلی اپلیکیشن + مسیرها
import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import Header from './components/layout/Header';
import Footer from './components/layout/Footer';
import ChatWidget from './components/layout/ChatWidget';
import { ScrollToTop, ScrollTopButton, RequireAuth, GuestOnly } from './components/layout/Scroll';
import { ToastProvider } from './components/shared/Overlay';

import Home from './pages/Home';
import { Services, ServiceDetail } from './pages/Services';
import { Projects, Team, About } from './pages/About';
import { Contact } from './pages/Contact';
import { Products, ProductDetail } from './pages/Shop';
import { Cart, Checkout } from './pages/ShopCart';
import { Blog, BlogPost } from './pages/Blog';
import { Consultation } from './pages/Consultation';
import { Careers, JobDetail, ExamPage } from './pages/Careers';
import Auth from './pages/Auth';

import UserDashboard from './pages/panel/UserPanel';
import SupportTickets from './pages/panel/Support';
import { Invoices, PayInvoicePage } from './pages/panel/Invoices';
import { Plans, Orders, Notifications, Logout } from './pages/panel/PlansOrders';

import AdminDashboard, { AdminCrm } from './pages/admin/Admin';
import { AdminSupport, AdminCustomers, AdminInvoices, AdminNotifications } from './pages/admin/AdminPages';

function NotFound() {
  return (
    <div className="container text-center" style={{ padding: 120 }}>
      <div className="grad-text" style={{ fontSize: 72, fontWeight: 900 }}>۴۰۴</div>
      <h2 className="mb-2">صفحه پیدا نشد</h2>
      <p className="muted mb-3">آدرس واردشده وجود ندارد یا جابه‌جا شده است.</p>
      <a href="/" className="btn btn-primary">بازگشت به خانه</a>
    </div>
  );
}

function Shell() {
  const loc = useLocation();
  // در صفحات ادمین/پنل هدر و فوتر اصلی را مخفی می‌کنیم (پوسته اختصاصی دارند)
  const isPanel = loc.pathname.startsWith('/panel') || loc.pathname.startsWith('/admin');
  return (
    <>
      <ScrollToTop />
      {!isPanel && <Header />}
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/services" element={<Services />} />
        <Route path="/services/:slug" element={<ServiceDetail />} />
        <Route path="/projects" element={<Projects />} />
        <Route path="/team" element={<Team />} />
        <Route path="/about" element={<About />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/consultation" element={<Consultation />} />
        <Route path="/products" element={<Products />} />
        <Route path="/products/:slug" element={<ProductDetail />} />
        <Route path="/cart" element={<Cart />} />
        <Route path="/checkout" element={<Checkout />} />
        <Route path="/blog" element={<Blog />} />
        <Route path="/blog/:slug" element={<BlogPost />} />
        <Route path="/careers" element={<Careers />} />
        <Route path="/careers/:id" element={<JobDetail />} />
        <Route path="/careers/:id/exam" element={<RequireAuth><ExamPage /></RequireAuth>} />
        <Route path="/auth" element={<Auth />} />

        {/* پنل کاربری */}
        <Route path="/panel" element={<UserDashboard />} />
        <Route path="/panel/support" element={<SupportTickets />} />
        <Route path="/panel/plans" element={<Plans />} />
        <Route path="/panel/invoices" element={<Invoices />} />
        <Route path="/panel/invoices/pay/:id" element={<PayInvoicePage />} />
        <Route path="/panel/orders" element={<Orders />} />
        <Route path="/panel/notifications" element={<Notifications />} />
        <Route path="/panel/logout" element={<Logout />} />

        {/* پنل مدیریت */}
        <Route path="/admin" element={<AdminDashboard />} />
        <Route path="/admin/crm" element={<AdminCrm />} />
        <Route path="/admin/support" element={<AdminSupport />} />
        <Route path="/admin/customers" element={<AdminCustomers />} />
        <Route path="/admin/invoices" element={<AdminInvoices />} />
        <Route path="/admin/notifications" element={<AdminNotifications />} />

        <Route path="*" element={<NotFound />} />
      </Routes>
      {!isPanel && <Footer />}
      <ChatWidget />
      <ScrollTopButton />
    </>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <ToastProvider>
        <Shell />
      </ToastProvider>
    </BrowserRouter>
  );
}
