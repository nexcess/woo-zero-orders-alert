<?php
/**
 * The queries and comparisons for our orders.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Process\Orders;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;

/**
 * Get all the orders from the previous day.
 *
 * @param  boolean $purge_cache  Optional to purge the cache'd version before looking up.
 *
 * @return mixed
 */
function fetch_previous_day_orders( $purge_cache = false ) {

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
		$fetch_query_stamps = Utilities\get_order_check_timestamps();

		// Set the args for a specific lookup.
		$setup_single_args  = array(
			'limit'        => -1,
			'type'         => 'shop_order',
			'return'       => 'ids',
			'status'       => array( 'wc-completed' ),
			'date_created' => $fetch_query_stamps['query'],
		);

		// Now run our lookup.
		$run_query_lookup   = new \WC_Order_Query( $setup_single_args );

		// Bail out if none exist.
		if ( empty( $run_query_lookup ) || is_wp_error( $run_query_lookup ) ) {
			return false;
		}

		// Now fetch all the orders.
		$fetch_batch_orders = $run_query_lookup->get_orders();

		// Return "none" if we have none, so it's easy to compare.
		if ( empty( $fetch_batch_orders ) ) {
			return 'none';
		}

		// Set our transient with our data.
		set_transient( $ky, $fetch_batch_orders, HOUR_IN_SECONDS );

		// And change the variable to do the things.
		$cached_dataset = $fetch_batch_orders;
	}

	// Return the order count.
	return count( $cached_dataset );
}
