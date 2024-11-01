<?php
if( ! class_exists('BeRocket_Wish_List_compat_product_table') ) {
    class Product_Table_Data_BeRocket_Wait extends Abstract_Product_Table_Data {
        public function get_data() {
            ob_start();
            $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
            $BeRocket_Wish_List->get_wish_button('wait');
            return ob_get_clean();
        }
    }
    class Product_Table_Data_BeRocket_Wish extends Abstract_Product_Table_Data {
        public function get_data() {
            ob_start();
            $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
            $BeRocket_Wish_List->get_wish_button('wish');
            return ob_get_clean();
        }
    }
    class Product_Table_Data_BeRocket_WW extends Abstract_Product_Table_Data {
        public function get_data() {
            ob_start();
            $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
            $BeRocket_Wish_List->get_wish_button('ww');
            return ob_get_clean();
        }
    }
    class BeRocket_Wish_List_compat_product_table {
        function __construct() {
            add_filter('wc_product_table_custom_table_data_br_wait', array($this, 'wait'), 10, 3);
            add_filter('wc_product_table_custom_table_data_br_wish', array($this, 'wish'), 10, 3);
            add_filter('wc_product_table_custom_table_data_br_ww', array($this, 'ww'), 10, 3);
        }
        function wait($data, $product, $args) {
            return new Product_Table_Data_BeRocket_Wait($product);
        }
        function wish($data, $product, $args) {
            return new Product_Table_Data_BeRocket_Wish($product);
        }
        function ww($data, $product, $args) {
            return new Product_Table_Data_BeRocket_WW($product);
        }
    }
    new BeRocket_Wish_List_compat_product_table();
}
