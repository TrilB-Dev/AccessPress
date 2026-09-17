<?php
/**
 * Security gateway for AccessPress user-management.
 *
 * @package AccessPress\Includes\UserManagement\Security
 */

namespace AccessPress\Includes\UserManagement\Security;

use AccessPress\Includes\UserManagement\Security\Turnstile\Turnstile;
use AccessPress\Includes\UserManagement\Security\reCAPTCHA\reCAPTCHA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Security {
	/**
	 * Render the configured Google reCAPTCHA widget.
	 *
	 * @param array<string, mixed> $args Widget options.
	 * @return string
	 */
	public static function render_recaptcha( array $args = array() ): string {
		return reCAPTCHA::render( $args );
	}

	/**
	 * Verify a submitted Google reCAPTCHA token.
	 *
	 * @param string $token Response token from the client.
	 * @param string $secret Secret key.
	 * @return bool
	 */
	public static function verify_recaptcha( string $token = '', string $secret = '' ): bool {
		return reCAPTCHA::verify( $token, $secret );
	}

	/**
	 * Render the configured Cloudflare Turnstile widget.
	 *
	 * @param array<string, mixed> $args Widget options.
	 * @return string
	 */
	public static function render_turnstile( array $args = array() ): string {
		return Turnstile::render( $args );
	}

	/**
	 * Verify a submitted Cloudflare Turnstile token.
	 *
	 * @param string $token Response token from the client.
	 * @param string $secret Secret key.
	 * @return bool
	 */
	public static function verify_turnstile( string $token = '', string $secret = '' ): bool {
		return Turnstile::verify( $token, $secret );
	}
}