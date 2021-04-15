<?php
/**
 * Our activation call.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Activate;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;
use Nexcess\WooZeroOrdersAlert\Process\Cron as ProcessCron;

/**
 * Our inital setup function when activated.
 *
 * @return void
 */
function activate() {

	// Do the check for WooCommerce being active.
	check_active_woo();

	// First clear any possible existing cron and set the next one.
	ProcessCron\set_ongoing_order_check( true );

	// And set the last checked stamp.
	Utilities\set_initial_checked_stamp();

	// Include our action so that we may add to this later.
	do_action( Core\HOOK_PREFIX . 'activate_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( Core\FILE, __NAMESPACE__ . '\activate' );

/**
 * Handle checking if WooCommerce is present and activated.
 *
 * @return void
 */
function check_active_woo() {

	// If we weren't false, we are OK.
	if ( false !== class_exists( 'woocommerce' ) ) {
		return;
	}

	// Deactivate the plugin.
	deactivate_plugins( Core\BASE );

	// And display the notice.
	wp_die( sprintf( __( 'Using the WooCommerce Zero Orders Alert plugin requires that you have WooCommerce installed and activated. <a href="%s">Click here</a> to return to the plugins page.', 'woo-zero-orders-alert' ), admin_url( '/plugins.php' ) ) );
}

