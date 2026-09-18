<?php
/**
 * AccessPress Assets
 *
 * @package AccessPress
 * @subpackage Assets
 * @since 1.0.0
 */
namespace AccessPress\Assets;

use AccessPress\Includes\Functions\Helpers\ImageHelper;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


final class Assets {
	/**
	 * Array to hold registered assets for different pages.
	 *
	 * @var array
	 */
	private array $pages = array();
	/**
	 * Registers the default assets for the plugin.
	 *
	 * @return void
	 */
	public function register(): void {
		( new LoaderHelper() )->register_component(
			$this,
			array(
				array(
					'type'          => 'filter',
					'hook'          => 'accesspress_base_assets',
					'callback'      => 'default_assets',
					'priority'      => 90,
					'accepted_args' => 2,
				),
				array(
					'type'     => 'action',
					'hook'     => 'wp_enqueue_scripts',
					'callback' => 'enqueue_frontend',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_enqueue_scripts',
					'callback' => 'enqueue_admin',
				),
			)
		)->run();
	}
	/**
	 * Registers assets for a specific page.
	 *
	 * @param string $page The page identifier.
	 * @param array  $assets The assets to register for the page.
	 * @return void
	 */
	public function register_page( string $page, array $assets ): void {
		$page                 = SanitizationHelper::key( $page, 'accesspress' );
		$this->pages[ $page ] = array(
			'styles'  => array_merge( $this->pages[ $page ]['styles'] ?? array(), $assets['styles'] ?? array() ),
			'scripts' => array_merge( $this->pages[ $page ]['scripts'] ?? array(), $assets['scripts'] ?? array() ),
		);
	}
	/**
	 * Returns the default assets for the plugin.
	 *
	 * @param array  $assets The current assets.
	 * @param string $context The context (e.g., 'frontend', 'admin').
	 * @return array The default assets.
	 */
	public function default_assets( array $assets, string $context ): array {
		$defaults = array(
			'styles'  => array(
				array(
					'handle' => 'accesspress-wp-override',
					'src'    => ACCESSPRESS_ASSETS_URL . '/dist/css/wpoverride.css',
					'deps'   => array( 'forms' ),
				),
				array(
					'handle'  => 'accesspress-bootstrap',
					'src'     => ACCESSPRESS_ASSETS_URL . '/dist/css/bootstrap.css',
					'version' => '5.3.8',
					'deps'    => array( 'accesspress-wp-override' ),
				),
				array(
					'handle'  => 'accesspress-bootstrap-select',
					'src'     => ACCESSPRESS_ASSETS_URL . '/dist/css/bootstrap-select.min.css',
					'version' => '1.2.2',
					'deps'    => array( 'accesspress-bootstrap' ),
				),
				array(
					'handle'  => 'accesspress-bs-country-data',
					'src'     => ACCESSPRESS_ASSETS_URL . '/dist/css/bs-country-data.min.css',
					'version' => '1.0.4',
					'deps'    => array( 'accesspress-bootstrap-select' ),
				),
			),
			'scripts' => array(
				array(
					'handle'    => 'accesspress-bootstrap',
					'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/bootstrap.js',
					'version'   => '5.3.8',
					'in_footer' => true,
				),
				array(
					'handle'    => 'accesspress-bootstrap-select',
					'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/bootstrap-select.min.js',
					'version'   => '1.2.2',
					'deps'      => array( 'accesspress-bootstrap' ),
					'in_footer' => true,
				),
				/*
				array(
					'handle'    => 'accesspress-bootstrap-select-control',
					'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/bootstrap-select-control.js',
					'version'   => '1.0.0',
					'in_footer' => true,
				),*/
				array(
					'handle'	=> 'accesspress-bs-country-data',
					'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/bs-country-data.min.js',
					'version'   => '1.0.4',
					'deps'      => array( 'accesspress-bootstrap-select' ),
					'in_footer' => true,
				)
			),
		);

		return array( 'base' => $defaults ) + $defaults;
	}

