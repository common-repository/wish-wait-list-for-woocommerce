<?php
define( "BeRocket_Wish_List_domain", 'wish-wait-list-for-woocommerce'); 
define( "wish_list_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('wish-wait-list-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'berocket/framework.php');
foreach (glob(__DIR__ . "/includes/*.php") as $filename)
{
    include_once($filename);
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once("includes/compatibility/product_preview.php");

class BeRocket_Wish_List extends BeRocket_Framework {
    public static $settings_name = 'br-wish_list-options';
    protected $plugin_version_capability = 15;
    protected static $instance;
    protected $disable_settings_for_admin = array(
        array('javascript_settings', 'before_wish'),
        array('javascript_settings', 'after_wish'),
        array('javascript_settings', 'before_wait'),
        array('javascript_settings', 'after_wait'),
        array('text_settings', 'instock_message'),
        array('instock_email'),
    );
    protected $check_init_array = array(
        array(
            'check' => 'woocommerce_version',
            'data' => array(
                'version' => '3.0',
                'operator' => '>=',
                'notice'   => 'Plugin Wishlist and Waitlist for WooCommerce required WooCommerce version 3.0 or higher'
            )
        ),
        array(
            'check' => 'framework_version',
            'data' => array(
                'version' => '2.1',
                'operator' => '>=',
                'notice'   => 'Please update all BeRocket plugins to the most recent version. Wishlist and Waitlist for WooCommerce is not working correctly with older versions.'
            )
        ),
    );
    function __construct () {
        $this->info = array(
            'id'          => 5,
            'lic_id'      => 77,
            'version'     => BeRocket_Wish_List_version,
            'plugin'      => '',
            'slug'        => '',
            'key'         => '',
            'name'        => '',
            'plugin_name' => 'wish_list',
            'full_name'   => __('Wishlist and Waitlist for WooCommerce', 'wish-wait-list-for-woocommerce'),
            'norm_name'   => __('Wish/Wait List', 'wish-wait-list-for-woocommerce'),
            'price'       => '',
            'domain'      => 'wish-wait-list-for-woocommerce',
            'templates'   => wish_list_TEMPLATE_PATH,
            'plugin_file' => BeRocket_Wish_List_file,
            'plugin_dir'  => __DIR__,
        );
        $this->defaults = array(
            'general_settings'     => array(
                'wish_page'                         => '',
                'wait_page'                         => '',
                'wish_list'                         => '',
                'wait_list'                         => '',
                'only_logged_in'                    => '',
                'cookie_use'                        => '',
            ),
            'style_settings'       => array(
                'icon_wish'                         => 'fa-heart-o',
                'icon_wait'                         => 'fa-clock-o',
                'icon_load'                         => 'fa-cog',
                'selected_back_color'               => 'FFFFFF',
            ),
            'text_settings'        => array(
                'wish_list'                         => 'Wish List',
                'wait_list'                         => 'Wait List',
                'show_wish'                         => 'Show all products from wish list',
                'show_wait'                         => 'Show all products from wait list',
                'instock_from_name'                 => '',
                'instock_from_email'                => '',
                'instock_subject'                   => '%product_title% is now available',
                'instock_message'                   => '<h3>%product_title% is now available</h3>
    <div style="width: 250px;">
        <a href="%product_link%" style="text-decoration:none;">
            <p style="font-size:24px;">%product_title%</p>
            %product_image%
            <p>%product_price%</p>
        </a>
    </div>',
                'guest_instock_subject'             => '%product_title% is now available',
                'guest_instock_message'             => '<h3>%product_title% is now available</h3>
    <div style="width: 250px;">
        <a href="%product_link%" style="text-decoration:none;">
            <p style="font-size:24px;">%product_title%</p>
            %product_image%
            <p>%product_price%</p>
        </a>
    </div>',
                'cookie_use_text'                   => 'This website collects cookies to deliver better user experience',
                'cookie_use_button'                 => 'Accept',
                'wishlist_empty'                    => 'No Wishlists yet!',
                'waitlist_empty'                    => 'No Waitlists yet!',
            ),
            'javascript_settings'  => array(
                'before_wish'                       => '',
                'before_wait'                       => '',
                'after_wish'                        => '',
                'after_wait'                        => '',
            ),
            'custom_css'        => '',
            'fontawesome_frontend_disable'    => '',
            'fontawesome_frontend_version'    => '',
        );
        $this->values = array(
            'settings_name' => 'br-wish_list-options',
            'option_page'   => 'br-wish_list',
            'premium_slug'  => 'woocommerce-wish-wait-list',
            'free_slug'     => 'wish-wait-list-for-woocommerce',
            'hpos_comp'     => true
        );
        $this->feature_list = array();
        $this->framework_data['fontawesome_frontend'] = true;
        parent::__construct( $this );
        if( $this->check_framework_version() ) {
            if ( $this->init_validation() ) {
                $options = $this->get_option();
                if( empty($options['text_settings']['instock_from_email']) || empty($options['text_settings']['instock_from_email']) ) {
                    if( empty($text_options['instock_from_name']) ) {
                        $options['text_settings']['instock_from_name'] = get_bloginfo('name');
                    }
                    if( empty($text_options['instock_from_email']) ) {
                        $options['text_settings']['instock_from_email'] = get_bloginfo('admin_email');
                    }
                    update_option($this->values['settings_name'], $options);
                }
                add_action ( "widgets_init", array( $this, 'widgets_init' ) );
                add_filter ( 'the_content', array( $this, 'wish_page' ) );
                add_action( "wp_ajax_br_wish_add", array ( $this, 'listener_wish_add' ) );
                add_action( "wp_ajax_br_wait_add", array ( $this, 'listener_wait_add' ) );
                add_action( "wp_ajax_nopriv_br_wish_add", array ( $this, 'listener_wish_add' ) );
                add_action( "wp_ajax_nopriv_br_wait_add", array ( $this, 'listener_wait_add' ) );
                add_action( "woocommerce_product_set_stock_status", array ( $this, 'send_mail' ), 10, 2 );
                add_action ( 'berocket_add_ww_buttons_actions', array($this, 'add_ww_buttons') );
                add_action ( 'berocket_remove_ww_buttons_actions', array($this, 'remove_ww_buttons') );
                add_filter ( 'berocket_wait_list_replace_product_variable', array($this, 'replace_product_variables_in_text'), 10, 2 );
                add_filter ( 'berocket_wait_list_replace_user_variable', array($this, 'replace_user_variables_in_text'), 10, 2 );
                add_shortcode( 'br_wishwait_list', array( $this, 'shortcode' ) );
                if( ! empty($options['general_settings']['cookie_use']) ) {
                    include_once 'includes/options/cookiemessage.php';
                }
                add_action( 'divi_extensions_init', array($this, 'divi_initialize_extension') );
            }
        } else {
            add_filter( 'berocket_display_additional_notices', array(
                $this,
                'old_framework_notice'
            ) );
        }
    }
    function init_validation() {
        return parent::init_validation() && $this->check_framework_version();
    }
    function check_framework_version() {
        return ( ! empty(BeRocket_Framework::$framework_version) && version_compare(BeRocket_Framework::$framework_version, 2.1, '>=') );
    }
    function old_framework_notice($notices) {
        $notices[] = array(
            'start'         => 0,
            'end'           => 0,
            'name'          => $this->info[ 'plugin_name' ].'_old_framework',
            'html'          => __('<strong>Please update all BeRocket plugins to the most recent version. Wishlist and Waitlist for WooCommerce is not working correctly with older versions.</strong>', 'wish-wait-list-for-woocommerce'),
            'righthtml'     => '',
            'rightwidth'    => 0,
            'nothankswidth' => 0,
            'contentwidth'  => 1600,
            'subscribe'     => false,
            'priority'      => 10,
            'height'        => 50,
            'repeat'        => false,
            'repeatcount'   => 1,
            'image'         => array(
                'local'  => '',
                'width'  => 0,
                'height' => 0,
                'scale'  => 1,
            )
        );
        return $notices;
    }
    public function plugins_loaded () {
        parent::plugins_loaded();
        if( class_exists('Abstract_Product_Table_Data') ) {
            include_once("includes/compatibility/product_table.php");
        }
    }
    public function widgets_init() {
        register_widget("berocket_wish_wait_widget_1");
    }
    public function get_wish_wait_html($list_name, $args = array()) {
        if( ! is_array($args) ) {
            $args = array();
        }
        $args = array_merge(array(
            'header'    => 1,
            'expand'    => 0,
            'list_name' => $list_name,
        ), $args);
        $options_global = $this->get_option();
        $options = $options_global['general_settings'];
        $text_options = $options_global['text_settings'];
        set_query_var( 'text_options', $text_options );
        set_query_var( 'options', $options );
        set_query_var( 'brargs', $args );
        ob_start();
        $this->br_get_template_part($list_name);
        $this->br_get_template_part('wish_wait');
        return ob_get_clean();
    }
    public function wish_page ($content) {
        global $wp_query;
        $options_global = $this->get_option();
        $options = $options_global['general_settings'];
        $echo_script = false;
        $page_id = ( isset($wp_query->queried_object->ID) ? $wp_query->queried_object->ID : '' );
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $page_id = apply_filters( 'wpml_object_id', $page_id, 'page', true, $default_language );
        foreach(array('wish', 'wait') as $name) {
            $page = $options[$name.'_page'];
            if ( $page == $page_id ) {
                $content .= $this->get_wish_wait_html($name);
            }
        }
        return $content;
    }
    public function shortcode($args = array()) {
        if( ! is_array($args) ) {
            $args = array();
        }
        $args = array_merge(array(
            'list' => 'wish,wait'
        ), $args);
        $args['list'] = explode(',', $args['list']);
        $html = '';
        foreach($args['list'] as $name) {
            $html .= $this->get_wish_wait_html($name, $args);
        }
        return $html;
    }
    public function init () {
        parent::init();
        $options_global = $this->get_option();
        add_action( 'woocommerce_after_order_notes', array($this, 'remove_products_from_lists') );
        add_action( 'woocommerce_checkout_process', array($this, 'remove_products_on_checkout') );
        global $br_current_wish, $br_current_wait;
        $is_logged_in = is_user_logged_in();
        $list_names = array('wish', 'wait');
        if( $is_logged_in ) {
            $user_id = get_current_user_id();
        }
        foreach($list_names as $list_name) {
            if( $is_logged_in ) {
                $unsanitezed_array = get_user_meta($user_id, "berocket_{$list_name}", true);
            } else {
                $unsanitezed_array = empty($_COOKIE["brww_{$list_name}"]) ? array() : json_decode($_COOKIE["brww_{$list_name}"]);
            }
            $sanitized_array = $this->sanitize_array_product_ids($unsanitezed_array);
            if( $is_logged_in ) {
                $sanitized_array = $this->add_from_cookie($list_name, $sanitized_array, true, $user_id);
            }
            ${"br_current_{$list_name}"} = array_flip($sanitized_array);
        }
        wp_register_style( 'berocket_cart_suggestion_slider', plugins_url( 'css/unslider.css', __FILE__ ) );
        wp_enqueue_style( 'berocket_cart_suggestion_slider' );
        wp_enqueue_script( 'berocket_cart_suggestion_slider_js', plugins_url( 'js/unslider-min.js', __FILE__ ), array( 'jquery' ) );
        wp_register_style( 'berocket_wish_list_style', plugins_url( 'css/wish_list.css', __FILE__ ), "", BeRocket_Wish_List_version );
        wp_enqueue_style( 'berocket_wish_list_style' );
        wp_enqueue_script( 'berocket_wish_list_script', plugins_url( 'js/wish_list.js', __FILE__ ), array( 'jquery' ), BeRocket_Wish_List_version );
        $style_options = $options_global['style_settings'];
        $this->add_ww_buttons();
        $javascript_options = $options_global['javascript_settings'];
        wp_localize_script(
            'berocket_wish_list_script',
            'the_wish_list_data',
            array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'user_func'     => apply_filters( 'berocket_wish_wait_user_func', $javascript_options ),
                'icon_load'     => ( ( @ $style_options['icon_load'] ) ? ( ( substr( $style_options['icon_load'], 0, 3 ) == 'fa-' ) ? '<i class="fa ww_animate ' . $style_options['icon_load'] . '"></i>' : '<i class="fa ww_animate"><image src="' . $style_options['icon_load'] . '" alt=""></i>' ) : '<i class="fa"></i>' ),
            )
        );
    }
    public function sanitize_array_product_ids($unsanitized_array = array()) {
        $sanitized_array = array();
        if( ! is_array($unsanitized_array) ) {
            $unsanitized_array = array();
        }
        foreach($unsanitized_array as $unsanitezed_item) {
            $unsanitezed_item = intval($unsanitezed_item);
            if( $unsanitezed_item ) {
                $sanitized_array[] = $unsanitezed_item;
            }
        }
        return $sanitized_array;
    }
    public function add_from_cookie($type, $sanitized_array = array(), $save = false, $user_id = false) {
        if( ! empty($_COOKIE["brww_{$type}"]) ) {
            if( $save && $user_id === false ) {
                $user_id = get_current_user_id();
            }
            $unsanitezed_array_cookie = empty($_COOKIE["brww_{$type}"]) ? array() : json_decode($_COOKIE["brww_{$type}"]);
            $sanitezed_array_cookie = $this->sanitize_array_product_ids($unsanitezed_array_cookie);
            if( count($sanitezed_array_cookie) > 0 ) {
                foreach($sanitezed_array_cookie as $sanitezed_cookie) {
                    if( ! in_array( $sanitezed_cookie, $sanitized_array ) ) {
                        $sanitized_array[] = $sanitezed_cookie;
                        if( $save ) {
                            $this->update_product_users($sanitezed_cookie, $user_id, $type, 'add');
                        }
                    }
                }
                $sanitized_array = array_merge(array_values($sanitized_array), array_values($sanitezed_array_cookie));
            }
            if( $save ) {
                $sanitized_array = array_unique($sanitized_array);
                update_user_meta( $user_id, 'berocket_'.$type, $sanitized_array );
                setcookie('brww_'.$type, '', 0, '/');
            }
        }
        return $sanitized_array;
    }
    public function add_ww_buttons() {
        $this->add_remove_ww_buttons('add');
    }
    public function remove_ww_buttons() {
        $this->add_remove_ww_buttons('remove');
    }
    public function add_remove_ww_buttons($add_remove = 'add') {
        $options = $this->get_option();
        $action = $add_remove.'_action';
        $style_options = $options['style_settings'];
        if ( empty($options['general_settings']['only_logged_in']) || is_user_logged_in() ) {
            $position = array(
                'wait_pos' => 'wait',
                'wish_pos' => 'wish',
                'ww_pos' => 'ww'
            );
            foreach( $position as $pos_name => $positions ) {
                if( ! empty($style_options[$pos_name]) ) {
                    if( ! empty($style_options[$pos_name]['before_all']) ) {
                        $action( 'woocommerce_before_shop_loop_item', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'lgv_advanced_before', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                    if( ! empty($style_options[$pos_name]['after_image']) ) {
                        $action( 'woocommerce_before_shop_loop_item_title', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'lgv_advanced_after_img', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                    if( ! empty($style_options[$pos_name]['after_title']) ) {
                        $action( 'woocommerce_shop_loop_item_title', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'lgv_advanced_before_description', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                    if( ! empty($style_options[$pos_name]['after_price']) ) {
                        $action( 'woocommerce_after_shop_loop_item_title', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'lgv_advanced_after_price', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                    if( ! empty($style_options[$pos_name]['after_add_to_cart']) ) {
                        $action( 'woocommerce_after_shop_loop_item', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'lgv_advanced_after_add_to_cart', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                    if( ! empty($style_options[$pos_name]['single_product']) ) {
                        $action( 'woocommerce_single_product_summary', array( $this, 'get_wish_button_'.$positions ), 32 );
                        $action( 'berocket_pp_popup_after_buttons', array( $this, 'get_wish_button_'.$positions ), 32 );
                    }
                }
            }
        }
    }
    public function admin_init () {
        parent::admin_init();
        $options = $this->get_option();
        wp_enqueue_script( 'berocket_wish_list_admin_script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ) );
        wp_register_style( 'berocket_wish_list_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_Wish_List_version );
        wp_enqueue_style( 'berocket_wish_list_admin_style' );
        $this->update_from_not_framework();
        add_filter( 'manage_edit-product_columns', array($this, 'add_product_columns') );
        add_action( 'manage_product_posts_custom_column', array($this, 'add_product_columns_data'), 2 );
        add_filter( "manage_edit-product_sortable_columns", array($this, 'product_columns_sort') );
        add_action( 'pre_get_posts', array($this, 'product_apply_sort') );
    }
    public function get_wish_button($type = 'ww') {
        global $product, $wp_query, $br_current_wish, $br_current_wait;
        $product_id = br_wc_get_product_id($product);
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
        $options_global = $this->get_option();
        $options = $options_global['general_settings'];
        $style_options = $options_global['style_settings'];
        echo '<div class="br_wish_wait_block br_wish_wait_'.$product_id.'" data-id="'.$product_id.'">';
        if( $type == 'ww' || $type == 'wish' ) {
            do_action( 'berocket_before_wish_button' );
            if ( ! $options['wish_list'] ) {
                echo '<span class="'.( ( array_key_exists( $product_id, $br_current_wish ) ) ? 'br_ww_button_true ' : '' ).(( $type == 'ww' && ! $product->is_in_stock() && ! $options['wait_list'] ) ? 'br_ww_button_40 ' : '').'br_ww_button br_wish_button br_wish_add button" data-type="wish" href="#add_to_wish_list">'.( ( @ $style_options['icon_wish'] ) ? ( ( substr( $style_options['icon_wish'], 0, 3 ) == 'fa-' ) ? '<i class="fa ' . $style_options['icon_wish'] . '"></i>' : '<i class="fa"><image src="' . $style_options['icon_wish'] . '" alt=""></i>' ) : '' ).'</span>';
            }
            do_action( 'berocket_after_wish_button' );
        }
        if( $type == 'ww' || $type == 'wait' ) {
            do_action( 'berocket_before_wait_button' );
            if ( ! $product->is_in_stock() && ! $options['wait_list'] ) {
                echo '<span class="'.( ( array_key_exists( $product_id, $br_current_wait ) ) ? 'br_ww_button_true ' : '' ).($type == 'ww' ? 'br_ww_button_40 ' : '').'br_ww_button br_wait_button br_wait_add button" data-type="wait" href="#add_to_wait_list">'.( ( @ $style_options['icon_wait'] ) ? ( ( substr( $style_options['icon_wait'], 0, 3 ) == 'fa-' ) ? '<i class="fa ' . $style_options['icon_wait'] . '"></i>' : '<i class="fa"><image src="' . $style_options['icon_wait'] . '" alt=""></i>' ) : '' ).'</span>';
            }
            do_action( 'berocket_after_wait_button' );
        }
        echo '</div>';
    }
    public function get_wish_button_wish() {
        $this->get_wish_button('wish');
    }
    public function get_wish_button_wait() {
        $this->get_wish_button('wait');
    }
    public function get_wish_button_ww() {
        $this->get_wish_button('ww');
    }
    public function listener_wish_add() {
        $this->update_list( 'wish' );
        wp_die();
    }
    public function listener_wait_add() {
        $this->update_list( 'wait' );
        wp_die();
    }
    public function update_list( $type, $product_id = false, $only_remove = false ) {
        if( $product_id == false ) {
            $product_id = $_POST[$type.'_id'];
        }
        $product_id = intval($product_id);
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
        if( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $current_wish = get_user_meta($user_id, 'berocket_'.$type, true);
        } else {
            $current_wish = empty($_COOKIE['brww_'.$type]) ? array() : json_decode($_COOKIE['brww_'.$type]);
        }
        if( ! is_array($current_wish) ) {
            $current_wish = array();
        }
        $sanitize_current_wish = array();
        foreach($current_wish as $current_wish_product) {
            $current_wish_product = intval($current_wish_product);
            if( $current_wish_product ) {
                $sanitize_current_wish[] = $current_wish_product;
            }
        }
        $current_wish = $sanitize_current_wish;
        if ( isset( $current_wish ) && is_array( $current_wish ) && count( $current_wish ) > 0 ) {
            $find = array_search( $product_id, $current_wish );
            if ( $find === FALSE ) {
                if( ! $only_remove ) {
                    $current_wish[] = $product_id;
                    $operation = 'add';
                } else {
                    $operation = 'remove';
                }
            } else {
                unset( $current_wish[$find] );
                $operation = 'remove';
            }
        } else {
            $current_wish = array();
            $current_wish[] = $product_id;
            $operation = 'add';
        }
        $status = 'error';
        $current_wish = array_values($current_wish);
        if( is_user_logged_in() ) {
            if ( update_user_meta( $user_id, 'berocket_'.$type, $current_wish ) ) {
                $status = 'ok';
                $this->update_product_users($product_id, $user_id, $type, $operation);
            }
        } else {
            if( apply_filters('brwwl_update_list_not_logged_in', true, $current_wish, $type, $product_id, $only_remove) ) {
                setcookie('brww_'.$type, json_encode($current_wish), time()+60*60*24*30, '/');
            }
            $status = 'ok';
        }
        $result = apply_filters('brwwl_update_list_result', array('current_wish' => $current_wish, 'status' => $status, 'operation' => $operation, 'user' => empty($user_id) ? 0 : $user_id ), $current_wish, $type, $product_id, $only_remove);
        echo json_encode( $result );
    }
    public function update_product_users($product_id, $user_id, $type, $operation = 'add') {
        $users = get_post_meta($product_id, $type.'_users', true);
        if ( $operation == 'remove' ) {
            if ( isset( $users ) && is_array( $users ) ) {
                $find = array_search( $user_id, $users );
                if ( $find !== FALSE ) {
                    unset( $users[$find] );
                }
            }
        } else {
            if ( ! isset( $users ) || ! is_array( $users ) || count( $users ) == 0 ) {
                $users = array();
            }
            $find = array_search( $user_id, $users );
            if ( $find === FALSE ) {
                $users[] = $user_id;
            }
        }
        update_post_meta($product_id, $type.'_users', $users);
        if( count($users) > 0 ) {
            update_post_meta($product_id, $type.'_users_count', count($users));
        } else {
            delete_post_meta($product_id, $type.'_users_count');
        }
    }
    public function send_mail( $product_id, $status ) {
        if ( $status == 'instock' ) {
            $users = get_post_meta($product_id, 'wait_users', true);
            if ( isset( $users ) && is_array( $users ) && count( $users ) > 0 ) {
                $this->send_email_instock($product_id, $users);
                $users = array();
                update_post_meta($product_id, 'wait_users', $users);
            }
            do_action('brwwl_send_email_instock', $product_id, $users);
        }
    }
    public function send_email_instock($product_id, $users) {
        $options_global = $this->get_option();
        $text_options = $options_global['text_settings'];
        $subject = apply_filters('berocket_wait_list_replace_product_variable', $text_options['instock_subject'], $product_id);
        $message = apply_filters('berocket_wait_list_replace_product_variable', $text_options['instock_message'], $product_id);
        set_query_var( 'product_id', $product_id );
        foreach ( $users as $key_users => $user_id ) {
            $user = new WP_User( $user_id );
            $subject = apply_filters('berocket_wait_list_replace_user_variable', $subject, $user_id);
            $message = apply_filters('berocket_wait_list_replace_user_variable', $message, $user_id);
            set_query_var( 'user_id', $user_id );
            set_query_var( 'subject', $subject );
            set_query_var( 'message', $message );
            ob_start();
            $this->br_get_template_part('email/product_instock');
            $message = ob_get_clean();
            $headers = array(
                'From: '.$text_options['instock_from_name'].' <'.$text_options['instock_from_email'].'>',
                'Content-Type: text/html',
            );
            wp_mail( $user->user_email, $subject, $message );
        }
    }

    public function replace_product_variables_in_text($text, $product_id) {
        $product = wc_get_product($product_id);
        $search = array(
            '%product_id%',
            '%product_title%',
            '%product_link%',
            '%product_image%',
            '%product_price%',
            '%product_description%',
            '%product_short_description%',
        );
        $replace = array(
            $product->get_id(),
            $product->get_title(),
            $product->get_permalink(),
            $product->get_image(),
            $product->get_price_html(),
            $product->get_description(),
            $product->get_short_description(),
        );
        $text = str_replace($search, $replace, $text);
        return $text;
    }

    public function replace_user_variables_in_text($text, $user_id) {
        $user = new WP_User( $user_id );
        $search = array(
            '%user_id%',
            '%user_first_name%',
            '%user_last_name%',
            '%user_email%',
            '%user_display_name%',
        );
        $replace = array(
            $user->ID,
            $user->first_name,
            $user->last_name,
            $user->user_email,
            $user->display_name,
        );
        $text = str_replace($search, $replace, $text);
        return $text;
    }

    public function add_product_columns($columns) {
        $new_columns = (is_array($columns)) ? $columns : array();
        $new_columns['wish_count'] = __( 'Wish', 'wish-wait-list-for-woocommerce' );
        $new_columns['wait_count'] = __( 'Wait', 'wish-wait-list-for-woocommerce' );
        return $new_columns;
    }

    public function add_product_columns_data($column) {
        global $post;
        if( $column == 'wish_count' ) {
            $data = get_post_meta($post->ID, 'wish_users_count', true);
            if( @ ! $data ) {
                $data = 0;
            }
            echo $data;
        }
        if( $column == 'wait_count' ) {
            $data = get_post_meta($post->ID, 'wait_users_count', true);
            if( @ ! $data ) {
                $data = 0;
            }
            echo $data;
        }
    }

    public function product_columns_sort($columns) {
        $custom = array(
            'wish_count'    => array( 'wish_users_count', true ),
            'wait_count'    => array( 'wait_users_count', true )
        );
        return wp_parse_args( $custom, $columns );
    }

    public function product_apply_sort($query) {
        if( ! is_admin() ) {
            return false;
        }
        $orderby = $query->get('orderby');
        if( @ $orderby == 'wish_users_count' || @ $orderby == 'wait_users_count' ) {
            $query->set('meta_key',$orderby);  
            $query->set('orderby','meta_value_num');
        }
    }

    public function remove_products_from_lists($checkout) {
        $user_id = get_current_user_id();
        $array_cart = array();
        $array_user = array();
        $array_intersect = array();
        $types = array('wait', 'wish');
        foreach($types as $type) {
            $array_user[$type] = get_user_meta($user_id, 'berocket_'.$type, true);
            if(empty($array_user[$type]) || ! is_array($array_user[$type])) {
                $array_user[$type] = array();
            }
        }
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        foreach($items as $item => $values) {
            $array_cart[] = $values['product_id'];
        }
        $array_intersect['wait'] = array_intersect($array_user['wait'], $array_cart);
        $array_intersect['wish'] = array_intersect($array_user['wish'], $array_cart);
        if( count($array_intersect['wait']) > 0 || count($array_intersect['wish']) > 0 ) {
            echo '<div id="id_remove_wait_product"><h3>' . __('Products in your lists', 'wish-wait-list-for-woocommerce') . '</h3>';
            if( count($array_intersect['wait']) > 0 ) { 
                woocommerce_form_field( 'remove_wait_product', array(
                    'type'         => 'checkbox',
                    'class'         => array('remove_wait_product'),
                    'label'         => __('Remove products from your wait list', 'wish-wait-list-for-woocommerce'),
                ), true);
            }
            if( count($array_intersect['wish']) > 0 ) { 
                woocommerce_form_field( 'remove_wish_product', array(
                    'type'         => 'checkbox',
                    'class'         => array('remove_wish_product'),
                    'label'         => __('Remove products from your wish list', 'wish-wait-list-for-woocommerce'),
                ), true);
            }

            echo '</div>';
        }
    }

    public function remove_products_on_checkout() {
        $types = array('wait', 'wish');
        foreach($types as $type) {
            if( @ $_POST['remove_'.$type.'_product'] ) {
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                foreach($items as $item => $values) {
                    $product_id = $values['product_id'];
                    $default_language = apply_filters( 'wpml_default_language', NULL );
                    $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
                    $this->update_list( $type, $product_id, true );
                }
            }
        }
    }
    public function product_class($classes) {
        $classes[] = 'brcs_product';
        return $classes;
    }
    public function update_from_not_framework() {
        $update_option = false;
        $options = $this->get_option();
        $settings_list = array('general_settings', 'style_settings', 'text_settings', 'javascript_settings');
        foreach($settings_list as $setting_list) {
            $settings = get_option('br_wish_list_'.$setting_list);
            if( ! empty($settings) && is_array($settings) ) {
                $update_option = true;
                $options[$setting_list] = $settings;
                delete_option('br_wish_list_'.$setting_list);
            }
        }
        if($update_option) {
            update_option($this->values[ 'settings_name' ], $options);
        }
    }
    public function set_styles () {
        parent::set_styles();
        $options_global = $this->get_option();
        $style_options = $options_global['style_settings'];
        if( ! isset($style_options['styles']) || ! is_array($style_options['styles']) ) return;
        echo '<style>';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block .br_ww_button, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block .br_ww_button,
            div.br_wish_wait_block .br_ww_button {';
            $this->array_to_style($style_options['styles']['button']);
        echo '}';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button:hover, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button:hover,
            div.br_wish_wait_block span.br_ww_button:hover {';
            $this->array_to_style($style_options['styles']['button_hover']);
        echo '}';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_true, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_true,
            div.br_wish_wait_block span.br_ww_button_true {';
            $this->array_to_style($style_options['styles']['selected_button']);
        echo '}';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button.br_ww_button_true:hover, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button.br_ww_button_true:hover,
            div.br_wish_wait_block span.br_ww_button.br_ww_button_true:hover {';
            $this->array_to_style($style_options['styles']['selected_button_hover']);
        echo '}';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_40:first-child, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_40:first-child,
            div.br_wish_wait_block span.br_ww_button_40:first-child {';
            $this->array_to_style($style_options['styles']['first_button']);
        echo '}';
        echo '.woocommerce ul.products .berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_40:last-child, 
            div.berocket_lgv_additional_data .lgv_addtocart_advanced div.br_wish_wait_block span.br_ww_button_40:last-child,
            .products div.br_wish_wait_block span.br_ww_button_40:last-child {';
            $this->array_to_style($style_options['styles']['first_button']);
        echo '}';
        foreach(array('wish', 'wait') as $type) {
            echo '.berocket_'.$type.'_list {';
                $this->array_to_style($style_options['styles'][$type.'_products']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product {';
                $this->array_to_style($style_options['styles'][$type.'_product']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product .berocket_ww_title {';
                $this->array_to_style($style_options['styles'][$type.'_product_name']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product a:hover .berocket_ww_title,
            .berocket_'.$type.'_list .berocket_ww_product .berocket_ww_title:hover {';
                $this->array_to_style($style_options['styles'][$type.'_product_name_hover']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product .berocket_ww_price {';
                $this->array_to_style($style_options['styles'][$type.'_product_price']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product .stock {';
                $this->array_to_style($style_options['styles'][$type.'_out_of_stock']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product .berocket_ww_remove {';
                $this->array_to_style($style_options['styles'][$type.'_remove_button']);
            echo '}';
            echo '.berocket_'.$type.'_list .berocket_ww_product .berocket_ww_remove:hover {';
                $this->array_to_style($style_options['styles'][$type.'_remove_button_hover']);
            echo '}';
        }
        echo '</style>';
    }
    public function array_to_style ( &$styles ) {
        if( ! isset($styles) || ! is_array($styles) ) return;
        $color = array( 'color', 'background-color', 'border-color' );
        $size = array( 'border-width', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width',
            'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
            'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
            'margin-top', 'margin-bottom', 'margin-left', 'margin-right', 'top', 'bottom', 'left', 'right',
            'width', 'height', 'max-height', 'max-width', 'line-height', 'font-size', 'border-radius' );
        $border_color = array('border-color');
        $border_width = array('border-width', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width');
        $has_border_color = $has_border_width = false;
        foreach( $styles as $name => $value ) {
            if ( isset( $value ) && strlen($value) > 0 ) {
                if ( ! $has_border_color && in_array( $name, $border_color ) ) {
                    $has_border_color = true;
                }
                if ( ! $has_border_width && in_array( $name, $border_width ) ) {
                    $has_border_width = true;
                }
                if ( in_array( $name, $color ) ) {
                    if ( $value[0] != '#' ) {
                        $value = '#' . $value;
                    }
                    echo $name . ':' . $value . '!important;';
                } else if ( in_array( $name, $size ) ) {
                    if ( strpos( $value, '%' ) || strpos( $value, 'em' ) || strpos( $value, 'px' ) || $value == 'initial' || $value == 'inherit' ) {
                        echo $name . ':' . $value . '!important;';
                    } else {
                        echo $name . ':' . $value . 'px!important;';
                    }
                } else {
                    echo $name . ':' . $value . '!important;';
                }
            }
        }
        if( $has_border_color && $has_border_width ) {
            echo 'border-style:solid!important;';
        }
    }
    public function admin_settings( $tabs_info = array(), $data = array() ) {
        $pages = get_pages();
        $pages_option = array();
        $pages_option[] = array('value' => '', 'text' => '==No Page==');
        foreach($pages as $page) {
            $pages_option[] = array('value' => $page->ID, 'text' => $page->post_title);
        }
        parent::admin_settings(
            array(
                'General' => array(
                    'icon' => 'cog',
                    'name' => __( 'General', "wish-wait-list-for-woocommerce" ),
                ),
                'Style' => array(
                    'icon' => 'eye',
                    'name' => __( 'Style', "wish-wait-list-for-woocommerce" ),
                ),
                'Text' => array(
                    'icon' => 'align-center',
                    'name' => __( 'Text', "wish-wait-list-for-woocommerce" ),
                ),
                'Custom CSS/JavaScript' => array(
                    'icon' => 'css3',
                    'name' => __( 'Custom CSS/JavaScript', "wish-wait-list-for-woocommerce" ),
                ),
                'License' => array(
                    'icon' => 'unlock-alt',
                    'link' => admin_url( 'admin.php?page=berocket_account' ),
                    'name' => __( 'License', "wish-wait-list-for-woocommerce" ),
                ),
            ),
            array(
            'General' => array(
                'only_logged_in' => array(
                    "label"    => __( 'Only for logged-in users', "wish-wait-list-for-woocommerce" ),
                    "name"     => array("general_settings", "only_logged_in"),
                    "type"     => "checkbox",
                    "value"    => '1'
                ),
				'wish_page' => array(
                    "label"    => __( 'Wish Page', "wish-wait-list-for-woocommerce" ),
                    "name"     => array("general_settings", "wish_page"),
                    "type"     => "selectbox",
                    "options"  => $pages_option,
                    "value"    => ''
                ),
                'wait_page' => array(
                    "label"    => __( 'Wait Page', "wish-wait-list-for-woocommerce" ),
                    "name"     => array("general_settings", "wait_page"),
                    "type"     => "selectbox",
                    "options"  => $pages_option,
                    "value"    => ''
                ),
                'wish_list' => array(
                    "label"     => __('Disable wish list button', 'wish-wait-list-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => array("general_settings", "wish_list"),
                    "value"     => '1',
                ),
                'wait_list' => array(
                    "label"     => __('Disable wait list button', 'wish-wait-list-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => array("general_settings", "wait_list"),
                    "value"     => '1',
                ),
                'cookie_use' => array(
                    "label"     => __('Enable cookie usage popup', 'wish-wait-list-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => array("general_settings", "cookie_use"),
                    "value"     => '1',
                ),
            ),
            'Style' => array(
                'wcshortcode_use' => array(
                    "label"     => __('WooCommerce shortcode', 'wish-wait-list-for-woocommerce'),
                    "label_for" => __('Use [products] shortcode for wish/wait lists instead custom elements', 'wish-wait-list-for-woocommerce'),
                    "type"      => "checkbox",
                    "name"      => array("style_settings", "wcshortcode_use"),
                    "class"     => 'brwwl_wcshortcode_use',
                    "value"     => '1',
                ),
                'position_for_buttons' => array(
                    "label"     => "",
                    "section"   => 'position_for_buttons'
                ),
                'icon_wish' => array(
                    "label"     => __('Wish list button icon', 'wish-wait-list-for-woocommerce'),
                    "type"      => "faimage",
                    "name"      => array("style_settings", "icon_wish"),
                    "value"     => '',
                ),
                'icon_wait' => array(
                    "label"     => __('Wait list button icon', 'wish-wait-list-for-woocommerce'),
                    "type"      => "faimage",
                    "name"      => array("style_settings", "icon_wait"),
                    "value"     => '',
                ),
                'icon_load' => array(
                    "label"     => __('Update status icon', 'wish-wait-list-for-woocommerce'),
                    "type"      => "faimage",
                    "name"      => array("style_settings", "icon_load"),
                    "value"     => '',
                ),
                'all_styles' => array(
                    "label"     => "",
                    "section"   => 'all_styles'
                ),
            ),
            'Text' => array(
                'wish_list' => array(
                    "label"     => __('Text before wish list', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "wish_list"),
                    "value"     => '',
                ),
                'wait_list' => array(
                    "label"     => __('Text before wait list', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "wait_list"),
                    "value"     => '',
                ),
                'show_wish' => array(
                    "label"     => __('Text on button to show all products in wish list', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "show_wish"),
                    "value"     => '',
                ),
                'show_wait' => array(
                    "label"     => __('Text on button to show all products in wait list', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "show_wait"),
                    "value"     => '',
                ),
                'instock_from_name' => array(
                    "label"     => __('Product instock Email FROM name', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "instock_from_name"),
                    "value"     => '',
                ),
                'instock_from_email' => array(
                    "label"     => __('Product instock Email FROM Email', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "instock_from_email"),
                    "value"     => '',
                ),
                'instock_subject' => array(
                    "label"     => __('Product instock Email subject', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "instock_subject"),
                    "value"     => '',
                ),
                'instock_message' => array(
                    "label"     => __('Product instock Email message', 'wish-wait-list-for-woocommerce'),
                    "type"      => "textarea",
                    "name"      => array("text_settings", "instock_message"),
                    "value"     => '',
                ),
                'instock_email' => array(
                    "label"     => "",
                    "section"   => 'instock_email',
                    "name"      => "instock_email",
                ),
                'guest_instock_subject' => array(
                    "label"     => __('Guest product instock Email subject', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "guest_instock_subject"),
                    "value"     => $this->defaults['text_settings']['guest_instock_subject'],
                ),
                'guest_instock_message' => array(
                    "label"     => __('Guest product instock Email message', 'wish-wait-list-for-woocommerce'),
                    "type"      => "textarea",
                    "name"      => array("text_settings", "guest_instock_message"),
                    "value"     => $this->defaults['text_settings']['guest_instock_message'],
                ),
                'cookie_use_text' => array(
                    "label"     => __('Cookie usage popup message', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "cookie_use_text"),
                    "value"     => $this->defaults['text_settings']['cookie_use_text'],
                ),
                'cookie_use_button' => array(
                    "label"     => __('Cookie usage popup button', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "cookie_use_button"),
                    "value"     => $this->defaults['text_settings']['cookie_use_button'],
                ),
                'wishlist_empty' => array(
                    "label"     => __('Wishlist empty', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "wishlist_empty"),
                    "value"     => $this->defaults['text_settings']['wishlist_empty'],
                ),
                'waitlist_empty' => array(
                    "label"     => __('Waitlist empty', 'wish-wait-list-for-woocommerce'),
                    "type"      => "text",
                    "name"      => array("text_settings", "waitlist_empty"),
                    "value"     => $this->defaults['text_settings']['waitlist_empty'],
                ),
            ),
            'Custom CSS/JavaScript' => array(
                'global_font_awesome_disable' => array(
                    "label"     => __( 'Disable Font Awesome', "wish-wait-list-for-woocommerce" ),
                    "type"      => "checkbox",
                    "name"      => "fontawesome_frontend_disable",
                    "value"     => '1',
                    'label_for' => __('Don\'t load Font Awesome css files on site front end. Use it only if you don\'t use Font Awesome icons in widgets or your theme has Font Awesome.', 'wish-wait-list-for-woocommerce'),
                ),
                'global_fontawesome_version' => array(
                    "label"    => __( 'Font Awesome Version', "wish-wait-list-for-woocommerce" ),
                    "name"     => "fontawesome_frontend_version",
                    "type"     => "selectbox",
                    "options"  => array(
                        array('value' => '', 'text' => __('Font Awesome 4', 'wish-wait-list-for-woocommerce')),
                        array('value' => 'fontawesome5', 'text' => __('Font Awesome 5', 'wish-wait-list-for-woocommerce')),
                    ),
                    "value"    => '',
                    "label_for" => __('Version of Font Awesome that will be used on front end. Please select version that you have in your theme', 'wish-wait-list-for-woocommerce'),
                ),
                array(
                    "label"   => "Custom CSS",
                    "name"    => "custom_css",
                    "type"    => "textarea",
                    "value"   => "",
                ),
                array(
                    "label"   => "JavaScript After add to wish list",
                    "name"    => array("javascript_settings", "before_wish"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                array(
                    "label"   => "JavaScript Before add to wish list",
                    "name"    => array("javascript_settings", "after_wish"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                array(
                    "label"   => "JavaScript After add to wait list",
                    "name"    => array("javascript_settings", "before_wait"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
                array(
                    "label"   => "JavaScript Before add to wait list",
                    "name"    => array("javascript_settings", "after_wait"),
                    "type"    => "textarea",
                    "value"   => "",
                ),
            ),
        ) );
    }
    public function section_position_for_buttons($data, $options) {
        $html = '<th>' . __( 'Position for buttons', 'wish-wait-list-for-woocommerce' ) . '</th>
        <td><table class="berocket_ww_position_table">';
        $position = array(
            'head' => array(
                '', 
                __( 'Wait', 'wish-wait-list-for-woocommerce' ),
                __( 'Wish', 'wish-wait-list-for-woocommerce' ),
                __( 'Wait/Wish', 'wish-wait-list-for-woocommerce' ),
            ),
            'before_all' => array(
                'th' => __( 'Before all', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            ),
            'after_image' => array(
                'th' => __( 'After image', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            ),
            'after_title' => array(
                'th' => __( 'After title', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            ),
            'after_price' => array(
                'th' => __( 'After price', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            ),
            'after_add_to_cart' => array(
                'th' => __( 'After add to cart button', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            ),
            'single_product' => array(
                'th' => __( 'Single product page', 'wish-wait-list-for-woocommerce' ),
                'wait_pos',
                'wish_pos',
                'ww_pos'
            )
        );
        foreach( $position as $pos_name => $positions ) {
            $html .= '<tr class="berocket_pos_table_'.$pos_name.'">';
            foreach( $positions as $but_name => $button_pos ) {
                if( $pos_name === 'head' || $but_name === 'th' ) {
                    $html .= '<th>'.$button_pos.'</th>';
                } else {
                    $html .= '<td><input type="checkbox" value="1" name="'.$this->values['settings_name'].'[style_settings]['.$button_pos.']['.$pos_name.']"'.(! empty($options['style_settings'][$button_pos][$pos_name]) ? ' checked' : '').'></td>';
                }
            }
            echo '</tr>';
        }
        $html .= '</table></td>';
        return $html;
    }
    public function section_all_styles($data, $options_global) {
        ob_start();
        $options = br_get_value_from_array($options_global, 'style_settings');
        $settings_name = $this->values['settings_name'];
        include('templates/style_section.php');
        return '<td colspan="2">' . ob_get_clean() . '</td>';
    }
    public function section_instock_email() {
        $html = '<th>' . __( 'Special variables for Email subject and message fields', 'wish-wait-list-for-woocommerce' ) . '</th>
        <td>
            <ul>
                <li><strong style="width:220px;display:inline-block;">%product_title%</strong> - ' . __( 'Product title', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%product_link%</strong> - ' . __( 'Link to product page', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%product_image%</strong> - ' . __( 'Main product image link', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%product_price%</strong> - ' . __( 'Product formated price', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%product_description%</strong> - ' . __( 'Product description', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%product_short_description%</strong> - ' . __( 'Product short description', 'wish-wait-list-for-woocommerce' ) . '</li>
                
                <li><strong style="width:220px;display:inline-block;">%user_id%</strong> - ' . __( 'User ID', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%user_first_name%</strong> - ' . __( 'User First Name', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%user_last_name%</strong> - ' . __( 'User Last Name', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%user_email%</strong> - ' . __( 'User Email', 'wish-wait-list-for-woocommerce' ) . '</li>
                <li><strong style="width:220px;display:inline-block;">%user_display_name%</strong> - ' . __( 'User Display Name', 'wish-wait-list-for-woocommerce' ) . '</li>
            </ul>
        </td>';
        return $html;
    }

    public function option_page_capability($capability = '') {
        return 'manage_berocket_wish_wait';
    }

    public function activation() {
        parent::activation();
        $this->update_from_not_framework();
        $options_global = $this->get_option();
        if ( ! $options_global['general_settings']['wish_page'] ) {
            $wish_page = array(
                'post_title' => 'Products added in my wishlist',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
            );

            $post_id = wp_insert_post($wish_page);
            $options_global['general_settings']['wish_page'] = $post_id;
            update_option($this->values['settings_name'], $options_global);
        }
        if ( ! $options_global['general_settings']['wait_page'] ) {
            $wait_page = array(
                'post_title' => 'Products added in my waitlist',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
            );

            $post_id = wp_insert_post($wait_page);
            $options_global['general_settings']['wait_page'] = $post_id;
            update_option($this->values['settings_name'], $options_global);
        }
    }
    public function divi_initialize_extension() {
        require_once plugin_dir_path( __FILE__ ) . 'divi/includes/WishWaitExtension.php';
    }
}

new BeRocket_Wish_List;
