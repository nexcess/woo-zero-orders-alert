<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Helpers;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Check to see if WooCommerce is installed and active.
 *
 * @return boolean
 */
function maybe_woo_activated() {
	return class_exists( 'woocommerce' ) ? true : false;
}
