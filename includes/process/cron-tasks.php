<?php
/**
 * Run our order count checks.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Process\CronTasks;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;

/**
 * Start our engines.
 */
add_filter( 'cron_schedules', __NAMESPACE__ . '\add_sixhrs_cron_schedule' );
add_action( Core\ORDER_CHECK_CRON, __NAMESPACE__ . '\maybe_run_order_check' );

/**
 * Add our custom 6 hour cron schedule time.
 *
 * @param  array $schedules  The existing array of times.
 *
 * @return void
 *
 */
function add_sixhrs_cron_schedule( $schedules ) {

	// Set the 6 hour interval.
	$set_custom_intval   = HOUR_IN_SECONDS * 6;

	// And set the array.
	$schedules['sixhrs'] = array(
		'interval' => absint( $set_custom_intval ),
		'display'  => __( 'Every 6 Hours', 'woo-minimum-order-alerts' )
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
