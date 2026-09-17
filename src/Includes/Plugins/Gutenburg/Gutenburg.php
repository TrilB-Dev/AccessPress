<?php
/**
 * AccessPress - Demo Wiki Plugin
 *
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\Gutenburg
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Gutenburg;

use AccessPress\Includes\Plugins\AssetsProviderInterface;
use AccessPress\Includes\Plugins\I18nProviderInterface;
use AccessPress\Includes\Plugins\PluginInterface;
use AccessPress\Includes\Plugins\SettingsProviderInterface;
use AccessPress\Includes\Plugins\SettingsPageProviderInterface;
use AccessPress\Includes\Plugins\Gutenburg\Assets\Assets;
use AccessPress\Includes\Plugins\Gutenburg\Includes\Includes;
use AccessPress\Includes\Plugins\Gutenburg\Includes\I18n;

class Gutenburg implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    /**
     * Get the plugin slug.
     *
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'accesspress-gutenburg';
    }
    /**
     * Get the plugin name.
     *
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'Gutenburg Blocks';
    }
    /**
     * Get the plugin version.
     *
     * @return string The plugin version.
     */
    public function get_version(): string {
        return '1.0.0';
    }
    /**
     * Get the plugin author.
     *
     * @return string The plugin author.
     */
    public function get_author(): string {
        return 'AccessPress Team';
    }
    /**
     * Get the plugin author URI.
     *
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }
    /**
     * Get the plugin description.
     *
     * @return string The plugin description.
     */
    public function get_description(): string {
        return __( 'Gutenberg blocks for AccessPress content.', 'accesspress' );
    }
    /**
     * Get the plugin URI.
     *
     * @return string The plugin URI.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/accesspress';
    }
    /**
     * Get the plugin license.
     *
     * @return string The plugin license.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Check if the plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function init(): void {
        Includes::get_instance()->init();
    }
    /**
     * Register the settings for the plugin.
     *
     * @return void
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }
    /**
     * Get the settings page for the plugin.
     *
     * @return array The settings page configuration.
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }
    /**
     * Sanitize the settings input for the plugin.
     *
     * @param mixed $input The input to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }
    /**
     * Register the assets for the plugin.
     *
     * @return void
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Load the text domain for the plugin.
     *
     * @return void
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}


