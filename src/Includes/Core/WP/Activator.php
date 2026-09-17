<?php
/**
 * Define the activation functionality for the plugin.
 *
 * Handles the registration and execution of activation callbacks.
 *
 * @since 1.0.0
 *
 * @package    AccessPress
 * @subpackage AccessPress/Includes/Core/WP
 */
namespace AccessPress\Includes\Core\WP;

use AccessPress\Includes\Core\Capabilities;
use AccessPress\Includes\Plugins\Plugins;
use AccessPress\Includes\Settings\SettingsManager;
use AccessPress\Includes\Settings\Settings;
//use AccessPress\Includes\Core\PostType;
//use AccessPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Activator {
	/**
	 * Registered activation callbacks.
	 *
	 * @var array<int, callable>
	 */
	private static array $callbacks = array();

	/**
	 * Register extension activation callbacks.
	 *
	 * @param callable $callback Callback invoked during activation.
	 * @return void
	 */
	public static function register( callable $callback ): void {
		self::$callbacks[] = $callback;
	}

	/**
	 * Run AccessPress and extension activation tasks.
	 *
	 * @param array<int, callable>|null $callbacks Optional callbacks for this run.
	 * @return void
	 */
	public static function activate( ?array $callbacks = null ): void {
		Settings::register_group(
			'setup',
			array(
				'first_install_complete'    => false,
				'onboarding_steps_complete' => 0,
			)
		);

		Plugins::get_instance()->init();
		Capabilities::install();
		Database::install();
		SettingsManager::install();
		//( new PostType() )->register();
		//( new Taxonomy() )->register();

		foreach ( $callbacks ?? self::$callbacks as $callback ) {
			call_user_func( $callback );
		}

		flush_rewrite_rules();
	}
}



