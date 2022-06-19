<?php
/**
 * Plugin Name:       Guest Post Page Submission
 * Plugin URI:        https://example.com/plugins/guest-post-submission/
 * Description:       Allows guest user to create a post.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shailesh Vishwakarma
 * Author URI:        https://github.com/shaileshdevs
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       guest-post-page-submission
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'GPPS_DIR_PATH' ) ) {
	define( 'GPPS_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'GPPS_DIR_URI' ) ) {
	define( 'GPPS_DIR_URI', plugin_dir_url( __FILE__ ) );
}

// Include the files.
require_once 'file-includes.php';
