<?php
/**
 * Our utility functions to use across the plugin.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Utilities;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;

/**
 * Get the user cap for the actions.
 *
 * @return string
 */
function get_user_cap() {
	return apply_filters( Core\HOOK_PREFIX . 'user_menu_cap', 'manage_options' );
}
