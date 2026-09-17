<?php
/**
 * Demo capability definitions.
 *
 * @package AccessPress
 * @subpackage Includes\Plugins\Demo\Includes\Core
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Demo\Includes\Core;

use AccessPress\Includes\Core\Capabilities as CoreCapabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Capabilities {
	/**
	 * Register any plugin-level Demo capability metadata.
	 *
	 * @return void
	 */
	public static function register(): void {
		// This plugin contributes its capability checks through the core capability registry.
		CoreCapabilities::extend( 
			array(
				'accesspress_demo_view'               => array(
					'group'       => 'AccessPress Demo',
					'label'       => __( 'View Demo Dashboard', 'accesspress' ),
					'description' => __( 'Allows viewing the Demo operations dashboard.', 'accesspress' ),
				),
				'accesspress_demo_manage'             => array(
					'group'       => 'AccessPress Demo',
					'label'       => __( 'Manage Demo Settings', 'accesspress' ),
					'description' => __( 'Allows changing Demo settings.', 'accesspress' ),
				),
			)
		);
	}
}
