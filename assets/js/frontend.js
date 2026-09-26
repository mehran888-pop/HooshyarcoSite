/**
 * Hooshyar Commerce Kit — frontend core.
 *
 * Handles: fly-to-cart effects, toast notifications, cart drawer,
 * quantity steppers, user-area dropdown, scroll entrance animations.
 */
(function () {
  'use strict';

  var HCK = window.hckData || {};
  var FX = HCK.effects || {};

  /* ------------------------------------------------------------------
   * Utilities
   * ---------------------------------------------------------------- */
  function $(selector, scope) {
    return (scope || document).querySelector(selector);
  }

  function $$(selector, scope) {
    return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
  }

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /* ------------------------------------------------------------------
   * Toast
   * ---------------------------------------------------------------- */
  var toastEl = null;
  var toastTimer = null;

  function showToast(message, linkUrl) {
    if (FX.toast === false) return;

    if (!toastEl) {
      toastEl = document.createElement('div');
      toastEl.className = 'hck-toast';
      document.body.appendChild(toastEl);
    }

    toastEl.innerHTML =
      '<span class="hck-toast__icon">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
      '</span>' +
      '<span class="hck-toast__text"></span>' +
      (linkUrl ? '<a class="hck-toast__link" href="' + linkUrl + '"></a>' : '');

    $('.hck-toast__text', toastEl).textContent = message;
    if (linkUrl) {
      $('.hck-toast__link', toastEl).textContent = (HCK.i18n && HCK.i18n.viewCart) || 'View cart';
    }

    toastEl.classList.add('is-visible');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toastEl.classList.remove('is-visible');
    }, 3800);
  }

  /* ------------------------------------------------------------------
   * Cart bump
   * ---------------------------------------------------------------- */
  function bumpCart() {
    if (FX.cartBump === false) return;

    $$('.hck-cart-count').forEach(function (badge) {
      badge.classList.remove('hck-cart-bump');
      // Force reflow so the animation can restart.
      void badge.offsetWidth;
      badge.classList.add('hck-cart-bump');
    });
  }

  /* ------------------------------------------------------------------
   * Confetti
   * ---------------------------------------------------------------- */
  function confetti(x, y) {
    var colors = ['#6c5ce7', '#00cec9', '#fd79a8', '#f59e0b', '#16a34a'];
    for (var i = 0; i < 18; i++) {
      (function (i) {
        var piece = document.createElement('div');
        piece.className = 'hck-confetti';
        piece.style.left = x + 'px';
        piece.style.top = y + 'px';
        piece.style.background = colors[i % colors.length];
        document.body.appendChild(piece);

        var angle = (Math.PI * 2 * i) / 18 + Math.random();
        var velocity = 90 + Math.random() * 130;
        var dx = Math.cos(angle) * velocity;
        var dy = Math.sin(angle) * velocity - 110;
        var rot = Math.random() * 620 - 310;

        var animation = piece.animate(
          [
            { transform: 'translate(0, 0) rotate(0deg)', opacity: 1 },
            { transform: 'translate(' + dx + 'px, ' + (dy + 240) + 'px) rotate(' + rot + 'deg)', opacity: 0 }
          ],
          { duration: 1050 + Math.random() * 550, easing: 'cubic-bezier(0.2, 0.7, 0.3, 1)' }
        );

        animation.onfinish = function () {
          piece.remove();
        };
      })(i);
    }
  }

  /* ------------------------------------------------------------------
   * Fly to cart
   * ---------------------------------------------------------------- */
  function getCartTarget() {
    return (
      $('.hck-user-area__cart') ||
      $('.hck-mnav__item--cart') ||
      $('[data-hck-open-cart]') ||
      $('.hck-header-actions')
    );
  }

  function flyToCart(sourceEl) {
    var target = getCartTarget();
    if (!target || !sourceEl || prefersReducedMotion()) {
      bumpCart();
      showToast((HCK.i18n && HCK.i18n.added) || 'Added to cart', HCK.cartUrl);
      return;
    }

    var img = sourceEl.querySelector('img');
    var srcRect = (img || sourceEl).getBoundingClientRect();
    var targetRect = target.getBoundingClientRect();

    var clone = document.createElement('div');
    clone.className = 'hck-fly-clone';
    clone.style.left = srcRect.left + 'px';
    clone.style.top = srcRect.top + 'px';
    clone.style.width = srcRect.width + 'px';
    clone.style.height = srcRect.height + 'px';

    var cloneImg = document.createElement('img');
    cloneImg.src = img ? img.currentSrc || img.src : '';
    if (!img) {
      clone.style.background = 'linear-gradient(135deg, #6c5ce7, #00cec9)';
    }
    clone.appendChild(cloneImg);
    document.body.appendChild(clone);

    var scaleEnd = Math.max(0.12, targetRect.width / srcRect.width);
    var dx = targetRect.left + targetRect.width / 2 - (srcRect.left + srcRect.width / 2);
    var dy = targetRect.top + targetRect.height / 2 - (srcRect.top + srcRect.height / 2);

    var duration = FX.duration || 850;
    clone.style.transitionDuration = duration + 'ms';

    // Next frame: apply the transform so the transition runs.
    requestAnimationFrame(function () {
      if (FX.fly === 'arc') {
        // Two-step transition to fake an arc path.
        clone.style.transform =
          'translate(' + dx * 0.55 + 'px, ' + (dy * 0.25 - 90) + 'px) scale(' + (scaleEnd * 1.7) + ')';
        clone.style.opacity = '0.95';

        setTimeout(function () {
          clone.style.transitionDuration = Math.round(duration * 0.55) + 'ms';
          clone.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) scale(' + scaleEnd + ')';
          clone.style.opacity = '0.25';
        }, duration * 0.42);
      } else if (FX.fly === 'zoom') {
        clone.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) scale(' + scaleEnd * 2.4 + ')';
        clone.style.opacity = '0';
      } else {
        clone.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) scale(' + scaleEnd + ') rotate(8deg)';
        clone.style.opacity = '0.25';
        clone.style.borderRadius = '50%';
      }
    });

    setTimeout(function () {
      clone.remove();
      bumpCart();
      if (FX.confetti || FX.fly === 'confetti') {
        confetti(targetRect.left + targetRect.width / 2, targetRect.top + targetRect.height / 2);
      }
      showToast((HCK.i18n && HCK.i18n.added) || 'Added to cart', HCK.cartUrl);
    }, duration + 40);
  }

  /* ------------------------------------------------------------------
   * Add-to-cart listeners
   * ---------------------------------------------------------------- */
  function bindAddToCart() {
    // Standard WooCommerce AJAX add-to-cart events.
    jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
      if (!$button) return;
      var card = $button.closest('.hck-card, .hck-pbanner, .product')[0];
      flyToCart(card || $button[0]);
    });

    // Non-WooCommerce custom buttons with data-hck-add-to-cart.
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-hck-fly-source]');
      if (btn) {
        flyToCart(btn.closest('.hck-card, .hck-pbanner, .product') || btn);
      }
    });
  }

  /* ------------------------------------------------------------------
   * Cart drawer
   * ---------------------------------------------------------------- */
  function openCartDrawer() {
    var drawer = $('#hck-cart-drawer');
    if (drawer) {
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeCartDrawer() {
    var drawer = $('#hck-cart-drawer');
    if (drawer) {
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  }

  function bindCartDrawer() {
    document.addEventListener('click', function (e) {
      if (e.target.closest('[data-hck-open-cart]')) {
        // Let the link work on middle-click / modifier keys.
        if (e.target.closest('a') && (e.metaKey || e.ctrlKey || e.shiftKey)) return;

        var drawer = $('#hck-cart-drawer');
        if (drawer) {
          e.preventDefault();
          openCartDrawer();
          return;
        }
        // No drawer: navigate to the cart page.
        if (HCK.cartUrl && e.target.closest('a')) {
          e.preventDefault();
          window.location.href = HCK.cartUrl;
        }
      }

      if (e.target.closest('[data-hck-close-cart]')) {
        e.preventDefault();
        closeCartDrawer();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeCartDrawer();
      }
    });

    // Keep the drawer body in sync with WooCommerce fragments.
    if (window.jQuery) {
      jQuery(document.body).on('updated_wc_div removed_from_cart added_to_cart', function () {
        var body = $('[data-hck-mini-cart]');
        if (!body || !HCK.ajaxUrl) return;

        body.classList.add('is-loading');

        window
          .fetch(HCK.ajaxUrl + '?action=hck_get_mini_cart&nonce=' + encodeURIComponent(HCK.nonce), {
            credentials: 'same-origin'
          })
          .then(function (r) {
            return r.json();
          })
          .then(function (res) {
            body.classList.remove('is-loading');
            if (res && res.success && res.data && res.data.html) {
              body.innerHTML = res.data.html;
            }
          })
          .catch(function () {
            body.classList.remove('is-loading');
          });
      });
    }
  }

  /* ------------------------------------------------------------------
   * Quantity steppers (cart page)
   * ---------------------------------------------------------------- */
  function bindQtySteppers() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-hck-qty]');
      if (!btn) return;

      e.preventDefault();

      var wrap = btn.closest('.hck-qty');
      var input = wrap && wrap.querySelector('[data-hck-qty-input]');
      if (!input) return;

      var step = parseFloat(btn.getAttribute('data-hck-qty')) || 0;
      var value = parseFloat(input.value) || 0;
      value = Math.max(parseInt(input.min || 0, 10), value + step);

      input.value = value;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  }

  /* ------------------------------------------------------------------
   * User area dropdown
   * ---------------------------------------------------------------- */
  function bindUserArea() {
    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('[data-hck-user-toggle]');
      var areas = $$('[data-hck-user-area]');

      if (toggle) {
        e.preventDefault();
        var area = toggle.closest('[data-hck-user-area]');
        var isOpen = area.classList.contains('is-open');

        areas.forEach(function (a) {
          a.classList.remove('is-open');
          var t = a.querySelector('[data-hck-user-toggle]');
          if (t) t.setAttribute('aria-expanded', 'false');
        });

        if (!isOpen) {
          area.classList.add('is-open');
          toggle.setAttribute('aria-expanded', 'true');
        }
        return;
      }

      if (!e.target.closest('[data-hck-user-area]')) {
        areas.forEach(function (a) {
          a.classList.remove('is-open');
          var t = a.querySelector('[data-hck-user-toggle]');
          if (t) t.setAttribute('aria-expanded', 'false');
        });
      }
    });
  }

  /* ------------------------------------------------------------------
   * Scroll entrance animations (IntersectionObserver)
   * ---------------------------------------------------------------- */
  function bindAnimations() {
    var selector =
      '[data-hck-banner], [data-hck-pbanner], .hck-products, [data-hck-animate], .hck-card, .hck-dash-welcome, .hck-hero';

    var elements = $$(selector);

    if (!('IntersectionObserver' in window) || prefersReducedMotion()) {
      elements.forEach(function (el) {
        el.classList.add('is-inview');
      });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-inview');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    elements.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ------------------------------------------------------------------
   * Header burger (mobile) — toggles the nav display
   * ---------------------------------------------------------------- */
  function bindBurger() {
    document.addEventListener('click', function (e) {
      var burger = e.target.closest('[data-hck-mobile-toggle]');
      if (!burger) return;

      e.preventDefault();
      var nav = $('.hck-nav');
      if (!nav) return;

      var open = nav.classList.toggle('is-mobile-open');
      nav.style.display = open ? 'flex' : '';
      if (open) {
        nav.style.position = 'absolute';
        nav.style.top = '100%';
        nav.style.left = '0';
        nav.style.right = '0';
        nav.style.background = 'var(--hck-bg)';
        nav.style.flexDirection = 'column';
        nav.style.padding = '14px 20px';
        nav.style.borderBottom = '1px solid var(--hck-border)';
        nav.style.zIndex = '99';
      }
    });
  }

  /* ------------------------------------------------------------------
   * Category menu (stacked list + side flyout / inline accordion)
   * ---------------------------------------------------------------- */
  function bindCategoryMenus() {
    function closeAll(except) {
      $$('.hck-cats.is-open').forEach(function (wrap) {
        if (wrap !== except) {
          wrap.classList.remove('is-open');
          var btn = wrap.querySelector('[data-hck-cats-toggle]');
          if (btn) {
            btn.setAttribute('aria-expanded', 'false');
          }
        }
      });
    }

    function closeItems(list, except) {
      $$('.hck-cats__item.is-open', list).forEach(function (item) {
        if (item !== except) {
          item.classList.remove('is-open');
          var more = item.querySelector('[data-hck-cats-more]');
          if (more) {
            more.setAttribute('aria-expanded', 'false');
          }
        }
      });
    }

    document.addEventListener('click', function (e) {
      var toggle = e.target.closest ? e.target.closest('[data-hck-cats-toggle]') : null;

      if (toggle) {
        e.preventDefault();
        var wrap = toggle.closest('.hck-cats');
        if (!wrap) {
          return;
        }
        var open = wrap.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
          closeAll(wrap);
        }
        return;
      }

      // Expand / collapse sub-categories of a row (accordion / mobile flyout).
      var more = e.target.closest ? e.target.closest('[data-hck-cats-more]') : null;

      if (more) {
        e.preventDefault();
        e.stopPropagation();
        var item = more.closest('.hck-cats__item');
        if (!item) {
          return;
        }
        var isOpen = item.classList.toggle('is-open');
        more.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        var list = item.parentElement;
        if (list && isOpen) {
          closeItems(list, item);
        }
        return;
      }

      if (!e.target.closest || !e.target.closest('.hck-cats')) {
        closeAll(null);
      }
    });

    // Desktop hover: highlight the row (and open the mega flyout via CSS).
    document.addEventListener('mouseover', function (e) {
      if (!e.target.closest) {
        return;
      }
      var item = e.target.closest('.hck-cats__item');
      if (!item || !item.parentElement) {
        return;
      }
      var list = item.parentElement;
      $$('.hck-cats__item', list).forEach(function (el) {
        if (el !== item) {
          el.classList.remove('is-hover');
        }
      });
      item.classList.add('is-hover');
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        closeAll(null);
      }
    });
  }

  /* ------------------------------------------------------------------
   * Init
   * ---------------------------------------------------------------- */
  function init() {
    if (window.jQuery) {
      bindAddToCart();
    }
    bindCartDrawer();
    bindQtySteppers();
    bindUserArea();
    bindAnimations();
    bindBurger();
    bindCategoryMenus();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
