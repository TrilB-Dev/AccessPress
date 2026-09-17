<?php
/**
 * Language internationalization (i18n) for the Demo plugin.
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 * 
 */
namespace AccessPress\Includes\Plugins\Demo\Includes\Core;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'accesspress',
            false,
            dirname( plugin_basename( ACCESSPRESS_PLUGINS ) ) . '/Demo/Language/'
        );
    }
}