<?php
/**
 * Frontend profile support for AccessPress user management.
 *
 * @package AccessPress\Includes\UserManagement\Profile
 */
namespace AccessPress\Includes\UserManagement\Profile;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Includes\UserManagement\Profile\ProfileTabs;
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

		$data = array(
			'ID' => $current_user->ID,
		);

		$is_admin = current_user_can( 'manage_options' );
		$tabs     = ProfileTabs::get_tabs();
		$fields   = array();
		foreach ( $tabs as $tab ) {
			if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
				$fields = array_merge( $fields, $tab['fields'] );
			}
		}

		if ( empty( $fields ) ) {
			$fields = ProfileFields::get_builtin_fields();
		}

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			if ( ! ProfileFields::can_edit_field( $field, $is_admin ) ) {
				continue;
			}

			$key = isset( $field['wp_field'] ) && '' !== $field['wp_field'] ? (string) $field['wp_field'] : (string) ( $field['key'] ?? '' );
			if ( '' === $key || ! isset( $_POST[ $field['key'] ?? $key ] ) ) {
				continue;
			}

			$value = wp_unslash( $_POST[ $field['key'] ?? $key ] );
			if ( 'user_email' === $key || 'user_pass' === $key || 'user_login' === $key ) {
				$data[ $key ] = 'user_email' === $key ? sanitize_email( $value ) : sanitize_text_field( $value );
				continue;
			}

			$data[ $key ] = sanitize_text_field( (string) $value );
		}

		if ( isset( $_POST['display_name'] ) && ProfileFields::can_edit_field( array( 'key' => 'display_name', 'wp_field' => 'display_name', 'editable' => true ), $is_admin ) ) {
			$data['display_name'] = sanitize_text_field( wp_unslash( $_POST['display_name'] ) );
		}

		if ( isset( $_POST['email'] ) && ProfileFields::can_edit_field( array( 'key' => 'user_email', 'wp_field' => 'user_email', 'editable' => true ), $is_admin ) ) {
			$data['user_email'] = sanitize_email( wp_unslash( $_POST['email'] ) );
		}

		if ( 1 === count( $data ) ) {
			return;
		}

		$redirect = $redirect_to;
		if ( '' === $redirect ) {
			$redirect = home_url( '/my-account/' );
		}

		wp_update_user( $data );

		wp_safe_redirect( $redirect );
		exit;
	}
}