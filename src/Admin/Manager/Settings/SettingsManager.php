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
use AccessPress\Includes\Functions\Admin\FunctionsSettings;
use AccessPress\Includes\Functions\Admin\FunctionsPlugins;
use AccessPress\Includes\Settings\Settings;
use AccessPress\Admin\Manager\Settings\SettingsAccess;
use AccessPress\Admin\Manager\Settings\SettingsEmail;
use AccessPress\Admin\Manager\Settings\SettingsGeneral;
use AccessPress\Admin\Manager\Settings\SettingsPlugins;
use AccessPress\Admin\Manager\Settings\SettingsLayout;
use AccessPress\Admin\Manager\Settings\SettingsSecurity;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\AlertHelper;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsManager extends Manager {
	/**
	 * General settings page manager instance.
	 *
	 * @var SettingsGeneral|null
	 * @since 1.0.0
	 */
	private ?SettingsGeneral $general_page = null;
	/**
	 * Access settings page manager instance.
	 *
	 * @var SettingsAccess|null
	 * @since 1.0.0
	 */
	private ?SettingsAccess $access_page = null;
	/**
	 * Email settings page manager instance.
	 *
	 * @var SettingsEmail|null
	 * @since 1.0.0
	 */
	private ?SettingsEmail $email_page = null;
	/**
	 * Layout settings page manager instance.
	 *
	 * @var SettingsLayout|null
	 * @since 1.0.0
	 */
	private ?SettingsLayout $layout_page = null;
	/**
	 * Security settings page manager instance.
	 *
	 * @var SettingsSecurity|null
	 * @since 1.0.0
	 */
	private ?SettingsSecurity $security_page = null;
	/**
	 * Plugin settings page manager instance.
	 *
	 * @var SettingsPlugins|null
	 * @since 1.0.0
	 */
	private ?SettingsPlugins $plugins_page = null;
	/**
	 * FunctionsSettings instance.
	 *
	 * @var FunctionsSettings|null
	 * @since 1.0.0
	 */
	private ?FunctionsSettings $settings_functions = null;
	/**
	 * FunctionsPlugins instance.
	 *
	 * @var FunctionsPlugins|null
	 * @since 1.0.0
	 */
	private ?FunctionsPlugins $plugin_functions = null;
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
         * Set the current settings page slug.
         * 
         * @since 1.0.0
         */
		$this->page        = 'settings';
        /**
         * Initialize the internal settings page managers.
         *
         * @since 1.0.0
         */
		$this->general_page = new SettingsGeneral();
        /**
         * Initialize the layout, email, security, access, and plugins pages.
         *
         * @since 1.0.0
         */
		$this->layout_page  = new SettingsLayout();
        /**
         * Initialize the email, security, access, and plugins pages.
         *
         * @since 1.0.0
         */
		$this->email_page   = new SettingsEmail();
        /**
         * Initialize the security, access, and plugins pages.
         *
         * @since 1.0.0
         */
		$this->security_page = new SettingsSecurity();
        /**
         * Initialize the access and plugins pages.
         *
         * @since 1.0.0
         */
		$this->access_page  = new SettingsAccess();
        /**
         * Initialize the plugins page.
         *
         * @since 1.0.0
         */
		$this->plugins_page = new SettingsPlugins();
        /**
         * Initialize the plugin and settings functions.
         *
         * @since 1.0.0
         */
		$this->plugin_functions   = new FunctionsPlugins();
        /**
         * Initialize the settings functions.
         *
         * @since 1.0.0
         */
		$this->settings_functions = new FunctionsSettings( $this->plugin_functions );
        /**
         * Register the admin actions.
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
		add_action( 'admin_post_accesspress_save_general_settings', array( $this, 'save_general_settings' ) );
		add_action( 'admin_post_accesspress_save_access_settings', array( $this, 'save_access_settings' ) );
		add_action( 'admin_post_accesspress_save_layout_settings', array( $this, 'save_layout_settings' ) );
		add_action( 'admin_post_accesspress_save_email_settings', array( $this, 'save_email_settings' ) );
		add_action( 'admin_post_accesspress_save_security_settings', array( $this, 'save_security_settings' ) );
	}

	/**
	 * Save the general settings using a lazily-created helper instance.
	 *
	 * @return void
	 */
	public function save_general_settings(): void {
		$this->settings_functions->general_save();
	}

	/**
	 * Save the access settings using a lazily-created helper instance.
	 *
	 * @return void
	 */
	public function save_access_settings(): void {
		$this->settings_functions->access_save();
	}

	/**
	 * Save the layout settings using a lazily-created helper instance.
	 *
	 * @return void
	 */
	public function save_layout_settings(): void {
		$this->settings_functions->layout_save();
	}

	/**
	 * Save the email settings using a lazily-created helper instance.
	 *
	 * @return void
	 */
	public function save_email_settings(): void {
		$this->settings_functions->email_save();
	}

	/**
	 * Save the security settings using a lazily-created helper instance.
	 *
	 * @return void
	 */
	public function save_security_settings(): void {
		$this->settings_functions->security_save();
	}
	/**
     * Register the settings page asset bundles.
     *
     * @param Assets $assets Asset registry.
     * @return void
     */
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, array( 'accesspress'), 'settings' );
        $this->register_page_assets( $assets, array( 'accesspress'), 'plugins' );
    }

    /**
     * Renders the settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render(): void {
        $tab = SanitizationHelper::key( wp_unslash( $_GET['tab'] ?? 'general' ), 'general' );
        $layout_section = SanitizationHelper::key( wp_unslash( $_GET['s'] ?? $_GET['layout_section'] ?? 'general' ), 'general' );
        $tab = $this->normalize_tab( $tab );
        $tab_context = [
            'general' => [ 
                'description' => __( 'Configure PluginName names, URL slugs, and permalink settings.', 'accesspress' ), 
                'tooltip' => __( 'These settings affect how PluginName content is identified and linked throughout the site.', 'accesspress' ) 
            ],
			'emails' => [ 
                'description' => __( 'Configure email notifications and templates for PluginName.', 'accesspress' ), 
                'tooltip' => __( 'Email settings control how PluginName communicates with users via email.', 'accesspress' ) 
            ],
			'security' => [ 
                'description' => __( 'Configure security settings for PluginName.', 'accesspress' ), 
                'tooltip' => __( 'Security settings help protect your site and PluginName content.', 'accesspress' ) 
            ],
            'layout' => [ 
                'description' => __( 'Choose which navigation and page layout features PluginName displays.', 'accesspress' ), 
                'tooltip' => __( 'Layout settings control the visitor-facing PluginName interface.', 'accesspress' ) 
            ],
            'access' => [ 
                'description' => __( 'Set the minimum WordPress capabilities required for PluginName tasks.', 'accesspress' ), 
                'tooltip' => __( 'Choose carefully so editors and administrators retain the access they need.', 'accesspress' ) 
            ],
            'plugins' => [ 
                'description' => __( 'View the PluginName plugins installed on this site.', 'accesspress' ), 
                'tooltip' => __( 'Plugin-specific configuration is available from each plugin settings page when provided.', 'accesspress' ) 
            ],
            'third-party' => [ 
                'description' => __( 'View third-party plugins installed on this site.', 'accesspress' ), 
                'tooltip' => __( 'Third-party plugin settings are managed through WordPress or the plugin author’s own settings page.', 'accesspress' ) 
            ],
        ];

        $this->header( __( 'Settings', 'accesspress' ) );

        if ( ! empty( $_GET['settings_saved'] ) ) {
            $message = isset( $_GET['settings_message'] ) ? rawurldecode( wp_unslash( $_GET['settings_message'] ) ) : __( 'ModPress settings saved successfully.', 'accesspress' );
            echo AlertHelper::get_admin_notice( $message, 'success' );
        } elseif ( ! empty( $_GET['settings_failed'] ) ) {
            $message = isset( $_GET['settings_message'] ) ? rawurldecode( wp_unslash( $_GET['settings_message'] ) ) : __( 'Failed to save ModPress settings. Please try again.', 'accesspress' );
            echo AlertHelper::get_admin_notice( $message, 'error' );
        }

        if ( function_exists( 'settings_errors' ) ) {
            settings_errors( 'accesspress_settings' );
        }
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
                'accesspress_settings_general_view' 
            ],
            'layout' => [ 
                'accesspress_settings_layout_view' 
            ],
			'email' => [ 
                'accesspress_settings_email_view' 
            ],
			'security' => [ 
                'accesspress_settings_security_view' 
            ],
            'access' => [ 
                'accesspress_settings_access_view' 
            ],
            'plugins' => [ 
                'accesspress_settings_plugins_view' 
            ],
            'third-party' => [ 
                'accesspress_settings_plugins_view', 
                'accesspress_settings_plugins_ext_view' 
            ],
        ];
        $can_view = true;
        foreach ( $view_capabilities[ $tab ] ?? [] as $capability ) {
            if ( current_user_can( 'manage_options' ) || current_user_can( $capability ) ) {
                continue;
            }

            $can_view = false;
            break;
        }
        if ( ! $can_view ) {
            wp_die( esc_html__( 'You are not authorized to view these AccessPress settings.', 'accesspress' ) );
        }
        $edit_capabilities = [
            'general' => 'accesspress_settings_general_edit',
            'layout' => 'accesspress_settings_layout_edit',
            'access' => 'accesspress_settings_access_edit',
			'email' => 'accesspress_settings_email_edit',
			'security' => 'accesspress_settings_security_edit',
        ];
        $can_edit = isset( $edit_capabilities[ $tab ] ) && ( current_user_can( 'manage_options' ) || current_user_can( $edit_capabilities[ $tab ] ) );

        $tab_context = [
            'general' => [
                'description' => __( 'Configure AccessPress names, URL slugs, and permalink settings.', 'accesspress' ),
                'tooltip' => __( 'These settings affect how AccessPress content is identified and linked throughout the site.', 'accesspress' ),
            ],
            'layout' => [
                'description' => __( 'Choose which navigation and page layout features AccessPress displays.', 'accesspress' ),
                'tooltip' => __( 'Layout settings control the visitor-facing AccessPress interface.', 'accesspress' ),
            ],
			'email' => [
                'description' => __( 'Configure email settings for AccessPress notifications.', 'accesspress' ),
                'tooltip' => __( 'These settings control how AccessPress sends email notifications to users.', 'accesspress' ),
            ],
			'security' => [
                'description' => __( 'Configure security settings for AccessPress.', 'accesspress' ),
                'tooltip' => __( 'These settings control the security measures applied to AccessPress.', 'accesspress' ),
            ],
            'access' => [
                'description' => __( 'Set the minimum WordPress capabilities required for AccessPress tasks.', 'accesspress' ),
                'tooltip' => __( 'Choose carefully so editors and administrators retain the access they need.', 'accesspress' ),
            ],
            'plugins' => [
                'description' => __( 'View the AccessPress plugins installed on this site.', 'accesspress' ),
                'tooltip' => __( 'Plugin-specific configuration is available from each plugin settings page when provided.', 'accesspress' ),
            ],
            'third-party' => [
                'description' => __( 'View third-party plugins installed on this site.', 'accesspress' ),
                'tooltip' => __( 'Third-party plugin settings are managed through WordPress or the plugin author’s own settings page.', 'accesspress' ),
            ],
        ];
        ?>
            <div class="accesspress-settings-tab-content" role="tabpanel">
                <?php if ( isset( $tab_context[ $tab ] ) ) : ?>
                    <p class="text-secondary mb-4">
                        <?php echo esc_html( $tab_context[ $tab ]['description'] ); ?>
                        <?php echo FormFieldHelper::label(
                            'accesspress-settings-context',
                            __( 'Settings information', 'accesspress' ),
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

                <div class="accesspress-settings-page">
                <div class="accesspress-settings-card card shadow-sm">
                    <div class="card-body">
                        <?php if ( 'layout' === $tab ) : ?>
                            <?php $this->layout_page->render( Settings::get_group( 'layout' ) ); ?>
                        <?php else : ?>
                            <table class="form-table table align-middle" role="presentation">
                                <tbody>
                                    <?php if ( 'general' === $tab ) : ?>
                                        <?php $this->general_page->render( Settings::get_group( 'general' ) ); ?>
                                    <?php elseif ( 'access' === $tab ) : ?>
                                        <?php $this->access_page->render( Settings::get_group( 'access' ) ); ?>
                                    <?php elseif ( 'email' === $tab ) : ?>
                                        <?php $this->email_page->render( Settings::get_group( 'email' ) ); ?>
                                    <?php elseif ( 'security' === $tab ) : ?>
                                        <?php $this->security_page->render( Settings::get_group( 'security' ) ); ?>
                                    <?php elseif ( $this->plugins_page->has_settings_page( $tab ) ) : ?>
                                        <?php $this->plugins_page->render_settings_page( $tab, Settings::get_all() ); ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
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

        if ( in_array( $tab, $allowed, true ) ) {
            return $tab;
        }

        if ( null === $this->plugins_page ) {
            return 'general';
        }

        if ( $this->plugins_page->has_settings_page( $tab ) ) {
            return $tab;
        }

        return 'general';
    }
}
