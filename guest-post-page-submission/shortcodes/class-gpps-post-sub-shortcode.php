<?php
/**
 * Post Submission Form
 *
 * Post submission form for guest user.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gpps_Post_Sub_Shortcode' ) ) {
	/**
	 * Class for handling Post Submission form.
	 *
	 * @since 1.0.0
	 */
	class Gpps_Post_Sub_Shortcode {
		/**
		 * This class's instance.
		 *
		 * @since 1.0.0
		 *
		 * @var Gpps_Post_Sub_Shortcode Singleton instance of the class.
		 */
		private static $instance = null;

		/**
		 * Return the single instance of the class.
		 *
		 * @since 1.0.0
		 *
		 * @return Gpps_Post_Sub_Shortcode Instance of the class Gpps_Post_Sub_Shortcode.
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
			add_shortcode( 'gpps_post_submission_form', array( $this, 'display_post_submission_form' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

			add_action( 'wp_ajax_nopriv_post_form_submission', array( $this, 'save_post_form' ) );
		}

		/**
		 * Enqueue the scripts and styles required.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function enqueue_scripts_styles() {
			global $post;

			/**
			 * Don't enqueue scripts and styles if:
			 *  - User is logged in.
			 *  - The shortcode is not present on the page.
			 */
			if ( ! is_user_logged_in() && has_shortcode( $post->post_content, 'gpps_post_submission_form' ) ) {
				// Enqueue styles.
				wp_enqueue_style(
					'gpps-bootstrap-css',
					gpps_get_assets_uri() . 'css/bootstrap.min.css',
					array(),
					'4.0.0',
				);

				wp_enqueue_style(
					'gpps-post-submission-css',
					gpps_get_assets_uri() . 'css/post-submission.css',
					array(),
					filemtime( gpps_get_assets_dir( 'css/post-submission.css' ) ),
				);

				// Enqueue scripts.
				wp_enqueue_script(
					'gpps-post-submission-js',
					gpps_get_assets_uri() . 'js/post-submission.js',
					array( 'jquery' ),
					filemtime( gpps_get_assets_dir( 'js/post-submission.js' ) ),
					true
				);

				// Localize data.
				$gpps_post_sub_data = array(
					'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
					'post_title_required_error' => __( 'Post title is a mandatory field.', 'guest-post-page-submission' ),
				);
				wp_localize_script( 'gpps-post-submission-js', 'gpps_post_sub_data', $gpps_post_sub_data );
			}

		}

		/**
		 * Return the form content to submit the post.
		 *
		 * Return the form content. The form content would be visible to
		 * non-logged in users only.
		 *
		 * @since 1.0.0
		 *
		 * @return string Return the form content if user is non-logged in,
		 *                empty string otherwise.
		 */
		public function display_post_submission_form() {
			// If user is logged in, return.
			if ( is_user_logged_in() ) {
				return '';
			}

			// Show the form only to the guest user.
			ob_start();

			gpps_get_templates( 'post-submission-form.php' );

			return ob_get_clean();
		}

		/**
		 * Validate, sanitize and save the post form data.
		 *
		 * @since 1.0.0
		 *
		 * @return string JSON containing the response.
		 */
		public function save_post_form() {
			$response = array();

			/**
			 * Don't create a post if:
			 *   1. User is logged in.
			 *   2. Nonce is not valid.
			 */
			if ( is_user_logged_in() || false === check_ajax_referer( 'gpps-submit-post-form', 'gpps-submit-post-form', false ) ) {
				$response = array(
					'status'  => 'failure',
					'message' => __( 'You are not authorized.', 'guest-post-page-submission' ),
				);
			} else {
				// Sanitize.
				$post_title   = isset( $_POST['gpps-post-title'] ) ? sanitize_text_field( wp_unslash( $_POST['gpps-post-title'] ) ) : '';
				$post_desc    = isset( $_POST['gpps-post-description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gpps-post-description'] ) ) : '';
				$post_excerpt = isset( $_POST['gpps-post-excerpt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gpps-post-excerpt'] ) ) : '';

				// Validate.
				// Validate image.
				$is_image_valid = $this->validate_featured_image();

				if ( true !== $is_image_valid ) {
					// If image is not valid.
					$response = array(
						'status'  => 'failure',
						'message' => $is_image_valid,
					);
				}

				// Validate post title.
				if ( empty( $response ) && empty( $post_title ) ) {
					// If post title is empty.
					$response = array(
						'status'  => 'failure',
						'message' => __( 'Post title is a mandatory field.', 'guest-post-page-submission' ),
					);
				}

				// Create a post.
				if ( empty( $response ) ) {
					// If response is empty.
					$post_data = array(
						'post_title'   => $post_title,
						'post_excerpt' => $post_excerpt,
						'post_content' => $post_desc,
						'post_author'  => 0,
						'post_type'    => 'post',
						'post_status'  => 'pending',
					);

					/**
					 * Filter to modify the post data to be inserted in DB.
					 *
					 * @since 1.0.0
					 *
					 * @param array $post_data Array of data that make up a post to update
					 *                         or insert.
					 */
					$post_data = apply_filters( 'gpps_post_form_data', $post_data );

					/**
					 * Perform an action before post data is saved in DB.
					 *
					 * @since 1.0.0
					 *
					 * @param array $post_data Array of data that make up a post to update
					 *                         or insert.
					 */
					do_action( 'gpps_before_post_saved_in_db', $post_data );

					$post_insert_status = wp_insert_post( $post_data );

					/**
					 * Perform an action after post data is saved in DB.
					 *
					 * @since 1.0.0
					 *
					 * @param array $post_data Array of data that make up a post to update
					 *                         or insert.
					 * @param int|WP_Error $post_insert_status Post insertion status.
					 *                         The post ID on success. The value 0 or WP_Error
					 *                         on failure.
					 */
					do_action( 'gpps_after_post_saved_in_db', $post_data, $post_insert_status );

					$attachment_insert_status = $this->upload_featured_image( $post_insert_status );

					// If a valid post id.
					if ( $post_insert_status > 0 && ( $attachment_insert_status > 0 || true === $attachment_insert_status ) ) {
						// Successful insertion.
						$response = array(
							'status'  => 'success',
							'message' => __( 'Post has been created.', 'guest-post-page-submission' ),
						);

						$this->send_moderation_email( $post_insert_status );
					} else {
						// Failure insertion.
						$message = is_wp_error( $post_insert_status ) ? $post_insert_status->get_error_message() : $attachment_insert_status;

						$response = array(
							'status'  => 'failure',
							'message' => $message,
						);
					}
				}
			}

			/**
			 * Filter to modify the form submission response.
			 *
			 * @since 1.0.0
			 *
			 * @param array $response Array containing the response.
			 */
			$response = apply_filters( 'gpps_save_post_form_reposnse', $response );

			echo wp_json_encode( $response );

			wp_die();
		}

		/**
		 * Validate the feature image uploaded.
		 *
		 * @since 1.0.0
		 *
		 * @return bool|string Return true if image is valid, error message otherwise.
		 */
		public function validate_featured_image() {
			if ( empty( $_FILES ) ) {
				return true;
			}

			$tempname = empty( $_FILES['file']['tmp_name'] ) ? '' : $_FILES['file']['tmp_name'];

			if ( file_is_valid_image( $tempname ) ) {
				return true;
			} else {
				return __( 'Only image is allowed.', 'guest-post-page-submission' );
			}
		}

		/**
		 * Upload the featured image.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_parent_id Post id for which this image will be used.
		 *
		 * @return true|int|string Return true if no file is uploaded,
		 *                         attachment ID on success,
		 *                         error message on failure.
		 */
		public function upload_featured_image( $post_parent_id ) {
			if ( empty( $_FILES ) ) {
				return true;
			}

			$filename = empty( $_FILES['file']['name'] ) ? '' : $_FILES['file']['name'];
			$tempname = empty( $_FILES['file']['tmp_name'] ) ? '' : $_FILES['file']['tmp_name'];

			$upload_file = wp_upload_bits( $filename, null, file_get_contents( $tempname ) );

			if ( ! $upload_file['error'] ) {
				$wp_filetype = wp_check_filetype( $filename, null );

				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_parent'    => $post_parent_id,
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
					'post_content'   => '',
					'post_status'    => 'publish',
				);

				$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_parent_id );

				if ( is_wp_error( $attachment_id ) ) {
					return $attachment_id->get_error_message();
				} else {
					// If attachment post was successfully created, insert it as a thumbnail to the post $post_id.
					require_once ABSPATH . 'wp-admin/includes/image.php';

					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );

					wp_update_attachment_metadata( $attachment_id, $attachment_data );
					set_post_thumbnail( $post_parent_id, $attachment_id );

					return $attachment_id;
				}
			} else {
				return $upload_file['error'];
			}
		}

		/**
		 * Send moderation email to admin.
		 *
		 * Send email to admin when a post is created from the custom form.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id Post id.
		 *
		 * @return void
		 */
		public function send_moderation_email( $post_id ) {
			$post_edit_link = admin_url( sprintf( 'post.php?post=%d&action=edit', $post_id ) );
			$subject        = __( 'Post created from GPPS form.', 'guest-post-page-submission' );

			/* translators: 1: opening anchor tag 2: closing anchor tag */
			$message = sprintf( __( 'A post has been created from GPPS form. The %1$spost%2$s is waiting for an admin approval.', 'guest-post-page-submission' ), '<a href="' . esc_url( $post_edit_link ) . '">', '</a>' );

			wp_mail( get_option( 'admin_email' ), $subject, $message );
		}
	}
}
