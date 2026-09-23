<?php
/**
 * Base AccessPress Elementor widget class.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets;

use Elementor\Widget_Base;
use AccessPress\Includes\Plugins\Elementor\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Widgets extends Widget_Base {
	/**
	 * Get the default title for the widget.
	 *
	 * @return string The default title.
	 */
	abstract protected function get_default_title(): string;

	/**
	 * Get the default icon for the widget.
	 *
	 * @return string The default icon.
	 */
	abstract protected function get_default_icon(): string;

	/**
	 * Get the default category for the widget.
	 *
	 * @return string The default category.
	 */
	protected function get_default_category(): string {
		return 'accesspress-user-management';
	}

	/**
	 * Get the default keywords for the widget.
	 *
	 * @return array<int, string>
	 */
	protected function get_default_keywords(): array {
		return array( 'accesspress', 'user management', 'login', 'profile' );
	}

	/**
	 * Get the widget slug.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return static::SLUG;
	}

	/**
	 * Get the widget title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->get_default_title();
	}

	/**
	 * Get the widget icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return $this->get_default_icon();
	}

	/**
	 * Get the widget categories.
	 *
	 * @return array<int, string>
	 */
	public function get_categories(): array {
		return array( $this->get_default_category() );
	}

	/**
	 * Get the widget keywords.
	 *
	 * @return array<int, string>
	 */
	public function get_keywords(): array {
		return $this->get_default_keywords();
	}

	/**
	 * Register the AccessPress widgets with Elementor.
	 *
	 * @param mixed $manager Elementor widget manager.
	 * @return void
	 */
	public static function register( $manager ): void {
		$widget_files = array(
			dirname( __FILE__ ) . '/Login-Register/LoginForm.php',
			dirname( __FILE__ ) . '/Login-Register/RegisterForm.php',
			dirname( __FILE__ ) . '/Login-Register/UserMenuItem.php',
			dirname( __FILE__ ) . '/Profile/UserProfile.php',
			dirname( __FILE__ ) . '/Fields/FieldWidget.php',
			dirname( __FILE__ ) . '/Fields/UsernameField.php',
			dirname( __FILE__ ) . '/Fields/EmailField.php',
			dirname( __FILE__ ) . '/Fields/PasswordField.php',
			dirname( __FILE__ ) . '/Fields/DisplayNameField.php',
			dirname( __FILE__ ) . '/Fields/FirstNameField.php',
			dirname( __FILE__ ) . '/Fields/LastNameField.php',
			dirname( __FILE__ ) . '/Fields/NicknameField.php',
			dirname( __FILE__ ) . '/Fields/WebsiteField.php',
			dirname( __FILE__ ) . '/Fields/DescriptionField.php',
			dirname( __FILE__ ) . '/Fields/RoleField.php',
			dirname( __FILE__ ) . '/Fields/LocaleField.php',
			dirname( __FILE__ ) . '/Fields/CustomMetaField.php',
		);

		foreach ( $widget_files as $file ) {
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}

		$classes = array(
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\LoginRegister\\LoginForm',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\LoginRegister\\RegisterForm',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\LoginRegister\\UserMenuItem',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Profile\\UserProfile',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\UsernameField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\EmailField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\PasswordField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\DisplayNameField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\FirstNameField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\LastNameField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\NicknameField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\WebsiteField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\DescriptionField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\RoleField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\LocaleField',
			'AccessPress\\Includes\\Plugins\\Elementor\\Includes\\Widgets\\Fields\\CustomMetaField',
		);

		foreach ( $classes as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$slug = defined( $class . '::SLUG' ) ? $class::SLUG : '';
			if ( '' === $slug ) {
				continue;
			}

			$setting_slug = str_replace( 'accesspress_', '', $slug );
			if ( Settings::widget_enabled( $setting_slug ) ) {
				$manager->register( new $class() );
			}
		}
	}
}

