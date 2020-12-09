<?php
/**
 * Load our custom settings inside the larger WooCommerce settings API.
 *
 * @package WooMinimumDailyOrders
 */

// Declare our namespace.
namespace Nexcess\WooMinimumDailyOrders\Admin\Settings;

// Set our aliases.
use Nexcess\WooMinimumDailyOrders as Core;
use Nexcess\WooMinimumDailyOrders\Helpers as Helpers;
use Nexcess\WooMinimumDailyOrders\Utilities as Utilities;

// And pull in any other namespaces.
use WP_Error;

/**
 * Start our engines.
 */
add_action( 'woo_nx_mdo_activate_process', __NAMESPACE__ . '\set_initial_options' );
add_filter( 'woocommerce_get_sections_products', __NAMESPACE__ . '\add_settings_section' );
add_filter( 'woocommerce_get_settings_products', __NAMESPACE__ . '\load_settings_fields', 10, 2 );

/**
 * Set our initial options in the DB at activation.
 *
 * @return void
 */
function set_initial_options() {
	update_option( Core\OPTION_PREFIX . 'min_val', 5, 'no' );
	update_option( Core\OPTION_PREFIX . 'alert_email', 'yes', 'no' );
	update_option( Core\OPTION_PREFIX . 'alert_other', 'no', 'no' );
}

/**
 * Add our new settings section for display later.
 *
 * @param array
 */
function add_settings_section( $sections ) {

	// Add our new section, assuming it doesnt exist.
	if ( ! isset( $sections[ Core\MENU_SLUG ] ) ) {
		$sections[ Core\MENU_SLUG ] = __( 'Minimum Daily Orders', 'woo-minimum-daily-orders' );
	}

	// And return it.
	return $sections;
}

/**
 * Load up our custom settings to be added.
 *
 * @param  array  $settings         The current array of settings for that section.
 * @param  string $current_section  Which section is being done.
 *
 * @return array
 */
function load_settings_fields( $settings, $current_section ) {

	// Return whatever we have if we aren't on our settings.
	if ( empty( $current_section ) || Core\MENU_SLUG !== $current_section ) {
		return $settings;
	}

	// Now set up our settings array.
	$set_settings_args  = array(

		// Add our title.
		array(
			'title' => __( 'Minimum Daily Orders', 'woo-minimum-daily-orders' ),
			'type'  => 'title',
			'id'    => Core\MENU_SLUG,
		),

		// Set the input for our number.
		array(
			'title'             => __( 'Minimum Order Amount', 'woo-minimum-daily-orders' ),
			'desc'              => __( 'Set a minimum that matches your expected daily order volume.', 'woo-minimum-daily-orders' ),
			'id'                => Core\OPTION_PREFIX . 'min_val',
			'css'               => 'width:50px;',
			'default'           => '5',
			'desc_tip'          => true,
			'autoload'          => false,
			'type'              => 'number',
			'custom_attributes' => array(
				'min'  => 0,
				'step' => 1,
			),
		),

		// Add the first alert method.
		array(
			'title'         => __( 'Notifications', 'woo-minimum-daily-orders' ),
			'desc'          => __( 'Send an email when the minimum has not been met', 'woo-minimum-daily-orders' ),
			'id'            => Core\OPTION_PREFIX . 'alert_email',
			'default'       => 'yes',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'autoload'      => false,
		),

		// Add the secondary alert method.
		array(
			'desc'          => __( 'Send an alert some other way we dont know yet', 'woo-minimum-daily-orders' ),
			'id'            => Core\OPTION_PREFIX . 'alert_other',
			'default'       => 'no',
			'type'          => 'checkbox',
			'autoload'      => false,
			'checkboxgroup' => 'end',
		),

		// Add our section end.
		array(
			'type' => 'sectionend',
			'id'   => Core\MENU_SLUG,
		),
	);

	// And return our new args.
	return $set_settings_args;
}
