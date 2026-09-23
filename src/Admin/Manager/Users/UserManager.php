<?php
/**
 * UserManager class for AccessPress plugin.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Admin\Manager\Manager;
use AccessPress\Assets\Assets;
use AccessPress\Admin\Manager\Users\UserDashboard;
use AccessPress\Admin\Manager\Users\UserGroups;
use AccessPress\Admin\Manager\Users\UserLogin;
use AccessPress\Admin\Manager\Users\UserProfile;
use AccessPress\Admin\Manager\Users\UserRegistration;
use AccessPress\Admin\Manager\Users\UserRoles;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserManager extends Manager {
	/**
	 * User Dashboard manager instance.
	 *
	 * @var UserDashboard
	 * @since 1.0.0
	 */
	private UserDashboard $user_dashboard;
	/**
	 * User Groups manager instance.
	 *
	 * @var UserGroups
	 * @since 1.0.0
	 */
	private UserGroups $user_groups;
	/**
	 * User Login manager instance.
	 *
	 * @var UserLogin
	 * @since 1.0.0
	 */
	private UserLogin $user_login;
	/**
	 * User Profile manager instance.
	 *
	 * @var UserProfile
	 * @since 1.0.0
	 */
	private UserProfile $user_profile;
	/**
	 * User Registration manager instance.
	 *
	 * @var UserRegistration
	 * @since 1.0.0
	 */
	private UserRegistration $user_registration;
	/**
	 * User Roles manager instance.
	 *
	 * @var UserRoles
	 * @since 1.0.0
	 */
	private UserRoles $user_roles;
	/**
	 * Current settings page slug.
	 *
	 * @var string
	 */
	protected string $page;
	/**
	 * Constructor for the settings manager.
	 */
	public function __construct( bool $initialize_users = true ) {
		/**
		 * Set the page variable to 'tools'.
		 *
		 * @since 1.0.0
		 */
		$this->page = 'user-management';
		/**
		 * Initialize the user management section.
		 *
		 * @since 1.0.0
		 */
		if ( ! $initialize_users ) {
			return;
		}
		/**
		 * Initialize the user management components.
		 *
		 * @since 1.0.0
		 */
		$this->user_dashboard    = new UserDashboard();
		/**
		 * Initialize the user groups manager.
		 *
		 * @since 1.0.0
		 */
		$this->user_groups       = new UserGroups();
		/**
		 * Initialize the user login manager.
		 *
		 * @since 1.0.0
		 */
		$this->user_login        = new UserLogin();
		/**
		 * Initialize the user profile manager.
		 *
		 * @since 1.0.0
		 */
		$this->user_profile      = new UserProfile();
		/**
		 * Initialize the user registration manager.
		 *
		 * @since 1.0.0
		 */
		$this->user_registration = new UserRegistration();
		/**
		 * Initialize the user roles manager.
		 *
		 * @since 1.0.0
		 */
		$this->user_roles        = new UserRoles();
	}
	/**
	 * Render the selected user-management tab.
	 *
	 * @return void
	 */
	public function render(): void {
		$tab = sanitize_key( RequestHelper::get_key( 'tab', 'dashboard' ) );
		$tab = $this->normalize_tab( $tab );

		$capabilities = array(
			'dashboard'   => 'accesspress_user_management_dashboard',
			'groups'      => 'accesspress_user_management_groups',
			'login'       => 'accesspress_user_management_login',
			'profile'     => 'accesspress_user_management_profile',
			'registration'=> 'accesspress_user_management_registration',
			'roles'       => 'accesspress_user_management_roles',
		);

		if ( ! PermissionHelper::can( $capabilities[ $tab ] ) ) {
			wp_die( esc_html__( 'You are not authorized to access this AccessPress user-management screen.', 'accesspress' ) );
		}

		$this->header( $this->title( $tab ) );
		$this->render_tab_content( $tab );
		$this->footer();
	}

	/**
	 * Render the content for a specific user-management tab.
	 *
	 * @param string $tab The tab to render.
	 * @return void
	 */
	public function render_tab_content( string $tab ): void {
		$tab = $this->normalize_tab( $tab );

		$instances = array(
			'dashboard'    => new UserDashboard(),
			'groups'       => new UserGroups(),
			'login'        => new UserLogin(),
			'profile'      => new UserProfile(),
			'registration' => new UserRegistration(),
			'roles'        => new UserRoles(),
		);

		if ( ! isset( $instances[ $tab ] ) ) {
			return;
		}

		$instance = $instances[ $tab ];
		if ( method_exists( $instance, 'render_page_content' ) ) {
			$instance->render_page_content();
			return;
		}

		if ( method_exists( $instance, 'render' ) ) {
			$instance->render();
		}
	}

	/**
	 * Normalizes the tab value to ensure it is valid.
	 *
	 * @param string $tab The tab to normalize.
	 * @return string The normalized tab.
	 */
	private function normalize_tab( string $tab ): string {
		$aliases = array(
			'overview'        => 'dashboard',
			'user-management' => 'dashboard',
			'dashboard'       => 'dashboard',
			'user-groups'     => 'groups',
			'groups'          => 'groups',
			'user-login'      => 'login',
			'login'           => 'login',
			'user-profile'    => 'profile',
			'profile'         => 'profile',
			'user-registration' => 'registration',
			'registration'    => 'registration',
			'user-roles'      => 'roles',
			'roles'           => 'roles',
		);

		return $this->normalize_route( $tab, $aliases, 'dashboard' );
	}

	/**
	 * Registers the assets for the user-management page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$user_management_assets = $this->assets( 'user-management' );
		$user_management_assets['styles'][] = array(
			'handle' => 'accesspress-bootstrap-drag-and-drop',
			'src'    => ACCESSPRESS_ASSETS_URL . '/dist/css/bootstrap-drag-and-drop.min.css',
			'deps'   => array( 'accesspress-bootstrap' ),
		);
		$user_management_assets['scripts'][] = array(
			'handle'    => 'accesspress-bootstrap-drag-and-drop-js',
			'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/bootstrap-drag-and-drop.min.js',
			'deps'      => array( 'accesspress-bootstrap' ),
			'in_footer' => true,
		);
		$user_management_assets['scripts'][] = array(
			'handle'    => 'accesspress-admin-user-management',
			'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/admin.user-management.js',
			'deps'      => array( 'accesspress-bootstrap', 'accesspress-bootstrap-drag-and-drop-js' ),
			'in_footer' => true,
		);
		$user_management_assets['scripts'][] = array(
			'handle'    => 'accesspress-admin-profile-builder',
			'src'       => ACCESSPRESS_ASSETS_URL . '/dist/js/admin.profile.js',
			'deps'      => array( 'accesspress-bootstrap', 'accesspress-bootstrap-drag-and-drop-js' ),
			'in_footer' => true,
		);
		$assets->register_page( 'accesspress-user-management', $user_management_assets );
	}

	/**
	 * Returns the title for the given page.
	 *
	 * @param string $tab The tab name.
	 * @return string The title for the tab.
	 */
	private function title( string $tab ): string {
		return array(
			'dashboard'    => __( 'User Management Dashboard', 'accesspress' ),
			'groups'       => __( 'User Groups', 'accesspress' ),
			'login'        => __( 'User Login', 'accesspress' ),
			'profile'      => __( 'User Profile', 'accesspress' ),
			'registration' => __( 'User Registration', 'accesspress' ),
			'roles'        => __( 'User Roles', 'accesspress' ),
		)[ $tab ];
	}
}

