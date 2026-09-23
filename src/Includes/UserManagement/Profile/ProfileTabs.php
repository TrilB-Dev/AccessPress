<?php
/**
 * Profile tab configuration and persistence.
 *
 * @package AccessPress\Includes\UserManagement\Profile
 */
namespace AccessPress\Includes\UserManagement\Profile;

use AccessPress\Includes\Functions\Helpers\DBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProfileTabs {
	/**
	 * Storage group key used in the forms table.
	 *
	 * @var string
	 */
	private const STORAGE_KEY = 'user_profile_tabs';

	/**
	 * Get the default tab configuration for the frontend profile builder.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_default_tabs(): array {
		return array(
			array(
				'id'      => 'account',
				'label'   => __( 'Account', 'accesspress' ),
				'layout'  => 'horizontal',
				'order'   => 0,
				'fields'  => array(
					array(
						'id'          => 'display_name',
						'type'        => 'display_name',
						'label'       => __( 'Display name', 'accesspress' ),
						'key'         => 'display_name',
						'placeholder' => '',
						'required'    => false,
						'order'       => 0,
					),
				),
			),
			array(
				'id'     => 'security',
				'label'  => __( 'Security', 'accesspress' ),
				'layout' => 'horizontal',
				'order'  => 1,
				'fields' => array(),
			),
		);
	}

	/**
	 * Load the saved profile configuration from the shared forms table.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_tabs(): array {
		$stored = self::read_storage();
		return self::normalize_tabs( is_array( $stored ) ? $stored : self::get_default_tabs() );
	}

	/**
	 * Save a tab configuration payload to the forms table.
	 *
	 * @param array<int, array<string, mixed>> $tabs Tab configuration.
	 * @return bool
	 */
	public static function save_tabs( array $tabs ): bool {
		$normalized = self::normalize_tabs( $tabs );
		$payload    = wp_json_encode( $normalized );
		if ( false === $payload ) {
			return false;
		}

		$table     = DBHelper::table_name( 'forms' );
		$query     = DBHelper::wpdb()->prepare( 'SELECT id FROM ' . $table . ' WHERE form_group = %s LIMIT 1', self::STORAGE_KEY );
		$existing  = DBHelper::get_var( $query );
		$data      = array(
			'form_group'      => self::STORAGE_KEY,
			'form_value'      => $payload,
			'form_updated_at' => current_time( 'mysql' ),
		);

		if ( ! empty( $existing ) ) {
			return false !== DBHelper::update(
				'forms',
				array(
					'form_value'      => $payload,
					'form_updated_at' => current_time( 'mysql' ),
				),
				array( 'form_group' => self::STORAGE_KEY )
			);
		}

		return false !== DBHelper::insert( 'forms', $data );
	}

	/**
	 * Normalize and clean incoming tab data.
	 *
	 * @param array<int, array<string, mixed>> $tabs Raw tab payload.
	 * @return array<int, array<string, mixed>>
	 */
	public static function normalize_tabs( array $tabs ): array {
		$normalized = array();
		foreach ( $tabs as $index => $tab ) {
			if ( ! is_array( $tab ) ) {
				continue;
			}

			$tab_id = isset( $tab['id'] ) ? (string) $tab['id'] : 'tab-' . ( $index + 1 );
			$tab_id = sanitize_key( str_replace( array( ' ', '_' ), '-', $tab_id ) );
			if ( '' === $tab_id ) {
				$tab_id = 'tab-' . ( $index + 1 );
			}

			$fields = array();
			foreach ( $tab['fields'] ?? array() as $field_index => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				$fields[] = ProfileFields::normalize_field( $field, $field_index );
			}

			$normalized[] = array(
				'id'     => $tab_id,
				'label'  => ! empty( $tab['label'] ) ? (string) $tab['label'] : __( 'Untitled tab', 'accesspress' ),
				'layout' => in_array( $tab['layout'] ?? 'horizontal', array( 'horizontal', 'vertical' ), true ) ? (string) $tab['layout'] : 'horizontal',
				'order'  => absint( $tab['order'] ?? $index ),
				'fields' => $fields,
			);
		}

		usort(
			$normalized,
			static function ( array $left, array $right ): int {
				return ( $left['order'] ?? 0 ) <=> ( $right['order'] ?? 0 );
			}
		);

		foreach ( $normalized as $index => $tab ) {
			$normalized[ $index ]['order'] = $index;
		}

		return array_values( $normalized );
	}

	/**
	 * Read the saved tab configuration from the forms table.
	 *
	 * @return array<int, array<string, mixed>>|null
	 */
	private static function read_storage(): ?array {
		$table = DBHelper::table_name( 'forms' );
		$query = DBHelper::wpdb()->prepare( 'SELECT form_value FROM ' . $table . ' WHERE form_group = %s LIMIT 1', self::STORAGE_KEY );
		$payload = DBHelper::get_var( $query );
		if ( empty( $payload ) || ! is_string( $payload ) ) {
			return null;
		}

		$decoded = json_decode( $payload, true );
		if ( ! is_array( $decoded ) ) {
			return null;
		}

		return $decoded;
	}
}