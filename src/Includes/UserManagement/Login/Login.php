<?php
/**
 * Frontend login support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */

namespace AccessPress\Includes\UserManagement\Login;

use AccessPress\Public\Templates\Login as LoginTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Login {
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

		$credentials = array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => true,
		);

		$user = wp_signon( $credentials, is_ssl() ? 'https' : 'http' );
		if ( is_wp_error( $user ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'login', $redirect ) );
			exit;
		}

		wp_safe_redirect( $redirect );
		exit;
	}
}