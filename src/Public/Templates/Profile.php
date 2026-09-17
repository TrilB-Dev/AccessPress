<?php
/**
 * Public profile template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Profile {
	/**
	 * Render the frontend profile template.
	 *
	 * @param array<string, mixed> $args Template args.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		$current_user = wp_get_current_user();
		if ( ! is_user_logged_in() || ! $current_user instanceof \WP_User ) {
			return '<p>' . esc_html__( 'Please log in to manage your profile.', 'accesspress' ) . '</p>';
		}

		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-profile-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-profile-display-name', __( 'Display name', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'display_name', $current_user->display_name, array( 'id' => 'accesspress-profile-display-name', 'required' => true ) ); ?>
			</div>
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-profile-email', __( 'Email', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'email', $current_user->user_email, array( 'id' => 'accesspress-profile-email', 'type' => 'email', 'required' => true ) ); ?>
			</div>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_profile" value="1" />
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Save profile', 'accesspress' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
	}
}