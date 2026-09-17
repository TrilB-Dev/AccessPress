<?php
/**
 * Editor class for managing the editing and saving of AccessPress pages.
 *
 * @package AccessPress\Includes\Core
 */
namespace AccessPress\Includes\Core;

use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Editor {
	public static function save_page( int $page_id, ?\WP_Post $page = null ): bool {
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) || 'save_accesspress_page' !== ( $_POST['accesspress_action'] ?? '' ) || ! check_admin_referer( 'accesspress_save_accesspress_page', 'accesspress_save_accesspress_page_nonce' ) ) {
			return false;
		}
		$page = $page_id ? get_post( $page_id ) : null;
		if ( ! $page_id && ! current_user_can( 'accesspress_page_create' ) ) {
			return false;
		}
		if ( $page_id && ( ! $page || PostType::ACCESSPRESS !== $page->post_type || ! current_user_can( 'accesspress_page_edit' ) || ( (int) $page->post_author !== get_current_user_id() && ! current_user_can( 'accesspress_page_edit_others' ) ) || ( 'publish' === $page->post_status && ! current_user_can( 'accesspress_page_edit_published' ) ) ) ) {
			return false;
		}

		$input = wp_unslash( $_POST['accesspress_page'] ?? array() );
		$input = is_array( $input ) ? $input : array();
		$title = SanitizationHelper::text( $input['title'] ?? '' );
		if ( '' === $title ) {
			return false;
		}

		if ( ! current_user_can( 'accesspress_page_publish' ) ) {
			return false;
		}

		$post_id = wp_insert_post(
			array(
				'ID'           => $page_id,
				'post_type'    => PostType::ACCESSPRESS,
				'post_title'   => $title,
				'post_content' => wp_kses_post( (string) ( $input['content'] ?? '' ) ),
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		update_post_meta( $post_id, '_accesspress_page_id', $page_id );
		return true;
	}

	public static function render_accesspress_page_form( ?\WP_Post $page = null ): void {
		?>
		<form method="post" class="card shadow-sm">
			<?php wp_nonce_field( 'accesspress_save_accesspress_page', 'accesspress_save_accesspress_page_nonce' ); ?>
			<input type="hidden" name="accesspress_action" value="save_accesspress_page">
			<div class="card-body"><div class="mb-3"><label class="form-label" for="accesspress-page-title"><?php esc_html_e( 'Page Title', 'accesspress' ); ?></label><input class="form-control" id="accesspress-page-title" name="accesspress_page[title]" value="<?php echo esc_attr( $page ? $page->post_title : '' ); ?>" required></div><?php FormFieldHelper::tinymce( 'accesspress-page-content', 'accesspress_page[content]', __( 'Page Content', 'accesspress' ), $page ? $page->post_content : '', 14, true ); ?></div>
			<div class="card-footer d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=accesspress-manage' ) ); ?>"><?php esc_html_e( 'Cancel', 'accesspress' ); ?></a><button class="btn btn-primary" type="submit"><?php echo esc_html( $page ? __( 'Save Page', 'accesspress' ) : __( 'Create Page', 'accesspress' ) ); ?></button></div>
		</form>
		<?php
	}
}



