<?php
/**
 * Our uninstall call.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Uninstall;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;
use Nexcess\WooZeroOrdersAlert\Process\Cron as ProcessCron;

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function uninstall() {

	// Pull in our scheduled cron and unschedule it.
	ProcessCron\clear_existing_cron( Core\ORDER_CHECK_CRON );

	// Delete our "last checked" option.
	delete_option( Core\OPTION_PREFIX . 'last_checked' );

	// Include our action so that we may add to this later.
	do_action( Core\HOOK_PREFIX . 'uninstall_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_uninstall_hook( Core\FILE, __NAMESPACE__ . '\uninstall' );
