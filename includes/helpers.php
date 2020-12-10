<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Helpers;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;

/**
 * Check our timestamp against the last stored.
 *
 * @return boolean
 */
function maybe_last_checked_passed() {

	// Get the last checked.
	$get_last_checked   = get_option( Core\OPTION_PREFIX . 'last_checked', false );

	// If we don't have a stamp, assume it's passed.
	if ( empty( $get_last_checked ) ) {
		return true;
	}

	// Now add a day.
	$set_check_compare = absint( $get_last_checked ) + DAY_IN_SECONDS;

	// Get the right now.
	$get_current_stamp  = current_time( 'timestamp', 1 );

	// Return the result.
	return absint( $get_current_stamp ) >= absint( $set_check_compare ) ? true : false;
}

/**
 * Check to see what alert configurations we have.
 *
 * @return mixed
 */
function maybe_alerts_configured() {

	// Grab the stored.
	$check_alert_types  = get_option( Core\OPTION_PREFIX . 'alert_types', false );

	// Return a basic array of the ones we have.
	return ! empty( $check_alert_types ) ? $check_alert_types : false;
}
