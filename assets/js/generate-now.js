/**
 * Created by vagenas on 7/9/2015.
 */

jQuery(document).ready(function ($) {
    $('#genarate-now').click(function (e) {
        e.preventDefault();

        var $button = $(this);

        if ($button.hasClass('disabled')) {
            return false;
        }

        $button.find('i').removeClass('hidden');
        $button.addClass('disabled');

        var data = {
            'action': 'skz_generate_now',
            'nonce': skz.ajax_nonce
        };

        $.post(ajaxurl, data, function ($response) {
            $button.find('i').addClass('hidden');
            $button.removeClass('disabled');

            alert($response.msg);
        }, 'json');
    })
});