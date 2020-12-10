<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Helpers;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Get the email address we wanna use for our alert.
 *
 * @return string
 */
function get_email_address_for_alert() {

	// Pull the primary email for now.
	$default_email  = get_option( 'admin_email' );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_address', $default_email );
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
