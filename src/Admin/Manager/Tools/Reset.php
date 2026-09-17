<?php
/**
 * Reset class for AccessPress plugin.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */

namespace AccessPress\Admin\Manager\Tools;

use AccessPress\Assets\Assets;
use AccessPress\Includes\Functions\Helpers\AjaxHelper;
use AccessPress\Includes\Functions\Helpers\AlertHelper;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Plugins\PluginInterface;
use AccessPress\Includes\Plugins\Plugins;
use AccessPress\Includes\Plugins\SettingsPageProviderInterface;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Reset extends ToolsManager {
	/**
	 * Register hooks owned by the plugin reset tool.
	 *
	 * @since 1.0.0
	 * @param LoaderHelper|null $loader WordPress hook loader.
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		( $loader ?? new LoaderHelper() )->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_reset',
					'callback' => 'handle_reset',
				),
			)
		)->run();
	}

	/**
	 * Render the plugin reset tool content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'accesspress-tools' ), 'tools' );
	}

	public function render_page_content(): void {
		if ( '1' === RequestHelper::get_text( 'reset_complete' ) ) {
			AlertHelper::render_admin_notice( __( 'The selected AccessPress data was reset successfully.', 'accesspress' ), 'success' );
		}
		if ( '1' === RequestHelper::get_text( 'reset_failed' ) ) {
			AlertHelper::render_admin_notice( __( 'The selected AccessPress data could not be reset.', 'accesspress' ), 'error' );
		}
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Reset AccessPress data', 'accesspress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Reset AccessPress settings and registered plugin data to their factory values. This does not delete WordPress content.', 'accesspress' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php echo FormFieldHelper::input( 'action', 'accesspress_reset', array( 'type' => 'hidden' ) ); ?>
					<?php wp_nonce_field( 'accesspress_reset', 'accesspress_reset_nonce' ); ?>
					<?php echo FormFieldHelper::label( 'accesspress-reset-scope', __( 'Reset scope', 'accesspress' ) ); ?>
					<?php echo FormFieldHelper::select( 'scope', $this->scope_options(), 'core', array( 'id' => 'accesspress-reset-scope' ) ); ?>
					<fieldset class="mt-4" id="accesspress-reset-plugins" data-accesspress-reset-plugins hidden>
						<legend><?php esc_html_e( 'Plugin data', 'accesspress' ); ?></legend>
						<?php
						foreach ( $this->plugin_options() as $slug => $plugin ) {
							echo FormFieldHelper::checkbox( 'plugins[]', $slug, $plugin['name'], array( 'id' => 'accesspress-reset-' . $slug ) );
						}
						?>
					</fieldset>
					<div class="mt-4">
						<?php
						echo FormFieldHelper::checkbox(
							'confirm',
							'1',
							__( 'I understand that this action cannot be undone.', 'accesspress' ),
							array(
								'id'       => 'accesspress-reset-confirm',
								'required' => true,
							)
						);
						?>
					</div>
					<div class="mt-4">
						<?php
						echo FormFieldHelper::button(
							__( 'Reset selected data', 'accesspress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-danger',
							)
						);
						?>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Process a plugin reset request.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_reset(): void {
		if ( ! AjaxHelper::is_method( 'POST' ) || ! PermissionHelper::can( 'accesspress_tools_reset' ) || ! AjaxHelper::has_valid_nonce( 'accesspress_reset', 'accesspress_reset_nonce' ) ) {
			wp_die( esc_html__( 'The reset request could not be authorized.', 'accesspress' ), '', array( 'response' => 403 ) );
		}
		if ( ! RequestHelper::boolean( $_POST, 'confirm' ) ) {
			$this->redirect( false );
		}
		$scope   = RequestHelper::key( $_POST, 'scope', 'core' );
		$groups  = $this->groups_for_scope( $scope, RequestHelper::array( $_POST, 'plugins' ) );
		$success = 'all' === $scope ? Settings::reset_all() : ( ! empty( $groups ) && Settings::reset_groups( $groups ) );
		$this->redirect( $success );
	}
	/**
	 * Redirect to the reset page with a success or failure message.
	 *
	 * @since 1.0.0
	 * @param bool $success Whether the reset was successful.
	 * @return void
	 */
	private function redirect( bool $success ): void {
		wp_safe_redirect( admin_url( 'admin.php?page=accesspress-tools&tool=reset&' . ( $success ? 'reset_complete=1' : 'reset_failed=1' ) ) );
		exit;
	}
	/**
	 * Get the available scope options for the reset action.
	 *
	 * @since 1.0.0
	 * @return array The available scope options.
	 */
	private function scope_options(): array {
		return array(
			'all'     => __( 'All AccessPress data', 'accesspress' ),
			'core'    => __( 'AccessPress core only', 'accesspress' ),
			'plugins' => __( 'Selected plugins', 'accesspress' ),
		);
	}
	/**
	 * Get the available plugin options for the reset action.
	 *
	 * @since 1.0.0
	 * @return array The available plugin options.
	 */
	private function plugin_options(): array {
		$options = array();
		foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
			if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface ) {
				continue;
			}
			$page  = $plugin->get_settings_page();
			$group = SanitizationHelper::key( $page['settings_group'] ?? '' );
			if ( '' !== $group ) {
				$options[ sanitize_key( $plugin->get_slug() ) ] = array(
					'name'  => $plugin->get_name(),
					'group' => $group,
				);
			}
		}
		return $options;
	}
	/**
	 * Get the groups for the specified scope and plugins.
	 *
	 * @since 1.0.0
	 * @param string $scope   The scope of the reset action.
	 * @param array  $plugins The selected plugins.
	 * @return array The groups for the specified scope and plugins.
	 */
	private function groups_for_scope( string $scope, array $plugins ): array {
		if ( 'core' === $scope ) {
			return Settings::core_groups();
		}
		if ( 'plugins' !== $scope ) {
			return array();
		}
		$options = $this->plugin_options();
		$groups  = array();
		foreach ( $plugins as $slug ) {
			if ( ! is_scalar( $slug ) ) {
				continue;
			}
			$slug = sanitize_key( (string) $slug );
			if ( isset( $options[ $slug ] ) ) {
				$groups[] = $options[ $slug ]['group'];
			}
		}
		return array_values( array_unique( $groups ) );
	}
}
