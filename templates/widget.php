<?php 
if( (int)$product_count < 1 || (int)$product_count > 20 ) {
    $product_count = 4;
}
if( $product_type == 'rel_wait' || $product_type == 'rel_wish' ) {
    $user_id = get_current_user_id();
    if( $product_type == 'rel_wait' ) {
        $current_wait = get_user_meta($user_id, 'berocket_wait', true);
    } else {
        $current_wait = get_user_meta($user_id, 'berocket_wish', true);
    }
    $related = array();
    if ( isset( $current_wait ) && is_array( $current_wait ) && count( $current_wait ) > 0 ) {
        foreach( $current_wait as $wait_id ) {
            $product = new WC_Product($wait_id);
            $related = array_merge($related, $product->get_related( 2 ));
        }
        $related = array_unique($related);
    }
    $args = apply_filters( 'woocommerce_related_products_args', array(
        'post_type'            => 'product',
        'posts_per_page'       => $product_count,
        'orderby'              => 'rand',
        'post__in'             => $related,
        'post__not_in'         => $current_wait
    ) );
    $loop = new WP_Query( $args );
} elseif( $product_type == 'top_wait' || $product_type == 'top_wish' ) {
    if( $product_type == 'top_wait' ) {
        $meta_key = 'wait_users_count';
    } else {
        $meta_key = 'wish_users_count';
    }
    $loop = new WP_Query(array(
        'post_type'			=> 'product',
        'posts_per_page'	=> $product_count,
        'meta_key'			=> $meta_key,
        'orderby'			=> 'meta_value_num',
        'order'				=> 'DESC'
    ));
} elseif( $product_type == 'instock_wait' ) {
    $user_id = get_current_user_id();
    $current_wait = get_user_meta($user_id, 'berocket_wait', true);
    if( $user_id != 0 && isset( $current_wait ) && is_array( $current_wait ) && count( $current_wait ) > 0 ) {
        $loop = new WP_Query(array(
            'post_type'			=> 'product',
            'posts_per_page'	=> $product_count,
            'meta_key'			=> $meta_key,
            'orderby'           => 'rand',
            'post__in'          => $current_wait,
            'meta_query'        => array(
                array(
                    'key'       => '_stock_status',
                    'value'     => 'instock',
                    'operator'  => 'IN'
                ),
            ),
        ));
    } else {
        $loop = false;
    }
} elseif( $product_type == 'onsale_wish' ) {
    $user_id = get_current_user_id();
    $current_wait = get_user_meta($user_id, 'berocket_wish', true);
    $loop = false;
    if( $user_id != 0 && isset( $current_wait ) && is_array($current_wait) ) {
        $onsale = wc_get_product_ids_on_sale();
        $current_wait = array_intersect($current_wait, $onsale);
        if( count( $current_wait ) > 0 ) {
            $loop = new WP_Query(array(
                'post_type'			=> 'product',
                'posts_per_page'	=> $product_count,
                'meta_key'			=> $meta_key,
                'orderby'           => 'rand',
                'post__in'          => $current_wait,
            ));
        }
    }
}
if( is_object($loop) && $loop->post_count == 0 ) {
    $loop= false;
}
if($loop === false) {
    return false;
}
$brcs_slider_rand = rand();
$slider_count_max = 3;
if( isset($slider_count) ) {
    $slider_count--;
    $slider_count_max = $slider_count;
}
echo '<style>.br_products_ww.br_products_ww_', $brcs_slider_rand, ' .brcs_product{width:'.(100 / ($slider_count+1)).'%!important;float: left;}</style>';
?>
<div class="woocommerce br_products_ww br_products_ww_<?php echo $brcs_slider_rand; ?>">
<?php
if ($display_type === false || $display_type == 'default' ) {
    if( $display_type === false ) {
        echo '<h2>', $options['suggestions_title'], '</h2>';
    }
    echo '<style>.brcs_products > * {display: inline-block;float:left;position:relative;}</style>';
    $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
    add_filter ( 'post_class', array( $BeRocket_Wish_List, 'product_class' ), 9999, 3 );
    echo '<ul class="brcs_products">';
    $x = 0;

    if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product, $post;
        $product = wc_get_product(get_the_ID());
        $post = get_post( get_the_ID() );
        if ( !$product->is_visible() ) continue;
        if( function_exists('wc_get_template') ) {
            wc_get_template('content-product.php', array('product' => $product));
        } else {
            woocommerce_get_template('content-product.php', array('product' => $product));
        }
    endwhile; endif;
    echo '</ul>';
    remove_filter ( 'post_class', array( $BeRocket_Wish_List, 'product_class' ), 9999, 3 );
} elseif( $display_type == 'image' || $display_type == 'image_title' || $display_type == 'image_title_price' ) {
    ?>
    <ul class="brcs_image">
    <?php
        if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product;
            $product = wc_get_product(get_the_ID($product));
            $product_id = br_wc_get_product_id($product);
            if ( !$product->is_visible() ) continue;
            echo '<li class="brcs_product"><a href="', get_permalink($product_id), '">', woocommerce_get_product_thumbnail(), ($display_type == 'image_title' ? $product->get_title() : ($display_type == 'image_title_price' ? $product->get_title().' - '.( function_exists('wc_price') ? wc_price( $product->get_price() ) : woocommerce_price( $product->get_price() ) ) : '')), '</a>';
            if ( $add_to_cart ) {
                woocommerce_template_loop_add_to_cart();
            }
            echo '</li>';
        endwhile; endif;
    ?>
    </ul>
    <?php
} elseif( $display_type == 'title' || $display_type == 'title_price' ) {
    ?>
    <ul class="brcs_name">
    <?php
        if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product;
            $product = wc_get_product(get_the_ID());
            $product_id = br_wc_get_product_id($product);
            if ( !$product->is_visible() ) continue;
            echo '<li class="brcs_product"><a href="', get_permalink($product_id), '">', ($display_type == 'title' ? $product->get_title() : ($display_type == 'title_price' ? $product->get_title().' - '.( function_exists('wc_price') ? wc_price( $product->get_price() ) : woocommerce_price( $product->get_price() ) ) : '')), '</a>';
            if ( $add_to_cart ) {
                woocommerce_template_loop_add_to_cart();
            }
            echo '</li>';
        endwhile; endif;
    ?>
    </ul>
    <?php
} elseif( $display_type == 'slider' || $display_type == 'slider_title' ) {
    ?>
    <div class="brcs_slider brcs_slider_<?php echo $brcs_slider_rand; ?>">
        <ul>
    <?php
        $slide_count = 0;
        if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product;
            $product = wc_get_product(get_the_ID());
            $product_id = br_wc_get_product_id($product);
            if ( !$product->is_visible() ) continue;
            if( $slide_count == 0 ) {
                echo '<li class="brcs_slide">';
            }
            echo '<div class="brcs_product"><a class="br_ww_links" href="', get_permalink($product_id), '">', woocommerce_get_product_thumbnail(), ($display_type == 'slider_title' ? $product->get_title() : ''), '</a>';
            if ( $add_to_cart ) {
                woocommerce_template_loop_add_to_cart();
            }
            echo '</div>';
            if( $slide_count == $slider_count_max ) {
                echo '</li>';
                $slide_count = -1;
            }
            $slide_count++;
        endwhile; 
        if( $slide_count != 0 ) {
            echo '</li>';
        }
        endif;
    ?>
        </ul>
    </div>
    <?php
}
?>
<div style="clear:both; height:1px;"></div>
</div>
<?php
wp_reset_query();
 ?>
