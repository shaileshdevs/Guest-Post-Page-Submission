<?php
/**
 * Post List
 *
 * Post list with pending status for admin approval.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gpps_Post_Pending_List' ) ) {
	/**
	 * Class to list the post with pending status.
	 *
	 * @since 1.0.0
	 */
	class Gpps_Post_Pending_List {
		/**
		 * This class's instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Gpps_Post_Pending_List Singleton instance of the class.
		 */
		private static $instance = null;

		/**
		 * Return the single instance of the class.
		 *
		 * @since 1.0.0
		 *
		 * @return Gpps_Post_Pending_List Instance of the class Gpps_Post_Pending_List.
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {
			add_shortcode( 'gpps_post_pending_list', array( $this, 'display_post_pending_list' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

			add_action( 'wp_ajax_gpps_post_approved', array( $this, 'approve_post' ) );
		}

		/**
		 * Return the post list with pending status.
		 *
		 * Return the post list with pending status for admin approval.
		 * The list would be visible to an administrator only.
		 *
		 * @since 1.0.0
		 *
		 * @return string Return the list if administrator, empty string otherwise.
		 */
		public function display_post_pending_list() {
			$user         = wp_get_current_user();
			$allowed_role = 'administrator';

			// If user is not an administrator.
			if ( ! in_array( $allowed_role, $user->roles ) ) {
				return '';
			}

			$query_args = array(
				'post_type'      => 'post',
				'post_status'    => 'pending',
				'fields'         => 'ids',
				'order'          => 'DESC',
				'orderby'        => 'ID',
				'posts_per_page' => -1,
			);

			$query = new WP_Query( $query_args );

			$args = array(
				'gpps_theads' => array(
					'post-id-head'             => __( 'ID', 'guest-post-page-submission' ),
					'post-title-head'          => __( 'Title', 'guest-post-page-submission' ),
					'post-excerpt-head'        => __( 'Excerpt', 'guest-post-page-submission' ),
					'post-edit-link-head'      => __( 'Edit Link', 'guest-post-page-submission' ),
					'post-approve-button-head' => __( 'Action', 'guest-post-page-submission' ),
				),
				'gpps_query'  => $query,
			);

			ob_start();

			// Show the list to an administrator.
			gpps_get_templates( 'post-pending-list.php', $args );

			return ob_get_clean();
		}

		/**
		 * Enqueue the scripts and styles.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function enqueue_scripts_styles() {
			global $post;
			$user = wp_get_current_user();

			/**
			 * Enqueue scripts and styles only if:
			 *  - User is an administrator.
			 *  - The shortcode is present on the page.
			 */
			if ( in_array( 'administrator', $user->roles ) && has_shortcode( $post->post_content, 'gpps_post_pending_list' ) ) {
				// Enqueue styles.
				wp_enqueue_style(
					'gpps-data-tables-css',
					gpps_get_assets_uri() . 'css/data-tables.min.css',
					array(),
					'1.12.1'
				);

				wp_enqueue_style(
					'gpps-bootstrap-css',
					gpps_get_assets_uri() . 'css/bootstrap.min.css',
					array(),
					'4.0.0',
				);

				// Enqueue scripts.
				wp_enqueue_script(
					'gpps-data-tables-js',
					gpps_get_assets_uri() . 'js/data-tables.min.js',
					array( 'jquery' ),
					'1.12.1',
					true
				);

				wp_enqueue_script(
					'gpps-post-pending-list-js',
					gpps_get_assets_uri() . 'js/post-pending-list.js',
					array( 'jquery' ),
					filemtime( gpps_get_assets_dir( 'js/post-pending-list.js' ) ),
					true
				);

				// Localize data.
				$gpps_post_pending_list = array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				);
				wp_localize_script( 'gpps-post-pending-list-js', 'gpps_post_pending_list', $gpps_post_pending_list );
			}
		}

		/**
		 * Approve the post.
		 *
		 * @since 1.0.0
		 *
		 * @return string JSON containing the response.
		 */
		public function approve_post() {
			$response = array();

			// Verify nonce.
			if ( false === check_ajax_referer( 'gpps-post-approve-nonce', 'gpps-post-approve-nonce', false ) ) {
				$response = array(
					'status'  => 'failure',
					'message' => __( 'You are not authorized.', 'guest-post-page-submission' ),
				);
			} else {
				$user = wp_get_current_user();
				// If the user is an administrator.
				if ( in_array( 'administrator', $user->roles ) ) {
					// Validate post id.
					$post_id            = isset( $_POST['gpps-post-id'] ) ? absint( $_POST['gpps-post-id'] ) : 0;
					$post_update_status = 0;

					if ( $post_id > 0 ) {
						// If valid post id.
						$post_data = array(
							'ID'          => $post_id,
							'post_status' => 'publish',
						);

						/**
						 * Perform an action before post is approved.
						 *
						 * @since 1.0.0
						 *
						 * @param array $post_data Array of post data that needs
						 *                         to be approved.
						 */
						do_action( 'gpps_before_post_approved', $post_data );

						// Change post status to publish.
						$post_update_status = wp_update_post( $post_data );

						/**
						 * Perform an action after post is approved.
						 *
						 * @since 1.0.0
						 *
						 * @param array $post_data Array of post data that needs
						 *                         to be approved.
						 * @param int|WP_Error $post_insert_status Post update status.
						 *                         The post ID on success. The value 0 or WP_Error
						 *                         on failure.
						 */
						do_action( 'gpps_after_post_approved', $post_data, $post_update_status );
					}

					if ( 0 === $post_update_status || is_wp_error( $post_update_status ) ) {
						// Error while approving the post.
						$response = array(
							'status'  => 'failure',
							'message' => __( 'There is an error while approving the post.', 'guest-post-page-submission' ),
						);
					} else {
						// Approved the post successfully.
						$response = array(
							'status'  => 'success',
							'message' => __( 'Approved.', 'guest-post-page-submission' ),
						);
					}
				} else {
					// If the user is not an administrator.
					$response = array(
						'status'  => 'failure',
						'message' => __( 'You are not authorized.', 'guest-post-page-submission' ),
					);
				}
			}

			/**
			 * Filter to modify the post approval response.
			 *
			 * @since 1.0.0
			 *
			 * @param array $response Array containing the response.
			 */
			$response = apply_filters( 'gpps_approved_post_response', $response );

			echo wp_json_encode( $response );

			wp_die();
		}
	}
}
