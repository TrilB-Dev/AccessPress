<?php
/**
 * Public lost-password template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

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
		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-lost-password-form">
			<div class="mb-3">
				<?php echo FormFieldHelper::label( 'accesspress-lost-password-user', __( 'Username or email', 'accesspress' ) ); ?>
				<?php echo FormFieldHelper::input( 'user_login', '', array( 'id' => 'accesspress-lost-password-user', 'required' => true ) ); ?>
			</div>
			<?php wp_nonce_field( 'lost_password', 'lost_password_nonce' ); ?>
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Reset password', 'accesspress' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
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