	/**
	 * Enqueues the frontend assets for the plugin.
	 *
	 * @return void
	 */
	public function enqueue_frontend(): void {
		if ( ! is_singular( 'accesspress_page' ) ) {
			return;
		}

		$assets = apply_filters( 'accesspress_base_assets', array(), 'frontend' );
		$this->enqueue_registered(
			'frontend',
			array(
				'styles'  => array_merge(
					$assets['base']['styles'] ?? array(),
					array(
						array(
							'handle' => 'accesspress-public',
							'src'    => ACCESSPRESS_ASSETS_URL . '/dist/css/public.css',
						),
					)
				),
				'scripts' => array_merge(
					$assets['base']['scripts'] ?? array(),
					array(
						array(
							'handle'    => 'accesspress-public',
							'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/public.js',
							'in_footer' => true,
						),
					)
				),
			)
		);
	}
	/**
	 * Enqueues the admin assets for the plugin.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin( string $hook_suffix ): void {
		if ( false === strpos( $hook_suffix, 'accesspress' ) ) {
			return;
		}

		$page       = RequestHelper::get_key( 'page', 'accesspress' );
		$registered = $this->pages[ $page ] ?? array();
		$base       = apply_filters( 'accesspress_base_assets', array(), 'admin' );
		$this->enqueue_registered(
			'admin',
			array(
				'styles'  => array_merge(
					$base['styles'] ?? array(),
					array(
						array(
							'handle'  => 'accesspress-admin-ui',
							'src'     => ACCESSPRESS_ASSETS_URL . '/dist/css/admin.ui.css',
							'version' => '1.0.0',
							'deps'    => array( 'accesspress-bootstrap' ),
						),
					),
					$registered['styles'] ?? array()
				),
				'scripts' => array_merge(
					$base['scripts'] ?? array(),
					array(
						array(
							'handle'    => 'accesspress-admin-ui',
							'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/admin.ui.js',
							'deps'      => array( 'accesspress-bootstrap' ),
							'in_footer' => true,
						),
					),
					$registered['scripts'] ?? array()
				),
			)
		);
	}
	/**
	 * Enqueues the registered assets for a given context.
	 *
	 * @param string $context The context (e.g., 'frontend', 'admin').
	 * @param array  $assets The assets to enqueue.
	 * @return void
	 */
	private function enqueue_registered( string $context, array $assets ): void {
		$assets = apply_filters( 'accesspress_' . $context . '_assets', $assets, $context );
		$this->enqueue_bundle( $assets );
	}
	/**
	 * Enqueues a bundle of assets (styles and scripts).
	 *
	 * @param array $assets The assets to enqueue.
	 * @return void
	 */
	private function enqueue_bundle( array $assets ): void {
		if ( isset( $assets['styles'] ) && is_string( $assets['styles'] ) ) {
			$assets['styles'] = array(
				array(
					'handle' => 'accesspress-admin-' . $assets['styles'],
					'src'    => ACCESSPRESS_ASSETS_URL . '/dist/css/admin.' . $assets['styles'] . '.css',
				),
			);
		}
		if ( isset( $assets['scripts'] ) && is_string( $assets['scripts'] ) ) {
			$assets['scripts'] = array(
				array(
					'handle' => 'accesspress-admin-' . $assets['scripts'],
					'src'    => ACCESSPRESS_ASSETS_URL . '/dist/js/admin.' . $assets['scripts'] . '.js',
					'deps'   => array( 'accesspress-bootstrap' ),
				),
			);
		}
		foreach ( $assets['styles'] ?? array() as $style ) {
			LoaderHelper::enqueue_style( $style['handle'], $style['src'], $style['deps'] ?? array(), $style['version'] ?? ACCESSPRESS_VERSION, $style['media'] ?? 'all' );
		}
		foreach ( $assets['scripts'] ?? array() as $script ) {
			LoaderHelper::enqueue_script( $script['handle'], $script['src'], $script['deps'] ?? array(), $script['version'] ?? ACCESSPRESS_VERSION, $script['in_footer'] ?? true );
			if ( isset( $script['localize']['object_name'], $script['localize']['data'] ) ) {
				LoaderHelper::localize_script( $script['handle'], $script['localize']['object_name'], $script['localize']['data'] );
			}
		}

		if ( wp_script_is( 'accesspress-admin-ui', 'enqueued' ) ) {
			LoaderHelper::localize_script(
				'accesspress-admin-ui',
				'accesspressOnboarding',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'accesspress_dismiss_onboarding' ),
				)
			);
		}

		$current_page = RequestHelper::get_key( 'page', '' );

		if ( 'accesspress-settings' === $current_page ) {
			$settings_config = array(
				'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'accesspress_settings_tabs' ),
				'pluginNonce'         => wp_create_nonce( 'accesspress_plugin_toggle' ),
				'pluginSettingsNonce' => wp_create_nonce( 'accesspress_plugin_settings' ),
			);
			foreach ( array( 'accesspress-admin-settings', 'accesspress-admin-plugins' ) as $handle ) {
				if ( wp_script_is( $handle, 'enqueued' ) ) {
					LoaderHelper::localize_script( $handle, 'accesspressSettingsTabs', $settings_config );
				}
			}
		}
		if ( 'accesspress-manage' === $current_page && wp_script_is( 'accesspress-admin', 'enqueued' ) ) {
			LoaderHelper::localize_script(
				'accesspress-admin',
				'accesspressWikiManager',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'accesspress_manage' ),
				)
			);
		}
	}
	/**
	 * Retrieves the URL of an image asset.
	 *
	 * @param string $file The image file name.
	 * @return string The URL of the image asset.
	 */
	public static function get_image( string $file ): string {

		return ImageHelper::get_image_url( 'core', $file );
	}
}



