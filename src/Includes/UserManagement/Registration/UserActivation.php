<?php
/**
 * User activation lifecycle for AccessPress registration.
 *
 * @package AccessPress\Includes\UserManagement\Registration
 */
namespace AccessPress\Includes\UserManagement\Registration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserActivation {
	/**
	 * Register a user via the AccessPress core flow.
	 *
	 * @param array<string, mixed> $data User registration data.
	 * @return int|\WP_Error
	 */
	public static function register_user( array $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'user_login'   => '',
				'user_email'   => '',
				'user_pass'    => '',
				'display_name' => '',
			)
		);

		if ( '' === $data['user_login'] || '' === $data['user_email'] || '' === $data['user_pass'] ) {
			return new \WP_Error( 'accesspress_registration_missing_fields', __( 'All registration fields are required.', 'accesspress' ) );
		}

		$user_id = wp_create_user( $data['user_login'], $data['user_pass'], $data['user_email'] );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		wp_update_user(
			array(
				'ID'           => $user_id,
				'display_name' => '' !== $data['display_name'] ? $data['display_name'] : $data['user_login'],
			)
		);

		self::activate_user( $user_id );

		return $user_id;
	}

	/**
	 * Mark a user as activated in the AccessPress core user-management flow.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function activate_user( int $user_id ): bool {
		if ( $user_id <= 0 ) {
			return false;
		}

		update_user_meta( $user_id, 'accesspress_user_activated', true );
		update_user_meta( $user_id, 'accesspress_registration_status', 'activated' );

		return true;
	}

	/**
	 * Determine whether the user has been activated.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function is_user_activated( int $user_id ): bool {
		if ( $user_id <= 0 ) {
			return false;
		}

		return (bool) get_user_meta( $user_id, 'accesspress_user_activated', true );
	}
}