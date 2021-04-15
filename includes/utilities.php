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

/**
 * Determine if we should run an order check.
 *
 * @return boolean
 */
function maybe_run_order_check() {

	// Get the last date checked.
	$get_last_checked   = get_option( Core\OPTION_PREFIX . 'last_checked', 0 );

	// Bail without a last checked.
	if ( empty( $get_last_checked ) ) {
		return false;
	}

	// Add a day to the stamp for comparison.
	$define_check_stamp = absint( $get_last_checked ) + DAY_IN_SECONDS;

	// Return the result.
	return time() > absint( $define_check_stamp ) ? true : false;
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
 * Get the timestamp of yesterday at midnight.
 *
 * @return integer
 */
function get_yesterday_timestamp() {

	// Pull today's timestamp.
	$define_today_stamp = get_today_timestamp();

	// Subtract the day and return it.
	return absint( $define_today_stamp ) - DAY_IN_SECONDS;
}

/**
 * A simple wrapper function to return both stamps.
 *
 * @return array
 */
function get_order_check_timestamps() {

	// Pull today's timestamp.
	$define_today_stamp = get_today_timestamp();

	// Subtract the day and return it.
	$define_start_stamp = absint( $define_today_stamp ) - DAY_IN_SECONDS;

	// Return an array.
	return array(
		'today' => $define_today_stamp,
		'start' => $define_start_stamp,
	);
}

/**
 * Handle setting the last checked timestamp.
 */
function set_last_checked_stamp() {

	// Get the today timestamp.
	$define_today_stamp = get_today_timestamp();

	// Set our initial options in the DB.
	update_option( Core\OPTION_PREFIX . 'last_checked', $define_today_stamp, 'no' );
}

