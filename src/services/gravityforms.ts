// سرویس اتصال فرم‌های گراویتی‌فرم
import { isLive, wpHttp } from './http';
import { APP_CONFIG } from '../config/site';

export const gfService = {
  /**
   * ارسال فرم به گراویتی‌فرم (REST).
   * در حالت واقعی از افزونه Gravity Forms REST API (gform) استفاده می‌شود.
   * @param formId شناسه فرم گراویتی‌فرم
   * @param payload مقادیر فیلدها (کلید: نام ستون)
   */
  async submit(formId: number | string, payload: Record<string, string>, type: 'contact' | 'consultation' | 'employment'): Promise<void> {
    if (!isLive()) {
      await new Promise((r) => setTimeout(r, 800));
      console.info('[GravityForms] form', formId, type, payload);
      return;
    }
    // با افزونه رسمی: POST /wp-json/gf/v2/forms/{id}/submissions با ساختار fieldValues
    await wpHttp.post(`/wp-json/gf/v2/forms/${formId}/submissions`, {
      fieldValues: Object.entries(payload).map(([k, v]) => ({ name: k, value: v })),
    });
  },
};
