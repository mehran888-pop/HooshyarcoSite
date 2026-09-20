// سرویس ووکامرس + مدیریت سفارش‌ها
import { isLive, wcHttp } from './http';
import { products as demoProducts } from '../mock/data';
import type { Order, Product } from '../lib/types';
import { uid } from '../lib/format';

export const productService = {
  async list(): Promise<Product[]> {
    if (!isLive()) return demoProducts;
    const { data } = await wcHttp.get('/products');
    return data.map((p: any) => ({
      id: p.id, name: p.name, slug: p.slug, sku: p.sku, price: Number(p.price),
      regularPrice: p.regular_price ? Number(p.regular_price) : undefined,
      image: p.images?.[0]?.src ?? '', category: p.categories?.[0]?.name ?? '',
      shortDescription: p.short_description?.replace(/<[^>]+>/g, '') ?? '',
      description: p.description?.replace(/<[^>]+>/g, '') ?? '',
      stock: p.stock_quantity ?? 999, rating: Number(p.average_rating ?? 0), reviews: p.rating_count ?? 0,
      attributes: [], sale: !!p.sale_price,
    }));
  },
  async get(id: number | string): Promise<Product | undefined> {
    if (!isLive()) return demoProducts.find((p) => p.id === id);
    return (await this.list()).find((p) => p.id === id);
  },
};

export const orderService = {
  /** ثبت سفارش و ساخت تراکنش پرداخت */
  async create(items: { name: string; qty: number; price: number }[], total: number, gateway: string): Promise<Order> {
    const order: Order = {
      id: uid('ord'), number: `ORD-${Math.floor(1000 + Math.random() * 9000)}`, date: new Date().toISOString(),
      status: 'pending', total, items, paymentMethod: gateway,
    };
    if (isLive()) {
      const { data } = await wcHttp.post('/orders', {
        payment_method: gateway,
        billing: {},
        line_items: items.map((i) => ({ name: i.name, quantity: i.qty, total: String(i.price * i.qty) })),
      });
      order.id = data.id;
    }
    return order;
  },
};
