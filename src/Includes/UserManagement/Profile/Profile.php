<?php
/**
 * Frontend profile support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Profile
 */
namespace AccessPress\Includes\UserManagement\Profile;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Public\Templates\Profile as PublicProfileTemplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Profile {
	/**
	 * Register profile shortcodes.
	 *
	 * @return void
	 */
	public static function register_shortcodes(): void {
		ShortcodeHelper::register_many(
			array(
				ShortcodeHelper::define( 'accesspress_profile', array( self::class, 'render_shortcode' ) ),
			)
		);
	}

	/**
	 * Render the shortcode output for the profile form.
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
	 * Render the profile form markup.
	 *
	 * @param array<string, mixed> $args Optional shortcode/form arguments.
	 * @return string
	 */
	public static function render_form( array $args = array() ): string {
		return PublicProfileTemplate::render( $args );
	}

	/**
	 * Process a submitted profile update.
	 *
	 * @param string $redirect_to URL to redirect to after save.
	 * @return void
	 */
	public static function process_profile_update( string $redirect_to = '' ): void {
		$current_user = wp_get_current_user();
		if ( ! $current_user instanceof \WP_User ) {
			return;
		}

		$display_name = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : $current_user->display_name;
		$email        = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : $current_user->user_email;
		$redirect     = $redirect_to;
		if ( '' === $redirect ) {
			$redirect = home_url( '/my-account/' );
		}

		wp_update_user(
			array(
				'ID'           => $current_user->ID,
				'display_name' => $display_name,
				'user_email'   => $email,
			)
		);

		wp_safe_redirect( $redirect );
		exit;
	}
}