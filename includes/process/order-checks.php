<?php
/**
 * The queries and comparisons for our orders.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Process\OrderChecks;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;

/**
 * Run the comparison of the orders.
 *
 * @return boolean
 */
function process_order_comparison() {

	// First we fetch the order count.
	$get_order_nums = fetch_previous_day_orders();

	// If we had no orders, it does not
	// matter what the minimum is.
	if ( empty( $get_order_nums ) ) {
		return false;
	}

	// Get the minimum value we stored.
	$get_min_value  = get_option( Core\OPTION_PREFIX . 'min_val', 5 );

	// Do our check and return a boolean.
	return absint( $get_order_nums ) >= absint( $get_min_value ) ? true : false;
}

/**
 * Get all the orders from the previous day.
 *
 * @param  boolean $return_counts  Whether or not to return the count or the IDs.
 * @param  boolean $purge_cache    Optional to purge the cache'd version before looking up.
 *
 * @return mixed
 */
function fetch_previous_day_orders( $return_counts = true, $purge_cache = false ) {

	// Set the key to use in our transient.
	$ky = Core\TRANSIENT_PREFIX . 'prev_day_orders';

	// If we don't want the cache'd version, delete the transient first.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG || ! empty( $purge_cache ) ) {
		delete_transient( $ky );
	}

	// Attempt to get the reviews from the cache.
	$cached_dataset = get_transient( $ky );

	// If we have none, do the things.
	if ( false === $cached_dataset ) {

		// Get the today timestamp.
		$define_today_stamp = Utilities\get_today_timestamp();

		// Subtract a day from today to set our starting.
		$define_start_stamp = absint( $define_today_stamp ) - DAY_IN_SECONDS;

		// Set the args for a specific lookup.
		$setup_single_args  = array(
			'limit'        => -1,
			'type'         => 'shop_order',
			'return'       => 'ids',
			'status'       => array( 'wc-completed' ),
			'date_created' => $define_start_stamp . '...' . $define_today_stamp,
		);

		// Now run our lookup.
		$run_query_lookup   = new \WC_Order_Query( $setup_single_args );

		// Bail out if none exist.
		if ( empty( $run_query_lookup ) || is_wp_error( $run_query_lookup ) ) {
			return false;
		}

		// Now fetch all the orders.
		$fetch_batch_orders = $run_query_lookup->get_orders();

		// Return an actual zero or "none" if we have none.
		if ( empty( $fetch_batch_orders ) ) {
			return false !== $return_count ? 'none' : 0;
		}

		// Set our transient with our data.
		set_transient( $ky, $fetch_batch_orders, HOUR_IN_SECONDS );

		// And change the variable to do the things.
		$cached_dataset = $fetch_batch_orders;
	}

	// Return the entire dataset or just the counts.
	return false !== $return_counts ? $cached_dataset : count( $cached_dataset );
}
