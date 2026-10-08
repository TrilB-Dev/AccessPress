<?php
/**
 * SettingsManager class.
 *
 * @package AccessPress\Includes\Settings
 */

namespace AccessPress\Includes\Settings;

use AccessPress\Includes\Core\WP\Database;
use AccessPress\Includes\Functions\Helpers\DBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsManager {
	  /**
     * Registered setting groups.
     *
     * @var array<string, array<string, mixed>>
     * @since 1.0.0
     */
    private static array $registered_groups = [];
    /**
     * Registered setting keys.
     *
     * @var array<string, string>
     * @since 1.0.0
     */
    private static array $registered_keys = [];
    /**
     * Get the name of the settings table.
     *
     * @return string
     * @since 1.0.0
     */
    public static function table_name( ?string $table = 'settings' ): string {
        $table = trim( (string) $table );
        $table = '' === $table ? 'settings' : $table;
        return Database::table_name( str_replace( array( '-', ' ' ), '_', sanitize_key( $table ) ) );
    }
    /**
     * Install the settings table and initialize default settings.
     *
     * @return void
     * @since 1.0.0
     */
    public static function install(): void {
        Database::install();

        foreach ( self::registered_defaults() as $group => $settings ) {
            $stored_settings = self::get_group( $group );
            if ( null === $stored_settings ) {
                $legacy_settings = self::get_legacy_group( $group );
                $stored_settings = is_array( $legacy_settings ) ? $legacy_settings : [];
            }

            self::set_group( $group, array_merge( $settings, $stored_settings ) );
            self::delete_legacy_group( $group );
        }
    }
    /**
     * Get a setting by its key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get( string $key, $default = null, ?string $table = null ) {
        $table = $table ?? 'settings';
        foreach ( self::get_all( $table ) as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }
        return self::registered_default( $key, $default );
    }
    /**
     * Set a setting by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public static function set( string $key, $value, ?string $table = null ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group, array(), $table ) ?? [];
        $settings[ $key ] = $value;
        return self::set_group( $group, $settings, $table );
    }
    /**
     * Delete a setting by its key.
     *
     * @param string $key
     * @return boolean
     */
    public static function delete( string $key, ?string $table = null ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group, array(), $table );
        if ( ! is_array( $settings ) || ! array_key_exists( $key, $settings ) ) {
            return false;
        }
        unset( $settings[ $key ] );
        return self::set_group( $group, $settings, $table );
    }
    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     * @since 1.0.0
     */
    public static function has( string $key, ?string $table = null ): bool {
        foreach ( self::get_all( $table ) as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return true;
            }
        }

        return false;
    }
    /**
     * Get all settings, including stored and default values.
     *
     * @return array
     * @since 1.0.0
     */
    public static function get_all( ?string $table = null ): array {
        $table_name = self::table_name( $table ?? 'settings' );
        global $wpdb;
        $rows = $wpdb->get_results( 'SELECT setting_group, setting_value FROM ' . $table_name, ARRAY_A );
        $settings = array();

        foreach ( $rows ?: [] as $row ) {
            $group = self::logical_group( (string) ( $row['setting_group'] ?? '' ) );
            $stored = maybe_unserialize( $row['setting_value'] );

            if ( ! is_array( $stored ) || '' === $group ) {
                continue;
            }

            $settings[ $group ] = $stored;
        }

        return $settings;
    }
	/**
	 * Retrieve the default settings for all registered groups.
	 *
	 * @return array The default settings array.
	 */
	public static function defaults(): array {
		return array(
			'general' => array(
				'accesspress_title'             => '',
				'accesspress_logo'              => '',
				'accesspress_frontend_root_page' => '',
				'disable_wordpress_default' 	=> false,
				'frontend_login'            	=> false,
				'frontend_registration'     	=> false,
				'frontend_password_reset'   	=> false,
				'frontend_account_verification' => false,
				'frontend_profile'           	=> false,
			),
			'access'  => array(
				'accesspress_user_management' => array(),
			),
			'tools'   => array(
				'debug_logging'   => false,
				'console_logging' => false,
			),
		);
	}
    /**
     * Get the default settings.
     *
     * @return array
     * @since 1.0.0
     */
    public static function get_group( string $group, ?array $default = null, ?string $table = null ): ?array {
        $table_name = self::table_name( $table ?? 'settings' );
        global $wpdb;
        $group = self::normalize_group( $group );
        $value = $wpdb->get_var( $wpdb->prepare( 'SELECT setting_value FROM ' . $table_name . ' WHERE setting_group = %s', self::storage_group( $group ) ) );
        $settings = $value === null ? null : maybe_unserialize( $value );

        if ( ! is_array( $settings ) ) {
            return is_array( $default ) ? $default : null;
        }

        return $settings;
    }
    /**
     * Set the settings for a specific group.
     *
     * @param string $group
     * @param array $settings
     * @return bool
     * @since 1.0.0
     */
    public static function set_group( string $group, array $settings, ?string $table = null ): bool {
        $table_name = self::table_name( $table ?? 'settings' );
        global $wpdb;
        return false !== $wpdb->replace( $table_name, [
            'setting_group' => self::storage_group( $group ),
            'setting_value' => maybe_serialize( $settings ),
            'autoload' => 'yes',
            'updated_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
    }
    /**
     * Register a settings group with default values.
     *
     * @param string $group
     * @param array $defaults
     * @return bool
     * @since 1.0.0
     */
    public static function register_group( string $group, array $defaults = [] ): bool {
        $group = self::normalize_group( $group );
        if ( '' === $group ) {
            return false;
        }

        self::$registered_groups[ $group ] = array_merge( self::$registered_groups[ $group ] ?? [], $defaults );
        foreach ( $defaults as $key => $default ) {
            $key = sanitize_key( (string) $key );
            if ( '' !== $key ) {
                self::$registered_keys[ $key ] = $group;
            }
        }
        return true;
    }
    /**
     * Register a single setting key with a default value.
     *
     * @param string $key
     * @param string $group
     * @param mixed $default
     * @return bool
     * @since 1.0.0
     */
    public static function register_key( string $key, string $group, $default = null ): bool {
        $key = sanitize_key( $key );
        if ( '' === $key || ! self::register_group( $group ) ) {
            return false;
        }

        $group = self::normalize_group( $group );
        self::$registered_keys[ $key ] = $group;
        self::$registered_groups[ $group ][ $key ] = $default;
        return true;
    }
    /**
     * Get the storage group name for a logical group.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function storage_group( string $group ): string {
        $group = self::normalize_group( $group );
        return str_starts_with( $group, 'accesspress_' ) ? $group : 'accesspress_' . $group;
    }

    /**
     * Get the logical group name from a storage group name.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function logical_group( string $group ): string {
        return str_starts_with( $group, 'accesspress_' ) ? substr( $group, 10 ) : $group;
    }
    /**
     * Get the logical group name for a given setting key.
     *
     * @param string $key
     * @return string
     * @since 1.0.0
     */
    private static function group_for_key( string $key ): string {
        $key = sanitize_key( $key );
        if ( isset( self::$registered_keys[ $key ] ) ) {
            return self::$registered_keys[ $key ];
        }
        if ( in_array( $key, [ 'view_general', 'write_pages', 'view_analytics','view_user_management', 'manage_plugins' ], true ) ) {
            return 'access';
        }
        if ( str_contains( $key, 'layout' ) ) {
            return 'layout';
        }
        if ( str_contains( $key, 'access' ) ) {
            return 'access';
        }
		if ( str_contains( $key, 'user-management' ) ) {
            return 'user-management';
        }
        if ( str_contains( $key, 'tool' ) ) {
            return 'tools';
        }
        return 'general';
    }

    /**
     * Return core and extension defaults for activation and fallback reads.
     *
     * @return array<string, array<string, mixed>>
     * @since 1.0.0
     */
    private static function registered_defaults(): array {
        $defaults = self::defaults();
        foreach ( self::$registered_groups as $group => $settings ) {
            $defaults[ $group ] = array_merge( $defaults[ $group ] ?? [], $settings );
        }

        return $defaults;
    }
    /**
     * Get the registered default for a specific setting key.
     *
     * @param string $key
     * @param mixed $fallback
     * @return mixed
     * @since 1.0.0
     */
    private static function registered_default( string $key, $fallback ) {
        $key = sanitize_key( $key );
        foreach ( self::registered_defaults() as $settings ) {
            if ( array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }

        return $fallback;
    }
    /**
     * Normalize a group name by replacing dashes and spaces with underscores and removing the 'accesspress_' prefix if present.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function normalize_group( string $group ): string {
        $group = str_replace( '-', '_', sanitize_key( $group ) );
        $group = str_replace( ' ', '_', $group );

        if ( str_starts_with( $group, 'accesspress_' ) ) {
            return preg_replace( '/^accesspress_/', '', $group ) ?: $group;
        }

        return $group;
    }
}
