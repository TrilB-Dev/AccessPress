<?php
/**
 * Google reCAPTCHA widget and verification helper.
 *
 * @package AccessPress\Includes\UserManagement\Security\reCAPTCHA
 */
namespace AccessPress\Includes\UserManagement\Security\reCAPTCHA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class reCAPTCHA {
	/**
	 * Render a Google reCAPTCHA widget.
	 *
	 * @param array<string, mixed> $args Widget settings.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		$site_key = (string) ( $args['site_key'] ?? '' );
		if ( '' === $site_key ) {
			return '';
		}

		$theme    = sanitize_key( (string) ( $args['theme'] ?? 'light' ) );
		$size     = sanitize_key( (string) ( $args['size'] ?? 'normal' ) );
		$language = sanitize_key( (string) ( $args['language'] ?? '' ) );
		$action   = sanitize_key( (string) ( $args['action'] ?? 'submit' ) );

		$attributes = array(
			'class' => 'g-recaptcha',
			'data-sitekey' => esc_attr( $site_key ),
			'data-theme' => esc_attr( $theme ),
			'data-size' => esc_attr( $size ),
			'data-action' => esc_attr( $action ),
		);
		if ( '' !== $language ) {
			$attributes['data-lang'] = esc_attr( $language );
		}

		$widget = '<div ' . self::attributes_to_string( $attributes ) . '></div>';
		$widget .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';

		return $widget;
	}

	/**
	 * Verify a Google reCAPTCHA response token using the Google API.
	 *
	 * @param string $token  Client-side response token.
	 * @param string $secret Secret key for verification.
	 * @return bool
	 */
	public static function verify( string $token = '', string $secret = '' ): bool {
		if ( '' === $token || '' === $secret ) {
			return false;
		}

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'timeout' => 15,
				'body'    => array(
					'secret'   => $secret,
					'response' => $token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		return is_array( $body ) && ! empty( $body['success'] );
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