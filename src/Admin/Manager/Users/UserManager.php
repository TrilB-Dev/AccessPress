<?php
/**
 * UserManager class for AccessPress plugin.
 *
 * @package AccessPress
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Admin\Manager\Manager;
use AccessPress\Admin\Manager\Users\UserDashboard;
use AccessPress\Admin\Manager\Users\UserRoles;
use AccessPress\Admin\Manager\Users\UserGroups;
use AccessPress\Admin\Manager\Users\UserLogin;
use AccessPress\Admin\Manager\Users\UserRegistration;
use AccessPress\Admin\Manager\Users\UserProfile;
use AccessPress\Assets\Assets;
use AccessPress\Includes\Functions\Helpers\RequestHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;
use AccessPress\Includes\Functions\Helpers\PermissionHelper;


class UserManager extends Manager {
	/**
	 * The Page variable.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $page The page variable.
	 */
	protected $page;
	/**
	 * UserDashboard instance for managing the user dashboard.
	 *
	 * @since 1.0.0
	 * @var UserDashboard $user_dashboard The user dashboard instance.
	 */
	private UserDashboard $user_dashboard;
	/**
	 * UserRoles instance for managing the user roles.
	 *
	 * @since 1.0.0
	 * @var UserRoles $user_roles The user roles instance.
	 */
	private UserRoles $user_roles;
	/**
	 * UserGroups instance for managing the user groups.
	 *
	 * @since 1.0.0
	 * @var UserGroups $user_groups The user groups instance.
	 */
	private UserGroups $user_groups;
	/**
	 * UserLogin instance for managing the user login.
	 *
	 * @since 1.0.0
	 * @var UserLogin $user_login The user login instance.
	 */
	private UserLogin $user_login;
	/**
	 * UserRegistration instance for managing the user registration.
	 *
	 * @since 1.0.0
	 * @var UserRegistration $user_registration The user registration instance.
	 */
	private UserRegistration $user_registration;
	/**
	 * UserProfile instance for managing the user profile.
	 *
	 * @since 1.0.0
	 * @var UserProfile $user_profile The user profile instance.
	 */
	private UserProfile $user_profile;

	/**
	 * `Constructor` method for the `UserManager` class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( bool $initialize_tools = true ) {
		/**
		 * Set the page variable to 'tools'.
		 *
		 * @since 1.0.0
		 */
		$this->page = 'tools';

		if ( ! $initialize_tools ) {
			return;
		}

		/**
		 * Initialize the User Dashboard page.
		 *
		 * @since 1.0.0
		 */
		$this->user_dashboard = new UserDashboard();
		/**
		 * Initialize the User Roles page.
		 *
		 * @since 1.0.0
		 */
		$this->user_roles = new UserRoles();
		/**
		 * Initialize the User Groups page.
		 *
		 * @since 1.0.0
		 */
		$this->user_groups = new UserGroups();
		/**
		 * Initialize the User Login page.
		 *
		 * @since 1.0.0
		 */
		$this->user_login = new UserLogin();
		/**
		 * Initialize the User Registration page.
		 *
		 * @since 1.0.0
		 */
		$this->user_registration = new UserRegistration();
		/**
		 * Initialize the User Profile page.
		 *
		 * @since 1.0.0
		 */
		$this->user_profile = new UserProfile();
	}
	/**
	 * Renders the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render(): void {
		$tool = RequestHelper::get_key( 'tool', 'debug' );
		if ( ! in_array( $tool, array( 'user_dashboard', 'user_roles', 'user_groups', 'user_login', 'user_registration', 'user_profile' ), true ) ) {
			$tool = 'user_dashboard';
		}
		$capabilities = array(
			'user_dashboard'  => 'accesspress_tools_user_dashboard',
			'user_roles'  => 'accesspress_tools_user_roles',
			'user_groups' => 'accesspress_tools_user_groups',
			'user_login' => 'accesspress_tools_user_login',
			'user_registration' => 'accesspress_tools_user_registration',
			'user_profile' => 'accesspress_tools_user_profile',
		);
		if ( ! PermissionHelper::can( $capabilities[ $tool ] ) ) {
			wp_die( esc_html__( 'You are not authorized to access this AccessPress tool.', 'accesspress' ) );
		}
		$this->header( $this->title( $tool ) );
		if ( 'user_dashboard' === $tool ) {
			$this->user_dashboard->render_page_content();
		} elseif ( 'user_roles' === $tool ) {
			$this->user_roles->render_page_content();
		} elseif ( 'user_groups' === $tool ) {
			$this->user_groups->render_page_content();
		} elseif ( 'user_login' === $tool ) {
			$this->user_login->render_page_content();
		} elseif ( 'user_registration' === $tool ) {
			$this->user_registration->render_page_content();
		} elseif ( 'user_profile' === $tool ) {
			$this->user_profile->render_page_content();
		}
		$this->footer();
	}
	/**
	 * Registers the assets for the tools page.
	 *
	 * @since 1.0.0
	 * @param Assets $assets The Assets instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'accesspress-user-manager' ), 'tools' );
		if ( isset( $this->reset_manager ) ) {
			$this->reset_manager->register_assets( $assets );
		}
	}
	/**
	 * Returns the title for the given tool.
	 *
	 * @since 1.0.0
	 * @param string $tool The tool name.
	 * @return string The title for the tool.
	 */
	private function title( string $tool ): string {
		return array(
			'user_dashboard'  => __( 'User Dashboard', 'accesspress' ),
			'user_roles'  => __( 'User Roles', 'accesspress' ),
			'user_groups' => __( 'User Groups', 'accesspress' ),
			'user_login' => __( 'User Login', 'accesspress' ),
			'user_registration' => __( 'User Registration', 'accesspress' ),
			'user_profile' => __( 'User Profile', 'accesspress' ),
		)[ $tool ];
	}
}
