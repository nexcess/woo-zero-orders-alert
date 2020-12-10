<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Utilities;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;

/**
 * Get the user cap for the actions.
 *
 * @return string
 */
function get_user_cap() {
	return apply_filters( Core\HOOK_PREFIX . 'user_menu_cap', 'manage_options' );
}

/**
 * Take our existing cron job and update or remove the schedule.
 *
 * @param  boolean $clear      Whether to remove the existing one.
 * @param  string  $frequency  The new frequency we wanna use.
 *
 * @return void
 */
function modify_order_check_cron( $clear = true, $frequency = 'sixhrs' ) {

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
