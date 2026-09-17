<?php
/**
 * AccessPress - User Roles Manager
 *
 * @package AccessPress
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\UserRolesManager;

use AccessPress\Includes\Plugins\AssetsProviderInterface;
use AccessPress\Includes\Plugins\AdminPageProviderInterface;
use AccessPress\Includes\Plugins\AdminMenuProviderInterface;
use AccessPress\Includes\Plugins\AdminSidebarProviderInterface;
use AccessPress\Includes\Plugins\I18nProviderInterface;
use AccessPress\Includes\Plugins\PluginInterface;
use AccessPress\Includes\Plugins\SettingsProviderInterface;
use AccessPress\Includes\Plugins\SettingsPageProviderInterface;
use AccessPress\Includes\Plugins\UserRolesManager\Assets\Assets;
use AccessPress\Includes\Plugins\UserRolesManager\Includes\Includes;
use AccessPress\Includes\Plugins\UserRolesManager\Includes\Core\I18n;
use AccessPress\Includes\Plugins\UserRolesManager\Includes\Core\Capabilities;
use AccessPress\Includes\Plugins\UserRolesManager\Admin\RoleManager;
use AccessPress\Includes\Settings\Settings;

class UserRolesManager implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, AdminPageProviderInterface, AdminMenuProviderInterface, AdminSidebarProviderInterface, I18nProviderInterface {
    /**
     * The role manager instance.
     * 
     * @since 1.0.0
     * @var RoleManager|null
     */
    private ?RoleManager $role_manager = null;
    /**
     * Returns the slug of the plugin.
     * 
     * @since 1.0.0
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'accesspress-user-roles-manager';
    }
    /**
     * Returns the name of the plugin.
     * 
     * @since 1.0.0
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'User Roles Manager';
    }
    /**
     * Returns the version of the plugin.
     * @since 1.0.0
     * @return string The plugin version.
     */
    public function get_version(): string {
        return '1.0.0';
    }
    /**
     * Returns the author of the plugin.
     * @since 1.0.0
     * @return string The plugin author.
     */
    public function get_author(): string {
        return 'TrilB.Dev Team';
    }
    /**
     * Returns the author URI of the plugin.
     * @since 1.0.0
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev/';
    }
    /**
     * Returns the description of the plugin.
     * @since 1.0.0
     * @return string The plugin description.
     */
    public function get_description(): string {
        return __( 'Allows you to manage user roles and capabilities in your WordPress site.', 'accesspress' );
    }
    /**
     * Returns the URI of the plugin.
     * @since 1.0.0
     * @return string The plugin URI.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/accesspress';
    }
    /**
     * Returns the license of the plugin.
     * @since 1.0.0
     * @return string The plugin license.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Checks if the plugin is active.
     * @since 1.0.0
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Initializes the plugin.
     * @since 1.0.0
     */
    public function init(): void {
        Includes::get_instance()->init();
    }
    /**
     * Registers the settings for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }
    /**
     * Get the settings page for the plugin.
     *
     * @return array<string, mixed> The settings page configuration.
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }
    /**
     * Sanitize and persist plugin settings.
     *
     * @param mixed $input Submitted settings.
     * @return array<string, mixed> Sanitized settings.
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }
    /**
     * Registers the assets for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Registers the admin pages for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_admin_pages(): void {
        $this->role_manager()->register();
    }
    /**
     * Returns the WordPress admin menu definition for the Roles Manager.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_admin_menu(): array {
        return [
            [
                'page_title' => __( 'Roles Manager', 'accesspress' ),
                'menu_title' => __( 'Roles Manager', 'accesspress' ),
                'capability' => 'accesspress_roles_view',
                'menu_slug' => 'accesspress-roles-manager',
                'parent' => 'users.php',
                'callback' => [ $this->role_manager(), 'render' ],
            ],
        ];
    }
    /**
     * Returns the AccessPress sidebar definition for the Roles Manager.
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_admin_sidebar(): array {
        return [
            [
                'type' => 'item',
                'parent' => 'tools',
                'label' => __( 'Roles Manager', 'accesspress' ),
                'page' => 'accesspress-roles-manager',
                'capability' => 'accesspress_roles_view',
                'icon' => 'fa-solid fa-user-shield',
            ],
        ];
    }

    private function role_manager(): RoleManager {
        if ( null === $this->role_manager ) {
            $this->role_manager = new RoleManager();
        }

        return $this->role_manager;
    }
    /**
     * Loads the text domain for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
