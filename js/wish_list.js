(function ($){
    $(document).ready( function () {
        $('.brcs_slider').unslider({
        arrows: {
            prev: '<a class="unslider-arrow prev"></a>',
            next: '<a class="unslider-arrow next"></a>',
        },
        autoplay: true});
        var wish_list_ajax_block = false;
        var class_backup = '';
        $(document).on( 'click', '.br_ww_button', function( event ) {
            event.preventDefault();
            var $block = $(this);
            if ( ww_ajax_start($block) ) {
                var id = $(this).parents('.br_wish_wait_block').data('id');
                var type = $(this).data('type');
                var args;
                if ( type == 'wish' ) {
                    wish_list_execute_func ( the_wish_list_data.user_func.before_wish );
                    jQuery(document).trigger('berocket_wish_wait-before_wish');
                    args = {action: 'br_'+type+'_add', 'wish_id': id};
                } else if ( type == 'wait' ) {
                    wish_list_execute_func ( the_wish_list_data.user_func.before_wait );
                    jQuery(document).trigger('berocket_wish_wait-before_wait');
                    args = {action: 'br_'+type+'_add', 'wait_id': id};
                }
                $.post( the_wish_list_data.ajax_url, args, function (data) {
                    if ( data.operation == 'add' ) {
                        $('.br_wish_wait_block.br_wish_wait_'+id+' .br_'+type+'_button').addClass('br_ww_button_true');
                    } else {
                        $('.br_wish_wait_block.br_wish_wait_'+id+' .br_'+type+'_button').removeClass('br_ww_button_true');
                    }
                    ww_ajax_end($block);
                    if ( type == 'wish' ) {
                        wish_list_execute_func ( the_wish_list_data.user_func.after_wish );
                        jQuery(document).trigger('berocket_wish_wait-after_wish');
                    } else if ( type == 'wait' ) {
                        wish_list_execute_func ( the_wish_list_data.user_func.after_wait );
                        jQuery(document).trigger('berocket_wish_wait-after_wait');
                    }
                    if( typeof(data.emailrequest) != 'undefined' && data.emailrequest ) {
                        $('.brwwl_mailset_back').show();
                    }
                }, 'json');
            }
        });
        function ww_ajax_start($block) {
            if ( wish_list_ajax_block ) {
                return false;
            } else {
                class_backup = $block.find('.fa');
                $block.find('.fa').replaceWith(the_wish_list_data.icon_load);
                wish_list_ajax_block = true;
                return true;
            }
        }
        function ww_ajax_end($block) {
            $block.find('.fa').replaceWith(class_backup);
            wish_list_ajax_block = false;
        }
        $(document).on( 'click', '.berocket_ww_remove', function ( event ) {
            event.preventDefault();
            $block = $('<div></div>');
            if ( ww_ajax_start($block) ) {
                var id = $(this).parents('.berocket_ww_product').data('id');
                var type = $(this).parents('.berocket_ww_list').data('type');
                var args;
                if ( type == 'wish' ) {
                    args = {action: 'br_'+type+'_add', 'wish_id': id};
                } else if ( type == 'wait' ) {
                    args = {action: 'br_'+type+'_add', 'wait_id': id};
                }
                $.post( the_wish_list_data.ajax_url, args, function (data) {
                    location.reload();
                    ww_ajax_end($block);
                }, 'json');
            }
        });
        $(document).on( 'click', '.berocket_ww_show_all', function ( event ) {
            event.preventDefault();
            $(this).parents('.berocket_ww_container').trigger('brww_expand');
            $(this).hide();
        });
        $(document).on( 'brww_expand', '.berocket_ww_container', function() {
            var $scroll_block = $(this).find('.berocket_ww_list');
            $scroll_block.animate( {maxHeight: $scroll_block.find('.berocket_ww_ul_container').height() + 100}, 200 );
        });
        $('.colorpicker_field').each(function (i,o){
            $(o).css('backgroundColor', '#'+$(o).data('color'));
            $(o).colpick({
                layout: 'hex',
                submit: 0,
                color: '#'+$(o).data('color'),
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css('backgroundColor', '#'+hex).next().val(hex);
                }
            })
        });
        $(document).on('click', '.brwwl_mail_close', function() {
            $(this).parents('.brwwl_mailset_back').hide();
        });
        $(document).on('submit', '.brwwl_mailset_back form', function(event) {
            event.preventDefault();
            $(this).parents('.brwwl_mailset_back').hide();
            $.post( the_wish_list_data.ajax_url, {action:'brwwl_setmail', mail:$('.brwwl_mailset_back input').val()});
        });
    });
})(jQuery);
function wish_list_execute_func ( func ) {
    if( the_wish_list_data.user_func != 'undefined'
        && the_wish_list_data.user_func != null
        && typeof func != 'undefined' 
        && func.length > 0 ) {
        try{
            eval( func );
        } catch(err){
            alert('You have some incorrect JavaScript code (Wish/Wait List)');
        }
    }
}
