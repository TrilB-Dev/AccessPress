<?php
/**
 * Frontend registration support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Registration
 */
namespace AccessPress\Includes\UserManagement\Registration;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Public\Templates\Registration as PublicRegistrationTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Registration {
	/**
	 * Register registration shortcodes.
	 *
	 * @return void
	 */
	public static function register_shortcodes(): void {
		ShortcodeHelper::register_many(
			array(
				ShortcodeHelper::define( 'accesspress_register', array( self::class, 'render_shortcode' ) ),
			)
		);
	}

	/**
	 * Render the shortcode output for the registration form.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @param string|null $content Unused content.
	 * @param string $tag Unused tag.
	 * @return string
	 */
	public static function render_shortcode( array $atts = array(), ?string $content = null, string $tag = '' ): string {
		return self::render_form( $atts );
	}

	/**
	 * Render the registration form markup.
	 *
	 * @param array<string, mixed> $args Optional shortcode/form arguments.
	 * @return string
	 */
	public static function render_form( array $args = array() ): string {
		return PublicRegistrationTemplate::render( $args );
	}

	/**
	 * Process a submitted registration form and replace the default WordPress flow.
	 *
	 * @param string $redirect_to URL to redirect to after registration.
	 * @return void
	 */
	public static function process_registration( string $redirect_to = '' ): void {
		if ( ! get_option( 'users_can_register' ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'registration_disabled', $redirect_to ?: home_url( '/' ) ) );
			exit;
		}

		$username = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
		$redirect = $redirect_to ?: home_url( '/my-account/' );

		if ( '' === $username || '' === $email || '' === $password ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'registration', $redirect ) );
			exit;
		}

		$user_id = UserActivation::register_user(
			array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $username,
			)
		);

		if ( is_wp_error( $user_id ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', rawurlencode( $user_id->get_error_message() ), $redirect ) );
			exit;
		}

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
		wp_safe_redirect( home_url( '/my-account/' ) );
		exit;
	}
}