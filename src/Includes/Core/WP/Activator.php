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

use AccessPress\Includes\Core\Defaults;
use AccessPress\Includes\Core\Roles;
use AccessPress\Includes\Plugins\Plugins;


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
		Plugins::get_instance()->init();
		Database::install();
		Database::update();
		Roles::install();
		Defaults::install_defaults();
		Defaults::install_default_settings();


		foreach ( $callbacks ?? self::$callbacks as $callback ) {
			call_user_func( $callback );
		}

		flush_rewrite_rules();
	}
}
