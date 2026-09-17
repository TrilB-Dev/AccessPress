<?php
/**
 * Cloudflare Turnstile widget and verification helper.
 *
 * @package AccessPress\Includes\UserManagement\Security\Turnstile
 */
namespace AccessPress\Includes\UserManagement\Security\Turnstile;

use Symfony\Component\HttpClient\Psr18Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Turnstile {
	/**
	 * Render a Cloudflare Turnstile widget.
	 *
	 * @param array<string, mixed> $args Widget settings.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		$site_key = (string) ( $args['site_key'] ?? '' );
		if ( '' === $site_key ) {
			return '';
		}

		$theme = sanitize_key( (string) ( $args['theme'] ?? 'light' ) );
		$language = sanitize_key( (string) ( $args['language'] ?? '' ) );
		$size = sanitize_key( (string) ( $args['size'] ?? 'normal' ) );

		$attrs = array(
			'class' => 'cf-turnstile',
			'data-sitekey' => esc_attr( $site_key ),
			'data-theme' => esc_attr( $theme ),
			'data-size' => esc_attr( $size ),
		);
		if ( '' !== $language ) {
			$attrs['data-language'] = esc_attr( $language );
		}

		$widget = '<div ' . self::attributes_to_string( $attrs ) . '></div>';
		$widget .= '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

		return $widget;
	}

	/**
	 * Verify a Cloudflare Turnstile response token using the official composer package.
	 *
	 * @param string $token  Client-side response token.
	 * @param string $secret Secret key for verification.
	 * @return bool
	 */
	public static function verify( string $token = '', string $secret = '' ): bool {
		if ( '' === $token || '' === $secret ) {
			return false;
		}

		try {
			$turnstile = new \Turnstile\Turnstile( new Psr18Client(), $secret );
			$response = $turnstile->verify( $token, self::get_client_ip() );
			return (bool) $response->success;
		} catch ( \Throwable $exception ) {
			return false;
		}
	}

	/**
	 * Get the current client IP address for verification.
	 *
	 * @return string|null
	 */
	private static function get_client_ip(): ?string {
		$keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		foreach ( $keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				if ( false !== strpos( $ip, ',' ) ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				return $ip;
			}
		}

		return null;
	}

	/**
	 * Convert an attribute array into an HTML attribute string.
	 *
	 * @param array<string, string> $attributes Attribute values.
	 * @return string
	 */
	private static function attributes_to_string( array $attributes ): string {
		$parts = array();
		foreach ( $attributes as $name => $value ) {
			$parts[] = sprintf( '%s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}

		return implode( ' ', $parts );
	}
}