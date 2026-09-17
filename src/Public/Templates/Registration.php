<?php
/**
 * Public registration template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Registration {
	/**
	 * Render the frontend registration template.
	 *
	 * @param array<string, mixed> $args Template args.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		if ( is_user_logged_in() ) {
			return '<p>' . esc_html__( 'You are already signed in.', 'accesspress' ) . '</p>';
		}

		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-register-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-register-username', __( 'Username', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'username', '', array( 'id' => 'accesspress-register-username', 'required' => true ) ); ?>
			</div>
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-register-email', __( 'Email', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'email', '', array( 'id' => 'accesspress-register-email', 'type' => 'email', 'required' => true ) ); ?>
			</div>
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-register-password', __( 'Password', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'password', '', array( 'id' => 'accesspress-register-password', 'type' => 'password', 'required' => true ) ); ?>
			</div>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_register" value="1" />
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Register', 'accesspress' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
	}
}