<?php

class ET_Builder_Module_br_wish_wait extends ET_Builder_Module {

	public $slug       = 'et_pb_br_wish_wait';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => '',
		'author_uri' => '',
	);

	public function init() {
        $this->name             = __('Wish/Wait List', 'wish-wait-list-for-woocommerce' );
		$this->folder_name = 'et_pb_berocket_modules';
		$this->main_css_element = '%%order_class%%';
        
        $this->fields_defaults = array(
            'list' => array('wish'),
            'header' => array('on'),
            'expand' => array('off'),
        );

		$this->advanced_fields = array(
			'fonts'           => array(
				'title'   => array(
					'label' => esc_html__( 'List Title', 'wish-wait-list-for-woocommerce' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .berocket_ww_list_title",
						'important' => true,
					),
                    'hide_font_size' => true,
                    'hide_letter_spacing' => true,
                    'hide_line_height' => true,
                    'hide_text_shadow' => true,
				),
				'product_title'   => array(
					'label' => esc_html__( 'Product Title', 'wish-wait-list-for-woocommerce' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .berocket_ww_list .berocket_ww_products .berocket_ww_title",
						'important' => true,
					),
                    'hide_font_size' => true,
                    'hide_letter_spacing' => true,
                    'hide_line_height' => true,
                    'hide_text_shadow' => true,
				),
				'product_price'   => array(
					'label' => esc_html__( 'Product Price/Stock', 'wish-wait-list-for-woocommerce' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .berocket_ww_list .berocket_ww_products .berocket_ww_price, {$this->main_css_element} .berocket_ww_list .berocket_ww_products .stock",
						'important' => true,
					),
                    'hide_font_size' => true,
                    'hide_letter_spacing' => true,
                    'hide_line_height' => true,
                    'hide_text_shadow' => true,
				),
			),
			'link_options'  => false,
			'visibility'    => false,
			'text'          => false,
			'transform'     => false,
			'animation'     => false,
			'background'    => false,
			'borders'       => false,
			'box_shadow'    => false,
			'button'        => false,
			'filters'       => false,
			'margin_padding'=> false,
			'max_width'     => false,
		);
	}

    function get_fields() {
        $fields = array(
            'list' => array(
                "label"           => esc_html__( 'List type', 'wish-wait-list-for-woocommerce' ),
                'type'            => 'select',
                'options'         => array(
                    'wish' => esc_html__( 'Wish', 'wish-wait-list-for-woocommerce' ),
                    'wait' => esc_html__( 'Wait', 'wish-wait-list-for-woocommerce' ),
                )
            ),
            'header' => array(
                "label"             => esc_html__( 'List Header', 'wish-wait-list-for-woocommerce' ),
                'type'              => 'yes_no_button',
                'options'           => array(
                    'off' => esc_html__( "No", 'et_builder' ),
                    'on'  => esc_html__( 'Yes', 'et_builder' ),
                ),
            ),
            'expand' => array(
                "label"             => esc_html__( 'Expandable', 'wish-wait-list-for-woocommerce' ),
                'type'              => 'yes_no_button',
                'options'           => array(
                    'off' => esc_html__( "No", 'et_builder' ),
                    'on'  => esc_html__( 'Yes', 'et_builder' ),
                ),
            ),
        );

        return $fields;
    }

    function render( $atts, $content = null, $function_name = '' ) {
        $atts = BAWW_WishWait_DiviExtension::convert_on_off($atts);
        $list = ( (! empty($atts['list']) && $atts['list'] == 'wait') ? 'wait' : 'wish' );
        return do_shortcode('[br_wishwait_list list="' . $list . '" header="' . (empty($atts['header']) ? '0' : '1') . '" expand="' . (empty($atts['expand']) ? '0' : '1') . '"]');
    }
}

new ET_Builder_Module_br_wish_wait;
