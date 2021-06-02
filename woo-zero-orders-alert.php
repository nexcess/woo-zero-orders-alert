<?php
/**
 * Plugin Name: Zero Orders Alert for WooCommerce
 * Plugin URI:  https://www.nexcess.net
 * Description: Notify store owners when a store has zero sales for a day.
 * Version:     1.0.1-dev
 * Author:      Nexcess
 * Author URI:  https://www.nexcess.net
 * Text Domain: woo-zero-orders-alert
 * Domain Path: /languages
 * WC requires at least: 5.1.0
 * WC tested up to: 5.2.2
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our plugin version.
define( __NAMESPACE__ . '\VERS', '1.0.1-dev' );

// Plugin root file.
define( __NAMESPACE__ . '\FILE', __FILE__ );

// Define our file base.
define( __NAMESPACE__ . '\BASE', plugin_basename( __FILE__ ) );

// Plugin Folder URL.
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

// Set our includes path constants.
define( __NAMESPACE__ . '\INCLUDES_PATH', __DIR__ . '/includes' );

// Set the various prefixes for our actions and filters.
define( __NAMESPACE__ . '\HOOK_PREFIX', 'woo_zero_orders_alert_' );
define( __NAMESPACE__ . '\TRANSIENT_PREFIX', 'nx_zoa_tr_' );
define( __NAMESPACE__ . '\OPTION_PREFIX', 'woo_nx_zoa_setting_' );

// Set our cron function name constants.
define( __NAMESPACE__ . '\ORDER_CHECK_CRON', 'wc_zero_orders_check' );

// Now we handle all the various file loading.
nx_woo_zero_orders_alert_file_load();

/**
 * Actually load our files.
 *
 * @return void
 */
function nx_woo_zero_orders_alert_file_load() {

	// Load the multi-use files first.
	require_once __DIR__ . '/includes/utilities.php';

	// Pull in the processing parts.
	require_once __DIR__ . '/includes/process/orders.php';
	require_once __DIR__ . '/includes/process/email.php';
	require_once __DIR__ . '/includes/process/cron.php';

	// Load the triggered file loads.
	require_once __DIR__ . '/includes/activate.php';
	require_once __DIR__ . '/includes/deactivate.php';
	require_once __DIR__ . '/includes/uninstall.php';
}
