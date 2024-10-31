<?php
/**
 * Forum Functions
 *
 * @package     RCP\bbPress\Forum Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines whether a given forum is restricted to paid members.
 *
 * @param int $forum_id ID of the forum to check.
 *
 * @since 1.0
 * @return bool
 */
function rcp_forum_is_premium( $forum_id = null ) {

	if ( ! function_exists( 'bbp_get_forum_id' ) ) {
		return false;
	}

	if ( is_null( $forum_id ) ) {
		$forum_id = bbp_get_forum_id();
	}

	return get_post_meta( $forum_id, '_is_forum_paid', true ) ? true : false;
}

/**
 * Determines whether or not the current user can access a given forum.
 *
 * @param int $forum_id ID of the forum to check.
 *
 * @return bool
 */
function rcp_bbp_can_access_forum( $forum_id = 0 ) {

	if ( ! function_exists( 'rcp_is_active' ) ) {
		return true;
	}

	$ret                  = true;
	$user_id              = get_current_user_id();
	$paid_only            = rcp_forum_is_premium( $forum_id );
	$access_level         = get_post_meta( $forum_id, 'rcp_access_level', true );
	$subscriptions        = get_post_meta( $forum_id, 'rcp_subscription_level', true );
	$has_paid_membership  = function_exists( 'rcp_user_has_paid_membership' ) ? rcp_user_has_paid_membership( $user_id ) : rcp_is_active( $user_id );
	$membership_level_ids = function_exists( 'rcp_get_customer_membership_level_ids' ) ? rcp_get_customer_membership_level_ids() : array( rcp_get_subscription_id() );

	// Return false if the forum is paid and the current user is not active/paid.
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

	// Moderators can always access forums
	if ( current_user_can( 'moderate' ) ) {
		$ret = true;
	}

	/**
	 * Filters the forum access.
	 *
	 * @param bool $ret      Whether or not the current user can access the forum.
	 * @param int  $forum_id ID of the forum being checked.
	 * @param int  $user     ID of the user.
	 *
	 * @since 1.0
	 */
	return apply_filters( 'rcp_bbp_can_access_forum', $ret, $forum_id, $user_id );

}

/**
 * Remove the RCP user level checks action.
 * This is only needed in RCP versions below 2.7.
 *
 * @since 1.0
 * @return void
 */
function rcp_bbp_remove_core_user_checks() {
	if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
		remove_action( 'loop_start', 'rcp_user_level_checks', 10 );
	}
}

add_action( 'wp_head', 'rcp_bbp_remove_core_user_checks' );