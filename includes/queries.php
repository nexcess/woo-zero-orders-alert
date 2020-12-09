<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Queries;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Get all the orders from the previous day.
 *
 * @param  boolean $return_count  Whether or not to return the count or the IDs.
 * @param  boolean $purge_cache   Optional to purge the cache'd version before looking up.
 *
 * @return mixed
 */
function fetch_previous_day_orders( $return_count = true, $purge_cache = false ) {

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

		// Set the two timestamps we need.
		// First set today as a formatted date.
		$setup_today_format = date( 'Y-m-d' );

		// Then flip it back to converted back to a stamp.
		$define_today_stamp = strtotime( $setup_today_format . ' 00:00:00' );
		$define_start_stamp = absint( $define_today_stamp ) - DAY_IN_SECONDS;

		// Set the args for a specific lookup.
		$setup_single_args  = array(
			'limit'        => -1,
			'orderby'      => 'date',
			'order'        => 'ASC',
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
		set_transient( $ky, $fetch_batch_orders, ( MINUTE_IN_SECONDS * 10 ) );

		// And change the variable to do the things.
		$cached_dataset = $fetch_batch_orders;
	}

	// Return the entire dataset or just the counts.
	return false !== $return_count ? $cached_dataset : count( $cached_dataset );
}
