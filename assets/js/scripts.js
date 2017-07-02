jQuery( document ).ready(
    function ( $ ) {
        $( "#general_options" ).find( "select[multiple]" ).select2({allowClear: true, placeholder: "Search"});
        $( "#map_options" ).find( "select[multiple]" ).select2({allowClear: true, placeholder: "Search"});

        function updateLogMarkUp( newMarkUp ) {
            var $logTab = $( '#log-container' );
            $logTab.parent().html( newMarkUp );
        }

        function updateInfoMarkUp( newMarkUp ) {
            $( '.info-panel' ).html( newMarkUp );
        }

        $( '.gen-now-button' ).click(
            function ( e ) {
                e.preventDefault();

                var $button = $( this );

                var nonce  = $( '#skz-gen-now-action' ).val();
                var action = 'skz-gen-now-action';

                var data = {
                    action: action,
                    nonce: nonce
                };

                $.ajax(
                    {
                        url: ajaxurl,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            if ( $button.hasClass( 'disabled' ) ) {
                                return false;
                            }
                            $button.find('i.fa').fadeIn();
                            $button.addClass('disabled');
                        },
                        complete: function () {
                            $button.find('i.fa').fadeOut();
                            $button.removeClass('disabled');
                        },
                        success: function ( responseJson ) {
                            if ( ! responseJson.hasOwnProperty( 'data' ) ) {
                                return;
                            }

                            //noinspection JSUnresolvedVariable
                            alert( responseJson.data.msg );

                            //noinspection JSUnresolvedVariable
                            updateLogMarkUp( responseJson.data.logMarkUp );
                            //noinspection JSUnresolvedVariable
                            updateInfoMarkUp( responseJson.data.infoMarkUp );
                            bindHideButtons();
                        },
                        error: function ( response ) {
                            if ( ! response.hasOwnProperty( 'data' ) ) {
                                alert( 'Something went wrong, please contact the developer' );
                                return;
                            }

                            //noinspection JSUnresolvedVariable
                            alert( responseJson.data.msg );
                        }
                    }
                );
            }
        );

        function bindHideButtons() {
            $( '.hide-log' ).unbind( 'click' ).click(
                function () {
                    var scope = $( this ).data( 'scope' );
                    if ( $( this ).hasClass( 'active' ) ) {
                        $( this ).removeClass( 'active' );
                        $( '.' + scope ).slideUp( 'fast' );
                    } else {
                        $( this ).addClass( 'active' );
                        $( '.' + scope ).slideDown( 'fast' );
                    }
                }
            );
        }

        bindHideButtons();

        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
        postboxes.add_postbox_toggles( SKZ.pageHookSuffix );
        $('#fx-smb-form').submit( function(){
            $('#publishing-action').find('.spinner').css('display','inline');
        });
        $('#delete-action').find('.submitdelete').on('click', function() {
            return confirm(/*'Are you sure want to do this?'*/'Sorry, not yet implemented');
        });
    }
);
