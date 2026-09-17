<?php
/**
 * This file manages the internationalization functionality of the plugin.
 *
 * @package AccessPress\Includes\Plugins\FontAwesome\Includes
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\FontAwesome\Includes\Core;

final class I18n {
	public static function load_textdomain(): void {
		load_plugin_textdomain(
			'accesspress',
			false,
			dirname( plugin_basename( ACCESSPRESS_FILE ) ) . '/src/Includes/Plugins/FontAwesome/Language/'
		);
	}
}



