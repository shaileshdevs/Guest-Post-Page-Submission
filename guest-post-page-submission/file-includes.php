<?php
/**
 * Include all the necessary files.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the wrapper functions.
require_once 'includes/wrapper-functions.php';

// Include the class for post submission form shortcode.
require_once 'shortcodes/class-gpps-post-sub-shortcode.php';
Gpps_Post_Sub_Shortcode::get_instance();

// Include the class for post list shortcode with pending status.
require_once 'shortcodes/class-gpps-post-pending-list.php';
Gpps_Post_Pending_List::get_instance();
