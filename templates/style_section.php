<table class="berocket_ww_position_table">
    <thead><tr><th colspan="9"><h3 class="button br_show_hide_table"><?php _e( 'Buttons styles', 'wish-wait-list-for-woocommerce' ) ?><i class="fa fa-chevron-down"></i></h3></th></tr></thead>
    <tbody style="display: none;">
    <tr>
        <th></th>
        <th><?php _e( 'Button', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Button on mouse over', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Selected button', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Selected button on mouse over', 'wish-wait-list-for-woocommerce' ) ?></th>
    </tr>
    <?php 
    $style_column = array('button', 'button_hover', 'selected_button', 'selected_button_hover');
    $style_row = array(
        'font-size' => __( 'Font size', 'wish-wait-list-for-woocommerce' ), 
        'color' => __( 'Font color', 'wish-wait-list-for-woocommerce' ), 
        'background-color' => __( 'Background color', 'wish-wait-list-for-woocommerce' ), 
        'border-width' => __( 'Border width', 'wish-wait-list-for-woocommerce' ), 
        'border-color' => __( 'Border color', 'wish-wait-list-for-woocommerce' ), 
        'border-radius' => __( 'Border radius', 'wish-wait-list-for-woocommerce' ), 
        'width' => __( 'Width', 'wish-wait-list-for-woocommerce' ), 
        'height' => __( 'Height', 'wish-wait-list-for-woocommerce' ),
        'padding-top' => __( 'Padding from top inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-bottom' => __( 'Padding from bottom inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-left' => __( 'Padding from left inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-right' => __( 'Padding from right inside button', 'wish-wait-list-for-woocommerce' ),
    );
    $color_field = array( 'color', 'background-color', 'border-color' );
    $size_field = array( 'border-width', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width',
        'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
        'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
        'margin-top', 'margin-bottom', 'margin-left', 'margin-right', 'top', 'bottom', 'left', 'right',
        'width', 'height', 'max-height', 'max-width', 'line-height', 'font-size', 'border-radius' );
    foreach($style_row as $row => $row_name) {
        echo '<tr>';
        echo '<th>'.$row_name.'</th>';
        foreach($style_column as $column) {
            $name = $settings_name.'[style_settings][styles]['.$column.']['.$row.']';
            $data_option = (isset($options['styles'][$column][$row]) ? $options['styles'][$column][$row] : '');
            echo '<td>';
            if( in_array($row, $color_field) ) {
                echo br_color_picker($name, $data_option, -1);
            }
            if( in_array($row, $size_field) ) {
                ?>
                <input type="text" value="<?php echo $data_option; ?>" name="<?php echo $name; ?>">
                <?php
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<a href="#" class="button ww_reset_styles"><?php _e( 'Reset Button Styles to Default', 'wish-wait-list-for-woocommerce' ) ?></a>
</div>
<div class="ww_reset_block">
<table class="berocket_ww_position_table">
    <thead><tr><th colspan="9"><h3 class="button br_show_hide_table"><?php _e( 'Wish/Wait Twin Buttons Style', 'wish-wait-list-for-woocommerce' ) ?><i class="fa fa-chevron-down"></i></h3></th></tr></thead>
    <tbody style="display: none;">
    <tr>
        <th></th>
        <th><?php _e( 'First Button', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Last Button', 'wish-wait-list-for-woocommerce' ) ?></th>
    </tr>
    <?php 
    $style_column = array('first_button', 'last_button');
    $style_row = array(
        'width' => __( 'Width', 'wish-wait-list-for-woocommerce' ),
        'padding-top' => __( 'Padding from top inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-bottom' => __( 'Padding from bottom inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-left' => __( 'Padding from left inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-right' => __( 'Padding from right inside button', 'wish-wait-list-for-woocommerce' ),
        'margin-top' => __( 'Margin from top', 'wish-wait-list-for-woocommerce' ),
        'margin-bottom' => __( 'Margin from bottom', 'wish-wait-list-for-woocommerce' ),
        'margin-left' => __( 'Margin from left', 'wish-wait-list-for-woocommerce' ),
        'margin-right' => __( 'Margin from right', 'wish-wait-list-for-woocommerce' ),
    );
    $color_field = array( 'color', 'background-color', 'border-color' );
    $size_field = array( 'border-width', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width',
        'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
        'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
        'margin-top', 'margin-bottom', 'margin-left', 'margin-right', 'top', 'bottom', 'left', 'right',
        'width', 'height', 'max-height', 'max-width', 'line-height', 'font-size', 'border-radius' );
    foreach($style_row as $row => $row_name) {
        echo '<tr>';
        echo '<th>'.$row_name.'</th>';
        foreach($style_column as $column) {
            $name = $settings_name.'[style_settings][styles]['.$column.']['.$row.']';
            $data_option = (isset($options['styles'][$column][$row]) ? $options['styles'][$column][$row] : '');
            echo '<td>';
            if( in_array($row, $color_field) ) {
                echo br_color_picker($name, $data_option, -1);
            }
            if( in_array($row, $size_field) ) {
                ?>
                <input type="text" value="<?php echo $data_option; ?>" name="<?php echo $name; ?>">
                <?php
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<a href="#" class="button ww_reset_styles"><?php _e( 'Reset Button Styles to Default', 'wish-wait-list-for-woocommerce' ) ?></a>
</div>
<?php foreach(array('wish', 'wait') as $list_type) { ?>
<div class="ww_reset_block ww_reset_block_<?php echo $list_type; ?>">

<table class="berocket_ww_position_table">
    <thead><tr><th colspan="9"><h3 class="button br_show_hide_table"><?php ($list_type == 'wish' ? _e( 'Wish list styles', 'wish-wait-list-for-woocommerce' ) : _e( 'Wait list styles', 'wish-wait-list-for-woocommerce' )); ?><i class="fa fa-chevron-down"></i></h3></th></tr></thead>
    <tbody style="display:none;">
    <tr>
        <th></th>
        <th><?php _e( 'Products block', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Product block', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Product name', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Product name on mouse over', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Price', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Out of stock', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Remove button', 'wish-wait-list-for-woocommerce' ) ?></th>
        <th><?php _e( 'Remove button on mouse over', 'wish-wait-list-for-woocommerce' ) ?></th>
    </tr>
    <?php 
    $style_column = array(
        $list_type.'_products' => array(),
        $list_type.'_product' => array(),
        $list_type.'_product_name' => array(),
        $list_type.'_product_name_hover' => array(),
        $list_type.'_product_price' => array(),
        $list_type.'_out_of_stock' => array(),
        $list_type.'_remove_button' => array(),
        $list_type.'_remove_button_hover' => array()
    );
    $style_row = array(
        'font-size' => __( 'Font size', 'wish-wait-list-for-woocommerce' ), 
        'color' => __( 'Font color', 'wish-wait-list-for-woocommerce' ), 
        'background-color' => __( 'Background color', 'wish-wait-list-for-woocommerce' ), 
        'border-width' => __( 'Border width', 'wish-wait-list-for-woocommerce' ), 
        'border-color' => __( 'Border color', 'wish-wait-list-for-woocommerce' ), 
        'border-radius' => __( 'Border radius', 'wish-wait-list-for-woocommerce' ), 
        'width' => __( 'Width', 'wish-wait-list-for-woocommerce' ), 
        'height' => __( 'Height', 'wish-wait-list-for-woocommerce' ),
        'max-height' => __( 'Maximum height', 'wish-wait-list-for-woocommerce' ),
        'padding-top' => __( 'Padding from top inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-bottom' => __( 'Padding from bottom inside', 'wish-wait-list-for-woocommerce' ),
        'padding-left' => __( 'Padding from left inside button', 'wish-wait-list-for-woocommerce' ),
        'padding-right' => __( 'Padding from right inside button', 'wish-wait-list-for-woocommerce' ),
        'margin-top' => __( 'Margin from top', 'wish-wait-list-for-woocommerce' ),
        'margin-bottom' => __( 'Margin from bottom', 'wish-wait-list-for-woocommerce' ),
        'margin-left' => __( 'Margin from left', 'wish-wait-list-for-woocommerce' ),
        'margin-right' => __( 'Margin from right', 'wish-wait-list-for-woocommerce' ),
    );
    foreach($style_row as $row => $row_name) {
        echo '<tr>';
        echo '<th>'.$row_name.'</th>';
        foreach($style_column as $column => $not_display) {
            if( ! in_array($row, $not_display) ) {
                $name = $settings_name.'[style_settings][styles]['.$column.']['.$row.']';
                $data_option = (isset($options['styles'][$column][$row]) ? $options['styles'][$column][$row] : '');
                echo '<td>';
                if( in_array($row, $color_field) ) {
                    echo br_color_picker($name, $data_option, -1);
                }
                if( in_array($row, $size_field) ) {
                    ?>
                    <input type="text" value="<?php echo $data_option; ?>" name="<?php echo $name; ?>">
                    <?php
                }
                echo '</td>';
            } else {
                echo '<td></td>';
            }
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<a href="#" class="button ww_reset_styles"><?php ($list_type == 'wish' ? _e( 'Reset Wish List Styles to Default', 'wish-wait-list-for-woocommerce' ) : _e( 'Reset Wait List Styles to Default', 'wish-wait-list-for-woocommerce' )); ?></a>
</div>
<?php } ?>
