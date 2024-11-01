<?php

class BAWW_WishWait_DiviExtension extends DiviExtension {
	public $gettext_domain = 'brww-wishWait';
	public $name = 'brww-wishWait';
	public $version = '1.0.0';
    public $props = array();
	public function __construct( $name = 'brww-wishWait', $args = array() ) {
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

		parent::__construct( $name, $args );
        add_action('wp_ajax_brww_wish_wait', array($this, 'wish_wait'));
	}
    public function wish_wait() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die();
        }
        $atts = berocket_sanitize_array($_POST);
        $atts = self::convert_on_off($atts);
        $list = ( (! empty($atts['list']) && $atts['list'] == 'wait') ? 'wait' : 'wish' );
        echo do_shortcode('[br_wishwait_list list="' . $list . '" header="' . (empty($atts['header']) ? '0' : '1') . '" expand="' . (empty($atts['expand']) ? '0' : '1') . '"]');
        wp_die();
    }
	public function wp_hook_enqueue_scripts() {
		if ( $this->_debug ) {
			$this->_enqueue_debug_bundles();
		} else {
			$this->_enqueue_bundles();
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->_enqueue_backend_styles();
		}

		// Normalize the extension name to get actual script name. For example from 'divi-custom-modules' to `DiviCustomModules`.
		$extension_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->name ) ) );

		// Enqueue frontend bundle's data.
		if ( ! empty( $this->_frontend_js_data ) ) {
			wp_localize_script( "{$this->name}-frontend-bundle", "{$extension_name}FrontendData", $this->_frontend_js_data );
		}

		// Enqueue builder bundle's data.
		if ( et_core_is_fb_enabled() && ! empty( $this->_builder_js_data ) ) {
			wp_localize_script( "{$this->name}-builder-bundle", "{$extension_name}BuilderData", $this->_builder_js_data );
		}
	} 
    public static function convert_on_off($atts) {
        foreach($atts as &$attr) {
            if( $attr === 'on' || $attr === 'off' ) {
                $attr = ( $attr === 'on' ? TRUE : FALSE );
            }
        }
        return $atts;
    }
}

new BAWW_WishWait_DiviExtension;
