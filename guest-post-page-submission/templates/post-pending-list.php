<?php
/**
 * Post pending list.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form class="gpps-pending-post-list-form mw-100">
	<?php wp_nonce_field( 'gpps-post-approve-nonce', 'gpps-post-approve-nonce' ); ?>
	<table id="gpps-post-pending-list-table" class="gpps-post-pending-list-table table table-striped table-bordered table-responsive-sm" style="width:100%">
		<thead>
			<tr>
				<?php
				foreach ( $gpps_theads as $key => $thead ) :
					?>
					<th class="gpps-<?php echo esc_attr( $key ); ?>">
						<?php
						echo esc_html( $thead );
						?>
					</th>
					<?php
				endforeach;
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			// The Loop.
			if ( $gpps_query->have_posts() ) {
				while ( $gpps_query->have_posts() ) {
					$gpps_query->the_post();
					$gpps_post_id        = get_the_ID();
					$gpps_post_edit_link = admin_url( sprintf( 'post.php?post=%d&action=edit', $gpps_post_id ) );
					?>
					<tr class="gpps-pending-post-list-tr-<?php echo esc_attr( $gpps_post_id ); ?>">
						<td class="gpps-post-id-td"><?php echo esc_html( $gpps_post_id ); ?></td>
						<td class="gpps-post-title-td"><?php echo esc_html( get_the_title() ); ?></td>
						<td class="gpps-post-excerpt-td"><?php echo wp_kses_post( get_the_excerpt() ); ?></td>
						<td class="gpps-post-edit-link-td"><a href="<?php echo esc_url( $gpps_post_edit_link ); ?>" target="_blank"><?php esc_html_e( 'Edit', 'guest-post-page-submission' ); ?></a></td>
						<td class="gpps-post-arppove-button-td"><input type="button" id="gpps-approve-<?php echo esc_attr( $gpps_post_id ); ?>" class="btn btn-primary gpps-approve-post" value="<?php echo esc_attr__( 'Approve', 'guest-post-page-submission' ); ?>" data-post-id="<?php echo esc_attr( $gpps_post_id ); ?>"/></td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td class="dataTables_empty" colspan="5"><?php esc_html_e( 'No data found', 'guest-post-page-submission' ); ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</form>
<?php
// Restore original Post Data.
wp_reset_postdata();
