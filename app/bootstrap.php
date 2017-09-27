<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 9/15/17
 * Time: 11:38 AM
 */

require_once 'ProductManager.php';
require_once 'OrderManager.php';
require_once 'CustomerManager.php';


/*
 * Install new statuses
 *
 * (i) Pending Dispatch
 * (ii) Courier Collection Requested
 * (iii) Courier Collection Request Received
 * (iv) Order Collected
 * (v) Order Delivered
 * (vi) Order Completed
 * (vii) Order Invoiced
 * (viii) Order Returned
 * (ix) Order Cancelled
 */

add_action( 'init', 'register_my_new_order_statuses' );

function register_my_new_order_statuses() {
	register_post_status( 'wc-invoiced', array(
		'label'                     => _x( 'Invoiced', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Invoiced <span class="count">(%s)</span>', 'Invoiced<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-pending-dispatch', array(
		'label'                     => _x( 'Pending Dispatch', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Pending Dispatch <span class="count">(%s)</span>', 'Pending Dispatch<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-courier-collection-requested', array(
		'label'                     => _x( 'Courier Collection Requested', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Courier Collection Requested <span class="count">(%s)</span>', 'Courier Collection Requested<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-courier-collection-request-received', array(
		'label'                     => _x( 'Courier Collection Request Received', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Courier Collection Request Received <span class="count">(%s)</span>', 'Courier Collection Request Received<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-collected', array(
		'label'                     => _x( 'Collected', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Collected <span class="count">(%s)</span>', 'Collected<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-delivered', array(
		'label'                     => _x( 'Delivered', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered<span class="count">(%s)</span>', 'woocommerce' )
	) );
	register_post_status( 'wc-returned', array(
		'label'                     => _x( 'Returned', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Returned <span class="count">(%s)</span>', 'Returned<span class="count">(%s)</span>', 'woocommerce' )
	) );
}

add_filter( 'wc_order_statuses', 'my_new_wc_order_statuses' );

// Register in wc_order_statuses.
function my_new_wc_order_statuses( $order_statuses ) {
	$order_statuses['wc-invoiced']                            = _x( 'Invoiced', 'Order status', 'woocommerce' );
	$order_statuses['wc-pending-dispatch']                    = _x( 'Pending Dispatch', 'Order status', 'woocommerce' );
	$order_statuses['wc-courier-collection-requested']        = _x( 'Courier Collection Requested', 'Order status', 'woocommerce' );
	$order_statuses['wc-courier-collection-request-received'] = _x( 'Courier Collection Request Received', 'Order status', 'woocommerce' );
	$order_statuses['wc-collected']                           = _x( 'Collected', 'Order status', 'woocommerce' );
	$order_statuses['wc-delivered']                           = _x( 'Delivered', 'Order status', 'woocommerce' );
	$order_statuses['wc-returned']                            = _x( 'Returned', 'Order status', 'woocommerce' );

	return $order_statuses;
}


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