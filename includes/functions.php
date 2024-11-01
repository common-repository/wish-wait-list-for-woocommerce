<?php
function generate_ww_list ( $terms ) {
    $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
    $options = $BeRocket_Wish_List->get_option();
    if( empty($options["style_settings"]["wcshortcode_use"]) ) {
        echo '<ul class="berocket_ww_products">';
        foreach ( $terms as $term ) {
            $elements = array();
            $elements['open_li'] = '<li class="berocket_ww_product" data-id="'.$term['id'].'">';
            $elements['remove_link'] = '<a class="berocket_ww_remove" href="#remove"><i class="fa fa-times"></i></a>';
            $elements['open_a'] = '<a href="'.$term['link'].'">';
            $elements['header'] = '<h3 class="berocket_ww_title">'.$term['title'].'</h3>';
            $elements['image'] = $term['image'];
            $elements['close_a'] = '</a>';
            $class_stock = '';
            $text_stock = '';
            if ( $term['availability'] && ! empty( $term['availability']['availability'] ) ) {
                $product = wc_get_product($term['id']);
                $availability_html = empty( $term['availability']['availability'] ) ? '' : '<p class="stock ' . esc_attr( $term['availability']['class'] ) . '">' . esc_html( $term['availability']['availability'] ) . '</p>';
                $text_stock = apply_filters( 'woocommerce_stock_html', $availability_html, $term['availability']['availability'], $product );
                $class_stock = esc_attr( $term['availability']['class'] );
            } else {
                if ( $term['is_in_stock'] ) {
                    $text_stock = __( 'In stock', 'woocommerce' );
                    $class_stock = 'in-stock';
                } else {
                    $text_stock = __( 'Out of stock', 'woocommerce' );
                    $class_stock = 'out-of-stock';
                }
                $text_stock = '<p class="stock '.$class_stock.'">'.$text_stock.'</p>';
            }
            $elements['text_stock'] = apply_filters( 'berocket_wish_in_stock_status', $text_stock, $term['is_in_stock'] );
            $elements['text_price'] = '<p class="berocket_ww_price price">'.$term['price'].'</p>';
            $elements['close_li'] =  '</li>';
            $echo_elements = implode(apply_filters('berocket_wishwait_list_elements', $elements, $term, $terms));
            echo $echo_elements;
        }
        echo '</ul>';
    } else {
        $products = array();
        foreach ( $terms as $term ) {
            $products[] = intval($term['id']);
        }
        echo do_shortcode('[products ids="' . implode(',', $products) . '"]');
    }
}
