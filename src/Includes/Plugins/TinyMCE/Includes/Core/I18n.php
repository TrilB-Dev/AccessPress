<?php
/**
 * Language internationalization (i18n) for the TinyMCE plugin.
 *
 * @package AccessPress
 * @subpackage Plugins\TinyMCE\Includes
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\TinyMCE\Includes\Core;

final class I18n {
	/**
	 * Loads the plugin's text domain for translation.
	 */
	public static function load_textdomain(): void {
		load_plugin_textdomain(
			'accesspress',
			false,
			dirname( plugin_basename( ACCESSPRESS_FILE ) ) . '/src/Includes/Plugins/TinyMCE/Language/'
		);
	}
}



