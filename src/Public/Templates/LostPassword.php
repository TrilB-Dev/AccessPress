<?php
/**
 * Public lost-password template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LostPassword {
	/**
	 * Render the lost-password form template.
	 *
	 * @param array<string, mixed> $args Template args.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		if ( Settings::get_bool( 'elementor_integration', false ) ) {
			return '';
		}

		$template_path = self::locate_theme_template( 'lost-password' );
		if ( '' !== $template_path ) {
			ob_start();
			include $template_path;
			return (string) ob_get_clean();
		}

		$mode = Settings::get( 'login_identifier_mode', 'default' );
		$mode = in_array( (string) $mode, array( 'default', 'username', 'email' ), true ) ? (string) $mode : 'default';
		$label = 'email' === $mode ? __( 'Email', 'accesspress' ) : ( 'username' === $mode ? __( 'Username', 'accesspress' ) : __( 'Username or email', 'accesspress' ) );
		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-lost-password-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-lost-password-user', $label ); ?>
				<?php echo FormFieldHelper::input( 'user_login', '', array( 'id' => 'accesspress-lost-password-user', 'required' => true ) ); ?>
			</div>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_lost_password" value="1" />
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Reset password', 'accesspress' ); ?></button>
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

	/**
	 * Render the lost-password link for the login form.
	 *
	 * @param string $label Optional link label.
	 * @return string
	 */
	public static function render_link( string $label = '' ): string {
		$label = '' !== $label ? $label : __( 'Lost your password?', 'accesspress' );
		return '<a href="' . esc_url( wp_lostpassword_url() ) . '">' . esc_html( $label ) . '</a>';
	}
}