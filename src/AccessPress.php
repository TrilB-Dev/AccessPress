<?php

/**
 * The file that defines the core AccessPress class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://trilb.dev/MrTrilB
 * @since      1.0.0
 *
 * @package    AccessPress
 * @subpackage AccessPress/Includes
 */
namespace AccessPress;

use AccessPress\Admin\Admin;
use AccessPress\Assets\Assets;
use AccessPress\Includes\Includes;
use AccessPress\Includes\Core\WP\I18n;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\API\Routes;
use AccessPress\Includes\Analytics\Analytics;
use AccessPress\Includes\Plugins\Plugins;
use AccessPress\Includes\UserManagement\UserManagement;
use AccessPress\Public\Frontend;

class AccessPress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected LoaderHelper $loader;

	/**
	 * The file path to the main plugin file.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $accesspress_file    The file path to the main plugin file.
	 */
	protected string $accesspress_file;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $accesspress_name    The string used to uniquely identify this plugin.
	 */
	protected $accesspress_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * The instance of the Includes class that handles the plugin's includes.
	 *
	 * @var Includes
	 * @since 1.0.0
	 * @access protected
	 */
	protected Includes $includes;

	/**
	 * The instance of the Assets class that handles the plugin's assets.
	 *
	 * @var Assets
	 * @since 1.0.0
	 * @access protected
	 */
	protected Assets $assets;

	/**
	 * The instance of the Admin class that handles the plugin's admin functionality.
	 *
	 * @var Admin
	 * @since 1.0.0
	 * @access protected
	 */
	protected Admin $admin;

	/**
	 * The instance of the Frontend class that handles the plugin's frontend functionality.
	 *
	 * @var Frontend
	 * @since 1.0.0
	 * @access protected
	 */
	protected Frontend $frontend;

	/**
	 * The AccessPress plugin registry and discovery service.
	 *
	 * @var Plugins
	 * @since 1.0.0
	 * @access protected
	 */
	protected Plugins $plugins;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( string $accesspress_file = ACCESSPRESS_FILE, string $accesspress_name = ACCESSPRESS_NAME, string $version = ACCESSPRESS_VERSION ) {
		$this->accesspress_file = $accesspress_file;
		$this->accesspress_name = sanitize_key( $accesspress_name );
		$this->version           = $version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_core_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - AccessPress_Loader. Orchestrates the hooks of the plugin.
	 * - AccessPress_i18n. Defines internationalization functionality.
	 * - AccessPress_Admin. Defines all hooks for the admin area.
	 * - AccessPress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new LoaderHelper();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the AccessPress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n( $this->accesspress_name, null, $this->accesspress_file );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks() {
		$this->includes = Includes::get_instance();
		$this->assets   = new Assets();
		$this->assets->register();
		$this->admin              = new Admin( $this->assets );
		$this->frontend           = new Frontend();
		$this->plugins            = Plugins::get_instance();
		$user_management          = UserManagement::get_instance();

		$this->loader->add_action( 'init', $this->includes, 'init' );
		$this->loader->add_action( 'init', $this->plugins, 'init', -10 );
		$this->loader->add_action( 'init', $user_management, 'register', 5 );
		$this->loader->add_action( 'admin_menu', $this->admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->assets, 'enqueue_admin' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->assets, 'enqueue_frontend' );
		if ( class_exists( Analytics::class ) && method_exists( Analytics::class, 'track_view' ) ) {
			$this->loader->add_action( 'wp_head', Analytics::class, 'track_view' );
		}
		$this->loader->add_filter( 'the_content', $this->frontend, 'filter_content' );
		$this->loader->add_filter( 'body_class', $this->frontend, 'body_classes' );
		$this->loader->add_action( 'rest_api_init', Routes::class, 'register_routes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->accesspress_name;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_plugin_file(): string {
		return $this->accesspress_file;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_includes(): Includes {
		return $this->includes;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_assets(): Assets {
		return $this->assets;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_admin(): Admin {
		return $this->admin;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_frontend(): Frontend {
		return $this->frontend;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_plugins(): Plugins {
		return $this->plugins;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function register_extension( callable $extension ): self {
		$this->includes->register_extension( $extension );

		return $this;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}



