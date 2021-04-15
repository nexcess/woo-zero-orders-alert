<?php
/**
 * Handle our different notification methods.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Process\Notifications;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Helpers as Helpers;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;

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
	foreach ( $get_configured_alerts as $alert_type ) {

		// Now run through each one that we built.
		switch ( sanitize_text_field( $alert_type ) ) {

			// Run our emailer.
			case 'email' :
				\Nexcess\WooZeroOrdersAlert\AlertTypes\Email\send_email_alert();
				break;

			// Run our Woo inbox.
			case 'inbox' :
				\Nexcess\WooZeroOrdersAlert\AlertTypes\Inbox\send_inbox_alert();
				break;
		}

		// Allow a custom ones we don't know of yet.
		do_action( Core\HOOK_PREFIX . "send_alert_{$alert_type}" );
	}

	// Include an action for after all alerts are processed.
	do_action( Core\HOOK_PREFIX . 'after_alerts_sent' );

	// I think this is all we need to do here?
}
