<?php
/**
 * Our activation call.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Activate;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Process\CronTasks as CronTasks;

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

	// Schedule our cron job assuming it isn't there already.
	if ( ! wp_next_scheduled( Core\ORDER_CHECK_CRON ) ) {
		CronTasks\modify_order_check_cron( false );
	}

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
	wp_die( sprintf( __( 'Using the WooCommerce Minimum Order Alerts plugin requires that you have WooCommerce installed and activated. <a href="%s">Click here</a> to return to the plugins page.', 'woo-minimum-order-alerts' ), admin_url( '/plugins.php' ) ) );
}
