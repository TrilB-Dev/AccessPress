<?php
/**
 * Public login template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Login {
	/**
	 * Render the frontend login template.
	 *
	 * @param array<string, mixed> $args Template args.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		if ( is_user_logged_in() ) {
			return '<p>' . esc_html__( 'You are already logged in.', 'accesspress' ) . '</p>';
		}

		$redirect_to = ! empty( $args['redirect_to'] ) ? esc_url_raw( (string) $args['redirect_to'] ) : home_url( '/' );
		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-login-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-login-user', __( 'Username or email', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'log', '', array( 'id' => 'accesspress-login-user', 'required' => true ) ); ?>
			</div>
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-login-password', __( 'Password', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'pwd', '', array( 'id' => 'accesspress-login-password', 'type' => 'password', 'required' => true ) ); ?>
			</div>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_login" value="1" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Log in', 'accesspress' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
	}
}