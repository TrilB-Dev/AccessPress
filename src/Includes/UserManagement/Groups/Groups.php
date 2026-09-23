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

use AccessPress\Includes\Functions\Helpers\DBHelper;

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
	 * Ensure the default AccessPress groups exist in the schema-backed groups table.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function register_default_groups(): array {
		$definitions = self::get_default_group_definitions();
		$stored      = self::get_group_definitions();

		foreach ( $definitions as $slug => $group ) {
			if ( ! isset( $stored[ $slug ] ) ) {
				self::create_group( array(
					'group_slug'        => $slug,
					'group_name'        => $group['label'] ?? ucfirst( $slug ),
					'group_description' => $group['description'] ?? '',
					'group_priority'    => $group['priority'] ?? 0,
					'group_color'       => $group['color'] ?? '#4f46e5',
					'group_price'       => $group['price'] ?? 0,
					'group_status'      => 'active',
					'group_default'     => 1,
				) );
			}
		}

		return self::get_group_definitions();
	}

	/**
	 * Read all groups from the schema-backed table.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_group_definitions(): array {
		$table = DBHelper::table_name( 'groups' );
		$rows  = DBHelper::get_results( "SELECT * FROM {$table} ORDER BY group_priority ASC, group_name ASC" );
		$groups = array();

		foreach ( (array) $rows as $row ) {
			$slug = self::normalize_group_slug( $row['group_slug'] ?? '' );
			if ( '' === $slug ) {
				continue;
			}

			$groups[ $slug ] = self::map_group_row( $row );
		}

		if ( empty( $groups ) ) {
			$legacy = get_option( self::DEFAULT_GROUPS_OPTION, array() );
			$legacy = is_array( $legacy ) ? $legacy : array();

			foreach ( $legacy as $slug => $group ) {
				$slug = self::normalize_group_slug( (string) $slug );
				if ( '' === $slug ) {
					continue;
				}

				$groups[ $slug ] = self::normalize_group_definition( $group, $slug );
			}
		}

		if ( empty( $groups ) ) {
			foreach ( self::get_default_group_definitions() as $slug => $group ) {
				$groups[ $slug ] = self::normalize_group_definition( $group, $slug );
			}
		}

		return $groups;
	}

	/**
	 * Return a single group definition by slug.
	 *
	 * @param string $slug Group slug.
	 * @return array<string, mixed>|null
	 */
	public static function get_group_by_slug( string $slug ): ?array {
		$slug = self::normalize_group_slug( $slug );
		if ( '' === $slug ) {
			return null;
		}

		$definitions = self::get_group_definitions();
		return $definitions[ $slug ] ?? null;
	}

	/**
	 * Create a new group row in the schema-backed groups table.
	 *
	 * @param array<string, mixed> $data Group payload.
	 * @return int|false
	 */
	public static function create_group( array $data ) {
		$slug = self::normalize_group_slug( (string) ( $data['group_slug'] ?? '' ) );
		$name = self::normalize_group_name( (string) ( $data['group_name'] ?? '' ) );
		if ( '' === $slug || '' === $name ) {
			return false;
		}

		$definitions = self::get_group_definitions();
		if ( isset( $definitions[ $slug ] ) ) {
			return false;
		}

		$group = array(
			'group_name'               => $name,
			'group_slug'               => $slug,
			'group_description'        => (string) ( $data['group_description'] ?? '' ),
			'group_confirmation_message' => (string) ( $data['group_confirmation_message'] ?? '' ),
			'group_limit'              => isset( $data['group_limit'] ) ? absint( $data['group_limit'] ) : null,
			'group_owner'              => isset( $data['group_owner'] ) ? absint( $data['group_owner'] ) : null,
			'group_image'              => isset( $data['group_image'] ) ? sanitize_text_field( (string) $data['group_image'] ) : '',
			'group_color'              => sanitize_hex_color( (string) ( $data['group_color'] ?? '#4f46e5' ) ) ?: '#4f46e5',
			'group_priority'           => absint( $data['group_priority'] ?? 0 ),
			'group_type'               => isset( $data['group_type'] ) ? absint( $data['group_type'] ) : null,
			'group_parent'             => isset( $data['group_parent'] ) ? absint( $data['group_parent'] ) : null,
			'group_price'              => isset( $data['group_price'] ) ? floatval( $data['group_price'] ) : 0,
			'group_billing_cycle'      => isset( $data['group_billing_cycle'] ) ? absint( $data['group_billing_cycle'] ) : null,
			'group_status'             => (string) ( $data['group_status'] ?? 'active' ),
			'group_default'            => ! empty( $data['group_default'] ) ? 1 : 0,
			'group_created_at'         => current_time( 'mysql' ),
			'group_updated_at'         => current_time( 'mysql' ),
		);

		return DBHelper::insert( DBHelper::table_name( 'groups' ), $group );
	}

	/**
	 * Update a group record in the schema-backed table.
	 *
	 * @param string $old_slug Current group slug.
	 * @param array<string, mixed> $data Updated payload.
	 * @return bool
	 */
	public static function update_group( string $old_slug, array $data ): bool {
		$old_slug = self::normalize_group_slug( $old_slug );
		$slug      = self::normalize_group_slug( (string) ( $data['group_slug'] ?? $old_slug ) );
		$name      = self::normalize_group_name( (string) ( $data['group_name'] ?? '' ) );
		if ( '' === $old_slug || '' === $slug || '' === $name ) {
			return false;
		}

		$id = self::get_group_id_by_slug( $old_slug );
		if ( false === $id ) {
			return false;
		}

		$payload = array(
			'group_name'               => $name,
			'group_slug'               => $slug,
			'group_description'        => (string) ( $data['group_description'] ?? '' ),
			'group_confirmation_message' => (string) ( $data['group_confirmation_message'] ?? '' ),
			'group_limit'              => isset( $data['group_limit'] ) ? absint( $data['group_limit'] ) : null,
			'group_owner'              => isset( $data['group_owner'] ) ? absint( $data['group_owner'] ) : null,
			'group_image'              => isset( $data['group_image'] ) ? sanitize_text_field( (string) $data['group_image'] ) : '',
			'group_color'              => sanitize_hex_color( (string) ( $data['group_color'] ?? '#4f46e5' ) ) ?: '#4f46e5',
			'group_priority'           => absint( $data['group_priority'] ?? 0 ),
			'group_type'               => isset( $data['group_type'] ) ? absint( $data['group_type'] ) : null,
			'group_parent'             => isset( $data['group_parent'] ) ? absint( $data['group_parent'] ) : null,
			'group_price'              => isset( $data['group_price'] ) ? floatval( $data['group_price'] ) : 0,
			'group_billing_cycle'      => isset( $data['group_billing_cycle'] ) ? absint( $data['group_billing_cycle'] ) : null,
			'group_status'             => (string) ( $data['group_status'] ?? 'active' ),
			'group_default'            => ! empty( $data['group_default'] ) ? 1 : 0,
			'group_updated_at'         => current_time( 'mysql' ),
		);

		$updated = DBHelper::update( DBHelper::table_name( 'groups' ), $payload, array( 'id' => $id ) );
		return false !== $updated && 0 !== $updated;
	}

	/**
	 * Delete a group by slug.
	 *
	 * @param string $slug Group slug.
	 * @return bool
	 */
	public static function delete_group( string $slug ): bool {
		$slug = self::normalize_group_slug( $slug );
		if ( '' === $slug ) {
			return false;
		}

		$id = self::get_group_id_by_slug( $slug );
		if ( false === $id ) {
			return false;
		}

		$deleted = DBHelper::delete( DBHelper::table_name( 'groups' ), array( 'id' => $id ), array( '%d' ) );
		return false !== $deleted && 0 !== $deleted;
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
			$slug = self::normalize_group_slug( (string) $group );
			if ( '' !== $slug && isset( $definitions[ $slug ] ) ) {
				$normalized[] = $slug;
			}
		}

		$normalized = array_values( array_unique( $normalized ) );
		update_user_meta( $user_id, self::USER_GROUPS_META_KEY, $normalized );

		return $normalized;
	}

	/**
	 * Normalize a group slug.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function normalize_group_slug( $value ): string {
		$slug = strtolower( (string) sanitize_key( (string) $value ) );
		return preg_match( '/^[a-z0-9_-]+$/', $slug ) ? $slug : '';
	}

	/**
	 * Normalize a group label.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function normalize_group_name( $value ): string {
		$name = trim( (string) sanitize_text_field( (string) $value ) );
		return preg_match( '/^[A-Za-z0-9 _-]+$/', $name ) ? $name : '';
	}

	/**
	 * Normalize a raw definition array into the canonical group format.
	 *
	 * @param mixed  $definition Raw definition.
	 * @param string $slug      Group slug.
	 * @return array<string, mixed>
	 */
	private static function normalize_group_definition( $definition, string $slug ): array {
		if ( ! is_array( $definition ) ) {
			$definition = array();
		}

		return array(
			'label'       => (string) ( $definition['label'] ?? ucfirst( str_replace( '-', ' ', $slug ) ) ),
			'description' => (string) ( $definition['description'] ?? '' ),
			'priority'    => absint( $definition['priority'] ?? 0 ),
			'color'       => (string) ( $definition['color'] ?? '#4f46e5' ),
			'price'       => floatval( $definition['price'] ?? 0 ),
			'slug'        => $slug,
		);
	}

	/**
	 * Map a schema row to the public group definition array.
	 *
	 * @param array<string, mixed> $row Group row.
	 * @return array<string, mixed>
	 */
	private static function map_group_row( array $row ): array {
		$slug = self::normalize_group_slug( (string) ( $row['group_slug'] ?? '' ) );
		if ( '' === $slug ) {
			return array();
		}

		return array(
			'label'       => (string) ( $row['group_name'] ?? ucfirst( str_replace( '-', ' ', $slug ) ) ),
			'description' => (string) ( $row['group_description'] ?? '' ),
			'priority'    => absint( $row['group_priority'] ?? 0 ),
			'color'       => (string) ( $row['group_color'] ?? '#4f46e5' ),
			'price'       => floatval( $row['group_price'] ?? 0 ),
			'slug'        => $slug,
		);
	}

	/**
	 * Resolve the DB row ID for a given group slug.
	 *
	 * @param string $slug Group slug.
	 * @return int|false
	 */
	private static function get_group_id_by_slug( string $slug ) {
		$slug = self::normalize_group_slug( $slug );
		if ( '' === $slug ) {
			return false;
		}

		$table = DBHelper::table_name( 'groups' );
		$query = DBHelper::prepare( "SELECT id FROM {$table} WHERE group_slug = %s LIMIT 1", $slug );
		return DBHelper::get_var( $query );
	}
}