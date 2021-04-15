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

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function uninstall() {

	// Include our action so that we may add to this later.
	do_action( Core\HOOK_PREFIX . 'uninstall_process' );

	// And flush our rewrite rules.
	flush_rewrite_rules();
}
register_uninstall_hook( Core\FILE, __NAMESPACE__ . '\uninstall' );
