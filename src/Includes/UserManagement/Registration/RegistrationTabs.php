<?php
/**
 * Registration tab configuration and persistence.
 *
 * @package AccessPress\Includes\UserManagement\Registration
 */
namespace AccessPress\Includes\UserManagement\Registration;

use AccessPress\Includes\Functions\Helpers\DBHelper;
use AccessPress\Includes\UserManagement\Profile\ProfileFields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RegistrationTabs {
	/**
	 * Storage group key used in the forms table.
	 *
	 * @var string
	 */
	private const STORAGE_KEY = 'user_registration_tabs';

	/**
	 * Get the default registration page configuration.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_default_tabs(): array {
		return array(
			array(
				'id'          => 'account',
				'label'       => __( 'Account', 'accesspress' ),
				'layout'      => 'horizontal',
				'order'       => 0,
				'default_page' => true,
				'can_delete'  => false,
				'fields'      => array(
					self::default_field( 'username', __( 'Username', 'accesspress' ), 'text', 'user_login' ),
					self::default_field( 'email', __( 'Email address', 'accesspress' ), 'email', 'user_email' ),
					self::default_field( 'password', __( 'Password', 'accesspress' ), 'password', 'user_pass' ),
					self::default_field( 'confirm_password', __( 'Confirm password', 'accesspress' ), 'password', 'confirm_password' ),
				),
			),
		);
	}

	/**
	 * Load the saved registration configuration from the shared forms table.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_tabs(): array {
		$stored = self::read_storage();
		return self::normalize_tabs( is_array( $stored ) ? $stored : self::get_default_tabs() );
	}

	/**
	 * Save a registration page configuration payload to the forms table.
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

		$table    = DBHelper::table_name( 'forms' );
		$query    = DBHelper::wpdb()->prepare( 'SELECT id FROM ' . $table . ' WHERE form_group = %s LIMIT 1', self::STORAGE_KEY );
		$existing = DBHelper::get_var( $query );
		$data     = array(
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
	 * Normalize and clean incoming registration configuration.
	 *
	 * @param array<int, array<string, mixed>> $tabs Raw registration payload.
	 * @return array<int, array<string, mixed>>
	 */
	public static function normalize_tabs( array $tabs ): array {
		$normalized = array();
		$defaults   = self::get_default_tabs();
		$default_page = $defaults[0] ?? array();
		$default_keys = array();
		if ( ! empty( $default_page['fields'] ) && is_array( $default_page['fields'] ) ) {
			foreach ( $default_page['fields'] as $field ) {
				if ( is_array( $field ) && isset( $field['key'] ) ) {
					$default_keys[] = (string) $field['key'];
				}
			}
		}

		foreach ( $tabs as $index => $tab ) {
			if ( ! is_array( $tab ) ) {
				continue;
			}

			$tab_id = isset( $tab['id'] ) ? (string) $tab['id'] : 'page-' . ( $index + 1 );
			$tab_id = sanitize_key( str_replace( array( ' ', '_' ), '-', $tab_id ) );
			if ( '' === $tab_id ) {
				$tab_id = 'page-' . ( $index + 1 );
			}

			$fields = array();
			foreach ( $tab['fields'] ?? array() as $field_index => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				$field = ProfileFields::normalize_field( $field, $field_index );
				if ( 0 === $index && isset( $field['key'] ) && in_array( (string) $field['key'], $default_keys, true ) ) {
					$field['can_delete'] = false;
				}
				$fields[] = $field;
			}

			$normalized[] = array(
				'id'          => $tab_id,
				'label'       => ! empty( $tab['label'] ) ? (string) $tab['label'] : __( 'Registration page', 'accesspress' ),
				'layout'      => in_array( $tab['layout'] ?? 'horizontal', array( 'horizontal', 'vertical' ), true ) ? (string) $tab['layout'] : 'horizontal',
				'order'       => absint( $tab['order'] ?? $index ),
				'default_page' => 0 === $index,
				'can_delete'  => 0 !== $index,
				'fields'      => $fields,
			);
		}

		if ( empty( $normalized ) ) {
			return self::get_default_tabs();
		}

		usort(
			$normalized,
			static function ( array $left, array $right ): int {
				return ( $left['order'] ?? 0 ) <=> ( $right['order'] ?? 0 );
			}
		);

		foreach ( $normalized as $index => $tab ) {
			$normalized[ $index ]['order']       = $index;
			$normalized[ $index ]['default_page'] = 0 === $index;
			$normalized[ $index ]['can_delete']  = 0 !== $index;
		}

		return array_values( $normalized );
	}

	/**
	 * Read the saved registration config from the forms table.
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

	/**
	 * Build a default field definition for a required registration field.
	 *
	 * @param string $key     Field key.
	 * @param string $label   Field label.
	 * @param string $type    Field type.
	 * @param string $wpField Native WP field name.
	 * @return array<string, mixed>
	 */
	private static function default_field( string $key, string $label, string $type, string $wpField ): array {
		return array(
			'id'          => $key,
			'type'        => $type,
			'label'       => $label,
			'key'         => $key,
			'wp_field'    => $wpField,
			'placeholder' => '',
			'required'    => true,
			'order'       => 0,
			'group'       => 'built_in',
			'visibility'  => 'user',
			'editable'    => true,
			'admin_only'  => false,
			'can_delete'  => false,
			'options'     => array(),
			'multiple'    => false,
			'description' => '',
			'tooltip'     => '',
			'meta'        => array(),
		);
	}
}
