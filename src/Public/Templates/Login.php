<?php
/**
 * Public login template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Settings\Settings;

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

		if ( Settings::get_bool( 'elementor_integration', false ) ) {
			return '';
		}

		$template_path = self::locate_theme_template( 'login' );
		if ( '' !== $template_path ) {
			ob_start();
			include $template_path;
			return (string) ob_get_clean();
		}

		$mode = Settings::get( 'login_identifier_mode', 'default' );
		$mode = in_array( (string) $mode, array( 'default', 'username', 'email' ), true ) ? (string) $mode : 'default';
		$label = 'email' === $mode ? __( 'Email', 'accesspress' ) : ( 'username' === $mode ? __( 'Username', 'accesspress' ) : __( 'Username or email', 'accesspress' ) );
		$redirect_to = ! empty( $args['redirect_to'] ) ? esc_url_raw( (string) $args['redirect_to'] ) : home_url( '/' );
		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-login-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-login-user', $label ); ?>
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

	/**
	 * Locate an active theme override for the template.
	 *
	 * @param string $template_name Template slug.
	 * @return string
	 */
	private static function locate_theme_template( string $template_name ): string {
		$paths = array( get_stylesheet_directory(), get_template_directory() );
		$paths = array_values( array_unique( array_filter( $paths ) ) );
		foreach ( $paths as $path ) {
			foreach ( array(
				$path . '/accesspress/templates/' . $template_name . '.php',
				$path . '/accesspress/' . $template_name . '.php',
				$path . '/templates/accesspress/' . $template_name . '.php',
			) as $candidate ) {
				if ( file_exists( $candidate ) ) {
					return $candidate;
				}
			}
		}

		return '';
	}
}