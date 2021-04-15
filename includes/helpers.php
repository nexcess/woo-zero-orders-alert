<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Helpers;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;

/**
 * Determine if we should run an order check.
 *
 * @return boolean
 */
function maybe_run_order_check() {

	// Get the last date checked.
	$get_last_checked   = get_option( Core\OPTION_PREFIX . 'last_checked', 0 );

	// Bail without a last checked.
	if ( empty( $get_last_checked ) ) {
		return false;
	}

	// Add a day to the stamp for comparison.
	$define_check_stamp = absint( $get_last_checked ) + DAY_IN_SECONDS;

	// Return the result.
	return time() > absint( $define_check_stamp ) ? true : false;
}
