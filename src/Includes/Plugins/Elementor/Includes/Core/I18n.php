<?php
/**
 * This file manages the internationalization functionality of the plugin.
 * 
 * 
 * 
 * @package AccessPress\Includes\Plugins\Elementor\Includes
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Elementor\Includes\Core;

final class I18n {
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'accesspress',
            false,
            dirname( plugin_basename( ACCESSPRESS_FILE ) ) . '/src/includes/Plugins/Elementor/Language/'
        );
    }
}


