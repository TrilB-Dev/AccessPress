<?php
/**
 * FunctionsUsers class for AccessPress plugin.
 *
 * @package AccessPress
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class FunctionsUsers {
    /**
	 * Register AccessPress and provider-backed plugin settings.
	 *
	 * @return void
	 */
	public function register_user_management(): void {
		register_setting( 'accesspress_groups', 'accesspress_groups', array( 'sanitize_callback' => array( $this, 'sanitize_groups' ) ) );
		register_setting( 'accesspress_login', 'accesspress_login', array( 'sanitize_callback' => array( $this, 'sanitize_login' ) ) );
		register_setting( 'accesspress_profile', 'accesspress_profile', array( 'sanitize_callback' => array( $this, 'sanitize_profile' ) ) );
		register_setting( 'accesspress_registration', 'accesspress_registration', array( 'sanitize_callback' => array( $this, 'sanitize_registration' ) ) );
	}
    /**
	 * Sanitize the general settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 */
	public function sanitize_groups( $input ): array {
		if ( ! current_user_can( 'accesspress_users_groups_edit' ) ) {
			return (array) Settings::get_group( Settings::GENERAL, array() );
		}
		$input = is_array( $input ) ? $input : array();

		foreach ( array( 'registration_page', 'login_page', 'my_account_page', 'lost_password_page', 'enable_multi_roles', 'enable_membership_groups' ) as $key ) {
			$input[ $key ] = $this->resolve_page_setting( $key, $input[ $key ] ?? '' );
			Settings::set( $key, $input[ $key ] );
		}

		return $input;
	}
}