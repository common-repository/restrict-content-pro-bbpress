<?php
/**
 * Plugin Name: Restrict Content Pro - bbPress
 * Plugin URL: https://restrictcontentpro.com/downloads/bbpress/
 * Description: Adds support for bbPress forums and topics restriction to Restrict Content Pro
 * Version: 1.0.2
 * Author: iThemes, LLC
 * Author URI: https://ithemes.com
 * Contributors: jthillithemes, layotte, ithemes
 * iThemes Package: restrict-content-pro-bbpress
 */

/**
 * Load plugin text domain for translations.
 *
 * @since 1.0
 * @return void
 */
function rcp_bbp_load_textdomain() {
	load_plugin_textdomain( 'rcp_bbpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'rcp_bbp_load_textdomain' );

/**
 * Include plugin files.
 */
include dirname( __FILE__ ) . '/includes/metaboxes.php';
include dirname( __FILE__ ) . '/includes/topic-functions.php';
include dirname( __FILE__ ) . '/includes/reply-functions.php';
include dirname( __FILE__ ) . '/includes/feedback-filters.php';
include dirname( __FILE__ ) . '/includes/forum-functions.php';

if ( ! function_exists( 'ithemes_restrict_content_pro_bbpress_updater_register' ) ) {
	function ithemes_restrict_content_pro_bbpress_updater_register( $updater ) {
		$updater->register( 'REPO', __FILE__ );
	}
	add_action( 'ithemes_updater_register', 'ithemes_restrict_content_pro_bbpress_updater_register' );

	require( __DIR__ . '/lib/updater/load.php' );
}