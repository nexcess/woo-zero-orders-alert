<?php
/**
 * Run our order count checks.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Admin\Cron;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Start our engines.
 */
add_filter( 'cron_schedules', __NAMESPACE__ . '\add_cron_schedule' );
add_action( Core\ORDER_CHECK_CRON, __NAMESPACE__ . '\maybe_run_order_check' );

/**
 * Add our custom cron schedule time.
 *
 * @param  array $schedules  The existing array of times.
 *
 * @return void
 *
 */
function add_cron_schedule( $schedules ) {

	// Set the 4 hour interval.
	$set_custom_intval   = HOUR_IN_SECONDS * 6;

	// And set the array.
	$schedules['sixhrs'] = array(
		'interval' => absint( $set_custom_intval ),
		'display'  => __( 'Every 6 Hours' )
	);

	// And return the resulting array.
	return $schedules;
}

/**
 * Our cron job to check the orders.
 *
 * @return void
 */
function maybe_run_order_check() {

	// This will contain the code for handling our order checks.
}
