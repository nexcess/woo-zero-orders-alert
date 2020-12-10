<?php
/**
 * Load our custom settings inside the larger WooCommerce settings API.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\Admin\Settings;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;
use Nexcess\WooMinimumOrderAlerts\Helpers as Helpers;
use Nexcess\WooMinimumOrderAlerts\Utilities as Utilities;

/**
 * Start our engines.
 */
add_filter( 'woocommerce_get_sections_products', __NAMESPACE__ . '\add_settings_section' );
add_filter( 'woocommerce_get_settings_products', __NAMESPACE__ . '\load_settings_fields', 10, 2 );

/**
 * Add our new settings section for display later.
 *
 * @param array
 */
function add_settings_section( $sections ) {

	// Add our new section, assuming it doesnt exist.
	if ( ! isset( $sections[ Core\MENU_SLUG ] ) ) {
		$sections[ Core\MENU_SLUG ] = __( 'Minimum Order Alerts', 'woo-minimum-order-alerts' );
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
			'title' => __( 'Minimum Order Alerts', 'woo-minimum-order-alerts' ),
			'type'  => 'title',
			'id'    => Core\MENU_SLUG,
		),

		// Set the input for our number.
		array(
			'title'             => __( 'Minimum Order Amount', 'woo-minimum-order-alerts' ),
			'desc'              => __( 'Set a minimum that matches your expected daily order volume.', 'woo-minimum-order-alerts' ),
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
			'title'         => __( 'Notifications', 'woo-minimum-order-alerts' ),
			'desc'          => __( 'Send an email when the minimum has not been met', 'woo-minimum-order-alerts' ),
			'id'            => Core\OPTION_PREFIX . 'alert_email',
			'default'       => 'yes',
			'type'          => 'checkbox',
			'checkboxgroup' => 'start',
			'autoload'      => false,
		),

		// Add the secondary alert method.
		array(
			'desc'          => __( 'Send an alert some other way we dont know yet', 'woo-minimum-order-alerts' ),
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
