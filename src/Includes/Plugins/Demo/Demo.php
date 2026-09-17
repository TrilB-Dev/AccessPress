<?php
/**
 * Demo AccessPress Plugin
 *
 * @package AccessPress
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Demo;

use AccessPress\Includes\Plugins\AssetsProviderInterface;
use AccessPress\Includes\Plugins\AdminSidebarProviderInterface;
use AccessPress\Includes\Plugins\CapabilitiesProviderInterface;
use AccessPress\Includes\Plugins\I18nProviderInterface;
use AccessPress\Includes\Plugins\PluginInterface;
use AccessPress\Includes\Plugins\SettingsProviderInterface;
use AccessPress\Includes\Plugins\SettingsPageProviderInterface;
use AccessPress\Includes\Plugins\ShortcodeProviderInterface;
use AccessPress\Includes\Plugins\AdminMenuProviderInterface;
use AccessPress\Includes\Plugins\Demo\Assets\Assets;
use AccessPress\Includes\Plugins\Demo\Includes\Includes;
use AccessPress\Includes\Plugins\Demo\Includes\Core\I18n;
use AccessPress\Includes\Plugins\Demo\Includes\Core\Capabilities;
use AccessPress\Includes\Plugins\Demo\Admin\DemoSettings;
use AccessPress\Includes\Settings\Settings as BaseSettings;

class Demo implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface, ShortcodeProviderInterface, AdminSidebarProviderInterface, AdminMenuProviderInterface, CapabilitiesProviderInterface {
    /**
     * Get the plugin slug.
     *
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'accesspress-demo';
    }
    /**
     * Get the plugin name.
     *
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'Demo';
    }
    /**
     * Get the plugin icon.
     *
     * @return string The plugin icon.
     */
    public function get_icon(): array {
        return array( 'fa-solid fa-democrat', '#74c1fcff' );
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
        return 'TrilB.Dev Team';
    }
    /**
     * Get the plugin author URI.
     *
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
            return 'https://github.com/TrilB-Dev/AccessPress';
    }
    /**
     * Get the plugin description.
     *
     * @return string The plugin description.
     */
    public function get_description(): string {
        return __( 'Adds demo functionality to the AccessPress plugin.', 'accesspress' );
    }
    /**
     * Get the plugin URI.
     *
     * @return string The plugin URI.
     */
    public function get_uri(): string {
            return 'https://github.com/TrilB-Dev/AccessPress';
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
     * Initializes the plugin.
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        Includes::get_instance()->init();
    }

    /**
     * Return Exchange-owned admin pages.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_admin_menu(): array {
        return [
            [
                'page_title' => __( 'Demo', 'accesspress' ),
                'menu_title' => __( 'Demo', 'accesspress' ),
                'menu_slug' => 'accesspress-demo-settings',
                'parent' => 'accesspress',
                'callback' => [ DemoSettings::class, 'render' ],
                'capability' => 'accesspress_settings_plugins_view',
                'children' => [
                    [
                        'page_title' => __( 'Demo Settings', 'accesspress' ),
                        'menu_title' => __( 'Demo Settings', 'accesspress' ),
                        'menu_slug' => 'accesspress&group=demo&tab=general',
                        'callback' => [ DemoSettings::class, 'render' ],
                        'capability' => 'accesspress_settings_plugins_view',
                    ]
                ],
            ],
        ];
    }
    /**
     * Registers the settings for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }

    public function register_capabilities(): void {
        Capabilities::register();
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
     * Return the Exchange navigation group for the AccessPress admin sidebar.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_admin_sidebar(): array {
        $settings = BaseSettings::get_group( 'demo', [] ) ?? [];

        if ( empty( $settings ) ) {
            return [];
        }

        return [
            [
                'type' => 'group',
                'label' => __( 'Demo', 'accesspress' ),
                'slug' => 'demo',
                'icon' => 'fa-solid fa-democrat',
                'capability' => 'mspress_settings_plugins_view',
                'items' => [
                    [
                        'label' => __( 'Demo Overview', 'accesspress' ),
                        'page' => 'demo-overview',
                        'query' => [ 'tab' => 'overview' ],
                        'icon' => 'fa-solid fa-gauge-high',
                        'capability' => 'mspress_settings_plugins_view',
                    ],
                    [
                        'label' => __( 'Demo Settings', 'accesspress' ),
                        'page' => 'demo-settings',
                        'query' => [ 'tab' => 'settings' ],
                        'icon' => 'fa-solid fa-file-lines',
                        'capability' => 'edit_posts',
                    ]
                ],
            ],
        ];
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
     * Get the shortcodes for the plugin.
     *
     * @return array The shortcodes for the plugin.
     */
    public function get_shortcodes(): array {
        return Includes::get_instance()->shortcodes()->definitions();
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
