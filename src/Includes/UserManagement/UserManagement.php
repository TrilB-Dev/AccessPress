<?php
/**
 * Core AccessPress user management service.
 *
 * Provides frontend login, registration, profile management, access control,
 * and backend-profile override behavior as a core capability of AccessPress.
 *
 * @package AccessPress\Includes\UserManagement
 */

namespace AccessPress\Includes\UserManagement;

use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;
use AccessPress\Includes\UserManagement\Login\Login;
use AccessPress\Includes\UserManagement\Profile\Profile;
use AccessPress\Includes\UserManagement\Registration\Registration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserManagement {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Loader for hooks and routes.
	 *
	 * @var LoaderHelper
	 */
	private LoaderHelper $loader;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->loader = new LoaderHelper();
	}

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
	
		return self::$instance;
	}

	/**
	 * Current plugin slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return 'accesspress-user-management';
	}

	/**
	 * Register all user-management hooks, shortcodes, and access rules.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'init',
					'callback' => 'register_core_routes',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_init',
					'callback' => 'redirect_backend_profile',
				),
				array(
					'type'     => 'action',
					'hook'     => 'template_redirect',
					'callback' => 'handle_auth_requests',
				),
			)
		)->run();

		ShortcodeHelper::register_many(
			array(
				ShortcodeHelper::define( 'accesspress_login', array( $this, 'render_login_form' ) ),
				ShortcodeHelper::define( 'accesspress_register', array( $this, 'render_register_form' ) ),
				ShortcodeHelper::define( 'accesspress_profile', array( $this, 'render_profile_form' ) ),
			)
		);
	}

	/**
	 * Register core user-management pages and rewrites.
	 *
	 * @return void
	 */
	public function register_core_routes(): void {
		add_rewrite_rule( '^login/?$', 'index.php?accesspress_user_management=login', 'top' );
		add_rewrite_rule( '^register/?$', 'index.php?accesspress_user_management=register', 'top' );
		add_rewrite_rule( '^my-account/?$', 'index.php?accesspress_user_management=profile', 'top' );
		add_rewrite_tag( '%accesspress_user_management%', '([^&]+)' );
	}

	/**
	 * Redirect backend profile screens to the frontend account page.
	 *
	 * @return void
	 */
	public function redirect_backend_profile(): void {
		if ( ! is_user_logged_in() || wp_doing_ajax() ) {
			return;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		if ( in_array( $screen->id, array( 'profile', 'user-edit', 'profile-network' ), true ) ) {
			wp_safe_redirect( home_url( '/my-account/' ) );
			exit;
		}
	}

	/**
	 * Handle login, registration, and profile updates.
	 *
	 * @return void
	 */
	public function handle_auth_requests(): void {
		if ( ! isset( $_POST['accesspress_user_management_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['accesspress_user_management_nonce'] ) ), 'accesspress_user_management' ) ) {
			return;
		}

		$redirect_to = home_url( '/' );
		if ( ! empty( $_POST['redirect_to'] ) ) {
			$redirect_to = wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), $redirect_to );
		}

		if ( isset( $_POST['accesspress_login'] ) ) {
			$this->process_login( $redirect_to );
		}

		if ( isset( $_POST['accesspress_register'] ) ) {
			$this->process_registration( $redirect_to );
		}

		if ( isset( $_POST['accesspress_profile'] ) ) {
			$this->process_profile_update( $redirect_to );
		}
	}

	/**
	 * Render the frontend login form.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Unused content.
	 * @param string $tag Unused tag.
	 * @return string
	 */
	public function render_login_form( $atts = array(), string $content = '', string $tag = '' ): string {
		return Login::render_form( is_array( $atts ) ? $atts : array() );
	}

	/**
	 * Render the frontend registration form.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Unused content.
	 * @param string $tag Unused tag.
	 * @return string
	 */
	public function render_register_form( $atts = array(), string $content = '', string $tag = '' ): string {
		return Registration::render_form( is_array( $atts ) ? $atts : array() );
	}

	/**
	 * Render the frontend profile form.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Unused content.
	 * @param string $tag Unused tag.
	 * @return string
	 */
	public function render_profile_form( $atts = array(), string $content = '', string $tag = '' ): string {
		return Profile::render_form( is_array( $atts ) ? $atts : array() );
	}

	/**
	 * Process user login.
	 *
	 * @param string $redirect_to Redirect URL.
	 * @return void
	 */
	private function process_login( string $redirect_to ): void {
		Login::process_login( $redirect_to );
	}

	/**
	 * Process registration.
	 *
	 * @param string $redirect_to Redirect URL.
	 * @return void
	 */
	private function process_registration( string $redirect_to ): void {
		Registration::process_registration( $redirect_to );
	}

	/**
	 * Process profile updates.
	 *
	 * @param string $redirect_to Redirect URL.
	 * @return void
	 */
	private function process_profile_update( string $redirect_to ): void {
		Profile::process_profile_update( $redirect_to );
	}
}
