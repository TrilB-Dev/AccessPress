<?php
/**
 * Template wrapper for the frontend login screen.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */
namespace AccessPress\Includes\UserManagement\Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LoginTemplate {
	/**
	 * Render the login template.
	 *
	 * @param array<string, mixed> $args Template arguments.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		$redirect_to = ! empty( $args['redirect_to'] ) ? esc_url_raw( (string) $args['redirect_to'] ) : home_url( '/' );
		ob_start();
		?>
		<div class="accesspress-login-template">
			<?php echo Login::render_form( array( 'redirect_to' => $redirect_to ) ); ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}