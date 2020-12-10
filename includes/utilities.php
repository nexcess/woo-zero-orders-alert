<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Utilities;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;

/**
 * Get the user cap for the actions.
 *
 * @return string
 */
function get_user_cap() {
	return apply_filters( Core\HOOK_PREFIX . 'user_menu_cap', 'manage_options' );
}

/**
 * Get our section settings link.
 *
 * @return string
 */
function get_woo_section_settings_link() {

	// Bail if we aren't on the admin side.
	if ( ! is_admin() ) {
		return false;
	}

	// Set the args.
	$set_link_args  = array(
		'page'    => 'wc-settings',
		'tab'     => 'products',
		'section' => Core\MENU_SLUG,
	);

	// Return the link with our args.
	return add_query_arg( $set_link_args, admin_url( 'admin.php' ) );
}

/**
 * Get the timestamp of today at midnight.
 *
 * @return integer
 */
function get_today_timestamp() {

	// Set today as a formatted date.
	$setup_today_format = date( 'Y-m-d' );

	// Then add the zero'd time portion, and flip it back to a timestamp.
	$define_today_stamp = strtotime( $setup_today_format . ' 00:00:00' );

	// Return it as an integer.
	return absint( $define_today_stamp );
}

/**
 * Set the updated timestamp for the next check.
 *
 * @return void
 */
function set_next_check_stamp() {

	// Get my timestamp for today.
	$get_today_stamp    = get_today_timestamp();

	// And update my option.
	update_option( Core\OPTION_PREFIX . 'last_checked', absint( $get_today_stamp ), 'no' );
}
