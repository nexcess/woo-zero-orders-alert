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

	// Grab the two known alert types.
	$check_alert_email  = get_option( Core\OPTION_PREFIX . 'alert_email', false );
	$check_alert_other  = get_option( Core\OPTION_PREFIX . 'alert_other', false );

	// Now check each one for the "yes" value.
	$maybe_alert_email  = ! empty( $check_alert_email ) && 'yes' === sanitize_text_field( $check_alert_email ) ? 'yes' : 'no';
	$maybe_alert_other  = ! empty( $check_alert_other ) && 'yes' === sanitize_text_field( $check_alert_other ) ? 'yes' : 'no';

	// If both are empty, return false.
	if ( 'no' === $maybe_alert_email && 'no' === $maybe_alert_other ) {
		return false;
	}

	// Return a basic array of the ones we have.
	return array(
		'email' => $maybe_alert_email,
		'other' => $maybe_alert_other,
	);
}
