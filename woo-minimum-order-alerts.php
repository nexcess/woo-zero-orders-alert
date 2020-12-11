<?php
/**
 * Plugin Name: WooCommerce Minimum Order Alerts
 * Plugin URI:  https://www.nexcess.net
 * Description: Notify store owners when minimim daily order targets are missed.
 * Version:     0.0.1-dev
 * Author:      Nexcess
 * Author URI:  https://www.nexcess.net
 * Text Domain: woo-minimum-order-alerts
 * Domain Path: /languages
 * WC requires at least: 4.2.0
 * WC tested up to: 4.4.1
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Define our plugin version.
define( __NAMESPACE__ . '\VERS', '0.0.1-dev' );

// Plugin root file.
define( __NAMESPACE__ . '\FILE', __FILE__ );

// Define our file base.
define( __NAMESPACE__ . '\BASE', plugin_basename( __FILE__ ) );

// Plugin Folder URL.
define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

// Set our assets URL constant.
define( __NAMESPACE__ . '\ASSETS_URL', URL . 'assets' );

// Set our includes path constants.
define( __NAMESPACE__ . '\INCLUDES_PATH', __DIR__ . '/includes' );

// Set the various prefixes for our actions and filters.
define( __NAMESPACE__ . '\HOOK_PREFIX', 'woo_min_order_alerts_' );
define( __NAMESPACE__ . '\NONCE_PREFIX', 'woo_nxmoa_nonce_' );
define( __NAMESPACE__ . '\TRANSIENT_PREFIX', 'nx_moa_tr_' );
define( __NAMESPACE__ . '\OPTION_PREFIX', 'woo_nx_moa_setting_' );

// And define our menu slug.
define( __NAMESPACE__ . '\MENU_SLUG', 'min-order-alerts-settings' );

// Set our cron function name constants.
define( __NAMESPACE__ . '\ORDER_CHECK_CRON', 'wc_min_orders_check_prev' );

// Now we handle all the various file loading.
nx_woo_minimum_order_alerts_file_load();

/**
 * Actually load our files.
 *
 * @return void
 */
function nx_woo_minimum_order_alerts_file_load() {

	// Load the multi-use files first.
	require_once __DIR__ . '/includes/helpers.php';
	require_once __DIR__ . '/includes/utilities.php';

	// Pull in the processing parts.
	require_once __DIR__ . '/includes/process/notifications.php';
	require_once __DIR__ . '/includes/process/order-checks.php';
	require_once __DIR__ . '/includes/process/cron-tasks.php';

	// Load our individual alert types.
	require_once __DIR__ . '/includes/alert-types/email.php';

	// Load up our admin and WooCommerce settings stuff.
	require_once __DIR__ . '/includes/admin/setup.php';
	require_once __DIR__ . '/includes/admin/config.php';
	require_once __DIR__ . '/includes/admin/settings.php';

	// Load the triggered file loads.
	require_once __DIR__ . '/includes/activate.php';
	require_once __DIR__ . '/includes/deactivate.php';
	require_once __DIR__ . '/includes/uninstall.php';
}
