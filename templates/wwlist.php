<?php
$terms_list = array();
if ( isset( $br_current_list ) && is_array( $br_current_list ) && count( $br_current_list ) > 0 ) {
    foreach ( $br_current_list as $product => $i ) {        
        $term = array();
        $current_language= apply_filters( 'wpml_current_language', NULL );
        $product = apply_filters( 'wpml_object_id', $product, 'product', true, $current_language );
        $post_get = wc_get_product($product);
        if( ! empty($post_get) && is_object($post_get) && is_a($post_get, 'WC_Product') ) {
            $term['id'] = $product;
            $term['title'] = $post_get->get_title();
            $term['image'] = $post_get->get_image();
            $term['price'] = $post_get->get_price_html();
            $term['link'] = $post_get->get_permalink();
            $term['availability'] = $post_get->get_availability();
            $term['is_in_stock'] = $post_get->is_in_stock();
            $terms_list[] = $term;
        }
    }
do_action ( 'berocket_before_'.$list_name.'_list' );
if( ! empty($brargs['header']) ) { ?>
<h2 class="berocket_ww_list_title"><?php echo $text_options[$list_name.'_list']; ?></h2>
<?php } ?>
<div class="berocket_ww_container">
    <div class="berocket_ww_list berocket_<?php echo $list_name; ?>_list" data-type="<?php echo $list_name; ?>">
        <div class="berocket_ww_ul_container">
            <?php
            generate_ww_list ( $terms_list );
            ?>
            <div style="clear:both;"></div>
        </div>
    </div>
    <?php if( empty($brargs['expand']) ) { ?>
    <span class="berocket_ww_show_all" style="display: none;"><?php echo $text_options['show_'.$list_name]; ?></span>
    <?php } ?>
</div>
<?php do_action ( 'berocket_after_'.$list_name.'_list' );
} elseif( ! empty($text_options[$list_name.'list_empty']) ) {
    ?>
<div class="berocket_ww_container">
    <div class="berocket_ww_empty">
        <p><?php  echo $text_options[$list_name.'list_empty']; ?></p>
    </div>
</div>
<?php
}
?>
