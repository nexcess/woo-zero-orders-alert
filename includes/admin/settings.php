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
use Nexcess\WooMinimumOrderAlerts\Admin\Setup as AdminSetup;

/**
 * Start our engines.
 */
add_filter( 'woocommerce_get_sections_products', __NAMESPACE__ . '\add_settings_section' );
add_filter( 'woocommerce_get_settings_products', __NAMESPACE__ . '\load_settings_fields', 10, 2 );
add_action( 'woocommerce_admin_field_alert_types', __NAMESPACE__ . '\render_custom_admin_field' );
add_filter( 'woocommerce_admin_settings_sanitize_option', __NAMESPACE__ . '\sanitize_alert_types_option', 10, 3 );

/**
 * Add our new settings section for display later.
 *
 * @param array $sections  The existing sections.
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

		// Load our custom alert types field.
		array(
			'title'    => __( 'Notifications', 'woo-minimum-order-alerts' ),
			'id'       => Core\OPTION_PREFIX . 'alert_types',
			'type'     => 'alert_types',
			'autoload' => false,
			'options'  => AdminSetup\registered_alert_types(),
			'screen'   => __( 'The individual alert notification options', 'woo-minimum-order-alerts' ),
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

/**
 * Load up our custom admin field.
 *
 * @param  array $field_args  The array of field args.
 *
 * @return HTML
 */
function render_custom_admin_field( $field_args ) {

	// Set our value to make sure we can check against it.
	$set_stored_values  = ! empty( $field_args['value'] ) && is_array( $field_args['value'] ) ? $field_args['value'] : array();

	// Set a flag for our screen reader.
	$show_screen_text   = true;

	// Open our row.
	echo '<tr class="" valign="top">';

		// Handle the table row header.
		echo '<th scope="row" class="titledesc">' . esc_html( $field_args['title'] ) . '</th>';

		// Now wrap our checkboxes.
		echo '<td class="forminp forminp-checkbox">';

		// Now loop my options.
		foreach ( $field_args['options'] as $alert_type => $alert_label ) {

			// Set my field variables.
			$set_field_id   = $field_args['id'] . '_' . $alert_type;
			$set_field_name = $field_args['id'] . '[]';

			// Set if we are checked.
			$maybe_checked  = in_array( $alert_type, $set_stored_values ) ? 'checked="checked"' : '';

			// Open up the fieldset.
			echo '<fieldset>';

			// Show the screen text once.
			if ( ! empty( $field_args['screen'] ) && false !== $show_screen_text ) {

				// Set the flag.
				$show_screen_text   = false;

				// Show the text.
				echo '<legend class="screen-reader-text"><span>' . esc_html( $field_args['screen'] ) . '</span></legend>';
			}

				// Now the actual label / input.
				echo '<label for="' . sanitize_html_class( $set_field_id ) . '">';
					echo '<input name="' . $set_field_name . '" id="' . sanitize_html_class( $set_field_id ) . '" type="checkbox" value="' . esc_attr( $alert_type ) . '" ' . $maybe_checked . '>';
				echo ' ' . esc_html( $alert_label ) . '</label>';

			// Close the fieldset.
			echo '</fieldset>';
		}

		// Now close up the checkboxes.
		echo '</td>';

	// Close the row.
	echo '</tr>';
}

/**
 * Handle saving our multi-checkbox alert types.
 *
 * @param  mixed $value      The original value being passed.
 * @param  array $option     The array of args in the option.
 * @param  mixed $raw_value  Not really sure what this is.
 *
 * @return mixed
 */
function sanitize_alert_types_option( $value, $option, $raw_value ) {

	// Check to make sure we are looking at the option we want.
	if ( 'min-order-alerts-settings' !== $option['id'] ) {
		return $value;
	}

	// Return an empty if nothing was posted.
	if ( empty( $_POST[ Core\OPTION_PREFIX . 'alert_types' ] ) ) {
		return '';
	}

	// Return it, with each one sanitized.
	return array_map( 'sanitize_text_field', $_POST[ Core\OPTION_PREFIX . 'alert_types' ] );
}
