<?php
/**
 * Lost password support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */
namespace AccessPress\Includes\UserManagement\Login;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Public\Templates\LostPassword as LostPasswordTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LostPassword {
	/**
	 * Register lost-password shortcodes.
	 *
	 * @return void
	 */
	public static function register_shortcodes(): void {
		ShortcodeHelper::register_many(
			array(
				ShortcodeHelper::define( 'accesspress_lost_password', array( self::class, 'render_shortcode' ) ),
			)
		);
	}

	/**
	 * Render the shortcode output for the lost-password form.
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