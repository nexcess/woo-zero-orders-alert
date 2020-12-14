<?php
/**
 * Everything related to building and sending a message to the Woo inbox.
 *
 * @package WooMinimumOrderAlerts
 */

// Declare our namespace.
namespace Nexcess\WooMinimumOrderAlerts\AlertTypes\Inbox;

// Set our aliases.
use Nexcess\WooMinimumOrderAlerts as Core;

// Pull in the required a8c pieces.
use \Automattic\WooCommerce\Admin\Notes\WC_Admin_Notes;
use \Automattic\WooCommerce\Admin\Notes\WC_Admin_Note;
use \Automattic\WooCommerce\Admin\Notes\Notes;
use \Automattic\WooCommerce\Admin\Notes\Note;

/**
 * Send the actual inbox message.
 *
 * @return mixed
 */
function send_inbox_alert() {

	// Confirm the "WC_Data_Store" class is set.
	if ( ! class_exists( 'WC_Data_Store' ) ) {
		return;
	}

	// Set the Woo data store.
	$load_admin_notes   = \WC_Data_Store::load( 'admin-note' );

	// We will construct the note here.
	$fetch_note_args    = get_single_alert_args();

	// If we have no args, bail.
	if ( empty( $fetch_note_args ) ) {
		return;
	}

	/*
	// Check to see if our note already exists.
	$maybe_note_exists  = $load_admin_notes->get_notes_with_name( $fetch_note_args['name'] );

	// If we already have it, bail.
	if ( ! empty( $maybe_note_exists ) ) {
		return;
	}
	*/

	// Now get the version and make sure we use the correct class.
	$current_wc_admin   = get_option( 'woocommerce_admin_version' );
	$use_new_note_class = version_compare( '1.6.0', $current_wc_admin ) > 0;

	// Set the correct class based on the WC admin version.
	if ( $use_new_note_class ) {
		$set_alert  = new Note();
	} else {
		$set_alert  = new WC_Admin_Note();
	}

	// Now set each individual part of the alert.
	$set_alert->set_title( $fetch_note_args['title'] );
	$set_alert->set_content( $fetch_note_args['content'] );
	$set_alert->set_type( $fetch_note_args['type'] );
	$set_alert->set_name( $fetch_note_args['name'] );
	$set_alert->set_layout( $fetch_note_args['layout'] );
	$set_alert->set_source( $fetch_note_args['source'] );

	// Add an image if one was included.
	if ( ! empty( $fetch_note_args['image'] ) ) {
		$set_alert->set_image( $fetch_note_args['image'] );
	}

	// Include an action to look at the
	// $set_alert->add_action( $note[ 'action' ], 'Test action', wc_admin_url() );

	// And save the note.
	$set_alert->save();
}

/**
 * Construct and return the alert.
 *
 * @return array
 */
function get_single_alert_args() {

	// Construct the individual args.
	$set_note_args  = array(
		'title'   => get_alert_title(),
		'content' => get_alert_content(),
		'name'    => Core\WOO_INBOX_NOTE_ID,
		'type'    => 'info',
		'source'  => Core\WOO_INBOX_SOURCE,
		'layout'  => 'plain',
		'image'   => '',
	);


	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_inbox_args', $set_note_args );
}

/**
 * Construct and return our alert title.
 *
 * @return string
 */
function get_alert_title() {

	// Set up the title using today's date.
	$set_title  = sprintf( __( 'Minimum Order Alert for %s', 'woo-minimum-order-alerts' ), date( 'Y-m-d' ) );

	// Return it filtered.
	return apply_filters( Core\HOOK_PREFIX . 'alert_inbox_title', $set_title );
}

/**
 * Construct and return the alert body.
 *
 * @return array
 */
function get_alert_content() {

	// First write the content.
	$set_content    = __( 'Your store did not reach the minimum order count that you configured.', 'woo-minimum-order-alerts' );

	// Return the content.
	return apply_filters( Core\HOOK_PREFIX . 'alert_inbox_content', $set_content );
}
