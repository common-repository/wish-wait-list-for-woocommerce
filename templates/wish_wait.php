<script>
jQuery('.berocket_ww_container').each( function( i, o ) {
    if ( jQuery(o).height() + 20 < jQuery(o).find('.berocket_ww_ul_container').height() && jQuery(o).find('.berocket_ww_show_all').length ) {
        jQuery(o).find('.berocket_ww_show_all').show();
    } else if( ! jQuery(o).find('.berocket_ww_show_all').length ) {
        var $scroll_block = jQuery(o).find('.berocket_ww_list');
        $scroll_block.css( {maxHeight: 'initial'} );
    }
});
</script>
