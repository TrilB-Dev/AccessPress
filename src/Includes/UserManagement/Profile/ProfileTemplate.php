<?php
/**
 * Template wrapper for the frontend profile screen.
 *
 * @package AccessPress\Includes\UserManagement\Profile
 */
namespace AccessPress\Includes\UserManagement\Profile;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProfileTemplate {
	/**
	 * Render the profile template.
	 *
	 * @param array<string, mixed> $args Template arguments.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		return Profile::render_form( $args );
	}
}