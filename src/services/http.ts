// کلاینت HTTP سراسری بر پایه Axios
import axios from 'axios';
import { APP_CONFIG } from '../config/site';
import { useAuth } from '../store';

export const wpHttp = axios.create({
  baseURL: APP_CONFIG.wpBaseUrl || undefined,
  timeout: 20000,
  headers: { 'Content-Type': 'application/json' },
});

wpHttp.interceptors.request.use((cfg) => {
  const token = useAuth.getState().session?.token;
  if (token) cfg.headers.Authorization = `Bearer ${token}`;
  return cfg;
});

/** آیا اتصال به وردپرس واقعی برقرار است؟ */
export const isLive = () => !!APP_CONFIG.wpBaseUrl && !APP_CONFIG.demoMode;

// اتصال ووکامرس با OAuth یک‌بخشی (Consumer Key/Secret) — فقط سمت سرور امن است
/* در نسخه واقعی، ووکامرس باید از طریق یک واسطه سمت سرور صدا زده شود.
   این ساختار جای اتصال را مشخص می‌کند: */
export const wcHttp = axios.create({
  baseURL: `${APP_CONFIG.wpBaseUrl}${APP_CONFIG.woocommercePath}`,
  params: {
    consumer_key: APP_CONFIG.consumerKey,
    consumer_secret: APP_CONFIG.consumerSecret,
  },
});
