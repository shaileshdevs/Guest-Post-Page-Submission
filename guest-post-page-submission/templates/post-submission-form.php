<?php
/**
 * Post submission HTML form.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="gpps-form" enctype="multipart/form-data">
	<?php
	wp_nonce_field( 'gpps-submit-post-form', 'gpps-submit-post-form' );
	?>
	<h2 class="gpps-form-title"><?php esc_html_e( 'Create A Post', 'guest-post-page-submission' ); ?></h2>
	<div class="form-group">
		<label for="gpps-post-title"><?php esc_html_e( 'Post Title *', 'guest-post-page-submission' ); ?></label>
		<input type="text" id="gpps-post-title" name="gpps-post-title" class="form-control" value="">
	</div>

	<div class="form-group">
		<label for="gpps-post-description"><?php esc_html_e( 'Description', 'guest-post-page-submission' ); ?></label>
		<textarea id="gpps-post-description" name="gpps-post-description" class="form-control" value="" rows="4"></textarea>
	</div>

	<div class="form-group">
		<label for="gpps-post-excerpt"><?php esc_html_e( 'Excerpt', 'guest-post-page-submission' ); ?></label>
		<textarea id="gpps-post-excerpt" name="gpps-post-excerpt" class="form-control" value=""></textarea>
	</div>

	<div class="form-group">
		<label for="gpps-post-featured-image"><?php esc_html_e( 'Featured Image', 'guest-post-page-submission' ); ?></label>
		<input type="file" id="gpps-post-featured-image" name="gpps-post-featured-image" class="form-control-file">
	</div>

	<div class="form-group">
		<input type="submit" name="gpps-post-form-submit" id="gpps-post-form-submit" class="gpps-post-form-submit btn btn-primary" value="Create" />
	</div>

	<div class="form-group invalid-feedback-wrapper">
		<p class="invalid-feedback"></p>
	</div>

	<div class="form-group valid-feedback-wrapper">
		<p class="valid-feedback"></p>
	</div>
</form>
