<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Utilities;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Helpers as Helpers;

/**
 * Get the user cap for the actions.
 *
 * @return string
 */
function get_user_cap() {
	return apply_filters( Core\HOOK_PREFIX . 'user_menu_cap', 'manage_options' );
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
