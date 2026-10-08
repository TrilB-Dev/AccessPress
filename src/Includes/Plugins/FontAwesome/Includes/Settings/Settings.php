<?php
/**
 * Settings for the Font Awesome AccessPress plugin.
 * 
 * @package    AccessPress
 * @subpackage AccessPress/includes
 */
namespace AccessPress\Includes\Plugins\FontAwesome\Includes\Settings;
use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    /**
     * Registers the Font Awesome settings group.
     *
     * @since 1.0.0
     */
    public function register(): void {
        BaseSettings::register_group( 'fontawesome', [
            'fontawesome_type' => 'cdn',
            'fontawesome_cdn_technology' => 'webfont',
            'fontawesome_kit_id' => '',
            'fontawesome_enable_icon_picker' => true,
        ] );
    }
    /**
     * Determines whether the icon picker is enabled.
     *
     * @return bool True if the icon picker is enabled, false otherwise.
     * @since 1.0.0
     */
    public static function enable_icon_picker(): bool {
        return BaseSettings::get_bool( 'fontawesome_enable_icon_picker', true );
    }

    /**
     * Retrieves the selected Font Awesome source type.
     *
     * @return string The source type.
     */
    public static function get_type(): string {
        $type = BaseSettings::get_key( 'fontawesome_type', 'cdn' );
        return in_array( $type, [ 'cdn', 'kit' ], true ) ? $type : 'cdn';
    }

    /**
     * Retrieves the configured Font Awesome kit ID.
     *
     * @return string The Font Awesome kit ID.
     */
    public static function get_kit_id(): string {
        return BaseSettings::get_string( 'fontawesome_kit_id', '' );
    }

    /**
     * Retrieves the selected Font Awesome CDN technology.
     *
     * @return string The CDN technology.
     */
    public static function get_cdn_technology(): string {
        $technology = BaseSettings::get_key( 'fontawesome_cdn_technology', 'webfont' );
        return in_array( $technology, [ 'webfont', 'svg' ], true ) ? $technology : 'webfont';
    }
    /**
     * Retrieves the settings page configuration for the Font Awesome plugin.
     *
     * @return array The settings page configuration.
     * @since 1.0.0
     */
    public function get_settings_page(): array {
        return [
            'slug' => 'fontawesome',
            'label' => __( 'FontAwesome', 'accesspress' ),
            'title' => __( 'FontAwesome integration', 'accesspress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'fontawesome_type',
                    'label' => __( 'FontAwesome Type', 'accesspress' ),
                    'description' => __( 'Select how fontawesome is called in AccessPress.', 'accesspress' ),
                    'tooltip' => __( 'Choose between using the CDN or a Kit for FontAwesome.', 'accesspress' ),
                    'tooltip_type' => 'info',
                    'type' => 'select',
                    'class' => 'w-100',
                    'options' => [
                        'cdn' => __( 'CDN', 'accesspress' ),
                        'kit' => __( 'Kit', 'accesspress' ),
                    ],
                    'default' => 'cdn',
                ],
                [
                    'key' => 'fontawesome_cdn_technology',
                    'label' => __( 'FontAwesome CDN Technology', 'accesspress' ),
                    'description' => __( 'Select the technology used for the Font Awesome CDN.', 'accesspress' ),
                    'tooltip' => __( 'Choose between different CDN technologies for loading Font Awesome.', 'accesspress' ),
                    'tooltip_type' => 'info',
                    'type' => 'select',
                    'class' => 'w-100',
                    'options' => [
                        'svg' => __( 'SVG', 'accesspress' ),
                        'webfont' => __( 'Web Font', 'accesspress' ),
                    ],
                    'default' => 'webfont',
                    'visible_when' => [
                        'fontawesome_type' => 'cdn',
                    ],
                    'required' => [
                        'fontawesome_type' => 'cdn',
                    ],

                ],
                [
                    'key' => 'fontawesome_kit_id',
                    'label' => __( 'FontAwesome Kit ID', 'accesspress' ),
                    'description' => __( 'Enter the ID of your Font Awesome Kit if you selected "Kit" as the type.', 'accesspress' ),
                    'tooltip' => __( 'The FontAwesome Kit ID is required when using the Kit type.', 'accesspress' ),
                    'tooltip_type' => 'info',
                    'type' => 'text',
                    'class' => 'w-100',
                    'default' => '',
                    'visible_when' => [
                        'fontawesome_type' => 'kit',
                    ],
                    'required' => [
                        'fontawesome_type' => 'kit',
                    ],
                ],
                [
                    'key' => 'fontawesome_enable_icon_picker',
                    'label' => __( 'Enable icon picker', 'accesspress' ),
                    'description' => __( 'Enable the Font Awesome icon picker inside AccessPress admin interfaces.', 'accesspress' ),
                    'tooltip' => __( 'This only controls the AccessPress icon picker UI. The official Font Awesome admin bundle is still enqueued for the AccessPress screens.', 'accesspress' ),
                    'tooltip_type' => 'info',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
        ];
    }
    /**
     * Sanitizes the input for the Font Awesome settings.
     *
     * @param array $input The input array to sanitize.
     * @return array The sanitized input array.
     * @since 1.0.0
     */
    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];

        $type = SanitizationHelper::key( $input['fontawesome_type'] ?? 'cdn', 'cdn' );
        $input['fontawesome_type'] = in_array( $type, [ 'cdn', 'kit' ], true ) ? $type : 'cdn';

        if ( 'kit' === $input['fontawesome_type'] ) {
            $input['fontawesome_kit_id'] = trim( (string) ( $input['fontawesome_kit_id'] ?? '' ) );
        } else {
            $input['fontawesome_kit_id'] = '';
        }

        $input['fontawesome_enable_icon_picker'] = (bool) ( $input['fontawesome_enable_icon_picker'] ?? true );
        BaseSettings::set_group( 'fontawesome', $input );
        return $input;
    }
}



