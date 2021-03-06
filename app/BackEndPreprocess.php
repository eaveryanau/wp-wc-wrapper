<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/27/17
 * Time: 12:44 PM
 */

// Add plugin admin page.
$hub_page = 'hubparameters.php';

/*
 * Function that adds a page to the Settings menu item
 */
function hub_options() {
	global $hub_page;
	add_menu_page( 'Hub settings', 'Hub settings', 'manage_options', $hub_page, 'hub_option_page' );
}

add_action( 'admin_menu', 'hub_options' );

// Deafult shipping methods
function delete_default_shipping_methods($methods) {

    unset($methods['local_pickup']);
    unset($methods['flat_rate']);

    return $methods;
}

add_filter('woocommerce_shipping_methods', 'delete_default_shipping_methods');

/**
 * Callback
 */
function hub_option_page() {
	global $hub_page;
	?>
    <div class="wrap">
    <h2>Hub settings</h2>
    <form method="post" enctype="multipart/form-data" action="options.php">
		<?php
		settings_fields( 'hub_options' );
		do_settings_sections( $hub_page );
		?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>"/>
        </p>
    </form>
    </div><?php
}

/*
 * Register settings
 */
function hub_option_settings() {
	global $hub_page;
	// Add validate
	register_setting( 'hub_options', 'hub_options', 'hub_validate_settings' ); // true_options

	// Add setion
	add_settings_section( 'hub_section_1', 'Secure section', '', $hub_page );

	// Add field for secret key
	$hub_field_params = array(
		'type'      => 'password',
		'id'        => 'hub_secret_key',
		'desc'      => 'Hub secret key.',
		'label_for' => 'hub_secret_key'
	);
	add_settings_field( 'my_text_field', 'Hub secret key.', 'hub_option_display_settings', $hub_page, 'hub_section_1', $hub_field_params );

}

add_action( 'admin_init', 'hub_option_settings' );

/*
 * Output part
 */
function hub_option_display_settings( $args ) {
	extract( $args );

	$option_name = 'hub_options';

	$o = get_option( $option_name );

	switch ( $type ) {
		case 'text':
			$o[ $id ] = esc_attr( stripslashes( $o[ $id ] ) );
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
			echo ( $desc != '' ) ? "<br /><span class='description'>$desc</span>" : "";
			break;
		case 'password':
			$o[ $id ] = esc_attr( stripslashes( $o[ $id ] ) );
			echo "<input class='regular-text' type='password' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
			echo ( $desc != '' ) ? "<br /><span class='description'>$desc</span>" : "";
			break;
		case 'textarea':
			$o[ $id ] = esc_attr( stripslashes( $o[ $id ] ) );
			echo "<textarea class='code large-text' cols='50' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";
			echo ( $desc != '' ) ? "<br /><span class='description'>$desc</span>" : "";
			break;
		case 'checkbox':
			$checked = ( $o[ $id ] == 'on' ) ? " checked='checked'" : '';
			echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";
			echo ( $desc != '' ) ? $desc : "";
			echo "</label>";
			break;
		case 'select':
			echo "<select id='$id' name='" . $option_name . "[$id]'>";
			foreach ( $vals as $v => $l ) {
				$selected = ( $o[ $id ] == $v ) ? "selected='selected'" : '';
				echo "<option value='$v' $selected>$l</option>";
			}
			echo ( $desc != '' ) ? $desc : "";
			echo "</select>";
			break;
		case 'radio':
			echo "<fieldset>";
			foreach ( $vals as $v => $l ) {
				$checked = ( $o[ $id ] == $v ) ? "checked='checked'" : '';
				echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
			}
			echo "</fieldset>";
			break;
	}
}

/*
 * Validate function
 */
function hub_validate_settings( $input ) {
	foreach ( $input as $k => $v ) {
		$valid_input[ $k ] = trim( $v );
	}

	return $valid_input;
}

add_action( 'woocommerce_settings_general_options_after', function(){
    woocommerce_admin_fields( array(
        array(
            'name' => __( 'Order prefix', 'woocommerce' ),
            'type' => 'title',
            'id' => 'wc_order_number_options'
        ),
        array(
            'name' 		=> __( 'Order number prefix', 'woocommerce' ),
            'desc' 		=> __( 'Add prefix to order number', 'woocommerce' ),
            'id' 		=> 'wc_order_prefix',
            'type' 		=> 'text',
        ),
        array( 'type' => 'sectionend', 'id' => 'wc_order_number_options' ),
    ) );
}, 20 );

add_action( 'woocommerce_update_options_general',  'order_prefix_admin_settings' );

function order_prefix_admin_settings(){
    woocommerce_update_options( array(
        array(
            'name' => __( 'Order prefix', 'woocommerce' ),
            'type' => 'title',
            'id' => 'wc_order_number_options'
        ),
        array(
            'name' 		=> __( 'Order prefix', 'woocommerce' ),
            'desc' 		=> __( 'Add prefix to order number', 'woocommerce' ),
            'id' 		=> 'wc_order_prefix',
            'type' 		=> 'text',
        ),
        array( 'type' => 'sectionend', 'id' => 'wc_order_number_options' ),
    ) );
}

add_filter('woocommerce_order_number', function ($order_id){
    $prefix = get_option( 'wc_order_prefix', true );

    return ($prefix && $prefix !== '1' ) ? $prefix . $order_id : $order_id;
});
