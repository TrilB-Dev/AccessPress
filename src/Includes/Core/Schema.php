<?php
/**
 * Schema class for managing core and extension database schema definitions.
 *
 * @package AccessPress\Includes\Core
 * @since 1.0.0
 */
namespace AccessPress\Includes\Core;

use AccessPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Schema {
    /**
     * Register all core database tables.
     *
     * @return void
     */
    public static function register_tables(): void {
        self::settings_table();
        self::analytics_table();
        self::logs_table();
        self::groups_table();
        self::forms_table();
    }
    /**
     * Register the settings table schema.
     *
     * @return string
     */
    public static function settings_table(): string {
        Database::register_core_table(
            'settings',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                setting_group varchar(120) NOT NULL,
                setting_value longtext DEFAULT NULL,
                autoload varchar(20) DEFAULT 'yes',
                updated_at datetime DEFAULT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY setting_group (setting_group)
            ) {$charset};";
            }
        );

        return 'settings';
    }
    /**
     * Register the analytics table schema.
     *
     * @return string
     */
    public static function analytics_table(): string {
        Database::register_core_table(
            'analytics',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                event_key varchar(128) NOT NULL,
                event_value longtext DEFAULT NULL,
                user_id bigint(20) unsigned DEFAULT NULL,
                event_source varchar(64) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY event_key (event_key),
                KEY created_at (created_at),
                KEY user_id (user_id)
            ) {$charset};";
            }
        );

        return 'analytics';
    }
    /**
     * Register the logs table schema.
     *
     * @return string
     */
    public static function logs_table(): string {
        Database::register_core_table(
            'logs',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                log_level varchar(32) NOT NULL,
                message longtext NOT NULL,
                context longtext DEFAULT NULL,
                source varchar(64) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY log_level (log_level),
                KEY source (source),
                KEY created_at (created_at)
            ) {$charset};";
            }
        );

        return 'logs';
    }
    public static function groups_table(): string {
        Database::register_core_table(
            'groups',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                group_name varchar(120) NOT NULL,
                group_slug varchar(120) NOT NULL,
                group_description longtext DEFAULT NULL,
                group_confirmation_message longtext DEFAULT NULL,
                group_limit int(11) DEFAULT NULL,
                group_owner bigint(20) unsigned DEFAULT NULL,
                group_image varchar(255) DEFAULT NULL,
                group_color varchar(32) DEFAULT NULL,
                group_priority int(11) DEFAULT NULL,
                group_type bigint(20) unsigned DEFAULT NULL,
                group_parent bigint(20) unsigned DEFAULT NULL,
                group_price decimal(10,2) DEFAULT NULL,
                group_billing_cycle int(3) DEFAULT NULL,
                group_status varchar(32) DEFAULT NULL,
                group_created_at datetime DEFAULT CURRENT_TIMESTAMP,
                group_updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                group_expiration tinyint(1) DEFAULT '0',
                group_expiration_date datetime DEFAULT NULL,
                group_created_by bigint(20) unsigned DEFAULT NULL,
                group_updated_by bigint(20) unsigned DEFAULT NULL,
                group_roles varchar(255) DEFAULT NULL,
                group_pages bigint(20) unsigned DEFAULT NULL,
                group_post_types bigint(20) unsigned DEFAULT NULL,
                group_default tinyint(1) DEFAULT '0',
                PRIMARY KEY  (id),
                UNIQUE KEY group_name (group_name),
                UNIQUE KEY group_slug (group_slug)
            ) {$charset};";
            }
        );

        return 'groups';
    }
    public static function forms_table(): string {
        Database::register_core_table(
            'forms',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                form_group varchar(120) NOT NULL,
                form_value longtext DEFAULT NULL,
                form_updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY form_group (form_group)
            ) {$charset};";
            }
        );

        return 'forms';
    }
}