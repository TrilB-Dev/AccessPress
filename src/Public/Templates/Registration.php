<?php
/**
 * Public registration template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Settings\Settings;
use AccessPress\Includes\UserManagement\Profile\ProfileFields;
use AccessPress\Includes\UserManagement\Registration\RegistrationTabs;

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

		if ( Settings::get_bool( 'elementor_integration', false ) ) {
			return '';
		}

		$template_path = self::locate_theme_template( 'register' );
		if ( '' !== $template_path ) {
			ob_start();
			include $template_path;
			return (string) ob_get_clean();
		}

		$pages = RegistrationTabs::get_tabs();
		if ( empty( $pages ) ) {
			$pages = RegistrationTabs::get_default_tabs();
		}

		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-register-form" data-accesspress-registration-form>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_register" value="1" />

			<?php foreach ( $pages as $index => $page ) : ?>
				<div class="accesspress-registration-page" data-registration-page="<?php echo esc_attr( (string) $index ); ?>" <?php echo 0 === $index ? '' : 'style="display:none;"'; ?>>
					<?php foreach ( $page['fields'] ?? array() as $field ) : ?>
						<?php
						$field = is_array( $field ) ? ProfileFields::normalize_field( $field, 0 ) : array();
						if ( empty( $field ) ) {
							continue;
						}

						$key = (string) ( $field['key'] ?? '' );
						if ( '' === $key ) {
							continue;
						}

						$required = ! empty( $field['required'] );
						$type = (string) ( $field['type'] ?? 'text' );
						$name = $key;
						$id   = 'accesspress-register-' . sanitize_key( $key );
						$value = '';
						if ( 'confirm_password' === $key ) {
							$type = 'password';
						}
						?>
						<div class="mb-3">
							<?php echo FormFieldHelper::label( $id, (string) ( $field['label'] ?? $key ) ); ?>
							<?php echo FormFieldHelper::input( $name, $value, array( 'id' => $id, 'type' => $type, 'required' => $required, 'autocomplete' => 'new-password' ) ); ?>
						</div>
					<?php endforeach; ?>

					<?php if ( $index < count( $pages ) - 1 ) : ?>
						<div class="d-flex justify-content-end mt-4">
							<button type="button" class="btn btn-primary accesspress-registration-next" data-registration-next="<?php echo esc_attr( (string) ( $index + 1 ) ); ?>"><?php esc_html_e( 'Next', 'accesspress' ); ?></button>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<div class="mt-4">
				<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Register', 'accesspress' ); ?></button>
			</div>
		</form>

		<script>
			(function() {
				var form = document.querySelector('[data-accesspress-registration-form]');
				if (!form) {
					return;
				}
				var pages = form.querySelectorAll('[data-registration-page]');
				var nextButtons = form.querySelectorAll('.accesspress-registration-next');
				Array.prototype.forEach.call(nextButtons, function(button) {
					button.addEventListener('click', function() {
						var nextIndex = parseInt(button.getAttribute('data-registration-next'), 10);
						Array.prototype.forEach.call(pages, function(page, index) {
							page.style.display = index === nextIndex ? '' : 'none';
						});
					});
				});
			})();
		</script>
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