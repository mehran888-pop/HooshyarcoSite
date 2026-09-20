// فروشگاه متصل به ووکامرس
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import { Page } from '../components/shared/Overlay';
import { Button, Rating, Badge, Empty } from '../components/shared/ui';
import Icon from '../components/shared/Icon';
import { productService } from '../services/woocommerce';
import { useCart, useAuth } from '../store';
import { useToast } from '../components/shared/Overlay';
import { price, faNum } from '../lib/format';
import type { Product } from '../lib/types';
import { motion } from 'framer-motion';

function ProductTile({ p, i }: { p: Product; i: number }) {
  const add = useCart((s) => s.add);
  const toast = useToast();
  const auth = useAuth((s) => s.session);
  return (
    <motion.div className="card card-hover product-card" initial={{ opacity: 0, y: 20 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true }} transition={{ delay: (i % 3) * 0.07, duration: .4 }}>
      <div className={`product-thumb theme-${i % 4}`}>
        {p.sale && <span className="sale-flag badge badge-danger">٪ فروش‌ویژه</span>}
        <Icon name={['code', 'database', 'bot', 'trending', 'shield', 'headphones', 'palette', 'phone2'][i % 8]} size={44} />
      </div>
      <div className="product-body">
        <span className="badge badge-muted small">{p.category}</span>
        <h3><Link to={`/products/${p.slug}`}>{p.name}</Link></h3>
        <p className="muted small prod-excerpt">{p.shortDescription}</p>
        <Rating value={p.rating} size={13} />
        <div className="flex items-center justify-between mt-2">
          <div>
            {p.sale && p.regularPrice && <del className="small muted">{price(p.regularPrice)}</del>}
            <div className="price-big grad-gold-text">{price(p.price)}</div>
          </div>
          <Button size="sm" variant="primary" icon="cart" onClick={() => { add(p); toast(`${p.name} به سبد اضافه شد 🛒`); }}>
            افزودن
          </Button>
        </div>
        {!auth && <p className="small muted mt-1"><Icon name="info" size={12} /> برای خرید وارد شوید</p>}
      </div>
    </motion.div>
  );
}

export function Products() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [cat, setCat] = useState('همه');
  useEffect(() => { productService.list().then((p) => { setProducts(p); setLoading(false); }).catch(() => setLoading(false)); }, []);
  const cats = ['همه', ...Array.from(new Set(products.map((p) => p.category)))];
  const filtered = cat === 'همه' ? products : products.filter((p) => p.category === cat);
  return (
    <Page>
      <section className="page-hero">
        <div className="container">
          <span className="eyebrow"><Icon name="cart" size={14} /> فروشگاه</span>
          <h1>فروشگاه <span className="grad-text">هوش‌یار</span></h1>
          <p className="muted">محصولات و خدمات آماده، متصل به ووکامرس با پرداخت آنلاین.</p>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 30 }}>
        <div className="container">
          <div className="filter-row">
            {cats.map((c) => <button key={c} className={`filter-chip ${cat === c ? 'active' : ''}`} onClick={() => setCat(c)}>{c}</button>)}
          </div>
          {loading ? (
            <div className="text-center" style={{ padding: 60 }}><Icon name="loader" size={30} className="spin" /></div>
          ) : filtered.length === 0 ? (
            <Empty title="محصولی یافت نشد" text="در این دسته‌بندی محصولی موجود نیست." />
          ) : (
            <div className="grid-4 mt-3">
              {filtered.map((p, i) => <ProductTile key={p.id} p={p} i={i} />)}
            </div>
          )}
        </div>
      </section>
    </Page>
  );
}

export function ProductDetail() {
  const { slug } = useParams();
  const [p, setP] = useState<Product | undefined>();
  const [qty, setQty] = useState(1);
  const add = useCart((s) => s.add);
  const toast = useToast();

  useEffect(() => {
    productService.list().then((list) => setP(list.find((x) => x.slug === slug)));
  }, [slug]);

  if (!p) return <Page><div className="container text-center" style={{ padding: 120 }}><Icon name="loader" size={30} className="spin" /></div></Page>;

  return (
    <Page>
      <div className="container" style={{ paddingTop: 40 }}>
        <div className="breadcrumb small muted mb-3">
          <Link to="/products">فروشگاه</Link> <Icon name="chevron-down" size={13} style={{ transform: 'rotate(-90deg)' }} /> {p.name}
        </div>
        <div className="product-detail">
          <div className={`product-thumb theme-1 detail-thumb`}>
            <Icon name="package" size={80} />
          </div>
          <div>
            <div className="flex gap-2 items-center mb-2">
              <span className="badge badge-muted">{p.category}</span>
              {p.sale && <Badge tone="danger">فروش ویژه</Badge>}
            </div>
            <h1 style={{ fontSize: 26 }}>{p.name}</h1>
            <div className="flex items-center gap-2 my-2">
              <Rating value={p.rating} />
              <span className="small muted">({faNum(p.reviews)} دیدگاه)</span>
            </div>
            <p className="muted" style={{ lineHeight: 2.1 }}>{p.description}</p>
            {p.attributes.length > 0 && (
              <div className="attr-list mt-3">
                {p.attributes.map((a) => <div key={a.label} className="attr-item"><span className="small muted">{a.label}</span><b className="small">{a.value}</b></div>)}
              </div>
            )}
            <div className="flex items-end gap-3 mt-3">
              {p.sale && p.regularPrice && <del className="muted">{price(p.regularPrice)}</del>}
              <span className="grad-gold-text" style={{ fontSize: 30, fontWeight: 900 }}>{price(p.price)}</span>
            </div>
            <div className="flex items-center gap-2 mt-3">
              <div className="qty-stepper">
                <button onClick={() => setQty((q) => Math.max(1, q - 1))}><Icon name="minus" size={15} /></button>
                <span className="mono">{faNum(qty)}</span>
                <button onClick={() => setQty((q) => q + 1)}><Icon name="plus" size={15} /></button>
              </div>
              <Button variant="primary" icon="cart" onClick={() => { add(p, qty); toast('به سبد خرید اضافه شد 🛒'); }}>افزودن به سبد</Button>
            </div>
            <p className="small muted mt-2"><Icon name="shield" size={13} style={{ color: 'var(--teal)' }} /> پرداخت امن از طریق درگاه‌های رسمی بانکی</p>
          </div>
        </div>
      </div>
    </Page>
  );
}
