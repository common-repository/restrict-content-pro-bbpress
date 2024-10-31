<?php
/**
 * Topic Functions
 *
 * @package     RCP\bbPress\Topic Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks if a given topic is restricted to paid members.
 *
 * @param int $topic_id ID of the topic to check.
 *
 * @since 1.0
 * @return bool
 */
function rcp_topic_is_premium( $topic_id = null ) {

	if ( is_null( $topic_id ) ) {
		$topic_id = bbp_get_topic_id();
	}

	$ret = (bool) get_post_meta( $topic_id, '_is_topic_paid', true );

	if ( ! $ret ) {
		$ret = rcp_forum_is_premium( bbp_get_forum_id() );
	}

	return $ret;
}

/**
 * Determines if the current user can access the specified topic.
 *
 * @param int $topic_id ID of the topic to check.
 *
 * @since 1.0
 * @return bool
 */
function rcp_bbp_can_access_topic( $topic_id = 0 ) {

	if ( ! function_exists( 'rcp_is_active' ) ) {
		return true;
	}

	$ret                  = true;
	$user_id              = get_current_user_id();
	$paid_only            = rcp_topic_is_premium( $topic_id );
	$access_level         = get_post_meta( $topic_id, 'rcp_access_level', true );
	$subscriptions        = get_post_meta( $topic_id, 'rcp_subscription_level', true );
	$has_paid_membership  = function_exists( 'rcp_user_has_paid_membership' ) ? rcp_user_has_paid_membership( $user_id ) : rcp_is_active( $user_id );
	$membership_level_ids = function_exists( 'rcp_get_customer_membership_level_ids' ) ? rcp_get_customer_membership_level_ids() : array( rcp_get_subscription_id() );

	// Return false if the topic is paid and the current user is not active/paid.
	if ( $paid_only && ! $has_paid_membership ) {
		$ret = false;
	}

	// Return false if the user does not have the required access level.
	if ( $access_level > 0 && ! rcp_user_has_access( $user_id, $access_level ) ) {
		$ret = false;
	}

	// Return false if the user does not have the required membership level.
	if ( ! empty( $subscriptions ) && ! count( array_intersect( $membership_level_ids, $subscriptions ) ) ) {
		$ret = false;
	}

	// If the user can't view the forum, they can't view the topic.
	if ( ! rcp_bbp_can_access_forum( bbp_get_topic_forum_id( $topic_id ) ) ) {
		$ret = false;
	}

	// Moderators can always access topics.
	if ( current_user_can( 'moderate' ) ) {
		$ret = true;
	}

	/**
	 * Filters the topic access.
	 *
	 * @param bool $ret      Whether or not the current user can access the topic.
	 * @param int  $topic_id ID of the topic being checked.
	 * @param int  $user     ID of the user.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'rcp_bbp_can_access_topic', $ret, $topic_id, $user_id );

}

/**
 * Hides all topics in a restricted forum for non active users.
 *
 * @param array $query Query args.
 *
 * @since 1.0
 * @return array
 */
function rcp_filter_topics_list( $query ) {

	if ( bbp_is_single_forum() && ! rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {
		$query = array(); // return an empty query
	}

	return $query;
}

add_filter( 'bbp_after_has_topics_parse_args', 'rcp_filter_topics_list' );

/**
 * Retrieves an array of all premium topic IDs for the specified forum ID.
 * If no forum ID is provided, all premium topic IDs are returned.
 *
 * @param int|null $forum_id ID of the forum.
 *
 * @since 1.0
 * @return array|false Array of premium topic IDs or false if none.
 */
function rcp_get_premium_topics( $forum_id = null ) {

	if ( is_null( $forum_id ) ) {
		$forum_id = bbp_get_forum_id();
	}

	if ( $forum_id ) {
		$paid_ids = get_posts( array(
			'fields'      => 'ids',
			'meta_key'    => '_is_topic_paid',
			'meta_value'  => 1,
			'post_status' => 'publish',
			'post_type'   => 'topic'
		) );
	} else {
		$paid_ids = get_posts( array(
			'fields'      => 'ids',
			'meta_key'    => '_is_topic_paid',
			'meta_value'  => 1,
			'post_status' => 'publish',
			'post_type'   => 'topic',
			'post_parent' => absint( $forum_id )
		) );
	}

	if ( sizeof( $paid_ids ) >= 1 ) {
		return $paid_ids;
	}

	return false;
}

/**
 * Hides the new topic form if the current user cannot access the current forum.
 *
 * @param bool $can_access Whether or not the current user can access the new topics form.
 *
 * @since 1.0
 * @return bool
 */
function rcp_hide_new_topic_form( $can_access ) {

	if ( ! rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {
		return false;
	}

	return $can_access;

}

add_filter( 'bbp_current_user_can_access_create_topic_form', 'rcp_hide_new_topic_form' );

/**
 * Disable single topic views if the current user does not have permission to access the forum.
 *
 * @since 1.0
 * @return void
 */
function rcp_hide_single_topic() {

	$topic_id  = get_the_ID();
	$post_type = get_post_type( $topic_id );

	if ( ! function_exists( 'bbp_get_topic_post_type' ) ) {
		return;
	}

	if ( bbp_get_topic_post_type() !== $post_type ) {
		return;
	}

	if ( rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {
		return;
	}

	if ( is_user_logged_in() ) {
		$redirect = home_url();
	} else {
		$redirect = bbp_get_topic_permalink( bbp_get_topic_id( $topic_id ) );
	}
	wp_redirect( esc_url_raw( home_url( 'wp-login.php?redirect_to=' . urlencode( $redirect ) ) ) );
	exit;
}

add_action( 'template_redirect', 'rcp_hide_single_topic' );

/**
 * Filter topic content if the current user doesn't have access.
 * This ensures restricted topic content is not accessible via RSS.
 *
 * @param string $content  Topic content.
 * @param int    $topic_id ID of the topic.
 *
 * @since 1.0.1
 * @return string
 */
function rcp_bbpress_filter_topic_content( $content, $topic_id ) {

	$topic = bbp_get_topic( $topic_id );

	if ( empty( $topic ) ) {
		return $content;
	}

	if ( rcp_bbp_can_access_topic( $topic_id ) ) {
		return $content;
	}

	if ( function_exists( 'rcp_get_restricted_content_message' ) ) {
		return rcp_get_restricted_content_message();
	}

	return __( 'You must be a premium user to view this content.', 'rcp-bbpress' );

}

add_filter( 'bbp_get_topic_content', 'rcp_bbpress_filter_topic_content', 10, 2 );