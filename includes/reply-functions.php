<?php
/**
 * Reply Functions
 *
 * @package     RCP\bbPress\Reply Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hides the content of replies if the topic is restricted.
 *
 * @param string $content  Unfiltered content.
 * @param int    $reply_id ID of the reply.
 *
 * @since 1.0
 * @return string
 */
function rcp_filter_replies( $content, $reply_id ) {

	if ( ! rcp_bbp_can_access_topic( bbp_get_reply_topic_id() ) ) {
		return __( 'You must be a premium user to view this content', 'rcp_bbpress' );
	}

	return $content;
}

add_filter( 'bbp_get_reply_content', 'rcp_filter_replies', 2, 999 );


/**
 * Hides the reply form if the current user cannot access the topic.
 *
 * @param bool $can_access Whether or not the user can access the replies form.
 *
 * @since 1.0
 * @return bool
 */
function rcp_hide_new_replies_form( $can_access ) {

	if ( ! rcp_bbp_can_access_topic( bbp_get_reply_topic_id() ) ) {
		$can_access = false;
	}

	return $can_access;
}

add_filter( 'bbp_current_user_can_access_create_reply_form', 'rcp_hide_new_replies_form' );
add_filter( 'bbp_current_user_can_access_create_topic_form', 'rcp_hide_new_replies_form' ); // this is required for it to work with the default theme

/**
 * Disable single topic views if the current user does not have permission to access the topic.
 *
 * @since 1.0
 * @return void
 */
function rcp_hide_single_reply() {

	$topic_id  = get_the_ID();
	$post_type = get_post_type( $topic_id );

	if ( ! function_exists( 'bbp_get_topic_post_type' ) ) {
		return;
	}

	if ( bbp_get_topic_post_type() !== $post_type ) {
		return;
	}

	if ( rcp_bbp_can_access_topic( bbp_get_reply_topic_id() ) ) {
		return;
	}

	if ( is_user_logged_in() ) {
		$redirect = home_url();
	} else {
		$redirect = bbp_get_topic_permalink( bbp_get_reply_topic_id( $topic_id ) );
	}

	wp_redirect( esc_url_raw( home_url( 'wp-login.php?redirect_to=' . urlencode( $redirect ) ) ) );
	exit;

}

add_action( 'template_redirect', 'rcp_hide_single_reply' );