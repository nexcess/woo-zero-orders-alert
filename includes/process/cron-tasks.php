<?php
/**
 * Configure and run the triggers on the cron job.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Process\CronTasks;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;
use Nexcess\WooMinimumOrderAlerts\Process\OrderChecks as OrderChecks;
use  Nexcess\WooMinimumOrderAlerts\Process\Notifications as Notifications;

/**
 * Start our engines.
 */
add_filter( 'cron_schedules', __NAMESPACE__ . '\add_custom_cron_schedule' );
add_action( Core\ORDER_CHECK_CRON, __NAMESPACE__ . '\check_order_compare_schedule' );

/**
 * Add our custom 8 hour cron schedule time.
 *
 * @param  array $schedules  The existing array of times.
 *
 * @return void
 *
 */
function add_custom_cron_schedule( $schedules ) {

	// Set the 8 hour interval.
	$set_custom_intval   = HOUR_IN_SECONDS * 8;

	// And set the array.
	$schedules['eight-hrs'] = array(
		'interval' => absint( $set_custom_intval ),
		'display'  => __( 'Every 8 Hours', 'woo-minimum-order-alerts' )
	);

	// And return the resulting array.
	return $schedules;
}

/**
 * Take our existing cron job and update or remove the schedule.
 *
 * @param  boolean $clear      Whether to remove the existing one.
 * @param  string  $frequency  The new frequency we wanna use.
 *
 * @return void
 */
function modify_order_check_cron( $clear = true, $frequency = 'eight-hrs' ) {

	// Pull in the existing one and remove it.
	if ( ! empty( $clear ) ) {

		// Grab the next scheduled stamp.
		$timestamp  = wp_next_scheduled( Core\ORDER_CHECK_CRON );

		// Remove it from the schedule.
		wp_unschedule_event( $timestamp, Core\ORDER_CHECK_CRON );
	}

	// Now schedule our new one, assuming we passed a new frequency.
	if ( ! empty( $frequency ) ) {
		wp_schedule_event( current_time( 'timestamp' ), sanitize_text_field( $frequency ), Core\ORDER_CHECK_CRON );
	}
}

/**
 * Our cron job that checks to see if
 * the time has passed to run a check.
 *
 * @return void
 */
function check_order_compare_schedule() {

	// Run our timestamp comparisons first.
	$maybe_time_passed  = Helpers\maybe_last_checked_passed();

	// If it isn't time yet, don't do it.
	if ( false === $maybe_time_passed ) {
		return;
	}

	// Compare the orders.
	$check_order_target = OrderChecks\process_order_comparison();

	// If we came back true, then we hit the target.
	// Good job, everyone!
	if ( false !== $check_order_target ) {
		return;
	}

	// Handle any of our notifications.
	Notifications\process_minimum_orders_alerts();
}
