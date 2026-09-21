<?php
/**
 * Settings-related admin functions for AccessPress.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

use AccessPress\Includes\Functions\Helpers\PermalinkHelper;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsSettings {
	/**
	 * Plugin functions used to collect provider-backed settings pages.
	 *
	 * @var FunctionsPlugins
	 */
	private FunctionsPlugins $plugin_functions;
	/**
	 * Constructor for FunctionsSettings.
	 *
	 * @param FunctionsPlugins $plugin_functions The plugin functions instance.
	 */
	public function __construct( FunctionsPlugins $plugin_functions ) {
		$this->plugin_functions = $plugin_functions;
	}

	/**
	 * Register AccessPress and provider-backed plugin settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting( 'accesspress_settings', 'accesspress_general', array( 'sanitize_callback' => array( $this, 'sanitize_general' ) ) );
		register_setting( 'accesspress_settings', 'accesspress_layout', array( 'sanitize_callback' => array( $this, 'sanitize_layout' ) ) );
		register_setting( 'accesspress_settings', 'accesspress_access', array( 'sanitize_callback' => array( $this, 'sanitize_access' ) ) );
		register_setting( 'accesspress_settings', 'accesspress_security', array( 'sanitize_callback' => array( $this, 'sanitize_security' ) ) );
		register_setting( 'accesspress_settings', 'accesspress_email', array( 'sanitize_callback' => array( $this, 'sanitize_email' ) ) );
		register_setting( 'accesspress_settings', 'accesspress_tools', array( 'sanitize_callback' => array( $this, 'sanitize_tools' ) ) );

		foreach ( $this->plugin_functions->plugin_settings_pages() as $page ) {
			register_setting(
				'accesspress_settings',
				'accesspress_' . $page['slug'],
				array( 'sanitize_callback' => $page['provider']->sanitize_settings( ... ) )
			);
		}
	}
	/**
	 * Sanitize the general settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 */
	public function sanitize_general( $input ): array {
		if ( ! current_user_can( 'accesspress_settings_general_edit' ) ) {
			return (array) Settings::get_group( Settings::GENERAL, array() );
		}
		$input = is_array( $input ) ? $input : array();

		foreach ( array( 'registration_page', 'login_page', 'my_account_page', 'lost_password_page', 'enable_multi_roles', 'enable_membership_groups' ) as $key ) {
			$input[ $key ] = $this->resolve_page_setting( $key, $input[ $key ] ?? '' );
			Settings::set( $key, $input[ $key ] );
		}

		return $input;
	}

	/**
	 * Create a page for a selected page type when the user chooses the create-flow value.
	 *
	 * @param string $key The setting key.
	 * @param mixed  $value The submitted value.
	 * @return string The saved page ID or an empty string.
	 */
	private function resolve_page_setting( string $key, $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( '' === $value || '0' === $value ) {
			return '';
		}
		if ( 'create:' !== substr( $value, 0, 7 ) ) {
			return sanitize_text_field( $value );
		}

		$page_key = substr( $value, 7 );
		$definition = array(
			'register' => array(
				'title' => __( 'Register', 'accesspress' ),
				'slug' => 'register',
				'content' => '[accesspress_register]',
			),
			'login' => array(
				'title' => __( 'Login', 'accesspress' ),
				'slug' => 'login',
				'content' => '[accesspress_login]',
			),
			'profile' => array(
				'title' => __( 'My Account', 'accesspress' ),
				'slug' => 'account',
				'content' => '[accesspress_profile]',
			),
			'lost-password' => array(
				'title' => __( 'Lost Password', 'accesspress' ),
				'slug' => 'lost-password',
				'content' => '[accesspress_lost_password]',
			),
		);

		$target = $definition[ $page_key ] ?? null;
		if ( ! is_array( $target ) ) {
			return '';
		}

		$slug = sanitize_title( $target['slug'] );
		if ( function_exists( 'get_page_by_path' ) ) {
			$existing = get_page_by_path( $slug, defined( 'OBJECT' ) ? OBJECT : 1, 'page' );
			if ( class_exists( '\WP_Post' ) && $existing instanceof \WP_Post ) {
				return (string) $existing->ID;
			}
			if ( is_object( $existing ) ) {
				return (string) ( $existing->ID ?? '' );
			}
			if ( is_numeric( $existing ) ) {
				return (string) $existing;
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => $target['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $target['content'],
			)
		);

		return is_numeric( $post_id ) ? (string) $post_id : '';
	}

	/**
	 * Sanitize the layout settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 */
	public function sanitize_layout( $input ): array {
		if ( ! current_user_can( 'accesspress_settings_layout_edit' ) ) {
			return (array) Settings::get_group( Settings::LAYOUT, array() );
		}
		$input   = is_array( $input ) ? $input : array();
		$section = sanitize_key( $input['layout_section'] ?? 'general' );
		unset( $input['layout_section'] );
		$section_keys = array(
			'general' => array( 'show_search', 'show_breadcrumbs', 'show_sidebar' ),
			'search'  => array( 'show_search', 'search_placeholder', 'search_button_text', 'search_scope', 'search_no_results_message', 'search_results_count', 'search_min_chars', 'search_live_results' ),
			'sidebar' => array( 'show_sidebar', 'sidebar_position', 'sidebar_width', 'sidebar_sticky', 'sidebar_show_categories', 'sidebar_show_category_count', 'sidebar_expand_categories', 'sidebar_show_page_count' ),
			'page'    => array( 'page_show_title', 'show_breadcrumbs', 'page_show_toc', 'page_toc_position', 'toc_min_level', 'toc_max_level', 'show_last_updated', 'show_author', 'show_reading_time', 'reading_time_wpm', 'show_feedback', 'page_show_navigation', 'show_related_pages', 'related_pages_count' ),
		);
		$active_keys  = $section_keys[ $section ] ?? array_merge( ...array_values( $section_keys ) );
		foreach ( array( 'show_search', 'show_toc', 'show_breadcrumbs', 'show_last_updated', 'show_author', 'show_reading_time', 'show_feedback', 'show_related_pages', 'search_live_results', 'show_sidebar', 'sidebar_sticky', 'sidebar_show_categories', 'sidebar_show_category_count', 'sidebar_expand_categories', 'sidebar_show_page_count', 'page_show_title', 'page_show_toc', 'page_show_navigation' ) as $key ) {
			if ( ! in_array( $key, $active_keys, true ) ) {
				continue;
			}
			$value         = ! empty( $input[ $key ] );
			$input[ $key ] = $value;
			Settings::set( $key, $value );
		}
		foreach ( array( 'search_placeholder', 'search_button_text', 'search_no_results_message' ) as $key ) {
			if ( ! in_array( $key, $active_keys, true ) ) {
				continue;
			}
			$input[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
			Settings::set( $key, $input[ $key ] );
		}
		if ( in_array( 'search_scope', $active_keys, true ) ) {
			$input['search_scope'] = in_array( $input['search_scope'] ?? '', array( 'all', 'title', 'content' ), true ) ? $input['search_scope'] : 'all';
			Settings::set( 'search_scope', $input['search_scope'] );
		}
		if ( in_array( 'sidebar_position', $active_keys, true ) ) {
			$input['sidebar_position'] = in_array( $input['sidebar_position'] ?? '', array( 'left', 'right' ), true ) ? $input['sidebar_position'] : 'left';
			Settings::set( 'sidebar_position', $input['sidebar_position'] );
		}
		if ( in_array( 'page_toc_position', $active_keys, true ) ) {
			$input['page_toc_position'] = in_array( $input['page_toc_position'] ?? '', array( 'sidebar', 'content' ), true ) ? $input['page_toc_position'] : 'sidebar';
			Settings::set( 'page_toc_position', $input['page_toc_position'] );
		}
		foreach ( array(
			'related_pages_count'  => array( 1, 12 ),
			'search_results_count' => array( 1, 50 ),
			'search_min_chars'     => array( 1, 5 ),
			'sidebar_width'        => array( 180, 480 ),
			'toc_min_level'        => array( 1, 5 ),
			'toc_max_level'        => array( 2, 6 ),
			'reading_time_wpm'     => array( 100, 400 ),
		) as $key => [ $minimum, $maximum ] ) {
			if ( ! in_array( $key, $active_keys, true ) ) {
				continue;
			}
			$input[ $key ] = max( $minimum, min( $maximum, absint( $input[ $key ] ?? $minimum ) ) );
			Settings::set( $key, $input[ $key ] );
		}
		return $input;
	}
	/**
	 * Sanitize the access settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 */
	public function sanitize_access( $input ): array {
		if ( ! current_user_can( 'accesspress_settings_access_edit' ) ) {
			return (array) Settings::get_group( Settings::ACCESS, array() );
		}
		$input   = is_array( $input ) ? $input : array();
		$allowed = array( 'manage_options', 'edit_posts', 'publish_posts' );
		foreach ( array( 'create_wikis', 'write_pages', 'view_analytics', 'manage_plugins' ) as $key ) {
			$values        = is_array( $input[ $key ] ?? null ) ? $input[ $key ] : array( $input[ $key ] ?? 'manage_options' );
			$values        = array_values( array_unique( array_intersect( $allowed, array_map( 'sanitize_key', $values ) ) ) );
			$input[ $key ] = empty( $values ) ? array( 'manage_options' ) : $values;
			Settings::set( $key, $input[ $key ] );
		}
		return $input;
	}
	/**
	 * Sanitize the tools settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 */
	public function sanitize_tools( $input ): array {
		$input = is_array( $input ) ? $input : array();
		foreach ( array( 'debug_logging', 'console_logging' ) as $key ) {
			$input[ $key ] = ! empty( $input[ $key ] );
			Settings::set( $key, $input[ $key ] );
		}
		return $input;
	}
}



