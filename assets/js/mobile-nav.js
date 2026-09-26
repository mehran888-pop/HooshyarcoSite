/**
 * Hooshyar Commerce Kit — mobile navigation.
 */
(function () {
  'use strict';

  function init() {
    // Search overlay toggle.
    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('[data-hck-search-toggle]');
      if (!toggle) return;

      e.preventDefault();

      var overlay = document.getElementById('hck-mnav-search');
      if (!overlay) return;

      var isHidden = overlay.hasAttribute('hidden');
      if (isHidden) {
        overlay.removeAttribute('hidden');
        var input = overlay.querySelector('input[type="search"]');
        if (input) input.focus();
      } else {
        overlay.setAttribute('hidden', '');
      }
    });

    // Close the overlay on Escape.
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      var overlay = document.getElementById('hck-mnav-search');
      if (overlay && !overlay.hasAttribute('hidden')) {
        overlay.setAttribute('hidden', '');
      }
    });

    // Hide the bar while an input is focused (avoids covering keyboards).
    document.addEventListener('focusin', function (e) {
      var bar = document.getElementById('hck-mobile-nav');
      if (!bar) return;
      if (e.target.matches('input, textarea, select')) {
        bar.style.opacity = '0';
        bar.style.pointerEvents = 'none';
      }
    });

    document.addEventListener('focusout', function () {
      var bar = document.getElementById('hck-mobile-nav');
      if (bar) {
        bar.style.opacity = '';
        bar.style.pointerEvents = '';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
