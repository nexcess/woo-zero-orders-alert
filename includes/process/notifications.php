<?php
/**
 * Handle our different notification methods.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Process\Notifications;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;

// And pull in any other namespaces.
use WP_Error;

/**
 * Handle our various notification methods.
 *
 * @return void
 */
function process_minimum_orders_alerts() {

	// Check for the configured alerts.
	$get_configured_alerts  = Helpers\maybe_alerts_configured();

	// Bail without any alerts configured.
	if ( empty( $get_configured_alerts ) ) {
		return new WP_Error( 'no-configured-alerts', __( 'No alert settings have been configured.', 'woo-minimum-order-alerts' ) );
	}

	// Include an action for before any alerts are processed.
	do_action( Core\HOOK_PREFIX . 'before_alerts_sent' );

	// Now loop and run each known alert type.
	foreach ( $get_configured_alerts as $alert_type => $alert_flag ) {

		// Only do this for a "yes" value.
		if ( 'yes' !== sanitize_text_field( $alert_flag ) ) {
			continue;
		}

		// Now run each known.
		switch ( sanitize_text_field( $alert_type ) ) {

			// Run our emailer.
			case 'email' :
				send_alert_via_email();
				break;

			// This is really an unknown.
			case 'other' :
				send_alert_via_other();
				break;
		}

		// Allow a custom one we don't know of yet.
		do_action( Core\HOOK_PREFIX . "send_alert_{$alert_type}" );
	}

	// Include an action for after all alerts are processed.
	do_action( Core\HOOK_PREFIX . 'after_alerts_sent' );

	// I think this is all we need to do here?
}

/**
 * Send a generic email that a target was missed.
 *
 * @return void
 */
function send_alert_via_email() {

}

/**
 * Handle our nebulous one.
 *
 * @return void
 */
function send_alert_via_other() {

}
