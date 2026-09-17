<?php
/**
 * Settings for the User Roles Manager plugin.
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\UserRolesManager\Includes
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\UserRolesManager\Includes\Settings;
use AccessPress\Includes\Settings\Settings as BaseSettings;

final class Settings {
    /**
     * Returns the settings for the User Roles Manager plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 'user_roles_manager', [ 'role_manager_capability' => 'manage_options' ] );
    }
    /**
     * Returns the settings page configuration for the User Roles Manager plugin.
     *
     * @return array The settings page configuration array.
     */
    public function get_settings_page(): array {
        return [
            'slug' => 'user_roles_manager',
            'label' => __( 'User Roles Manager', 'accesspress' ),
            'title' => __( 'User Roles Manager settings', 'accesspress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'role_manager_capability',
                    'label' => __( 'Required capability', 'accesspress' ),
                    'description' => __( 'Choose the capability required to manage roles.', 'accesspress' ),
                    'type' => 'select',
                    'options' => [
                        'manage_options' => __( 'Manage options', 'accesspress' ),
                        'edit_users' => __( 'Edit users', 'accesspress' ),
                    ],
                    'default' => 'manage_options',
                ],
            ],
        ];
    }

    /**
     * Sanitizes the input for the User Roles Manager plugin settings.
     *
     * @param array $input The input array.
     * @return array The sanitized settings array.
     */
    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $capability = sanitize_key( $input['role_manager_capability'] ?? 'manage_options' );
        $settings = [
            'role_manager_capability' => in_array( $capability, [ 'manage_options', 'edit_users' ], true ) ? $capability : 'manage_options',
        ];
        BaseSettings::set_group( 'user_roles_manager', $settings );
        return $settings;
    }
}