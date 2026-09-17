<?php
/**
 * ToolsManager class for AccessPress plugin.
 *
 * @package AccessPress
 */
namespace AccessPress\Admin\Manager\Tools;

use AccessPress\Admin\Manager\Manager;
use AccessPress\Admin\Manager\Tools\Debug;
use AccessPress\Admin\Manager\Tools\Reset;
use AccessPress\Admin\Manager\Tools\Import;
use AccessPress\Admin\Manager\Tools\Export;
use AccessPress\Admin\Manager\Tools\General;
use AccessPress\Assets\Assets;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;


class ToolsManager extends Manager {
	/**
	 * The Page variable.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $page The page variable.
	 */
	protected $page;
	/**
	 * DebugManager instance for managing the debug tool.
	 *
	 * @since 1.0.0
	 * @var Debug $debug_manager The debug manager instance.
	 */
	private Debug $debug_manager;
	/**
	 * ResetManager instance for managing the plugin reset tool.
	 *
	 * @since 1.0.0
	 * @var Reset $reset_manager The reset manager instance.
	 */
	private Reset $reset_manager;
	/**
	 * Import instance for managing the import tool.
	 *
	 * @since 1.0.0
	 * @var Import $import_manager The import manager instance.
	 */
	private Import $import_manager;
	/**
	 * Export instance for managing the export tool.
	 *
	 * @since 1.0.0
	 * @var Export $export_manager The export manager instance.
	 */
	private Export $export_manager;
	/**
	 * General instance for managing the general tools.
	 *
	 * @since 1.0.0
	 * @var General $general_tools_manager The general tools manager instance.
	 */
	private General $general_tools_manager;

	/**
	 * `Constructor` method for the `ToolsManager` class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( bool $initialize_tools = true ) {
		/**
		 * Set the page variable to 'tools'.
		 *
		 * @since 1.0.0
		 */
		$this->page = 'tools';

		if ( ! $initialize_tools ) {
			return;
		}

		/**
		 * Initialize the Debug Manager page.
		 *
		 * @since 1.0.0
		 */
		$this->debug_manager = new Debug();
		/**
		 * Initialize the Plugin Reset page.
		 *
		 * @since 1.0.0
		 */
		$this->reset_manager = new Reset();
		/**
		 * Initialize the Import Manager page.
		 *
		 * @since 1.0.0
		 */
		$this->import_manager = new Import();
		/**
		 * Initialize the Export Manager page.
		 *
		 * @since 1.0.0
		 */
		$this->export_manager = new Export();
		/**
		 * Initialize the General Tools Manager page.
		 *
		 * @since 1.0.0
		 */
		$this->general_tools_manager = new General();
	}
	/**
	 * Renders the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render(): void {
		$tool = RequestHelper::get_key( 'tool', 'debug' );
		if ( ! in_array( $tool, array( 'debug', 'reset', 'import', 'export', 'general' ), true ) ) {
			$tool = 'debug';
		}
		$capabilities = array(
			'debug'  => 'accesspress_tools_debug',
			'reset'  => 'accesspress_tools_reset',
			'import' => 'accesspress_tools_import',
			'export' => 'accesspress_tools_export',
			'general' => 'accesspress_tools_general',
		);
		if ( ! PermissionHelper::can( $capabilities[ $tool ] ) ) {
			wp_die( esc_html__( 'You are not authorized to access this AccessPress tool.', 'accesspress' ) );
		}
		$this->header( $this->title( $tool ) );
		if ( 'reset' === $tool ) {
			$this->reset_manager->render_page_content();
		} elseif ( 'debug' === $tool ) {
			$this->debug_manager->render_page_content();
		} elseif ( 'import' === $tool ) {
			$this->import_manager->render();
		} elseif ( 'general' === $tool ) {
			$this->general_tools_manager->render_page_content();
		} else {
			$this->export_manager->render_page_content();
		}
		$this->footer();
	}
	/**
	 * Registers the assets for the tools page.
	 *
	 * @since 1.0.0
	 * @param Assets $assets The Assets instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'accesspress-tools' ), 'tools' );
		if ( isset( $this->reset_manager ) ) {
			$this->reset_manager->register_assets( $assets );
		}
	}
	/**
	 * Returns the title for the given tool.
	 *
	 * @since 1.0.0
	 * @param string $tool The tool name.
	 * @return string The title for the tool.
	 */
	private function title( string $tool ): string {
		return array(
			'debug'  => __( 'Debug', 'accesspress' ),
			'reset'  => __( 'Reset', 'accesspress' ),
			'import' => __( 'Import', 'accesspress' ),
			'export' => __( 'Export', 'accesspress' ),
			'general' => __( 'General', 'accesspress' ),
		)[ $tool ];
	}
}
