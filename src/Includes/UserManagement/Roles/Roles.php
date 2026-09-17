<?php
/**
 * AccessPress user-role manager.
 *
 * This layer works alongside WordPress roles by tracking which role slugs are
 * attached to a user and enforcing a configured maximum number of active roles.
 *
 * @package AccessPress\Includes\UserManagement\Roles
 */

namespace AccessPress\Includes\UserManagement\Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Roles {
	/**
	 * User meta key used to store AccessPress role assignment metadata.
	 *
	 * @var string
	 */
	private const USER_ROLES_META_KEY = 'accesspress_user_roles';

	/**
	 * Option key used to store the maximum number of roles per user.
	 *
	 * @var string
	 */
	private const MAX_ROLES_OPTION_KEY = 'accesspress_user_roles_max_count';

	/**
	 * Return the available WordPress role slugs.
	 *
	 * @return array<int, string>
	 */
	public static function get_available_roles(): array {
		if ( ! function_exists( 'wp_roles' ) ) {
			return array();
		}

		$roles = wp_roles()->roles;
		if ( ! is_array( $roles ) ) {
			return array();
		}

		return array_values( array_keys( $roles ) );
	}

	/**
	 * Return the role slugs assigned to a user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<int, string>
	 */
	public static function get_user_roles( int $user_id ): array {
		if ( $user_id <= 0 ) {
			return array();
		}

		$user = get_user_by( 'id', $user_id );
		if ( $user instanceof \WP_User ) {
			$roles = $user->roles;
		} else {
			$roles = get_user_meta( $user_id, self::USER_ROLES_META_KEY, true );
		}

		if ( ! is_array( $roles ) ) {
			$roles = array();
		}

		$available = self::get_available_roles();
		$roles     = array_values( array_unique( array_filter( array_map( 'strval', $roles ) ) ) );
		$roles     = array_values( array_intersect( $roles, $available ) );

		update_user_meta( $user_id, self::USER_ROLES_META_KEY, $roles );

		return $roles;
	}

	/**
	 * Sync a user's attached roles with the allowed WordPress roles and configured limit.
	 *
	 * @param int   $user_id WordPress user ID.
	 * @param array $roles   Role slug list.
	 * @return array<int, string>
	 */
	public static function sync_user_roles( int $user_id, array $roles ): array {
		if ( $user_id <= 0 ) {
			return array();
		}

		$available = self::get_available_roles();
		$normalized = array();
		foreach ( $roles as $role ) {
			$slug = sanitize_key( (string) $role );
			if ( '' !== $slug && in_array( $slug, $available, true ) ) {
				$normalized[] = $slug;
			}
		}

		$normalized = array_values( array_unique( $normalized ) );
		$max_roles  = self::get_max_roles();
		if ( $max_roles > 0 && count( $normalized ) > $max_roles ) {
			$normalized = array_slice( $normalized, 0, $max_roles );
		}

		$user = get_user_by( 'id', $user_id );
		if ( $user instanceof \WP_User ) {
			foreach ( $available as $existing_role ) {
				if ( $user->has_cap( $existing_role ) || in_array( $existing_role, $user->roles, true ) ) {
					$user->remove_role( $existing_role );
				}
			}

			foreach ( $normalized as $role ) {
				$user->add_role( $role );
			}
		}

		update_user_meta( $user_id, self::USER_ROLES_META_KEY, $normalized );

		return $normalized;
	}

	/**
	 * Add one more WordPress role to the user's attached roles.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $role    WordPress role slug.
	 * @return array<int, string>
	 */
	public static function add_user_role( int $user_id, string $role ): array {
		$roles = self::get_user_roles( $user_id );
		$slug  = sanitize_key( $role );
		if ( '' !== $slug && ! in_array( $slug, $roles, true ) ) {
			$roles[] = $slug;
		}

		return self::sync_user_roles( $user_id, $roles );
	}

	/**
	 * Remove a role from the user's attached roles.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $role    WordPress role slug.
	 * @return array<int, string>
	 */
	public static function remove_user_role( int $user_id, string $role ): array {
		$roles = self::get_user_roles( $user_id );
		$slug  = sanitize_key( $role );
		$roles = array_values( array_diff( $roles, array( $slug ) ) );

		return self::sync_user_roles( $user_id, $roles );
	}

	/**
	 * Return the maximum number of roles a user may have attached.
	 *
	 * @return int
	 */
	public static function get_max_roles(): int {
		return max( 0, (int) get_option( self::MAX_ROLES_OPTION_KEY, 1 ) );
	}

	/**
	 * Set the maximum number of roles a user may have attached.
	 *
	 * @param int $max_roles Maximum number of roles.
	 * @return int
	 */
	public static function set_max_roles( int $max_roles ): int {
		$max_roles = max( 0, (int) $max_roles );
		update_option( self::MAX_ROLES_OPTION_KEY, $max_roles, false );
		return $max_roles;
	}
}