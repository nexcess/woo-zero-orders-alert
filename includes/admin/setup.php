<?php
/**
 * Handle our initial admin setup.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Admin\Setup;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;
use Nexcess\WooMinimumOrderAlerts\Process\CronTasks as CronTasks;

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
		CronTasks\modify_order_check_cron( false );
	}

	// Get the today timestamp.
	$define_today_stamp = Utilities\get_today_timestamp();

	// Set our initial options in the DB.
	update_option( Core\OPTION_PREFIX . 'last_checked', $define_today_stamp, 'no' );
	update_option( Core\OPTION_PREFIX . 'min_val', 5, 'no' );
	update_option( Core\OPTION_PREFIX . 'alert_types', array(), 'no' );
}

/**
 * Delete some of the initial options we set.
 *
 * @return void
 */
function run_on_deactivate() {

	// Pull in our scheduled cron and unschedule it.
	CronTasks\modify_order_check_cron( true, false );

	// If we want to change options on deactivate, do it here.
}

/**
 * Purge out the settings and reset our cron.
 *
 * @return void
 */
function run_on_uninstall() {

	// Pull in our scheduled cron and unschedule it.
	CronTasks\modify_order_check_cron( true, false );

	// Delete the options we set.
	delete_option( Core\OPTION_PREFIX . 'last_checked' );
	delete_option( Core\OPTION_PREFIX . 'min_val' );
	delete_option( Core\OPTION_PREFIX . 'alert_types' );
}

/**
 * Set up the different types of alerts.
 *
 * @param  boolean $return_keys  Whether to return just the keys.
 *
 * @return array
 */
function registered_alert_types( $return_keys = false ) {

	// Set the alert types in a key/label pair.
	$define_alert_types = array(
		'email' => __( 'Email', 'woo-minimum-order-alerts' ),
		'other' => __( 'Other', 'woo-minimum-order-alerts' ),
	);

	// Get any possible custom types.
	$get_custom_types   = apply_filters( Core\HOOK_PREFIX . 'add_alert_types', array() );

	// Merge the custom values if we have them.
	if ( ! empty( $get_custom_types ) ) {
		$define_alert_types = wp_parse_args( $get_custom_types, $define_alert_types );
	}

	// Return the keys, or everything.
	return false !== $return_keys ? array_keys( $define_alert_types ) : $define_alert_types;
}
