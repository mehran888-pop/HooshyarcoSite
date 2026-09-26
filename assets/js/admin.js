/**
 * Hooshyar Commerce Kit — admin settings.
 */
(function ($) {
  'use strict';

  $(function () {
    // Colour pickers.
    if ($.fn.wpColorPicker) {
      $('.hck-color-picker').wpColorPicker();
    }

    var data = window.hckAdmin || {};

    function runTest(button, action, extra) {
      var resultBox = button.siblings('.hck-test-result');
      if (!resultBox.length) {
        resultBox = button.closest('.hck-test-panel').find('.hck-test-result');
      }

      resultBox
        .addClass('is-visible')
        .removeClass('is-success is-error')
        .text((data.i18n && data.i18n.testSending) || 'Sending…');

      button.prop('disabled', true);

      $.post(
        data.ajaxUrl,
        $.extend(
          {
            action: action,
            nonce: data.nonce
          },
          extra || {}
        )
      )
        .done(function (res) {
          button.prop('disabled', false);
          if (res && res.success) {
            resultBox.addClass('is-success').text(JSON.stringify(res.data, null, 2));
          } else {
            resultBox.addClass('is-error').text((res && res.data) || 'Error');
          }
        })
        .fail(function (xhr) {
          button.prop('disabled', false);
          resultBox.addClass('is-error').text(xhr.statusText || 'Request failed');
        });
    }

    // Telegram / Bale test message.
    $(document).on('click', '.hck-test-message', function () {
      runTest($(this), 'hck_test_message', {
        channel: $(this).data('channel')
      });
    });

    // DigiPay connection test.
    $(document).on('click', '.hck-test-digipay', function () {
      runTest($(this), 'hck_test_digipay');
    });

    /* ----------------------------------------------------------------
     * Custom fonts repeater (add / remove rows)
     * ------------------------------------------------------------- */
    function nextFontIndex() {
      var max = -1;
      $('input[name^="hck_settings[custom_fonts]["]').each(function () {
        var m = this.name.match(/\[custom_fonts\]\[(\d+)\]/);
        if (m) {
          max = Math.max(max, parseInt(m[1], 10));
        }
      });
      return max + 1;
    }

    $(document).on('click', '.hck-add-font-row', function (e) {
      e.preventDefault();
      var tplEl = document.querySelector('template.hck-font-row-tpl');
      var tmpl = tplEl ? tplEl.innerHTML : '';
      if (!tmpl) {
        return;
      }
      var html = tmpl.replace(/__i__/g, String(nextFontIndex()));
      $('.hck-font-rows').append(html);
    });

    $(document).on('click', '.hck-remove-font-row', function (e) {
      e.preventDefault();
      $(this).closest('.hck-font-row').fadeOut(150, function () {
        $(this).remove();
      });
    });

    /* ----------------------------------------------------------------
     * Custom fonts — WordPress media uploader
     * ------------------------------------------------------------- */
    $(document).on('click', '.hck-font-upload', function (e) {
      e.preventDefault();
      var button = $(this);
      var input = button.siblings('input.hck-font-url');
      if (!input.length) {
        input = button.closest('td').find('input.hck-font-url');
      }

      if (!window.wp || !wp.media) {
        window.alert(hckAdmin.i18n.mediaUnavailable || 'Media library is not available.');
        return;
      }

      var frame = wp.media({
        title: 'Select / upload font file',
        button: { text: 'Use this file' },
        multiple: false,
        library: { type: ['font/woff', 'font/woff2', 'font/ttf', 'font/otf', 'application/font-woff', 'application/x-font-woff', 'application/octet-stream'] }
      });

      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        if (attachment && attachment.url && input.length) {
          input.val(attachment.url).trigger('change');
        }
      });

      frame.open();
    });
  });
})(jQuery);
