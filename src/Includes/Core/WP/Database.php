<?php
/**
 * Database class for managing custom AccessPress database tables.
 *
 * @package AccessPress\Includes\Core\WP
 */
namespace AccessPress\Includes\Core\WP;

use AccessPress\Includes\Core\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Database {
	/**
	 * Registered custom database tables and their schema callbacks.
	 *
	 * @var array<string, callable>
	 */
	private static array $registered_plugin_tables = array();
	/**
	 * Registered core database tables and their schema callbacks.
	 *
	 * @var array<string, callable>
	 */
	private static array $registered_core_tables = array();

	/**
	 * Normalize a table slug before registering or resolving it.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return string The normalized table suffix.
	 */
	private static function normalize_table_key( string $table ): string {
		$table = sanitize_key( $table );
		return '' === $table ? '' : $table;
	}

	/**
	 * Register a core table schema for the next installation/update.
	 *
	 * The callback receives the fully prefixed table name and charset/collation
	 * string, and must return a dbDelta-compatible CREATE TABLE statement.
	 *
	 * @param string   $table  Unprefixed AccessPress table suffix.
	 * @param callable $schema Schema callback.
	 * @return bool Whether the table was registered.
	 */
	public static function register_core_table( string $table, callable $schema ): bool {
		$table = self::normalize_table_key( $table );
		if ( '' === $table ) {
			return false;
		}

		self::$registered_core_tables[ $table ] = $schema;
		return true;
	}

	/**
	 * Register an extension table schema for the next installation/update.
	 *
	 * @param string   $table  Unprefixed AccessPress table suffix.
	 * @param callable $schema Schema callback.
	 * @return bool Whether the table was registered.
	 */
	public static function register_plugin_table( string $table, callable $schema ): bool {
		$table = self::normalize_table_key( $table );
		if ( '' === $table ) {
			return false;
		}

		self::$registered_plugin_tables[ $table ] = $schema;
		return true;
	}

	/**
	 * Get the registered core table schema callbacks.
	 *
	 * @return array<string, callable>
	 */
	public static function get_core_tables(): array {
		return self::$registered_core_tables;
	}

	/**
	 * Get the registered plugin table schema callbacks.
	 *
	 * @return array<string, callable>
	 */
	public static function get_plugin_tables(): array {
		return self::$registered_plugin_tables;
	}

	/**
	 * Determine whether the requested table exists.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool True if the table exists.
	 */
	public static function table_exists( string $table ): bool {
		global $wpdb;

		$table_name = self::table_name( $table );
		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}

	/**
	 * Create or update a single table using the registered schema callback.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool Whether the table was created or updated.
	 */
	public static function create_table( string $table ): bool {
		global $wpdb;

		$table = self::normalize_table_key( $table );
		if ( '' === $table ) {
			return false;
		}

		$schema = self::$registered_core_tables[ $table ] ?? self::$registered_plugin_tables[ $table ] ?? null;
		if ( ! is_callable( $schema ) ) {
			return false;
		}

		if ( file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$statement = call_user_func( $schema, self::table_name( $table ), method_exists( $wpdb, 'get_charset_collate' ) ? $wpdb->get_charset_collate() : '' );
		if ( ! is_string( $statement ) || '' === trim( $statement ) ) {
			return false;
		}

		dbDelta( $statement );
		return true;
	}

	/**
	 * Empty all rows from a table.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool True if the content was dropped.
	 */
	public static function drop_table_contents( string $table ): bool {
		global $wpdb;

		if ( ! self::table_exists( $table ) ) {
			return false;
		}

		return false !== $wpdb->query( 'TRUNCATE TABLE ' . self::table_name( $table ) );
	}

	/**
	 * Alias for dropping table contents.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool True if the content was dropped.
	 */
	public static function truncate_table( string $table ): bool {
		return self::drop_table_contents( $table );
	}

	/**
	 * Drop a AccessPress table entirely.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool True if the table was deleted.
	 */
	public static function delete_table( string $table ): bool {
		global $wpdb;

		if ( ! self::table_exists( $table ) ) {
			return false;
		}

		return false !== $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_name( $table ) );
	}

	/**
	 * Alias for dropping a table.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return bool True if the table was deleted.
	 */
	public static function drop_table( string $table ): bool {
		return self::delete_table( $table );
	}

	/**
	 * Install or update all AccessPress Core & Plugin tables.
	 *
	 * @return void
	 */
	public static function install(): void {
		Schema::register_tables();

		foreach ( self::$registered_core_tables as $table => $schema ) {
			self::create_table( $table );
		}

		foreach ( self::$registered_plugin_tables as $table => $schema ) {
			self::create_table( $table );
		}

		if ( function_exists( 'update_option' ) ) {
			update_option( 'accesspress_db_version', defined( 'ACCESSPRESS_VERSION' ) ? ACCESSPRESS_VERSION : '1.0.0' );
		}
	}

	/**
	 * Get the WordPress database instance.
	 *
	 * @return \wpdb The WordPress database object.
	 */
	public static function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Prepare a SQL query using the WordPress database object.
	 *
	 * @param string $query Query string.
	 * @param mixed  ...$args Query arguments.
	 * @return string Prepared query string.
	 */
	public static function prepare( string $query, ...$args ): string {
		global $wpdb;
		return $wpdb->prepare( $query, ...$args );
	}

	/**
	 * Run a raw database query.
	 *
	 * @param string $query SQL statement.
	 * @return mixed Query result.
	 */
	public static function query( string $query ) {
		global $wpdb;
		return $wpdb->query( $query );
	}

	/**
	 * Get a single variable from the database.
	 *
	 * @param string $query SQL query.
	 * @param int    $x     Column index.
	 * @param int    $y     Offset.
	 * @return mixed Database value.
	 */
	public static function get_var( string $query, int $x = 0, int $y = 0 ) {
		global $wpdb;
		return $wpdb->get_var( $query, $x, $y );
	}

	/**
	 * Get a single row from the database.
	 *
	 * @param string $query SQL query.
	 * @param string $output Output format.
	 * @param int    $y      Offset.
	 * @return mixed Database row.
	 */
	public static function get_row( string $query, string $output = ARRAY_A, int $y = 0 ) {
		global $wpdb;
		return $wpdb->get_row( $query, $output, $y );
	}

	/**
	 * Get multiple rows from the database.
	 *
	 * @param string $query SQL query.
	 * @param string $output Output format.
	 * @return mixed Database rows.
	 */
	public static function get_results( string $query, string $output = ARRAY_A ) {
		global $wpdb;
		return $wpdb->get_results( $query, $output );
	}

	/**
	 * Insert a row into a table.
	 *
	 * @param string $table Table name.
	 * @param array  $data  Row data.
	 * @param array  $format Optional value format array.
	 * @return int|false Insert ID or false.
	 */
	public static function insert( string $table, array $data, ?array $format = null ) {
		global $wpdb;
		return $wpdb->insert( $table, $data, $format );
	}

	/**
	 * Update rows in a table.
	 *
	 * @param string $table       Table name.
	 * @param array  $data        Row data.
	 * @param array  $where       Where clause values.
	 * @param array  $format      Optional data format array.
	 * @param array  $where_format Optional where format array.
	 * @return int|false Number of rows updated or false.
	 */
	public static function update( string $table, array $data, array $where, ?array $format = null, ?array $where_format = null ) {
		global $wpdb;
		return $wpdb->update( $table, $data, $where, $format, $where_format );
	}

	/**
	 * Delete rows from a table.
	 *
	 * @param string $table       Table name.
	 * @param array  $where       Where clause values.
	 * @param array  $where_format Optional where format array.
	 * @return int|false Number of rows deleted or false.
	 */
	public static function delete( string $table, array $where, ?array $where_format = null ) {
		global $wpdb;
		return $wpdb->delete( $table, $where, $where_format );
	}

	/**
	 * Replace a row in a table.
	 *
	 * @param string $table Table name.
	 * @param array  $data  Row data.
	 * @param array  $format Optional value format array.
	 * @return int|false Insert or update result.
	 */
	public static function replace( string $table, array $data, ?array $format = null ) {
		global $wpdb;
		return $wpdb->replace( $table, $data, $format );
	}

	/**
	 * Return a prefixed AccessPress table name.
	 *
	 * @param string $table Unprefixed table suffix.
	 * @return string Full table name.
	 */
	public static function table_name( string $table ): string {
		global $wpdb;
		return $wpdb->prefix . 'accesspress_' . self::normalize_table_key( $table );
	}
}
