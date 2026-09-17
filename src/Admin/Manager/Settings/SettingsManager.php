<?php
/**
 * SettingsManager class for AccessPress plugin.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Settings;

use AccessPress\Admin\Manager\Manager;
use AccessPress\Assets\Assets;
use AccessPress\Admin\Manager\Settings\SettingsAccess;
use AccessPress\Admin\Manager\Settings\SettingsGeneral;
use AccessPress\Admin\Manager\Settings\SettingsPlugins;
use AccessPress\Admin\Manager\Settings\SettingsSecurity;
use AccessPress\Admin\Manager\Settings\SettingsLayout;
use AccessPress\Admin\Manager\Settings\SettingsEmail;
use AccessPress\Includes\Functions\Helpers\RequestHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsManager extends Manager {
	/**
	 * Plugin settings page manager instance.
	 *
	 * @var SettingsPlugins
	 */
	private SettingsPlugins $plugins_page;
	/**
	 * Current settings page slug.
	 *
	 * @var string
	 */
	protected $page;
	/**
	 * Constructor for the settings manager.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->page         = 'settings';
		$this->plugins_page = new SettingsPlugins();
	}
	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render(): void {
		$tab = sanitize_key( RequestHelper::get_key( 'tab', 'general' ) );
		$tab = $this->normalize_tab( $tab );

		$this->header( __( 'Settings', 'accesspress' ) );
		?>
		<div id="accesspress-settings-panel" data-current-tab="<?php echo esc_attr( $tab ); ?>">
			<?php $this->render_tab_content( $tab ); ?>
		</div>
		<?php
		$this->footer();
	}

	/**
	 * Render the content for a specific settings tab.
	 *
	 * @param string $tab The current tab slug.
	 * @since 1.0.0
	 */
	public function render_tab_content( string $tab ): void {
		$tab               = $this->normalize_tab( $tab );
		$view_capabilities = array(
			'general'     => array( 'accesspress_settings_general_view' ),
			'access'      => array( 'accesspress_settings_access_view' ),
			'security'    => array( 'accesspress_settings_security_view' ),
			'layout'      => array( 'accesspress_settings_layout_view' ),
			'plugins'     => array( 'accesspress_settings_plugins_view' ),
			'third-party' => array( 'accesspress_settings_plugins_view', 'accesspress_settings_plugins_ext_view' ),
		);

		if ( $this->plugins_page->has_settings_page( $tab ) && ! $this->plugins_page->can_view_settings_page( $tab ) ) {
			wp_die( esc_html__( 'You are not authorized to view these AccessPress settings.', 'accesspress' ) );
		}

		$can_view = true;
		foreach ( $view_capabilities[ $tab ] ?? array() as $capability ) {
			if ( ! current_user_can( $capability ) ) {
				$can_view = false;
				break;
			}
		}

		if ( ! $can_view ) {
			wp_die( esc_html__( 'You are not authorized to view these AccessPress settings.', 'accesspress' ) );
		}
		?>
		<div class="accesspress-settings-tab-content" role="tabpanel">
			<?php if ( 'general' === $tab ) : ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="mb-3">
							<h5 class="h5 mb-1"><?php esc_html_e( 'Frontend user management', 'accesspress' ); ?></h5>
							<p class="text-secondary mb-0"><?php esc_html_e( 'Configure the user portal pages, roles, and membership behaviour for the AccessPress frontend experience.', 'accesspress' ); ?></p>
						</div>
						<table class="form-table" role="presentation"><tbody>
							<?php ( new SettingsGeneral() )->render( array() ); ?>
						</tbody></table>
					</div>
				</div>
			<?php elseif ( 'access' === $tab ) : ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="mb-3">
							<h5 class="h5 mb-1"><?php esc_html_e( 'Access control', 'accesspress' ); ?></h5>
							<p class="text-secondary mb-0"><?php esc_html_e( 'Define who can issue, revoke, export, review, and manage licences.', 'accesspress' ); ?></p>
						</div>
						<table class="form-table" role="presentation"><tbody>
							<?php ( new SettingsAccess() )->render( array() ); ?>
						</tbody></table>
					</div>
				</div>
			<?php elseif ( 'security' === $tab ) : ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="mb-3">
							<h5 class="h5 mb-1"><?php esc_html_e( 'Security settings', 'accesspress' ); ?></h5>
							<p class="text-secondary mb-0"><?php esc_html_e( 'Control frontend protection, toolbar visibility, and admin restrictions by role.', 'accesspress' ); ?></p>
						</div>
						<table class="form-table" role="presentation"><tbody>
							<?php ( new SettingsSecurity() )->render( array() ); ?>
						</tbody></table>
					</div>
				</div>
			<?php elseif ( 'layout' === $tab ) : ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="mb-3">
							<h5 class="h5 mb-1"><?php esc_html_e( 'Layout settings', 'accesspress' ); ?></h5>
							<p class="text-secondary mb-0"><?php esc_html_e( 'Shape the frontend user portal branding, layout, and interaction behaviour.', 'accesspress' ); ?></p>
						</div>
						<table class="form-table" role="presentation"><tbody>
							<?php ( new SettingsLayout() )->render( array() ); ?>
						</tbody></table>
					</div>
				</div>
			<?php else : ?>
				<?php $this->plugins_page->render( $tab ); ?>
			<?php endif; ?>
		</div>
		<?php
	}
	/**
	 * Normalize the tab slug to ensure it is valid.
	 *
	 * @param string $tab The tab slug to normalize.
	 * @return string The normalized tab slug.
	 * @since 1.0.0
	 */
	private function normalize_tab( string $tab ): string {
		$allowed = array( 'general', 'access', 'security', 'layout', 'plugins', 'third-party' );
		if ( in_array( $tab, $allowed, true ) || $this->plugins_page->has_settings_page( $tab ) ) {
			return $tab;
		}
		return 'general';
	}
	/**
	 * Register the assets for the settings page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @since 1.0.0
	 */
	public function register_assets( Assets $assets ): void {
		$settings_assets              = $this->assets( 'settings' );
		$settings_assets['scripts'][] = array(
			'handle'    => 'accesspress-admin-plugins',
			'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/plugins.admin.js',
			'deps'      => array( 'accesspress-bootstrap' ),
			'in_footer' => true,
		);
		$assets->register_page( 'accesspress-settings', $settings_assets );
	}
}



