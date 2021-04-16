<?php
/**
 * Handle sending an alert email.
 *
 * @package WooZeroOrdersAlert
 */

// Declare our namespace.
namespace Nexcess\WooZeroOrdersAlert\Process\Email;

// Set our aliases.
use Nexcess\WooZeroOrdersAlert as Core;
use Nexcess\WooZeroOrdersAlert\Utilities as Utilities;

/**
 * Send our zero orders notification email.
 *
 * @return mixed
 */
function send_zero_orders_email() {

	// Set the timestamp.
	$set_date_stamp = Utilities\get_yesterday_timestamp();

	// Get my address.
	$email_to_addr  = get_email_to_address();

	// Pull my subject.
	$email_subject  = get_email_subject( $set_date_stamp );

	// And pull the content.
	$email_content  = get_email_content( $set_date_stamp );

	// And finally the headers.
	$email_headers  = get_email_headers();

	// Now attempt to send the actual email.
	return wp_mail( $email_to_addr, $email_subject, $email_content, $email_headers );
}

/**
 * Get the email address we wanna use for our alert.
 *
 * @return string
 */
function get_email_to_address() {

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
 * @param  integer $timestamp  The timestamp of the day with no orders.
 *
 * @return string
 */
function get_email_subject( $timestamp = 0 ) {

	// Make sure we have a timestamp, otherwise assume yesterday.
	$set_date_stamp = ! empty( $timestamp ) ? $timestamp : time() - DAY_IN_SECONDS;

	// Set the date stamp.
	$set_subj_date  = date( 'F jS', absint( $set_date_stamp ) );

	// Set up the subject using today's date.
	$set_subject    = sprintf( __( 'Zero Orders Alert for %s', 'woo-minimum-order-alerts' ), $set_subj_date );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_subject', $set_subject, $timestamp );
}

/**
 * Construct and return the actual email body.
 *
 * @param  integer $timestamp  The timestamp of the day with no orders.
 *
 * @return HTML
 */
function get_email_content( $timestamp = 0 ) {

	// Make sure we have a timestamp, otherwise assume yesterday.
	$set_date_stamp = ! empty( $timestamp ) ? $timestamp : time() - DAY_IN_SECONDS;

	// Set the date stamp.
	$set_email_date = date( 'F jS', absint( $set_date_stamp ) );

	// Set up the text.
	$message_text   = sprintf(
		__( '%s: No orders were recorded for %s. Please confirm that your store is working properly.', 'woo-minimum-order-alerts' ),
		'<strong>' . __( 'NOTICE', 'woo-minimum-order-alerts' ) . '</strong>', // formatted notice text.
		esc_attr( $set_email_date ), // date of check
	);

	// First write the content.
	$set_content    = wpautop( $message_text );

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
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_html_content', trim( $build_html ), $timestamp );
}

/**
 * Build a basic name.
 *
 * @return string
 */
function get_email_from_name() {

	// Pull the blog name we have.
	$get_site_title = get_bloginfo( 'name' );

	// Now set the name.
	$set_from_name  = sprintf( __( 'Order Alerts for %s', 'woo-minimum-order-alerts' ), $get_site_title );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_from_name', $set_from_name );
}

/**
 * Get the address we need to mail from.
 *
 * @return string
 */
function get_email_from_address() {

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
function get_email_headers() {

	// Set the from name.
	$set_from_name  = get_email_from_name();
	$set_from_email = get_email_from_address();

	// Now set my headers.
	$set_headers[]  = 'Content-Type: text/html; charset=UTF-8';
	$set_headers[]  = sprintf( __( 'From: %1$s <%2$s>', 'woo-minimum-order-alerts' ), esc_attr( $set_from_name ), $set_from_email );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_email_headers', $set_headers );
}
