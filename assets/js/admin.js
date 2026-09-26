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
  });
})(jQuery);
