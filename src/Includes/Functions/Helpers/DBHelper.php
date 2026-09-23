<?php

namespace AccessPress\Includes\Functions\Helpers;

use AccessPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DBHelper {
	/**
	 * Get the WordPress database instance.
	 *
	 * @return \wpdb
	 */
	public static function wpdb() {
		return Database::wpdb();
	}

	/**
	 * Prepare a query using the shared database wrapper.
	 *
	 * @param string $query SQL query.
	 * @param mixed  ...$args Query arguments.
	 * @return string Prepared SQL.
	 */
	public static function prepare( string $query, ...$args ): string {
		return Database::prepare( $query, ...$args );
	}

	/**
	 * Run a raw SQL query.
	 *
	 * @param string $query SQL to run.
	 * @return mixed
	 */
	public static function query( string $query ) {
		return Database::query( $query );
	}

	/**
	 * Get a single variable from the database.
	 *
	 * @param string $query SQL query.
	 * @param int    $x     Column offset.
	 * @param int    $y     Row offset.
	 * @return mixed
	 */
	public static function get_var( string $query, int $x = 0, int $y = 0 ) {
		return Database::get_var( $query, $x, $y );
	}

	/**
	 * Get one row from the database.
	 *
	 * @param string $query SQL query.
	 * @param string $output Output format.
	 * @param int    $y      Row offset.
	 * @return mixed
	 */
	public static function get_row( string $query, string $output = ARRAY_A, int $y = 0 ) {
		return Database::get_row( $query, $output, $y );
	}

	/**
	 * Get multiple rows from the database.
	 *
	 * @param string $query SQL query.
	 * @param string $output Output format.
	 * @return mixed
	 */
	public static function get_results( string $query, string $output = ARRAY_A ) {
		return Database::get_results( $query, $output );
	}

	/**
	 * Insert a row into a table.
	 *
	 * @param string $table Table name.
	 * @param array  $data  Row data.
	 * @param array  $format Optional value format.
	 * @return int|false
	 */
	public static function insert( string $table, array $data, ?array $format = null ) {
		return Database::insert( $table, $data, $format );
	}

	/**
	 * Update rows in a table.
	 *
	 * @param string $table       Table name.
	 * @param array  $data        Row data.
	 * @param array  $where       Where clause values.
	 * @param array  $format      Optional value format.
	 * @param array  $where_format Optional where format.
	 * @return int|false
	 */
	public static function update( string $table, array $data, array $where, ?array $format = null, ?array $where_format = null ) {
		return Database::update( $table, $data, $where, $format, $where_format );
	}

	/**
	 * Delete rows from a table.
	 *
	 * @param string $table       Table name.
	 * @param array  $where       Where clause values.
	 * @param array  $where_format Optional where format.
	 * @return int|false
	 */
	public static function delete( string $table, array $where, ?array $where_format = null ) {
		return Database::delete( $table, $where, $where_format );
	}

	/**
	 * Replace a row in a table.
	 *
	 * @param string $table Table name.
	 * @param array  $data  Row data.
	 * @param array  $format Optional value format.
	 * @return int|false
	 */
	public static function replace( string $table, array $data, ?array $format = null ) {
		return Database::replace( $table, $data, $format );
	}

	/**
	 * Check if a table exists.
	 *
	 * @param string $table Unprefixed table name.
	 * @return bool
	 */
	public static function table_exists( string $table ): bool {
		return Database::table_exists( $table );
	}

	/**
	 * Resolve the full table name.
	 *
	 * @param string $table Unprefixed table name.
	 * @return string
	 */
	public static function table_name( string $table ): string {
		return Database::table_name( $table );
	}
}