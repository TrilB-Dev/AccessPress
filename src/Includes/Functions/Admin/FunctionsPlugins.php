<?php
/**
 * Plugin-related admin functions for AccessPress.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

use AccessPress\Includes\Functions\Helpers\AjaxHelper;
use AccessPress\Includes\Functions\Helpers\AlertHelper;
use AccessPress\Includes\Plugins\PluginInterface;
use AccessPress\Includes\Plugins\Plugins;
use AccessPress\Includes\Plugins\SettingsPageProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsPlugins {
	/**
	 * Toggle the enabled state of a AccessPress plugin.
	 *
	 * @return void
	 */
	public function toggle_plugin(): void {
		if ( ! AjaxHelper::authorized( 'accesspress_plugin_toggle', 'accesspress_settings_plugins_int_edit' ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to manage AccessPress plugins.', 'accesspress' ) );
		}

		$slug    = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
		$enabled = ! empty( $_POST['enabled'] );
		$plugin  = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;

		if ( ! $plugin instanceof PluginInterface ) {
			AjaxHelper::error( array( 'message' => __( 'The requested AccessPress plugin was not found.', 'accesspress' ) ), 404 );
		}
		if ( ! $this->is_internal_plugin( $plugin ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to manage external AccessPress plugins.', 'accesspress' ) );
		}

		if ( ! Plugins::get_instance()->set_plugin_enabled( $slug, $enabled ) ) {
			AjaxHelper::error( array( 'message' => __( 'The AccessPress plugin state could not be saved.', 'accesspress' ) ), 500 );
		}

		AjaxHelper::success(
			array(
				'slug'    => $slug,
				'enabled' => $enabled,
			)
		);
	}

	/**
	 * Save settings submitted from a AccessPress plugin modal.
	 *
	 * @return void
	 */
	public function save_plugin_settings(): void {
		$slug   = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
		$plugin = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;
		if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface ) {
			$message = __( 'The requested AccessPress plugin settings were not found.', 'accesspress' );
			AjaxHelper::error(
				array(
					'message' => $message,
					'alert'   => AlertHelper::get_admin_notice( $message, 'error' ),
				),
				404
			);
		}
		$capability = $this->is_internal_plugin( $plugin ) ? 'accesspress_settings_plugins_int_edit' : 'accesspress_settings_plugins_ext_edit';
		if ( ! AjaxHelper::authorized( 'accesspress_plugin_settings', $capability ) ) {
			$message = __( 'You are not authorized to save AccessPress plugin settings.', 'accesspress' );
			AjaxHelper::error(
				array(
					'message' => $message,
					'alert'   => AlertHelper::get_admin_notice( $message, 'error' ),
				),
				403
			);
		}

		$input    = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();
		$settings = $plugin->sanitize_settings( $input );

		AjaxHelper::success(
			array(
				'slug'     => $slug,
				'settings' => $settings,
				'message'  => __( 'Plugin settings saved successfully.', 'accesspress' ),
				'alert'    => AlertHelper::get_admin_notice( __( 'Plugin settings saved successfully.', 'accesspress' ), 'success' ),
			)
		);
	}
	/**
	 * Determine if a plugin is an internal AccessPress plugin.
	 *
	 * @param PluginInterface $plugin The plugin instance.
	 * @return bool True if the plugin is internal, false otherwise.
	 */
	private function is_internal_plugin( PluginInterface $plugin ): bool {
		return 0 === strpos( get_class( $plugin ), 'AccessPress\\Includes\\Plugins\\' );
	}

	/**
	 * Collect settings pages from enabled AccessPress plugins.
	 *
	 * @return array<int, array{provider: SettingsPageProviderInterface, slug: string, label: string, title: string, fields: array}>
	 */
	public function plugin_settings_pages(): array {
		$pages = array();
		foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
			if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface || ! Plugins::get_instance()->is_plugin_enabled( $plugin->get_slug() ) ) {
				continue;
			}

			$page = $plugin->get_settings_page();
			if ( empty( $page['slug'] ) || empty( $page['label'] ) || empty( $page['fields'] ) ) {
				continue;
			}

			$page['provider'] = $plugin;
			$pages[]          = $page;
		}
		return $pages;
	}
}



