<?php
/**
 * Our activation call.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Activate;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;

/**
 * Our inital setup function when activated.
 *
 * @return void
 */
function activate() {

	// Do the check for WooCommerce being active.
	check_active_woo();

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

	// Pull the function check.
	$maybe_woo  = Helpers\maybe_woo_activated();

	// If we weren't false, we are OK.
	if ( false !== $maybe_woo ) {
		return;
	}

	// Deactivate the plugin.
	deactivate_plugins( Core\BASE );

	// And display the notice.
	wp_die( sprintf( __( 'Using the WooCommerce Minimum Daily Orders plugin required that you have WooCommerce installed and activated. <a href="%s">Click here</a> to return to the plugins page.', 'woo-minimum-daily-orders' ), admin_url( '/plugins.php' ) ) );
}
