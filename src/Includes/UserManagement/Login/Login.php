<?php
/**
 * Frontend login support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */

namespace AccessPress\Includes\UserManagement\Login;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Includes\Settings\Settings;
use AccessPress\Public\Templates\Login as LoginTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Login {
	/**
	 * Register login shortcodes.
	 *
	 * @return void
	 */
	public static function register_shortcodes(): void {
		ShortcodeHelper::register_many(
			array(
				ShortcodeHelper::define( 'accesspress_login', array( self::class, 'render_shortcode' ) ),
			)
		);
	}

	/**
	 * Render the shortcode output for the login form.
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
	 * Render the login form markup.
	 *
	 * @param array<string, mixed> $args Optional shortcode/form arguments.
	 * @return string
	 */
	public static function render_form( array $args = array() ): string {
		return LoginTemplate::render( $args );
	}

	/**
	 * Return the configured frontend login identifier mode.
	 *
	 * @return string
	 */
	private static function get_identifier_mode(): string {
		$mode = Settings::get( 'login_identifier_mode', 'default' );
		if ( ! in_array( (string) $mode, array( 'default', 'username', 'email' ), true ) ) {
			return 'default';
		}

		return (string) $mode;
	}

	/**
	 * Process a submitted login form.
	 *
	 * @param string $redirect_to URL to redirect to after success or failure.
	 * @return void
	 */
	public static function process_login( string $redirect_to = '' ): void {
		$username = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
		$password = isset( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';
		$redirect = $redirect_to;
		if ( '' === $redirect ) {
			$redirect = home_url( '/' );
		}

		if ( '' === $username || '' === $password ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'login', $redirect ) );
			exit;
		}

		$mode = self::get_identifier_mode();
		if ( 'email' === $mode && ! is_email( $username ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'login', $redirect ) );
			exit;
		}
		if ( 'username' === $mode && false !== strpos( $username, '@' ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'login', $redirect ) );
			exit;
		}

		$credentials = array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => ! empty( $_POST['remember'] ),
		);

		$credentials = apply_filters( 'accesspress_login_credentials', $credentials, $username, $password );
		$user        = wp_signon( $credentials, is_ssl() ? 'https' : 'http' );
		if ( is_wp_error( $user ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'login', $redirect ) );
			exit;
		}

		wp_safe_redirect( $redirect );
		exit;
	}
}