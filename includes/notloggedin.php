<?php
class brwwl_notloggedin {
    function __construct() {
        add_action('init', array($this, 'init'));
    }
    function accept_email($md5hash) {
        if( in_array($md5hash, array('active') ) && strlen($md5hash) < 20 ) return false;
        $md5hash = sanitize_text_field($md5hash);
        global $wpdb;
        $table_name = $wpdb->prefix . 'brwwl_guest';
        $email = $wpdb->get_var("SELECT email FROM {$table_name} WHERE status = '{$md5hash}' LIMIT 1");
        if( ! empty($email) ) {
            $wpdb->update( $table_name, array( 'status' => 'active' ), array( 'status' => $md5hash ), array( '%s' ), array( '%s' ) );
            setcookie('brww_hash', $md5hash, time()+60*60*24*30, '/');
            $products = $wpdb->get_col("SELECT product_id FROM {$table_name} WHERE email = '{$email}' and status ='active'");
            $this->set_product_usermail($email, $products);
        }
    }
    function set_product_usermail($email, $products) {
        $user = get_user_by('email', $email);
        if( ! empty($user) ) {
            $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
            $current_wait = get_user_meta($user->id, 'berocket_wait', true);
            if( empty($current_wait) || ! is_array($current_wait) ) {
                $current_wait = array();
            }
            $default_language = apply_filters( 'wpml_default_language', NULL );
            foreach($products as $product_id) {
                $product_id = intval($product_id);
                $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
                $current_wait[] = $product_id;
                if ( update_user_meta( $user->id, 'berocket_wait', $current_wait ) ) {
                    $BeRocket_Wish_List->update_product_users($product_id, $user->id, 'wait');
                }
            }
            return true;
        }
        return false;
    }
    function init() {
        if( ! empty($_GET['brwwl_hash']) ) {
            $this->accept_email($_GET['brwwl_hash']);
        }
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $options = $BeRocket_Wish_List->get_option();
        if( empty($options['only_logged_in']) ) {
            add_filter('brwwl_update_list_not_logged_in', array($this, 'update_list'), 10, 3); 
            add_filter('brwwl_update_list_result', array($this, 'update_list_result'), 10, 3); 
            add_action( "wp_ajax_brwwl_setmail", array ( $this, 'setmail' ) );
            add_action( "wp_ajax_nopriv_brwwl_setmail", array ( $this, 'setmail' ) );
            add_action('wp_footer', array($this, 'footer'));
            add_action('brwwl_send_email_instock', array($this, 'send_email_instock'), 10, 1);
        }
    }
    function update_list($setcookie, $current_wish, $type) {
        if( $type == 'wait' && ! empty($_COOKIE['brww_mail']) ) {
            global $wpdb;
            $this->check_table_created();
            $email = sanitize_email($_COOKIE['brww_mail']);
            if( ! empty($email) ) {
                $status = $this->checkmd5_or_sendrequest($email);
                if( ! $this->set_product_usermail($email, $current_wish) ) {
                    $table_name = $wpdb->prefix . 'brwwl_guest';
                    foreach($current_wish as $product_id) {
                        $exist = $wpdb->get_var("SELECT count(product_id) FROM {$table_name} WHERE product_id = '{$product_id}' AND email = '{$email}'");
                        if( empty($exist) ) {
                            $result = $wpdb->insert( $table_name, array( 
                                'product_id' => $product_id, 
                                'email' => $email,
                                'status' => $status
                            ), array( '%d', '%s', '%s' ) );
                        }
                    }
                }
                return true;
            }
            return false;
        }
        return $setcookie;
    }
    function send_email_accept($email, $md5) {
        $blogname = get_bloginfo('name');
        $subject = apply_filters('brwwl_send_email_accept_subject', __('Waitlist subscribe', 'wish-wait-list-for-woocommerce') . $blogname, $email, $md5);
        set_query_var( 'md5hash', $md5 );
        set_query_var( 'subject', $subject );
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        ob_start();
        $BeRocket_Wish_List->br_get_template_part('email/guest_accept');
        $message = ob_get_clean();
        $headers = array(
            'From: '.$text_options['instock_from_name'].' <'.$text_options['instock_from_email'].'>',
            'Content-Type: text/html',
        );
        wp_mail( $email, $subject, $message );
    }
    function checkmd5_or_sendrequest($email) {
        if( ! empty($_COOKIE['brww_hash']) ) {
            $md5 = sanitize_text_field($_COOKIE['brww_hash']);
            $start = date('z');
            for($i = 0; $i < 30; $i++) {
                $day = $start - $i;
                if( $day < 0 ) {
                    $day = $day + 365;
                }
                $testmd5 = $this->getmd5($email, $day);
                if( $md5 == $testmd5 ) {
                    return 'active';
                }
            }
        }
        return $this->getmd5($email);
    }
    function getmd5($email, $day = false) {
        $send_mail = false;
        if( $day === false ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'brwwl_guest';
            $get_last_hash = $wpdb->get_var("SELECT status FROM {$table_name} WHERE email = '{$email}' AND status != 'active'");
            if( ! empty($get_last_hash) ) {
                return $get_last_hash;
            }
            $send_mail = true;
            $day = date('z');
        }
        $hash = get_option('brwwl_hash');
        if( empty($hash) ) {
            $hash = date('z') . '-' . rand();
            update_option('brwwl_hash', $hash, false);
        }
        $md5hash = md5($day . $email . $hash);
        if( $send_mail ) {
            $this->send_email_accept($email, $md5hash);
        }
        return $md5hash;
    }
    function update_list_result($result, $current_wish, $type) {
        if( ! is_user_logged_in() && $type == 'wait' && empty($_COOKIE['brww_mail']) && $result['operation'] == 'add' ) {
            $result['emailrequest'] = true;
        }
        return $result;
    }
    function check_table_created() {
        global $wpdb;
        $collate = $this->get_charset_collate();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $table_name = $wpdb->prefix . 'brwwl_guest';
        $sql = "CREATE TABLE $table_name (
        product_id bigint(20) NOT NULL,
        email varchar(200) NOT NULL,
        status varchar(40) NOT NULL,
        UNIQUE KEY uniqueid (product_id, email)
        ) $collate;";
        $query_status = dbDelta( $sql );
    }
    function get_charset_collate() {
        global $wpdb;
        $collate = '';
        $result = $wpdb->get_row("SHOW TABLE STATUS where name like '{$wpdb->posts}'");
        if( ! empty($result) && ! empty($result->Collation) ) {
            $collate = 'DEFAULT CHARACTER SET ' . $wpdb->charset . ' COLLATE ' . $result->Collation;
        } else {
            if ( $wpdb->has_cap( 'collation' ) ) {
                $collate = $wpdb->get_charset_collate();
            }
        }
        return $collate;
    }
    function footer() {
        ?>
        <div class="brwwl_mailset_back" style="display: none;">
            <div class="brwwl_mailset">
                <form>
                    <h3>Join Waitlist</h3>
                    <p>We will inform you when the product arrives in stock. Please leave your valid email address below.</p>
                    <div><input required type="email" class="brwwl_mailset_email" placeholder="Email"></div>
                    <p><button>Join Waitlist</button></p>
                    <span class="brwwl_mail_close"></span>
                </form>
            </div>
        </div>
        <?php
    }
    function setmail() {
        if( ! empty($_POST['mail']) ) {
            $mail = sanitize_email($_POST['mail']);
            if( ! empty($mail) ) {
                setcookie('brww_mail', $mail, time()+60*60*24*30, '/');
                $current_wish = empty($_COOKIE['brww_wait']) ? array() : json_decode($_COOKIE['brww_wait']);
                $this->update_list(false, $current_wish, 'wait');
            }
        }
        wp_die();
    }
    function send_email_instock($product_id) {
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $options_global = $BeRocket_Wish_List->get_option();
        $text_options = $options_global['text_settings'];
        $subject = apply_filters('berocket_wait_list_replace_product_variable', $text_options['guest_instock_subject'], $product_id);
        $message = apply_filters('berocket_wait_list_replace_product_variable', $text_options['guest_instock_message'], $product_id);

        global $wpdb;
        $table_name = $wpdb->prefix . 'brwwl_guest';
        $users = $wpdb->get_col("SELECT email FROM {$table_name} WHERE product_id = '{$product_id}' AND status = 'active'");

        foreach ( $users as $email ) {
            set_query_var( 'subject', $subject );
            set_query_var( 'message', $message );
            ob_start();
            $BeRocket_Wish_List->br_get_template_part('email/product_instock');
            $message = ob_get_clean();
            $headers = array(
                'From: '.$text_options['instock_from_name'].' <'.$text_options['instock_from_email'].'>',
                'Content-Type: text/html',
            );
            wp_mail( $email, $subject, $message );
        }
    }        
}
new brwwl_notloggedin();