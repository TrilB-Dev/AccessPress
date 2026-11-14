<?php
/**
 * CronJobs class for registering plugin cron jobs.
 *
 * @package AccessPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace AccessPress\Includes\Pages;

use AccessPress\Includes\Core\CronJobs as CronJobCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CronJobs {
	/**
	 * Register all page-layer cron jobs for the plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::accesspress_cleanup();
	}

	/**
	 * Register the built-in AccessPress cleanup cron job.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function accesspress_cleanup(): bool {
		return CronJobCore::create(
			'core',
			'accesspress_cleanup',
			'daily',
			array(),
			array( self::class, 'cleanup' ),
			__( 'Daily AccessPress cleanup task.', 'accesspress' )
		);
	}

	/**
	 * Example built-in cron callback.
	 *
	 * @return void
	 */
	public static function cleanup(): void {
		do_action( 'accesspress_daily_cleanup' );
	}
}



