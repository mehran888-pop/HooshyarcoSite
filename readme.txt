=== Hooshyar Commerce Kit ===
Contributors: hooshyarco
Tags: woocommerce, elementor, cart, checkout, digipay
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional WooCommerce kit: advanced cart & checkout templates, user dashboard, dedicated Elementor elements, creative headers/footers, Telegram/Bale publishing and the DigiPay payment gateway.

== Description ==

Hooshyar Commerce Kit is an all-in-one professional toolkit for WooCommerce
stores (tested with WooCommerce 11.1.1+):

* Professional cart with selectable templates and layouts (default, modern, minimal, creative)
* Professional checkout page with selectable templates and layouts
* Professional user dashboard with selectable templates, synced with WooCommerce, plus a header user-area button
* Dedicated Elementor elements: products (grid / carousel / slider / list / masonry), banners & posters with creative effects, product banners
* Creative add-to-cart effects (fly to cart, arc, zoom, confetti)
* Multiple professional header & footer templates and layouts
* Creative shop page and professional product page layouts
* Publish new products to Telegram channels and Bale channels (bot API)
* Mobile bottom navigation that can replace the footer on mobile
* DigiPay UPG payment gateway (card, wallet, BNPL credit) with verify & reverse
* Every colour, font, size and layout is controllable from WordPress settings
  and from Elementor style controls
* Full Elementor & Elementor Pro support (custom theme locations, widgets, shortcodes)

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP.
2. Activate the plugin (WooCommerce must be active).
3. Go to "Hooshyar Kit → Settings" and configure the templates and integrations.
4. For Elementor widgets, open any page in Elementor and look for the
   "Hooshyar Kit" widget category.

== Frequently Asked Questions ==

= Does it work with Elementor Pro? =

Yes. The plugin registers custom theme locations (`hck_header`, `hck_footer`)
and its widgets work in both free and Pro editors. You can also assign an
Elementor template to the header/footer in the plugin settings.

= Which payment tools does DigiPay support? =

Card gateway (IPG), wallet, and credit/BNPL — the full UPG flow including
purchase tickets, verification and manual reverse.

== Changelog ==

= 1.0.7 =
* Fully Persian UI — the admin and storefront are Persian regardless of the site locale (runtime dictionary)
* Redesigned settings screen: modern dashboard with sidebar tabs, grouped cards, styled controls, sticky save bar
* New: header and footer width — full-width or boxed (inside the container)
* New: site content width — full-width (edge to edge) or boxed
* New: custom CSS field (Design tab) printed on the storefront

= 1.0.6 =
* Redesign: header/footer restyled after top Iranian e-commerce stores — clean white bar, wide centered search, outlined login/cart buttons, plain text navigation
* New header layout: logo | search | actions on top, category menu + main menu below
* Category menu restyled: plain trigger button, clean stacked list, gray counts, side flyout for sub-categories
* Footer is now light by default with subtle variants (Classic/Modern/Minimal/Creative incl. dark option)
* i18n build tool added under tools/ (regenerates .po/.mo without msgfmt)

= 1.0.5 =
* Header/footer templates now apply on every theme: added block-theme support (header/footer template parts are replaced with the selected HCK template)
* The four header/footer presets (Classic / Modern / Minimal / Creative) are now visually distinct and fully styled
* Product-category menu is enabled by default in the header
* Safer WooCommerce URL handling in header/footer (no fatals if helpers are missing)

= 1.0.4 =
* Improvement: category menu redesigned — categories stacked vertically with images, counts and accent hover; sub-categories open in a side panel (mega) or inline accordion
* Improvement: richer button/panel styling (gradient trigger, shadows, animations)
* Fix: dedicated Elementor widgets were not registered (plugin boot ran after `elementor/loaded` fired) — all HCK widgets now appear in the "Hooshyar Kit" category
* Fix: derived CSS variables (primary-soft, surface-alt, transitions, shadows) now provided — hover styles across templates render correctly

= 1.0.3 =
* New: professional product-category menu in the header (mega panel or dropdown) with images, counts and sub-categories
* New: choose the main navigation menu (any WP menu) and its alignment (right / center / left) in the header settings
* New: custom font upload (woff2/woff/ttf) — fonts are enqueued via @font-face and selectable everywhere
* New: [hck_category_menu] shortcode and "HCK Category Menu" Elementor widget with full styling controls

= 1.0.2 =
* Fix: call to undefined function wp_get_menus() on WordPress < 6.5 (now uses wp_get_nav_menus)
* Fix: safe substring helper when mbstring is unavailable
* Hardened bootstrap against partial uploads

= 1.0.0 =
* Initial release.
