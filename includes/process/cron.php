<?php
/**
 * Configure and run the triggers on the cron job.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Process\Cron;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;
use Nexcess\WooZeroOrdersAlert\Process\Orders as ProcessOrders;
use Nexcess\WooZeroOrdersAlert\Process\Email as ProcessEmail;

/**
 * Start our engines.
 */
add_filter( 'cron_schedules', __NAMESPACE__ . '\add_custom_cron_schedule' );
add_action( Core\ORDER_CHECK_CRON, __NAMESPACE__ . '\run_order_check_cron' );

/**
 * Add our custom 8 hour cron schedule time.
 *
 * @param  array $schedules  The existing array of times.
 *
 * @return void
 *
 */
function add_custom_cron_schedule( $schedules ) {

	// Only add it if it doesn't exist.
	if ( ! isset( $schedules['eighthours'] ) ) {

		// Set the 8 hour interval.
		$set_custom_intval   = HOUR_IN_SECONDS * 8;

		// Add our new one.
		$schedules['eighthours'] = array(
			'interval' => absint( $set_custom_intval ),
			'display'  => __( 'Every 8 Hours', 'woo-minimum-order-alerts' ),
		);
	}

	// And return the updated array.
	return $schedules;
}

/**
 * Clear out an existing cron entry before setting a new one.
 *
 * @return void
 */
function clear_existing_cron() {

	// Grab the next scheduled stamp.
	$maybe_has_schedule = wp_next_scheduled( Core\ORDER_CHECK_CRON );

	// If we have one, remove it from the schedule first.
	if ( ! empty( $maybe_has_schedule ) ) {
		wp_unschedule_event( $maybe_has_schedule, Core\ORDER_CHECK_CRON );
	}

	// And be done.
	return;
}

/**
 * Set the cron for the ongoing order checks.
 *
 * @return void
 */
function set_ongoing_order_check( $clear_existing = false ) {

	// Confirm we are clear first.
	if ( false !== $clear_existing ) {
		clear_existing_cron();
	}

	// Set a timestamp to have our first one run.
	$define_start_stamp = time() + HOUR_IN_SECONDS;

	// Now schedule our new one with our custom new frequency.
	wp_schedule_event( absint( $define_start_stamp ), 'eighthours', Core\ORDER_CHECK_CRON );
}

/**
 * The ongoing time check for running the single day check.
 *
 * @return void
 */
function run_order_check_cron() {

	// First check if we need to run the check.
	$maybe_order_check  = Utilities\maybe_run_order_check();

	// Nothing to do if we don't have the check to run.
	if ( false === $maybe_order_check ) {
		return;
	}

	// Fetch the order count.
	$fetch_order_count  = ProcessOrders\fetch_previous_day_orders();

	// If we came back with nothing, we send the notification.
	if ( false === $fetch_order_count || 'none' === sanitize_text_field( $fetch_order_count ) ) {
		ProcessEmail\send_zero_orders_email();
	}

	// Now set the last checked stamp.
	Utilities\set_last_checked_stamp();

	// And we are done here.
	return;
}
