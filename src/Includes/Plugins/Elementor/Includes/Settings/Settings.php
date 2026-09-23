<?php
/**
 * Settings for the AccessPress Elementor integration.
 *
 * @package AccessPress
 * @subpackage Includes\Plugins\Elementor\Includes\Settings
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Settings;

use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Settings {
	public const GROUP = 'elementor';

	public function register(): void {
		BaseSettings::register_group(
			self::GROUP,
			array(
				'elementor_enabled'                   => true,
				'elementor_widget_login_form'         => true,
				'elementor_widget_register_form'      => true,
				'elementor_widget_user_menu_item'     => true,
				'elementor_widget_user_profile'       => true,
				'elementor_widget_username_field'     => true,
				'elementor_widget_email_field'        => true,
				'elementor_widget_password_field'     => true,
				'elementor_widget_display_name_field' => true,
				'elementor_widget_first_name_field'   => true,
				'elementor_widget_last_name_field'    => true,
				'elementor_widget_nickname_field'     => true,
				'elementor_widget_website_field'      => true,
				'elementor_widget_description_field'  => true,
				'elementor_widget_role_field'         => true,
				'elementor_widget_locale_field'       => true,
				'elementor_widget_custom_meta_field'  => true,
			)
		);
	}

	public static function enabled(): bool {
		return BaseSettings::get_bool( 'elementor_enabled', true );
	}

	public static function widget_enabled( string $slug ): bool {
		return self::enabled() && BaseSettings::get_bool( 'elementor_widget_' . SanitizationHelper::key( $slug ), true );
	}

	public function get_settings_page(): array {
		return array(
			'slug'    => self::GROUP,
			'label'   => __( 'Elementor', 'accesspress' ),
			'title'   => __( 'Elementor integration', 'accesspress' ),
			'layout'  => 'box',
			'fields'  => array(
				array(
					'key'         => 'elementor_enabled',
					'label'       => __( 'Enable AccessPress Elementor widgets', 'accesspress' ),
					'description' => __( 'Enable the AccessPress widgets available in Elementor.', 'accesspress' ),
					'tooltip'     => __( 'Disable this to remove AccessPress widgets from the Elementor editor.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_login_form',
					'label'       => __( 'Login form', 'accesspress' ),
					'description' => __( 'Enable the AccessPress login form widget.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_register_form',
					'label'       => __( 'Register form', 'accesspress' ),
					'description' => __( 'Enable the AccessPress register form widget.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_user_menu_item',
					'label'       => __( 'User menu item', 'accesspress' ),
					'description' => __( 'Enable the user menu widget for login or account actions.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_user_profile',
					'label'       => __( 'User profile', 'accesspress' ),
					'description' => __( 'Enable the AccessPress user profile widget.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_username_field',
					'label'       => __( 'Username field', 'accesspress' ),
					'description' => __( 'Enable the username field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_email_field',
					'label'       => __( 'Email field', 'accesspress' ),
					'description' => __( 'Enable the email field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_password_field',
					'label'       => __( 'Password field', 'accesspress' ),
					'description' => __( 'Enable the password field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_display_name_field',
					'label'       => __( 'Display name field', 'accesspress' ),
					'description' => __( 'Enable the display-name field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_first_name_field',
					'label'       => __( 'First name field', 'accesspress' ),
					'description' => __( 'Enable the first-name field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_last_name_field',
					'label'       => __( 'Last name field', 'accesspress' ),
					'description' => __( 'Enable the last-name field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_nickname_field',
					'label'       => __( 'Nickname field', 'accesspress' ),
					'description' => __( 'Enable the nickname field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_website_field',
					'label'       => __( 'Website field', 'accesspress' ),
					'description' => __( 'Enable the website field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_description_field',
					'label'       => __( 'Bio field', 'accesspress' ),
					'description' => __( 'Enable the bio field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_role_field',
					'label'       => __( 'Role field', 'accesspress' ),
					'description' => __( 'Enable the role field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_locale_field',
					'label'       => __( 'Language field', 'accesspress' ),
					'description' => __( 'Enable the language field widget for AccessPress forms.', 'accesspress' ),
					'default'     => true,
				),
				array(
					'key'         => 'elementor_widget_custom_meta_field',
					'label'       => __( 'Custom meta field', 'accesspress' ),
					'description' => __( 'Enable the dynamic custom metadata widget with a list of available field keys.', 'accesspress' ),
					'default'     => true,
				),
			),
		);
	}

	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : [];
		foreach ( array_column( $this->get_settings_page()['fields'], 'key' ) as $key ) {
			$input[ $key ] = ! empty( $input[ $key ] );
			BaseSettings::set( $key, $input[ $key ] );
		}
		return $input;
	}
}


