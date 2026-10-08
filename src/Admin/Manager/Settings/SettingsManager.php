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
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Admin\FunctionsSettings;
use AccessPress\Includes\Functions\Admin\FunctionsPlugins;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsManager extends Manager {
	/**
	 * General settings page manager instance.
	 *
	 * @var SettingsGeneral
	 * @since 1.0.0
	 */
	private SettingsGeneral $general_page;
	/**
	 * Access settings page manager instance.
	 *
	 * @var SettingsAccess
	 * @since 1.0.0
	 */
	private SettingsAccess $access_page;
	/**
	 * Email settings page manager instance.
	 *
	 * @var SettingsEmail
	 * @since 1.0.0
	 */
	private SettingsEmail $email_page;
	/**
	 * Layout settings page manager instance.
	 *
	 * @var SettingsLayout
	 * @since 1.0.0
	 */
	private SettingsLayout $layout_page;
	/**
	 * Security settings page manager instance.
	 *
	 * @var SettingsSecurity
	 * @since 1.0.0
	 */
	private SettingsSecurity $security_page;
	/**
	 * Plugin settings page manager instance.
	 *
	 * @var SettingsPlugins
	 * @since 1.0.0
	 */
	private SettingsPlugins $plugins_page;
	/**
	 * FunctionsSettings instance.
	 *
	 * @var FunctionsSettings
	 * @since 1.0.0
	 */
	private FunctionsSettings $settings_functions;
	/**
	 * FunctionsPlugins instance.
	 *
	 * @var FunctionsPlugins
	 * @since 1.0.0
	 */
	private FunctionsPlugins $plugin_functions;
	/**
	 * Current settings page slug.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected string $page;
	/**
	 * Constructor for the settings manager.
	 */
	public function __construct() {
		/**
         * Set the page variable to 'settinigs'.
         *
         * @since 1.0.0
         */
        $this->page = 'settings';
        /**
         * Initialize the General Settings pages.
         *
         * @since 1.0.0
         */
        $this->general_page = new SettingsGeneral();
        /**
         * Initialize the Layout Settings pages.
         *
         * @since 1.0.0
         */
        $this->layout_page = new SettingsLayout();
        /**
         * Initialize the Email Settings page.
         *
         * @since 1.0.0
         */
        $this->email_page = new SettingsEmail();
        /**
         * Initialize the Security Settings page.
         *
         * @since 1.0.0
         */
		$this->security_page = new SettingsSecurity();
        /**
         * Initialize the Access Settings page.
         *
         * @since 1.0.0
         */
        $this->access_page = new SettingsAccess();
        /**
         * Initialize the Plugins Settings pages.
         *
         * @since 1.0.0
         */
        $this->plugins_page = new SettingsPlugins();
        /**
         * Initialize the Plugin and Settings function handlers.
         *
         * @since 1.0.0
         */
        $this->plugin_functions = new FunctionsPlugins();
        /**
         * Initialize the Settings function handler.
         *
         * @since 1.0.0
         */
        $this->settings_functions = new FunctionsSettings( $this->plugin_functions );
        /**
         * Register the admin actions for the settings manager.
         *
         * @since 1.0.0
         */
        $this->register_actions();
	}
	/**
     * Register the Settings Manager admin actions.
     *
     * @return void
     */
    private function register_actions(): void {
        add_action( 'admin_post_pluginname_save_settings', array( $this->settings_functions, 'save_settings' ) );
        add_action( 'wp_ajax_pluginname_toggle_plugin', array( $this->plugin_functions, 'toggle_plugin' ) );
        add_action( 'wp_ajax_pluginname_save_plugin_settings', array( $this->plugin_functions, 'save_plugin_settings' ) );
    }
	/**
     * Register the settings page asset bundles.
     *
     * @param Assets $assets Asset registry.
     * @return void
     */
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, array( 'pluginname'), 'settings' );
        $this->register_page_assets( $assets, array( 'pluginname'), 'plugins' );
    }

    /**
     * Renders the settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render(): void {
        $tab = SanitizationHelper::key( wp_unslash( $_GET['tab'] ?? 'general' ), 'general' );
        /*$layout_section = SanitizationHelper::key( wp_unslash( $_GET['layout_section'] ?? 'general' ), 'general' );*/
        $tab = $this->normalize_tab( $tab );
        /*$tab_context = [
            'general' => [ 
                'description' => __( 'Configure PluginName names, URL slugs, and permalink settings.', 'pluginname' ), 
                'tooltip' => __( 'These settings affect how PluginName content is identified and linked throughout the site.', 'pluginname' ) 
            ],
			'emails' => [ 
                'description' => __( 'Configure email notifications and templates for PluginName.', 'pluginname' ), 
                'tooltip' => __( 'Email settings control how PluginName communicates with users via email.', 'pluginname' ) 
            ],
			'security' => [ 
                'description' => __( 'Configure security settings for PluginName.', 'pluginname' ), 
                'tooltip' => __( 'Security settings help protect your site and PluginName content.', 'pluginname' ) 
            ],
            'layout' => [ 
                'description' => __( 'Choose which navigation and page layout features PluginName displays.', 'pluginname' ), 
                'tooltip' => __( 'Layout settings control the visitor-facing PluginName interface.', 'pluginname' ) 
            ],
            'access' => [ 
                'description' => __( 'Set the minimum WordPress capabilities required for PluginName tasks.', 'pluginname' ), 
                'tooltip' => __( 'Choose carefully so editors and administrators retain the access they need.', 'pluginname' ) 
            ],
            'plugins' => [ 
                'description' => __( 'View the PluginName plugins installed on this site.', 'pluginname' ), 
                'tooltip' => __( 'Plugin-specific configuration is available from each plugin settings page when provided.', 'pluginname' ) 
            ],
            'third-party' => [ 
                'description' => __( 'View third-party plugins installed on this site.', 'pluginname' ), 
                'tooltip' => __( 'Third-party plugin settings are managed through WordPress or the plugin author’s own settings page.', 'pluginname' ) 
            ],
        ];*/

        $this->header( __( 'Settings', 'accesspress' ) );
		?>
		<div id="accesspress-settings-panel" data-current-tab="<?php echo esc_attr( $tab ); ?>">
			<?php $this->render_tab_content( $tab ); ?>
		</div>
		<?php
		$this->footer();
    }
	/**
     * Render the settings panel returned by the AJAX tab loader.
     * @since 1.0.0
     * @param string $tab The tab to render.
     */
    public function render_tab_content( string $tab, string $layout_section = 'general' ): void {
        $tab = $this->normalize_tab( $tab );
        $view_capabilities = [
            'general' => [ 
                'pluginname_settings_general_view' 
            ],
            'layout' => [ 
                'pluginname_settings_layout_view' 
            ],
			'email' => [ 
                'pluginname_settings_email_view' 
            ],
			'security' => [ 
                'pluginname_settings_security_view' 
            ],
            'access' => [ 
                'pluginname_settings_access_view' 
            ],
            'plugins' => [ 
                'pluginname_settings_plugins_view' 
            ],
            'third-party' => [ 
                'pluginname_settings_plugins_view', 
                'pluginname_settings_plugins_ext_view' 
            ],
        ];
        $can_view = true;
        foreach ( $view_capabilities[ $tab ] ?? [] as $capability ) {
            if ( ! current_user_can( $capability ) ) {
                $can_view = false;
                break;
            }
        }
        if ( ! $can_view ) {
            wp_die( esc_html__( 'You are not authorized to view these PluginName settings.', 'pluginname' ) );
        }
        $edit_capabilities = [
            'general' => 'pluginname_settings_general_edit',
            'layout' => 'pluginname_settings_layout_edit',
            'access' => 'pluginname_settings_access_edit',
			'email' => 'pluginname_settings_email_edit',
			'security' => 'pluginname_settings_security_edit',
        ];
        $can_edit = isset( $edit_capabilities[ $tab ] ) && current_user_can( $edit_capabilities[ $tab ] );
        $groups = Settings::get_all();
        $values = $groups[ $tab ] ?? [];
        $tab_context = [
            'general' => [
                'description' => __( 'Configure PluginName names, URL slugs, and permalink settings.', 'pluginname' ),
                'tooltip' => __( 'These settings affect how PluginName content is identified and linked throughout the site.', 'pluginname' ),
            ],
            'layout' => [
                'description' => __( 'Choose which navigation and page layout features PluginName displays.', 'pluginname' ),
                'tooltip' => __( 'Layout settings control the visitor-facing PluginName interface.', 'pluginname' ),
            ],
			'email' => [
                'description' => __( 'Configure email settings for PluginName notifications.', 'pluginname' ),
                'tooltip' => __( 'These settings control how PluginName sends email notifications to users.', 'pluginname' ),
            ],
			'security' => [
                'description' => __( 'Configure security settings for PluginName.', 'pluginname' ),
                'tooltip' => __( 'These settings control the security measures applied to PluginName.', 'pluginname' ),
            ],
            'access' => [
                'description' => __( 'Set the minimum WordPress capabilities required for PluginName tasks.', 'pluginname' ),
                'tooltip' => __( 'Choose carefully so editors and administrators retain the access they need.', 'pluginname' ),
            ],
            'plugins' => [
                'description' => __( 'View the PluginName plugins installed on this site.', 'pluginname' ),
                'tooltip' => __( 'Plugin-specific configuration is available from each plugin settings page when provided.', 'pluginname' ),
            ],
            'third-party' => [
                'description' => __( 'View third-party plugins installed on this site.', 'pluginname' ),
                'tooltip' => __( 'Third-party plugin settings are managed through WordPress or the plugin author’s own settings page.', 'pluginname' ),
            ],
        ];
        ?>
        <div class="pluginname-settings-tab-content" role="tabpanel">
            <?php if ( isset( $tab_context[ $tab ] ) ) : ?>
                <p class="text-secondary mb-4">
                    <?php echo esc_html( $tab_context[ $tab ]['description'] ); ?>
                    <?php echo FormFieldHelper::label(
                        'pluginname-settings-context',
                        __( 'Settings information', 'pluginname' ),
                        [
                            'tooltip' => $tab_context[ $tab ]['tooltip'],
                            'tooltip_type' => 'info',
                            'tooltip_icon' => 'fa-circle-info',
                            'class' => 'visually-hidden',
                        ]
                    ); ?>
                </p>
            <?php endif; ?>

            <?php if ( in_array( $tab, [ 'plugins', 'third-party' ], true ) ) : ?>
                <?php $this->plugins_page->render( $tab ); ?>
            </div>
            <?php return; ?>
            <?php endif; ?>

            <div class="pluginname-settings-page">
            <div class="pluginname-settings-card card shadow-sm">
                <?php if ( $can_edit ) : ?>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pluginname-settings-form">
                        <input type="hidden" name="action" value="pluginname_save_settings" />
                        <input type="hidden" name="pluginname_tab" value="<?php echo esc_attr( $tab ); ?>" />
                        <?php echo wp_nonce_field( 'pluginname_save_settings', '_wpnonce_pluginname_save_settings', true, false ); ?>
                <?php endif; ?>

                <div class="card-body">
                    <?php if ( 'layout' === $tab ) : ?>
                        <?php $this->layout_page->render( $values, SanitizationHelper::key( $layout_section, 'general' ) ); ?>
                    <?php else : ?>
                        <table class="form-table table align-middle" role="presentation">
                            <tbody>
                                <?php if ( 'general' === $tab ) : ?>
                                    <?php $this->general_page->render( $values ); ?>
                                <?php elseif ( 'access' === $tab ) : ?>
                                    <?php $this->access_page->render( $values ); ?>
								<?php elseif ( 'email' === $tab ) : ?>
                                    <?php $this->email_page->render( $values ); ?>
								<?php elseif ( 'security' === $tab ) : ?>
                                    <?php $this->security_page->render( $values ); ?>
                                <?php elseif ( $this->plugins_page->has_settings_page( $tab ) ) : ?>
                                    <?php $this->plugins_page->render_settings_page( $tab, $values ); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if ( $can_edit ) : ?>
                        <div class="pluginname-settings-actions d-flex justify-content-end mt-3">
                            <?php echo FormFieldHelper::button( __( 'Save Changes', 'pluginname' ), [
                                'type' => 'submit',
                                'name' => 'submit',
                                'class' => 'btn-primary',
                            ] ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $can_edit ) : ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
        <?php
    }
    /**
     * Normalize the tab name to ensure it is valid.
     *
     * @since 1.0.0
     * @param string $tab The tab name to normalize.
     * @return string The normalized tab name.
     */
    private function normalize_tab( string $tab ): string {
        $allowed = [ 
			'general',
			'layout',
			'access',
			'email',
			'security',
			'plugins',
			'third-party',
		];
        if ( in_array( $tab, $allowed, true ) || $this->plugins_page->has_settings_page( $tab ) ) {
            return $tab;
        }
        return 'general';
    }
}
