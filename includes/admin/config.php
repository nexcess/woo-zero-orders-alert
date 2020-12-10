<?php
/**
 * Do some ongoing admin level configurations.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Admin\Config;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

/**
 * Start our engines.
 */
add_filter( 'plugin_action_links', __NAMESPACE__ . '\add_quick_link', 10, 2 );

/**
 * Add our "settings" links to the plugins page.
 *
 * @param  array  $links  The existing array of links.
 * @param  string $file   The file we are actually loading from.
 *
 * @return array  $links  The updated array of links.
 */
function add_quick_link( $links, $file ) {

	// Bail without caps.
	if ( ! current_user_can( Utilities\get_user_cap() ) ) {
		return $links;
	}

	// Set the static var.
	static $this_plugin;

	// Check the base if we aren't paired up.
	if ( ! $this_plugin ) {
		$this_plugin = Core\BASE;
	}

	// Check to make sure we are on the correct plugin.
	if ( $file != $this_plugin ) {
		return $links;
	}

	// Fetch our setting link.
	$settings_page  = Helpers\get_woo_section_settings_link();

	// Now create the link markup.
	$settings_link  = '<a href="' . esc_url( $settings_page ) . ' ">' . esc_html__( 'Settings', 'woo-minimum-daily-orders' ) . '</a>';

	// Add it to the array.
	array_unshift( $links, $settings_link );

	// Return the resulting array.
	return $links;
}
