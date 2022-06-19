<?php
/**
 * The file contains the useful wrapper functions.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include the template file.
 *
 * @since 1.0.0
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path with trailing '/'. (default: '').
 */
function gpps_get_templates( $template_name, $args = array(), $template_path = '' ) {
	$template = GPPS_DIR_PATH . 'templates/' . $template_path . $template_name;

	/**
	 * Allow 3rd party plugin to filter template file from their plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template      Absolute path of the template file.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. Can be an empty array.
	 * @param string $template_path Template path with trailing '/'.
	 *                              Can be an empty string.
	 */
	$filter_template = apply_filters( 'gpps_get_template', $template, $template_name, $args, $template_path );

	extract( $args ); // @codingStandardsIgnoreLine

	include $template;
}

/**
 * Return the assets directory URI with trailing slash.
 *
 * @since 1.0.0
 *
 * @return string Return the assets directory URI.
 */
function gpps_get_assets_uri() {
	return GPPS_DIR_URI . 'assets/';
}

/**
 * Return the assets directory with trailing slash.
 *
 * @since 1.0.0
 *
 * @return string Return the assets directory.
 */
function gpps_get_assets_dir() {
	return GPPS_DIR_PATH . 'assets/';
}
