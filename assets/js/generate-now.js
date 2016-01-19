/**
 * Created by vagenas on 7/9/2015.
 */

jQuery(document).ready(function ($) {
    $('.gen-now-button').click(function (e) {
        e.preventDefault();

        var $button = $(this);

        var nonce = $('#nonce').val();
        var action = 'skz_generate_now';

        if ($button.hasClass('disabled')) {
            return false;
        }

        $button.find('i').removeClass('hidden');
        $button.addClass('disabled');

        var data = {
            action: action,
            nonce: nonce
        };

        $.post(ajaxurl, data, function ($response) {
            $button.find('i').addClass('hidden');
            $button.removeClass('disabled');

            alert($response.data.msg);

            if($response.data.included.hasOwnProperty('logMarkUp')){
                var $logTab = $('a[data-title="Log"]');
                $($logTab.attr('href')).html($response.data.included.logMarkUp);
            }

        }, 'json');
    })
});