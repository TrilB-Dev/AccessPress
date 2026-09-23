<?php
/**
 * Lost password support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Login
 */
namespace AccessPress\Includes\UserManagement\Login;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Includes\Settings\Settings;
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

	/**
	 * Return the configured login identifier mode used by password reset.
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
	 * Process a submitted password reset request.
	 *
	 * @param string $redirect_to Redirect target after the request.
	 * @return void
	 */
	public static function process_lost_password( string $redirect_to = '' ): void {
		$user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) : '';
		$redirect   = $redirect_to;
		if ( '' === $redirect ) {
			$redirect = wp_login_url();
		}

		if ( '' === $user_login ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'lost_password', $redirect ) );
			exit;
		}

		$mode = self::get_identifier_mode();
		if ( 'email' === $mode && ! is_email( $user_login ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'lost_password', $redirect ) );
			exit;
		}
		if ( 'username' === $mode && false !== strpos( $user_login, '@' ) ) {
			wp_safe_redirect( add_query_arg( 'accesspress_error', 'lost_password', $redirect ) );
			exit;
		}

		retrieve_password( $user_login );
		wp_safe_redirect( add_query_arg( 'accesspress_message', 'reset_sent', $redirect ) );
		exit;
	}
}