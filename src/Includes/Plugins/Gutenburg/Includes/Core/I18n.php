<?php
/**
 * Language internationalization (i18n) for the AccessPress plugin.
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\AccessPress\Includes
 * @since 1.0.0
 * 
 */
namespace AccessPress\Includes\Plugins\Gutenburg\Includes\Core;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'accesspress',
            false,
            dirname( plugin_basename( ACCESSPRESS_FILE ) ) . '/src/includes/Plugins/Gutenburg/Language/'
        );
    }
}


