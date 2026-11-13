<?php
/**
 * Defaults class for defining plugin default values.
 *
 * @package AccessPress
 * @subpackage Includes\Core
 * @since 1.0.0
 */
namespace AccessPress\Includes\Core;

use AccessPress\Includes\Functions\Helpers\DBHelper;
use AccessPress\Includes\Functions\Helpers\LoggerHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Defaults {
    /**
     * Constructor for the Defaults class.
     *
     * @since 1.0.0
     */
	public function __construct() {
		// Initialize default values here.
	}
    /**
     * Install all default values for the plugin.
     *
     * @return void
     * @since 1.0.0
     */
	public static function install_defaults(): void {

	}
    /**
     * Install the default settings for the plugin.
     *
     * @return void
     * @since 1.0.0
     */
    public static function install_default_settings(): void {
        $default_settings = self::default_settings();
        foreach ( $default_settings as $section => $settings ) {
            foreach ( $settings as $key => $value ) {
                if ( get_option( "{$section}_{$key}" ) === false ) {
                    update_option( "{$section}_{$key}", $value );
                }
            }
        }
    }
    /**
     * Get the plugin default settings.
     *
     * @return array<string, array<string, mixed>>
     * @since 1.0.0
     */
    public static function default_settings(): array {
        return [
            'general' => array(
				'accesspress_title'             => '',
				'accesspress_logo'              => '',
				'accesspress_frontend_root_page' => '',
				'disable_wordpress_default' 	=> false,
				'frontend_login'            	=> false,
				'frontend_registration'     	=> false,
				'frontend_password_reset'   	=> false,
				'frontend_account_verification' => false,
				'frontend_profile'           	=> false,
			),
			'access'  => array(
				'accesspress_user_management' => array(),
			),
			'tools'   => array(
				'debug_logging'   => false,
				'console_logging' => false,
			),
        ];
    }

}