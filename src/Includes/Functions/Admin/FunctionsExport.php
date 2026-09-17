<?php
/**
 * Export-related admin functions for AccessPress.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

use AccessPress\Includes\Tools\DataTransfer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsExport {

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
}



