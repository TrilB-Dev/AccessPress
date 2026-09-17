<?php
/**
 * Lost password support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */
namespace AccessPress\Includes\UserManagement\Login;

use AccessPress\Public\Templates\LostPassword as LostPasswordTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LostPassword {
	/**
	 * Render the lost password link text.
	 *
	 * @param string $label Optional link label.
	 * @return string
	 */
	public static function render_link( string $label = '' ): string {
		return LostPasswordTemplate::render_link( $label );
	}

	/**
	 * Render the lost password form.
	 *
	 * @param array<string, mixed> $args Optional arguments.
	 * @return string
	 */
	public static function render_form( array $args = array() ): string {
		return LostPasswordTemplate::render( $args );
	}
}