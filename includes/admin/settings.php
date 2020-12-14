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
add_filter( 'woocommerce_settings_tabs_array', __NAMESPACE__ . '\add_settings_tab', 50 );
add_action( 'woocommerce_settings_tabs_order-monitoring', __NAMESPACE__ . '\display_admin_fields' );
add_action( 'woocommerce_admin_field_alert_types', __NAMESPACE__ . '\render_alert_types_field' );
add_filter( 'woocommerce_admin_settings_sanitize_option', __NAMESPACE__ . '\sanitize_alert_types_option', 40, 3 );
add_action( 'woocommerce_update_options_order-monitoring', __NAMESPACE__ . '\update_admin_settings' );

/**
 * Add a new settings tab to the WooCommerce settings tabs array.
 *
 * @param  array $tabs  The current array of WooCommerce setting tabs.
 *
 * @return array $tabs  The modified array of WooCommerce setting tabs.
 */
function add_settings_tab( $tabs ) {

	// Confirm we don't already have the tab.
	if ( ! isset( $tabs[ Core\TAB_SLUG ] ) ) {
		$tabs[ Core\TAB_SLUG ] = __( 'Order Monitoring', 'woo-minimum-order-alerts' );
	}

	// If we have the advanced tab, move it to the end.
	if ( isset( $tabs['advanced'] ) ) {

		// Set the advanced tab so we can add it back to the end.
		$advanced_tab   = $tabs['advanced'];

		// Now remove the existing.
		unset( $tabs['advanced'] );

		// Add the advanced tab back to the end.
		$tabs['advanced'] = $advanced_tab;
	}

	// And return the entire array.
	return $tabs;
}

/**
 * Uses the WooCommerce admin fields API to output settings.
 *
 * @see  woocommerce_admin_fields() function.
 *
 * @uses woocommerce_admin_fields()
 * @uses self::get_tab_settings()
 */
function display_settings_tab() {
	woocommerce_admin_fields( get_tab_settings() );
}

/**
 * Create the array of settings we are going to display.
 *
 * @return array $settings  The array of settings data.
 */
function get_tab_settings() {

	// Set up our array, including default Woo items.
	$setup_args = array(

		'mainheader' => array(
			'title' => __( 'Minimum Order Alerts', 'woo-minimum-order-alerts' ),
			'type'  => 'title',
			'desc'  => __( 'Set an alert when your store does not reach a daily target.', 'woo-minimum-order-alerts' ),
			'id'    => Core\SECTION_ID,
		),

		// Set the input for our number.
		'min-val' => array(
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
		'alert-types' => array(
			'title'    => __( 'Notifications', 'woo-minimum-order-alerts' ),
			'id'       => Core\OPTION_PREFIX . 'alert_types',
			'type'     => 'alert_types',
			'autoload' => false,
			'options'  => AdminSetup\registered_alert_types(),
			'screen'   => __( 'The individual alert notification options', 'woo-minimum-order-alerts' ),
			'default'  => '',
		),

		// Add our section end.
		'mainsection_end' => array(
			'type' => 'sectionend',
			'id'   => Core\SECTION_ID . '-end',
		),

	);

	// Return our set of fields with a filter.
	return apply_filters( Core\HOOK_PREFIX . 'settings_data_array', $setup_args );
}

/**
 * Load up our custom admin field.
 *
 * @param  array $field_args  The array of field args.
 *
 * @return HTML
 */
function render_alert_types_field( $field_args ) {

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
	if ( empty( $option['id'] ) || Core\SECTION_ID !== $option['id'] ) {
		return $value;
	}

	// If this field is totally empty, manually fix the setting
	// since Woo doesn't allow for saving an empty value.
	if ( ! isset( $_POST[ Core\OPTION_PREFIX . 'alert_types' ] ) ) {

		// Set our option back to the empty value.
		update_option( Core\OPTION_PREFIX . 'alert_types', '', 'no' );

		// And return an empty.
		return '';
	}

	// Return it, with each one sanitized.
	return array_map( 'sanitize_text_field', $_POST[ Core\OPTION_PREFIX . 'alert_types' ] );
}

/**
 * Uses the WooCommerce options API to save settings.
 *
 * @see woocommerce_update_options() function.
 *
 * @uses woocommerce_update_options()
 * @uses self::get_tab_settings()
 */
function update_admin_settings() {
	woocommerce_update_options( get_tab_settings() );
}
