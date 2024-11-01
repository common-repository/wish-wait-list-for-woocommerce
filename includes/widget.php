<?php
/**
 * List/Grid widget
 */
class BeRocket_wish_wait_widget_1 extends WP_Widget 
{
    public static $defaults = array(
        'title'         => '',
        'slider_count'  => '4',
        'product_count' => '4',
        'product_type'  => 'top_wait',
        'widget_type'   => 'default',
        'add_to_cart'   => '0',
    );
	public function __construct() {
        parent::__construct("BeRocket_wish_wait_widget_1", "WooCommerce Wish/Wait List",
            array("description" => "Widget for BeRocket Wish/Wait List plugin"));
    }
    /**
     * WordPress widget for display Curency Exchange buttons
     */
    public function widget($args, $instance)
    {
        do_action('BeRocket_wish_wait_widget_start', $args, $instance);
        $instance = wp_parse_args( (array) $instance, self::$defaults );
        $instance['title'] = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
        set_query_var( 'product_count', apply_filters( 'ww_widget_product_count', $instance['product_count'] ) );
        set_query_var( 'slider_count', apply_filters( 'ww_widget_slider_count', $instance['slider_count'] ) );
        set_query_var( 'product_type', apply_filters( 'ww_widget_product_type', $instance['product_type'] ) );
        set_query_var( 'display_type', apply_filters( 'ww_widget_display_type', $instance['widget_type'] ) );
        set_query_var( 'add_to_cart', apply_filters( 'ww_widget_add_to_cart', $instance['add_to_cart'] ) );
        ob_start();
        $BeRocket_Wish_List = BeRocket_Wish_List::getInstance();
        $BeRocket_Wish_List->br_get_template_part( 'widget' );
        $content = ob_get_clean();
        if( $content ) {
            echo $args['before_widget'];
            if( $instance['title'] ) echo $args['before_title'].$instance['title'].$args['after_title'];
            echo $content;
            echo $args['after_widget'];
        }
        do_action('BeRocket_wish_wait_widget_end', $args, $instance);
	}
    /**
     * Update widget settings
     */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['product_count'] = strip_tags( $new_instance['product_count'] );
		$instance['slider_count'] = strip_tags( $new_instance['slider_count'] );
		$instance['product_type'] = strip_tags( $new_instance['product_type'] );
		$instance['widget_type'] = strip_tags( $new_instance['widget_type'] );
		$instance['add_to_cart'] = ! empty($new_instance['add_to_cart']);
		return $instance;
	}
    /**
     * Widget settings form
     */
	public function form($instance)
	{
        $instance = wp_parse_args( (array) $instance, self::$defaults );
		$title = strip_tags($instance['title']);
		$product_count = strip_tags($instance['product_count']);
		$slider_count = strip_tags($instance['slider_count']);
		$product_type = strip_tags($instance['product_type']);
		$widget_type = strip_tags($instance['widget_type']);
		$add_to_cart = strip_tags($instance['add_to_cart']);
		?>
        <div class="br_ww_widget_form">
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('product_type'); ?>"><?php _e('Product type:', 'wish-wait-list-for-woocommerce'); ?></label>
            <select name="<?php echo $this->get_field_name('product_type'); ?>">
                <?php
                $product_type_array = array(
                    'top_wait'      => __('Top products from wait list', 'wish-wait-list-for-woocommerce'), 
                    'top_wish'      => __('Top products from wish list', 'wish-wait-list-for-woocommerce'), 
                    'rel_wait'      => __('Related products from user wait list', 'wish-wait-list-for-woocommerce'), 
                    'rel_wish'      => __('Related products from user wish list', 'wish-wait-list-for-woocommerce'),
                    'instock_wait'  => __('Products in user wait list in stock', 'wish-wait-list-for-woocommerce'),
                    'onsale_wish'   => __('Products in user wish list on sale', 'wish-wait-list-for-woocommerce')
                );
                foreach( $product_type_array as $d_type_slug => $d_type_name ) {
                    echo '<option value="'.$d_type_slug.'"'.($product_type == $d_type_slug ? ' selected' : '').'>'.$d_type_name.'</option>';
                }
                ?>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('widget_type'); ?>"><?php _e('Type:', 'wish-wait-list-for-woocommerce'); ?></label>
            <select name="<?php echo $this->get_field_name('widget_type'); ?>" class="br_ww_display_type">
                <?php
                $display_type = array(
                    'default' => __('Default', 'wish-wait-list-for-woocommerce'), 
                    'image' => __('Image', 'wish-wait-list-for-woocommerce'), 
                    'image_title' => 'Image with Title', 
                    'image_title_price' => 'Image with Title and Price', 
                    'title' => 'Title', 
                    'title_price' => 'Title with Price', 
                    'slider' => 'Slider', 
                    'slider_title' => 'Slider with title'
                );
                foreach( $display_type as $d_type_slug => $d_type_name ) {
                    echo '<option value="'.$d_type_slug.'"'.($widget_type == $d_type_slug ? ' selected' : '').'>'.$d_type_name.'</option>';
                }
                ?>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('slider_count'); ?>"><?php _e('Products per line:', 'wish-wait-list-for-woocommerce'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('slider_count'); ?>" name="<?php echo $this->get_field_name('slider_count'); ?>" type="number" value="<?php echo esc_attr( $slider_count ); ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('add_to_cart'); ?>"><?php _e('Show Add to cart button:', 'wish-wait-list-for-woocommerce'); ?></label>
            <input id="<?php echo $this->get_field_id('add_to_cart'); ?>" name="<?php echo $this->get_field_name('add_to_cart'); ?>" type="checkbox" value="1"<?php if( $add_to_cart ) echo ' checked'; ?>>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('product_count'); ?>"><?php _e('Products count:', 'wish-wait-list-for-woocommerce'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('product_count'); ?>" name="<?php echo $this->get_field_name('product_count'); ?>" type="number" value="<?php echo esc_attr( $product_count ); ?>" />
        </p>
        </div>
		<?php
	}
}
?>
