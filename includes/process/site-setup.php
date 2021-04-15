<?php
/**
 * Handle our initial setup.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Process\Setup;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Helpers as Helpers;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;
use Nexcess\WooZeroOrdersAlert\Process\CronTasks as CronTasks;

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

	// First clear any possible existing cron and set the next one.
	CronTasks\set_ongoing_order_check( true );

	// Get the today timestamp.
	$define_today_stamp = Utilities\get_today_timestamp();

	// Set our initial options in the DB.
	update_option( Core\OPTION_PREFIX . 'last_checked', $define_today_stamp, 'no' );
}

/**
 * Delete some of the initial options we set.
 *
 * @return void
 */
function run_on_deactivate() {

	// Pull in our scheduled cron and unschedule it.
	CronTasks\clear_existing_cron( Core\ORDER_CHECK_CRON );

	// If we want to change options on deactivate, do it here.
}

/**
 * Purge out the settings and reset our cron.
 *
 * @return void
 */
function run_on_uninstall() {

	// Pull in our scheduled cron and unschedule it.
	CronTasks\clear_existing_cron( Core\ORDER_CHECK_CRON );

	// Delete the options we set.
	delete_option( Core\OPTION_PREFIX . 'last_checked' );
}
