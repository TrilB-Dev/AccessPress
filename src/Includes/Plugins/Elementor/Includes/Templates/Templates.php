<?php
/**
 * AccessPress Elementor template loader.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Templates
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Templates {
	/**
	 * Render an AccessPress Elementor template.
	 *
	 * @param string $template Template identifier relative to the template root.
	 * @param array<string, mixed> $args Template variables.
	 * @return void
	 */
	public static function render( string $template, array $args = array() ): void {
		$normalized_template = trim( $template, '/' );
		$path                = self::resolve_path( $normalized_template );

		if ( ! is_readable( $path ) ) {
			return;
		}

		extract( $args, EXTR_SKIP );
		include $path;
	}

	/**
	 * Resolve a template path from the AccessPress template directory.
	 *
	 * @param string $template Template identifier.
	 * @return string
	 */
	private static function resolve_path( string $template ): string {
		$root = __DIR__;

		$path = $root . '/' . ltrim( $template, '/' ) . '.php';
		if ( is_readable( $path ) ) {
			return $path;
		}

		$normalized = str_replace( 'widgets/', '', $template );
		$normalized = trim( $normalized, '/' );
		$normalized = preg_replace( '/^accesspress-/', '', $normalized );
		if ( is_string( $normalized ) ) {
			$path = $root . '/' . $normalized . '.php';
			if ( is_readable( $path ) ) {
				return $path;
			}
		}

		$segments = array_filter( explode( '/', (string) $normalized ), 'strlen' );
		if ( ! empty( $segments ) ) {
			$directory = $segments[0];
			if ( 'login-register' === $directory || 'login_register' === $directory ) {
				$segments[0] = 'Login-Register';
			} elseif ( 'profile' === $directory ) {
				$segments[0] = 'Profile';
			} elseif ( 'fields' === $directory ) {
				$segments[0] = 'Fields';
			}

			$path = $root . '/' . implode( '/', $segments ) . '.php';
			if ( is_readable( $path ) ) {
				return $path;
			}
		}

		return $root . '/Login-Register/' . basename( (string) $normalized ) . '.php';
	}
}
