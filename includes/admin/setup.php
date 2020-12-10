<?php
/**
 * Handle our initial admin setup.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Admin\Setup;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Start our engines.
 */
add_action( Core\HOOK_PREFIX . 'activate_process', __NAMESPACE__ . '\run_on_activate' );
add_action( Core\HOOK_PREFIX . 'deactivate_process', __NAMESPACE__ . '\run_on_deactivate' );
add_action( Core\HOOK_PREFIX . 'uninstall_process', __NAMESPACE__ . '\run_on_uninstall' );

/**
 * Handle some of our initial setup.
 *
 * @return void
 */
function run_on_activate() {

	// Schedule our cron job assuming it isn't there already.
	if ( ! wp_next_scheduled( Core\ORDER_CHECK_CRON ) ) {
		Utilities\modify_order_check_cron( false );
	}

	// Get the today timestamp.
	$define_today_stamp = Helpers\get_today_timestamp();

	// Set our initial options in the DB.
	update_option( Core\OPTION_PREFIX . 'last_checked', $define_today_stamp, 'no' );
	update_option( Core\OPTION_PREFIX . 'min_val', 5, 'no' );
	update_option( Core\OPTION_PREFIX . 'alert_email', 'yes', 'no' );
	update_option( Core\OPTION_PREFIX . 'alert_other', 'no', 'no' );
}

/**
 * Delete some of the initial options we set.
 *
 * @return void
 */
function run_on_deactivate() {

	// Pull in our scheduled cron and unschedule it.
	Utilities\modify_order_check_cron( true, false );

	// If we want to change options on deactivate, do it here.
}

/**
 * Purge out the settings and reset our cron.
 *
 * @return void
 */
function run_on_uninstall() {

	// Pull in our scheduled cron and unschedule it.
	Utilities\modify_order_check_cron( true, false );

	// Delete the options we set.
	delete_option( Core\OPTION_PREFIX . 'last_checked' );
	delete_option( Core\OPTION_PREFIX . 'min_val' );
	delete_option( Core\OPTION_PREFIX . 'alert_email' );
	delete_option( Core\OPTION_PREFIX . 'alert_other' );
}
