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
use AccessPress\Admin\Manager\Settings\SettingsEmail;
use AccessPress\Admin\Manager\Settings\SettingsGeneral;
use AccessPress\Admin\Manager\Settings\SettingsPlugins;
use AccessPress\Admin\Manager\Settings\SettingsLayout;
use AccessPress\Admin\Manager\Settings\SettingsSecurity;
use AccessPress\Includes\Functions\Helpers\AjaxHelper;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsManager extends Manager {
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
	protected string $page;
	/**
	 * Constructor for the settings manager.
	 */
	public function __construct() {
		$this->page         = 'settings';
		$this->plugins_page = new SettingsPlugins();
	}
	/**
	 * Renders the settings page.
	 *
	 * @return void
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
	 * Renders the content for a specific settings tab.
	 *
	 * @param string $tab The tab to render.
	 * @return void
	 */
	public function render_tab_content( string $tab ): void {
		$tab               = $this->normalize_tab( $tab );
		$view_capabilities = array(
			'general'     => array( 'accesspress_settings_general_view' ),
			'email'       => array( 'accesspress_settings_email_view' ),
			'access'      => array( 'accesspress_settings_access_view' ),
			'layout'      => array( 'accesspress_settings_layout_view' ),
			'security'    => array( 'accesspress_settings_security_view' ),
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

		$values = Settings::get_group( $tab, array() ) ?? array();
		$pages  = array(
			'general'     => array(
				'title'    => __( 'AccessPress configuration', 'accesspress' ),
				'copy'     => __( 'Set the default commercial rules for generated licences, expiry, and validation.', 'accesspress' ),
				'instance' => new SettingsGeneral(),
			),
			'email'       => array(
				'title'    => __( 'Email settings', 'accesspress' ),
				'copy'     => __( 'Configure the default email settings for generated customer notifications.', 'accesspress' ),
				'instance' => new SettingsEmail(),
			),
			'access'      => array(
				'title'    => __( 'Access control', 'accesspress' ),
				'copy'     => __( 'Define who can issue, revoke, export, review, and manage licences.', 'accesspress' ),
				'instance' => new SettingsAccess(),
			),
			'layout'      => array(
				'title'    => __( 'Layout settings', 'accesspress' ),
				'copy'     => __( 'Control the default frontend account and member experience.', 'accesspress' ),
				'instance' => new SettingsLayout(),
			),
			'security'    => array(
				'title'    => __( 'Security', 'accesspress' ),
				'copy'     => __( 'Define the security settings for managing licences.', 'accesspress' ),
				'instance' => new SettingsSecurity(),
			),
		);
		?>
		<div class="accesspress-settings-tab-content" role="tabpanel">
			<?php if ( isset( $pages[ $tab ] ) ) : ?>
				<?php $this->render_tab_panel( $tab, $pages[ $tab ]['title'], $pages[ $tab ]['copy'], $pages[ $tab ]['instance'], $values ); ?>
			<?php else : ?>
				<?php $this->plugins_page->render( $tab ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Load a settings tab payload for an AJAX request.
	 *
	 * @return void
	 */
	public function load_tab(): void {
		$tab             = sanitize_key( RequestHelper::get_key( 'tab', 'general' ) );
		$view_capability = array(
			'general'     => 'accesspress_settings_general_view',
			'access'      => 'accesspress_settings_access_view',
			'layout'      => 'accesspress_settings_layout_view',
			'email'       => 'accesspress_settings_email_view',
			'security'    => 'accesspress_settings_security_view',
			'plugins'     => 'accesspress_settings_plugins_view',
			'third-party' => 'accesspress_settings_plugins_ext_view',
		)[ $tab ] ?? 'accesspress_settings_general_view';

		if ( ! AjaxHelper::authorized( 'accesspress_settings_tabs', $view_capability ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to load AccessPress settings.', 'accesspress' ) );
		}

		ob_start();
		$this->render_tab_content( $tab );
		$html = (string) ob_get_clean();
		AjaxHelper::success(
			array(
				'html' => $html,
				'tab'  => $tab,
			)
		);
	}

	/**
	 * Render a settings tab panel using the manager/page component pattern.
	 *
	 * @param string $title The panel title.
	 * @param string $copy The helper copy.
	 * @param object $instance The settings page instance.
	 * @param array  $values The values for the tab.
	 * @return void
	 */
	private function render_tab_panel( string $tab, string $title, string $copy, object $instance, array $values ): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<div class="mb-3">
					<h5 class="h5 mb-1"><?php echo esc_html( $title ); ?></h5>
					<p class="text-secondary mb-0"><?php echo esc_html( $copy ); ?></p>
				</div>
				<form method="post" action="">
					<?php settings_fields( 'accesspress_settings' ); ?>
					<?php wp_nonce_field( 'accesspress_settings_' . $tab, '_wpnonce_accesspress_settings_' . $tab ); ?>
					<?php if ( method_exists( $instance, 'render_page_content' ) ) : ?>
						<?php $instance->render_page_content( $values ); ?>
					<?php elseif ( method_exists( $instance, 'render' ) ) : ?>
						<?php $instance->render( $values ); ?>
					<?php endif; ?>
					<div class="mt-3 d-flex justify-content-end">
						<?php echo FormFieldHelper::button(
							__( 'Save Settings', 'accesspress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-primary',
								'name'  => 'accesspress_save_settings',
							)
						); ?>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
	/**
	 * Normalizes the tab value to ensure it is valid.
	 *
	 * @param string $tab The tab to normalize.
	 * @return string The normalized tab.
	 */
	private function normalize_tab( string $tab ): string {
		$allowed = array( 'general', 'layout', 'email', 'access', 'security', 'plugins', 'third-party' );
		if ( in_array( $tab, $allowed, true ) || $this->plugins_page->has_settings_page( $tab ) ) {
			return $tab;
		}
		return 'general';
	}
	/**
	 * Registers the assets for the settings page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$settings_assets              = $this->assets( 'settings' );
		$settings_assets['scripts'][] = array(
			'handle'    => 'accesspress-admin-plugins',
			'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/admin.plugins.js',
			'deps'      => array( 'accesspress-bootstrap' ),
			'in_footer' => true,
		);
		$assets->register_page( 'accesspress-settings', $settings_assets );
	}
}
