/**
 * Hooshyar Commerce Kit — widget behaviours.
 *
 * Vanilla carousel (RTL-aware), banner effects (parallax, tilt,
 * mask-reveal) and responsive slide counts.
 */
(function () {
  'use strict';

  function $$(selector, scope) {
    return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
  }

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /* ------------------------------------------------------------------
   * Carousel
   * ---------------------------------------------------------------- */
  function Carousel(root) {
    this.root = root;
    this.track = root.querySelector('.hck-products');
    if (!this.track) return;

    this.cards = $$('.hck-card', this.track);
    if (this.cards.length < 2) return;

    this.index = 0;
    this.autoplay = root.getAttribute('data-autoplay') === 'yes';
    this.speed = parseInt(root.getAttribute('data-speed'), 10) || 4500;
    this.loop = root.getAttribute('data-loop') !== 'no';
    this.nav = root.getAttribute('data-nav') || 'arrows-dots';
    this.columns = {
      desktop: parseInt(root.getAttribute('data-columns'), 10) || 4,
      tablet: parseInt(root.getAttribute('data-columns-tablet'), 10) || 3,
      mobile: parseInt(root.getAttribute('data-columns-mobile'), 10) || 1
    };

    this.prevBtn = root.querySelector('.hck-carousel__arrow--prev');
    this.nextBtn = root.querySelector('.hck-carousel__arrow--next');
    this.dotsWrap = root.querySelector('.hck-carousel__dots');

    this.bindEvents();
    this.buildDots();
    this.buildProgress();
    this.update();
    this.startAutoplay();
  }

  Carousel.prototype.getVisible = function () {
    var w = window.innerWidth;
    if (w <= 480) return this.columns.mobile;
    if (w <= 1024) return this.columns.tablet;
    return this.columns.desktop;
  };

  Carousel.prototype.getMaxIndex = function () {
    return Math.max(0, this.cards.length - this.getVisible());
  };

  Carousel.prototype.isRtl = function () {
    return document.documentElement.getAttribute('dir') === 'rtl' || document.body.classList.contains('rtl');
  };

  Carousel.prototype.update = function () {
    var visible = this.getVisible();
    var gap = 24;

    if (this.cards[0]) {
      var cardWidth = (this.track.clientWidth - gap * (visible - 1)) / visible;
      this.cards.forEach(function (card) {
        card.style.flex = '0 0 ' + cardWidth + 'px';
      });
    }

    var cardW = this.cards[0] ? this.cards[0].offsetWidth + gap : 0;
    var offset = this.index * cardW;

    this.track.style.transform = 'translateX(' + (this.isRtl() ? offset : -offset) + 'px)';

    // Dots.
    if (this.dotsWrap) {
      $$('button', this.dotsWrap).forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === this.index);
      }, this);
    }

    // Progress.
    if (this.progressFill) {
      var pct = ((this.index + visible) / this.cards.length) * 100;
      this.progressFill.style.width = Math.min(100, pct) + '%';
    }

    // Arrow visibility for non-loop mode.
    if (!this.loop) {
      if (this.prevBtn) this.prevBtn.style.visibility = this.index <= 0 ? 'hidden' : 'visible';
      if (this.nextBtn) this.nextBtn.style.visibility = this.index >= this.getMaxIndex() ? 'hidden' : 'visible';
    }
  };

  Carousel.prototype.goTo = function (index) {
    var max = this.getMaxIndex();
    if (this.loop) {
      if (index < 0) index = max;
      if (index > max) index = 0;
    } else {
      index = Math.max(0, Math.min(max, index));
    }
    this.index = index;
    this.update();
  };

  Carousel.prototype.buildDots = function () {
    if (!this.dotsWrap || this.nav === 'arrows' || this.nav === 'none' || this.nav === 'progress') {
      if (this.dotsWrap) this.dotsWrap.innerHTML = '';
      return;
    }

    var self = this;
    this.dotsWrap.innerHTML = '';

    for (var i = 0; i <= this.getMaxIndex(); i++) {
      (function (i) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'hck-carousel__dot';
        btn.setAttribute('aria-label', 'Slide ' + (i + 1));
        btn.addEventListener('click', function () {
          self.goTo(i);
          self.restartAutoplay();
        });
        self.dotsWrap.appendChild(btn);
      })(i);
    }
  };

  Carousel.prototype.buildProgress = function () {
    if (this.nav !== 'progress') return;

    var bar = document.createElement('div');
    bar.className = 'hck-carousel__progress';
    this.progressFill = document.createElement('div');
    this.progressFill.className = 'hck-carousel__progress-bar';
    bar.appendChild(this.progressFill);
    this.root.appendChild(bar);
  };

  Carousel.prototype.bindEvents = function () {
    var self = this;

    if (this.prevBtn && this.nav !== 'dots' && this.nav !== 'progress' && this.nav !== 'none') {
      this.prevBtn.addEventListener('click', function () {
        self.goTo(self.index - 1);
        self.restartAutoplay();
      });
    } else if (this.prevBtn) {
      this.prevBtn.style.display = 'none';
    }

    if (this.nextBtn && this.nav !== 'dots' && this.nav !== 'progress' && this.nav !== 'none') {
      this.nextBtn.addEventListener('click', function () {
        self.goTo(self.index + 1);
        self.restartAutoplay();
      });
    } else if (this.nextBtn) {
      this.nextBtn.style.display = 'none';
    }

    // Touch / drag support.
    var startX = 0;
    var dragging = false;

    this.track.addEventListener(
      'touchstart',
      function (e) {
        startX = e.touches[0].clientX;
        dragging = true;
        self.stopAutoplay();
      },
      { passive: true }
    );

    this.track.addEventListener(
      'touchend',
      function (e) {
        if (!dragging) return;
        dragging = false;
        var delta = e.changedTouches[0].clientX - startX;
        if (Math.abs(delta) > 42) {
          self.goTo(self.index + (delta > 0 ? -1 : 1));
        }
        self.startAutoplay();
      },
      { passive: true }
    );

    window.addEventListener('resize', function () {
      clearTimeout(self._resizeTimer);
      self._resizeTimer = setTimeout(function () {
        self.buildDots();
        self.update();
      }, 160);
    });

    // Pause on hover.
    this.root.addEventListener('mouseenter', function () {
      self.stopAutoplay();
    });
    this.root.addEventListener('mouseleave', function () {
      self.startAutoplay();
    });
  };

  Carousel.prototype.startAutoplay = function () {
    if (!this.autoplay || prefersReducedMotion()) return;
    var self = this;
    this.stopAutoplay();
    this._timer = setInterval(function () {
      self.goTo(self.index + 1);
    }, this.speed);
  };

  Carousel.prototype.stopAutoplay = function () {
    clearInterval(this._timer);
  };

  Carousel.prototype.restartAutoplay = function () {
    this.stopAutoplay();
    this.startAutoplay();
  };

  /* ------------------------------------------------------------------
   * Banner effects
   * ---------------------------------------------------------------- */
  function bindParallax() {
    if (prefersReducedMotion()) return;

    var layers = $$('.hck-banner--fx-parallax .hck-banner__image, .hck-pbanner--fx-parallax .hck-pbanner__image img');

    if (!layers.length) return;

    var ticking = false;

    function update() {
      layers.forEach(function (layer) {
        var rect = layer.getBoundingClientRect();
        var viewH = window.innerHeight;
        if (rect.bottom < 0 || rect.top > viewH) return;

        var progress = (rect.top + rect.height / 2 - viewH / 2) / viewH;
        var shift = progress * -36;
        layer.style.transform = 'translateY(' + shift + 'px)';
      });
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(update);
      }
    }, { passive: true });

    update();
  }

  function bindTilt() {
    if (prefersReducedMotion()) return;

    $$('.hck-banner--fx-tilt, .hck-pbanner--fx-tilt').forEach(function (el) {
      el.addEventListener('mousemove', function (e) {
        var rect = el.getBoundingClientRect();
        var x = (e.clientX - rect.left) / rect.width - 0.5;
        var y = (e.clientY - rect.top) / rect.height - 0.5;

        var media = el.querySelector('.hck-banner__media, .hck-pbanner__image');
        if (media) {
          media.style.transform =
            'perspective(1000px) rotateX(' + (-y * 7) + 'deg) rotateY(' + (x * 7) + 'deg)';
        }
      });

      el.addEventListener('mouseleave', function () {
        var media = el.querySelector('.hck-banner__media, .hck-pbanner__image');
        if (media) {
          media.style.transform = '';
        }
      });
    });
  }

  /* ------------------------------------------------------------------
   * Init
   * ---------------------------------------------------------------- */
  function init() {
    $$('[data-hck-carousel]').forEach(function (el) {
      // eslint-disable-next-line no-new
      new Carousel(el);
    });

    bindParallax();
    bindTilt();

    // Re-init carousels rendered by Elementor / fragments.
    if (window.jQuery) {
      window.jQuery(document.body).on('hck:reinit-widgets', function () {
        $$('[data-hck-carousel]').forEach(function (el) {
          if (!el.__hckCarousel) {
            el.__hckCarousel = new Carousel(el);
          }
        });
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Elementor frontend hook.
  window.addEventListener('elementor/frontend/init', function () {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
        init();
      });
    }
  });
})();
