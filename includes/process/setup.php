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
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;
use Nexcess\WooZeroOrdersAlert\Process\Cron as ProcessCron;

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
	ProcessCron\set_ongoing_order_check( true );

	// And set the last checked stamp.
	Utilities\set_last_checked_stamp();
}

/**
 * Delete some of the initial options we set.
 *
 * @return void
 */
function run_on_deactivate() {

	// Pull in our scheduled cron and unschedule it.
	ProcessCron\clear_existing_cron( Core\ORDER_CHECK_CRON );

	// If we want to change options on deactivate, do it here.
}

/**
 * Purge out the settings and reset our cron.
 *
 * @return void
 */
function run_on_uninstall() {

	// Pull in our scheduled cron and unschedule it.
	ProcessCron\clear_existing_cron( Core\ORDER_CHECK_CRON );

	// Delete the options we set.
	delete_option( Core\OPTION_PREFIX . 'last_checked' );
}
