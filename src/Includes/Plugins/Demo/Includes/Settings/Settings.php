<?php
/**
 * Settings for the Demo plugin.
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Demo\Includes\Settings;
use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    /**
     * Returns the settings for the Demo plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 'demo', [
            'demo_setting_1' => '',
            'demo_setting_2' => false,
        ] );
    }

    public function get_settings_page(): array {
        return [
            'slug' => 'demo',
            'label' => __( 'Demo', 'accesspress' ),
            'title' => __( 'Demo plugin settings', 'accesspress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'demo_setting_1',
                    'label' => __( 'Demo text setting', 'accesspress' ),
                    'description' => __( 'A short value used to demonstrate plugin setting metadata.', 'accesspress' ),
                    'tooltip' => __( 'This tooltip uses the default question icon.', 'accesspress' ),
                    'type' => 'text',
                    'default' => '',
                ],
                [
                    'key' => 'demo_setting_2',
                    'label' => __( 'Enable demo setting', 'accesspress' ),
                    'description' => __( 'Toggle the second Demo setting on or off.', 'accesspress' ),
                    'tooltip' => __( 'This tooltip uses the info icon and demonstrates a custom icon override.', 'accesspress' ),
                    'tooltip_type' => 'info',
                    'tooltip_icon' => 'fa-circle-exclamation',
                    'default' => false,
                ],
            ],
        ];
    }

    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $input['demo_setting_1'] = SanitizationHelper::text( $input['demo_setting_1'] ?? '' );
        $input['demo_setting_2'] = ! empty( $input['demo_setting_2'] );
        BaseSettings::set_group( 'demo', $input );
        return $input;
    }
}