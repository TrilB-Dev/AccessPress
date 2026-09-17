<?php
/**
 * Template wrapper for the frontend registration screen.
 *
 * @package AccessPress\Includes\UserManagement\Registration
 */
namespace AccessPress\Includes\UserManagement\Registration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RegistrationTemplate {
	/**
	 * Render the registration template.
	 *
	 * @param array<string, mixed> $args Template arguments.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		return Registration::render_form( $args );
	}
}