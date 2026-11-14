<?php
/**
 * Admin class for AccessPress plugin.
 *
 * @package AccessPress
 * @subpackage Admin
 * @since 1.0.0
 */
namespace AccessPress\Admin;

use AccessPress\Includes\Settings\Settings;
use AccessPress\Includes\Functions\Admin\FunctionsPlugins;
use AccessPress\Includes\Functions\Admin\FunctionsSidebar;
use AccessPress\Includes\Functions\Helpers\AjaxHelper;
use AccessPress\Includes\Core\Capabilities;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\LoggerHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Assets\Assets;
use AccessPress\Admin\Manager\Manager;
use AccessPress\Admin\Manager\Tools\ToolsManager;
use AccessPress\Admin\Manager\Dashboard\DashboardManager;
use AccessPress\Admin\Manager\Reports\ReportsManager;
use AccessPress\Admin\Manager\Users\UserManager;
use AccessPress\Admin\Manager\Settings\SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Admin {
	/**
	 * The DashboardManager instance for managing the dashboard page.
	 *
	 * @var DashboardManager
	 * */
	private DashboardManager $dashboard_manager;
	/**
	 * SettingsManager instance for managing settings-related admin pages.
	 *
	 * @var SettingsManager
	 */
	private SettingsManager $settings_manager;
	/**
	 * ReportsManager instance for managing customer reports and account data.
	 *
	 * @var ReportsManager
	 */
	private ReportsManager $reports_manager;
	/**
	 * LicencesManager instance for managing licence-type and issued licence pages.
	 *
	 * @var UserManager
	 */
	private UserManager $user_manager;
	/**
	 * ToolsManager instance for managing tools-related admin pages.
	 *
	 * @var ToolsManager
	 */
	private ToolsManager $tools_manager;
	/**
	 * Registry of the admin managers.
	 *
	 * @var array<string, Manager>
	 */
	private array $managers;
	/**
	 * LoaderHelper instance for managing action and filter hooks.
	 *
	 * @var LoaderHelper
	 */
	private LoaderHelper $loader;
	/**
	 * FunctionsPlugins instance for managing plugin-related admin functions.
	 *
	 * @var FunctionsPlugins
	 */
	private FunctionsPlugins $plugin_functions;
	/**
	 * Assets instance for managing admin assets.
	 *
	 * @var Assets
	 */
	private Assets $assets;
	/**
	 * Constructor for the Admin class.
	 *
	 * Initializes the various admin managers and registers their assets.
	 *
	 * @param Assets $assets The Assets instance for managing admin assets.
	 */
	public function __construct( Assets $assets ) {
		$this->managers = array();
		$this->plugin_functions = new FunctionsPlugins();
		$this->loader = new LoaderHelper();
		$this->assets = $assets;

		$this->loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'wp_ajax_accesspress_dismiss_onboarding',
					'callback' => 'dismiss_onboarding',
				),
			)
		);
		$this->loader->register_component(
			$this->plugin_functions,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'wp_ajax_accesspress_toggle_plugin',
					'callback' => 'toggle_plugin',
				),
				array(
					'type'     => 'action',
					'hook'     => 'wp_ajax_accesspress_save_plugin_settings',
					'callback' => 'save_plugin_settings',
				),
			)
		)->run();
	}

	/**
	 * Instantiate and cache a manager only when it is first used.
	 *
	 * @param string $key Manager key.
	 * @return Manager
	 */
	private function get_manager( string $key ): Manager {
		if ( ! isset( $this->managers[ $key ] ) ) {
			switch ( $key ) {
				case 'dashboard':
					$manager = new DashboardManager();
					break;
				case 'settings':
					$manager = new SettingsManager();
					break;
				case 'reports':
					$manager = new ReportsManager();
					break;
				case 'user':
					$manager = new UserManager();
					break;
				case 'tools':
					$manager = new ToolsManager();
					break;
				default:
					throw new \InvalidArgumentException( 'Unknown admin manager: ' . $key );
			}

			$manager->register_assets( $this->assets );
			$this->managers[ $key ] = $manager;

			switch ( $key ) {
				case 'dashboard':
					$this->dashboard_manager = $manager;
					break;
				case 'settings':
					$this->settings_manager = $manager;
					break;
				case 'reports':
					$this->reports_manager = $manager;
					break;
				case 'user':
					$this->user_manager = $manager;
					break;
				case 'tools':
					$this->tools_manager = $manager;
					break;
			}
		}

		return $this->managers[ $key ];
	}
	/**
	 * Register admin menu pages and subpages.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu(): void {
		LoggerHelper::write_log( 'AccessPress admin menu registration started.' );

		try {
			FunctionsSidebar::register_admin_menu( $this );
			LoggerHelper::write_log( 'AccessPress admin menu registration complete.' );
		} catch ( \Throwable $e ) {
			LoggerHelper::write_log( 'AccessPress admin menu registration failed: ' . $e->getMessage() );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				wp_die( esc_html( $e->getMessage() ), __( 'AccessPress admin menu error', 'accesspress' ), array( 'back_link' => true ) );
			}
		}
	}
	/**
	 * Render the dashboard page.
	 *
	 * This method is responsible for rendering the dashboard page of the AccessPress plugin.
	 * It delegates the rendering to the DashboardManager instance.
	 */
	public function render_dashboard(): void {
		$group = RequestHelper::get_key( 'group', '' );
		$tab   = RequestHelper::get_key( 'tab', '' );
		$this->route_manager( $group, $tab );
	}

	/**
	 * Dispatch the admin request to the correct manager.
	 *
	 * @param string $group Requested group slug.
	 * @param string $tab Requested tab slug.
	 * @return void
	 */
	public function route_manager( string $group, string $tab = '' ): void {
		$group = sanitize_key( $group );
		$normalized_group = array(
			'accesspress'     => 'dashboard',
			'dashboard'       => 'dashboard',
			'user-management' => 'user-management',
			'settings'        => 'settings',
			'tools'           => 'tools',
			'reports'         => 'reports',
		)[ $group ] ?? 'dashboard';

		LoggerHelper::write_log( sprintf( 'AccessPress dashboard render triggered. Group=%s Tab=%s', $group, $tab ) );

		try {
			switch ( $normalized_group ) {
				case 'user-management':
					LoggerHelper::write_log( 'AccessPress dashboard routed to user management page.' );
					$this->render_users();
					return;
				case 'reports':
					LoggerHelper::write_log( 'AccessPress dashboard routed to reports page.' );
					$this->render_reports();
					return;
				case 'settings':
					LoggerHelper::write_log( 'AccessPress dashboard routed to settings page.' );
					$this->render_settings();
					return;
				case 'tools':
					LoggerHelper::write_log( 'AccessPress dashboard routed to tools page.' );
					$this->render_tools();
					return;
				default:
					LoggerHelper::write_log( 'AccessPress dashboard default render path selected.' );
					$this->get_manager( 'dashboard' )->render();
			}
		} catch ( \Throwable $e ) {
			LoggerHelper::write_log( 'AccessPress dashboard render failed: ' . $e->getMessage() );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				wp_die( esc_html( $e->getMessage() ), __( 'AccessPress dashboard error', 'accesspress' ), array( 'back_link' => true ) );
			}
		}
	}
	/**
	 * Dismiss the onboarding modal.
	 *
	 * This method handles the AJAX request to dismiss the onboarding modal for the AccessPress plugin.
	 */
	public function dismiss_onboarding(): void {
		if ( ! AjaxHelper::authorized( 'accesspress_dismiss_onboarding', 'manage_options' ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to dismiss the AccessPress onboarding modal.', 'accesspress' ) );
		}

		Settings::register_group( 'setup', array( 'first_install_complete' => false ) );
		Settings::set( 'first_install_complete', true );
		Settings::set( 'onboarding_steps_complete', 3 );

		AjaxHelper::success( array( 'dismissed' => true ) );
	}
	/**
	 * Render AccessPress users page.
	 *
	 * This method is responsible for rendering the users page of the AccessPress plugin.
	 * It delegates the rendering to the CustomerManager instance.
	 */
	public function render_users(): void {
		LoggerHelper::write_log( 'AccessPress users render started.' );
		$this->get_manager( 'user' )->render();
		LoggerHelper::write_log( 'AccessPress users render complete.' );
	}
	/**
	 * Render AccessPress reports page.
	 *
	 * This method is responsible for rendering the reports page of the AccessPress plugin.
	 * It delegates the rendering to the ReportsManager instance.
	 */
	public function render_reports(): void {
		LoggerHelper::write_log( 'AccessPress reports render started.' );
		$this->get_manager( 'reports' )->render();
		LoggerHelper::write_log( 'AccessPress reports render complete.' );
	}
	/**
	 * Render the settings page.
	 *
	 * This method is responsible for rendering the settings page of the AccessPress plugin.
	 * It delegates the rendering to the SettingsManager instance.
	 */
	public function render_settings(): void {
		LoggerHelper::write_log( 'AccessPress settings page render started.' );
		$this->get_manager( 'settings' )->render();
		LoggerHelper::write_log( 'AccessPress settings page render complete.' );
	}
	/**
	 * Render the tools page.
	 *
	 * @return void
	 */
	public function render_tools(): void {
		LoggerHelper::write_log( 'AccessPress tools page render started.' );
		$this->get_manager( 'tools' )->render();
		LoggerHelper::write_log( 'AccessPress tools page render complete.' );
	}
	/**
	 * Render the analytics page.
	 *
	 * This method is responsible for rendering the analytics page of the AccessPress plugin.
	 * It delegates the rendering to the AnalyticsManager instance.
	 */
	/**
	 * Get the capability for a given key, with a fallback.
	 *
	 * @param string $key The settings key to retrieve the capability for.
	 * @param string $fallback The fallback capability if the key is not set or invalid.
	 * @return string The capability associated with the key, or the fallback if not valid.
	 */
	public function capability( string $key, string $fallback ): string {
		$value   = Settings::get( $key, $fallback );
		$values  = is_array( $value ) ? $value : array( $value );
		$allowed = array_merge( array( 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ), array_keys( Capabilities::definitions() ) );
		foreach ( $values as $value ) {
			$capability = SanitizationHelper::key( $value, $fallback );
			if ( in_array( $capability, $allowed, true ) ) {
				return $capability;
			}
		}
		return $fallback;
	}
}
