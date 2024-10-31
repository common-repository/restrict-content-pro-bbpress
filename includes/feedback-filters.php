<?php
/**
 * Feedback Filters
 *
 * @package     RCP\bbPress\Feedback Filters
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Changes the "no topics found" message.
 *
 * @param string $translated_text Translated text.
 * @param string $text            Untranslated text.
 * @param string $domain          Text domain.
 *
 * @since 1.0
 * @return string
 */
function rcp_feedback_messages( $translated_text, $text, $domain ) {

	switch ( $translated_text ) {
		case 'Oh bother! No topics were found here!':
			$translated_text = __( 'You must be a premium user to view this content.', 'rcp-bbpress' );
			break;
	}

	return $translated_text;
}

/**
 * Apply the message filter when viewing a premium forum and the user doesn't have permission to view it.
 *
 * @see   rcp_feedback_messages()
 *
 * @since 1.0
 * @return void
 */
function rcp_apply_feedback_messages() {
	global $user_ID;

	$has_paid_membership = function_exists( 'rcp_user_has_paid_membership' ) ? rcp_user_has_paid_membership( $user_ID ) : rcp_is_active( $user_ID );

	if ( rcp_forum_is_premium() && ( ! $has_paid_membership && ! current_user_can( 'moderate' ) ) ) {
		add_filter( 'gettext', 'rcp_feedback_messages', 20, 3 );
	}
}

add_action( 'template_redirect', 'rcp_apply_feedback_messages' );