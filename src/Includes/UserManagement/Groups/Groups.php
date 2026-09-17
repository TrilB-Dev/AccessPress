<?php
/**
 * Membership groups for the AccessPress core user-management layer.
 *
 * Groups are used as access grants for pages, content, and member portals rather
 * than as WordPress capability flags.
 *
 * @package AccessPress\Includes\UserManagement\Groups
 */

namespace AccessPress\Includes\UserManagement\Groups;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Groups {
	/**
	 * Storage key for default group definitions.
	 *
	 * @var string
	 */
	private const DEFAULT_GROUPS_OPTION = 'accesspress_default_user_groups';

	/**
	 * Storage key for a user's assigned groups.
	 *
	 * @var string
	 */
	private const USER_GROUPS_META_KEY = 'accesspress_user_groups';

	/**
	 * Return the default membership definitions used by AccessPress.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_default_group_definitions(): array {
		return array(
			'free'   => array(
				'label'       => __( 'Free', 'accesspress' ),
				'description' => __( 'Basic access for guest and trial members.', 'accesspress' ),
				'priority'    => 0,
				'color'       => '#6c757d',
				'price'       => 0,
			),
			'bronze' => array(
				'label'       => __( 'Bronze', 'accesspress' ),
				'description' => __( 'Entry-level paid member access.', 'accesspress' ),
				'priority'    => 1,
				'color'       => '#cd7f32',
				'price'       => 29,
			),
			'silver' => array(
				'label'       => __( 'Silver', 'accesspress' ),
				'description' => __( 'Expanded member access and priority features.', 'accesspress' ),
				'priority'    => 2,
				'color'       => '#b0b7c3',
				'price'       => 79,
			),
			'gold'   => array(
				'label'       => __( 'Gold', 'accesspress' ),
				'description' => __( 'Premium membership with the broadest access.', 'accesspress' ),
				'priority'    => 3,
				'color'       => '#d4af37',
				'price'       => 149,
			),
		);
	}

	/**
	 * Register the default AccessPress member groups if they do not already exist.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function register_default_groups(): array {
		$definitions = self::get_default_group_definitions();
		$stored      = get_option( self::DEFAULT_GROUPS_OPTION, array() );
		$stored      = is_array( $stored ) ? $stored : array();

		$merged = array_merge( $definitions, $stored );
		update_option( self::DEFAULT_GROUPS_OPTION, $merged, false );

		return $merged;
	}

	/**
	 * Get all groups assigned to a user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<int, string>
	 */
	public static function get_user_groups( int $user_id ): array {
		if ( $user_id <= 0 ) {
			return array();
		}

		$groups = get_user_meta( $user_id, self::USER_GROUPS_META_KEY, true );
		if ( ! is_array( $groups ) ) {
			$groups = array();
		}

		return array_values( array_unique( array_filter( array_map( 'strval', $groups ) ) ) );
	}

	/**
	 * Sync a user's assigned groups.
	 *
	 * @param int   $user_id WordPress user ID.
	 * @param array $groups  Group slug list.
	 * @return array<int, string>
	 */
	public static function sync_user_groups( int $user_id, array $groups ): array {
		if ( $user_id <= 0 ) {
			return array();
		}

		$definitions = self::register_default_groups();
		$normalized  = array();
		foreach ( $groups as $group ) {
			$slug = sanitize_key( (string) $group );
			if ( '' !== $slug && isset( $definitions[ $slug ] ) ) {
				$normalized[] = $slug;
			}
		}

		$normalized = array_values( array_unique( $normalized ) );
		update_user_meta( $user_id, self::USER_GROUPS_META_KEY, $normalized );

		return $normalized;
	}
}