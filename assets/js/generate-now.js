/**
 * Created by vagenas on 7/9/2015.
 */

jQuery(document).ready(function ($) {
    function updateLogMarkUp(newMarkUp) {
        var $logTab = $('a[data-title="Log"]');
        $($logTab.attr('href')).html(newMarkUp);
    }

    function updateInfoMarkUp(newMarkUp) {
        $('.info-panel').html(newMarkUp);
    }

    $('.gen-now-button').click(function (e) {
        e.preventDefault();

        var $button = $(this);

        var nonce = $('#nonce').val();
        var action = 'skz_generate_now';

        var data = {
            action: action,
            nonce: nonce
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                if ($button.hasClass('disabled')) {
                    return false;
                }
                WpmControls.startLoading($button, true);
            },
            complete: function () {
                WpmControls.endLoading($button, true);
            },
            success: function (responseJson) {
                if (!responseJson.hasOwnProperty('data')) {
                    return;
                }

                //noinspection JSUnresolvedVariable
                alert(responseJson.data.msg);

                //noinspection JSUnresolvedVariable
                updateLogMarkUp(responseJson.data.logMarkUp);
                //noinspection JSUnresolvedVariable
                updateInfoMarkUp(responseJson.data.infoMarkUp);
            },
            error: function (response) {
                if (!response.hasOwnProperty('data')) {
                    alert('Something went wrong, please contact the developer');
                    return;
                }

                //noinspection JSUnresolvedVariable
                alert(responseJson.data.msg);
            }
        });
    });

    $('.hide-log').click(function(){
        var scope = $(this).data('scope');
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $('.'+scope).slideUp('fast');
        }else{
            $(this).addClass('active');
            $('.'+scope).slideDown('fast');
        }
    });
});