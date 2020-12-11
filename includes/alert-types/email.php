<?php
/**
 * Everything related to building and sending an email.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\AlertTypes\Email;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;

/**
 * Send the actual email.
 *
 * @return mixed
 */
function send_email_alert() {

	// Get my address.
	$email_to_addr  = get_alert_address();

	// Pull my subject.
	$email_subject  = get_alert_subject();

	// And pull the content.
	$email_content  = get_alert_content();

	// And finally the headers.
	$email_headers  = get_alert_headers();

	// Now attempt to send the actual email.
	return wp_mail( $email_to_addr, $email_subject, $email_content, $email_headers );
}

/**
 * Get the email address we wanna use for our alert.
 *
 * @return string
 */
function get_alert_address() {

	// Check the Woo filter for email.
	$maybe_use_woo  = apply_filters( Core\HOOK_PREFIX . 'alert_email_use_woo_from', false );

	// Pull the appropriate email.
	$default_email  = false !== $maybe_use_woo ? get_option( 'woocommerce_email_from_address' ) : get_option( 'admin_email' );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_address', $default_email );
}

/**
 * Construct and return our email subject.
 *
 * @return string
 */
function get_alert_subject() {

	// Set up the subject using today's date.
	$set_subject    = sprintf( __( 'Minimum Order Alert for %s', 'woo-minimum-order-alerts' ), date( 'Y-m-d' ) );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_subject', $set_subject );
}

/**
 * Construct and return the actual email body.
 *
 * @return HTML
 */
function get_alert_content() {

	// First write the content.
	$set_content    = '<p>' . __( 'Your store did not reach the minimum order count that you configured.', 'woo-minimum-order-alerts' ) . '</p>';

	// Build our HTML.
	$build_html     = '';

	// Do the opening tags.
	$build_html    .= '<html>' . "\n";
	$build_html    .= '<body>' . "\n";

	// Inject the HTML.
	$build_html    .= apply_filters( Core\HOOK_PREFIX . 'alert_email_content', $set_content ) . "\n";

	// Close my tags.
	$build_html    .= '</body>' . "\n";
	$build_html    .= '</html>';

	// Now send it back with a second filter.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_html_content', trim( $build_html ) );
}

/**
 * Build a basic name.
 *
 * @return string
 */
function get_alert_from_name() {

	// Pull the blog name we have.
	$get_site_title = get_bloginfo( 'name' );

	// Now set the name.
	$set_from_name  = sprintf( __( '%s Order Alerts', 'woo-minimum-order-alerts' ), $get_site_title );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_from_name', $set_from_name );
}

/**
 * Get the site name and make it into a from name
 *
 * @return string
 */
function get_alert_from_address() {

	// First pull the domain and parse it.
	$get_site_home  = wp_parse_url( network_home_url(), PHP_URL_HOST );

	// Strip the opening WWW if it exists.
	if ( 'www.' === substr( $get_site_home, 0, 4 ) ) {
		$get_site_home  = substr( $get_site_home, 4 );
	}

	// Now set up the email.
	$set_from_email = 'wordpress@' . $get_site_home;

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_from_address', $set_from_email );
}

/**
 * Set up our standard return headers.
 *
 * @return string
 */
function get_alert_headers() {

	// Set the from name.
	$set_from_name  = get_alert_from_name();
	$set_from_email = get_alert_from_address();

	// Now set my headers.
	$set_headers[]  = 'Content-Type: text/html; charset=UTF-8';
	$set_headers[]  = sprintf( __( 'From: %1$s <%2$s>', 'woo-minimum-order-alerts' ), esc_attr( $set_from_name ), $set_from_email );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_headers', $set_headers );
}
