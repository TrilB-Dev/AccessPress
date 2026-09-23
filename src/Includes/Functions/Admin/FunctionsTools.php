<?php
/**
 * Tools-related admin functions for AccessPress.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsTools {
    /**
	 * Retrieve debug information about the WordPress environment.
	 *
	 * @return array<string, mixed> The debug information.
	 */
	public static function debug_info(): array {
		return array(
			'php_version'         => phpversion(),
			'wordpress_version'   => get_bloginfo( 'version' ),
			'active_plugins'      => get_option( 'active_plugins', array() ),
			'theme'               => wp_get_theme()->get( 'Name' ),
			'memory_limit'        => ini_get( 'memory_limit' ),
			'max_execution_time'  => ini_get( 'max_execution_time' ),
			'upload_max_filesize' => ini_get( 'upload_max_filesize' ),
			'post_max_size'       => ini_get( 'post_max_size' ),
		);
	}
    /**
	 * Export AccessPress data as a JSON file.
	 *
	 * @return void
	 */
	public function export_data(): void {
		if ( ! current_user_can( 'accesspress_tools_export' ) ) {
			wp_die( esc_html__( 'You are not allowed to export AccessPress data.', 'accesspress' ), 403 );
		}
		check_admin_referer( 'accesspress_export' );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=accesspress-export-' . gmdate( 'Y-m-d' ) . '.json' );
		echo wp_json_encode( DataTransfer::export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		exit;
	}
    /**
	 * Import AccessPress data from an uploaded JSON file.
	 *
	 * @return void
	 */
	public function import_data(): void {
		if ( ! current_user_can( 'accesspress_tools_import' ) ) {
			wp_die( esc_html__( 'You are not allowed to import AccessPress data.', 'accesspress' ), 403 );
		}
		check_admin_referer( 'accesspress_import' );
		$file = $_FILES['accesspress_import_file'] ?? array();
		if ( empty( $file['tmp_name'] ) || ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
			wp_die( esc_html__( 'Please upload a valid AccessPress JSON export.', 'accesspress' ), 400 );
		}
		$data = json_decode( file_get_contents( $file['tmp_name'] ), true );
		if ( ! is_array( $data ) ) {
			wp_die( esc_html__( 'The uploaded file is not valid JSON.', 'accesspress' ), 400 );
		}
		$result = DataTransfer::import( $data );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ), 400 );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=accesspress-settings&tab=tools&imported=1' ) );
		exit;
	}
}