<?php
class BeRocket_Wish_List_compat_product_preview {
    function __construct() {
        add_filter( 'br_product_preview_positions_elements', array(__CLASS__, 'br_product_preview_positions_elements') );
        add_action( 'br_build_preview_berocket_wish', array(__CLASS__, 'br_build_preview_wish') );
        add_action( 'br_build_preview_berocket_wait', array(__CLASS__, 'br_build_preview_wait') );
        add_action( 'br_build_preview_berocket_wish_wait', array(__CLASS__, 'br_build_preview_ww') );
    }
    public static function br_product_preview_positions_elements($elements) {
        $elements['berocket_wish'] = __( '<strong>BeRocket</strong> Wish Button', 'wish-wait-list-for-woocommerce' );
        $elements['berocket_wait'] = __( '<strong>BeRocket</strong> Wait Button', 'wish-wait-list-for-woocommerce' );
        $elements['berocket_wish_wait'] = __( '<strong>BeRocket</strong> Wish/Wait Button', 'wish-wait-list-for-woocommerce' );
        return $elements;
    }
    public static function br_build_preview_wish() {
        echo '<div>';
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $BeRocket_Wish_List->get_wish_button_wish();
        echo '</div>';
    }
    public static function br_build_preview_wait() {
        echo '<div>';
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $BeRocket_Wish_List->get_wish_button_wait();
        echo '</div>';
    }
    public static function br_build_preview_ww() {
        echo '<div>';
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $BeRocket_Wish_List->get_wish_button_ww();
        echo '</div>';
    }
}
new BeRocket_Wish_List_compat_product_preview();
