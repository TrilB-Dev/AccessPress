<?php
/**
 * Settings-related admin functions for AccessPress.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Admin;

use AccessPress\Includes\Functions\Admin\FunctionsPlugins;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\LoggerHelper;
use AccessPress\Includes\Functions\Helpers\PermalinkHelper;
use AccessPress\Includes\Settings\Settings;
use AccessPress\Includes\Settings\SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsSettings {

    /**
     * Register AccessPress and provider-backed plugin settings.
     *
     * @return void
     */
    public function register_settings(): void {
        // AccessPress stores settings in its own custom table rather than the default
        // WordPress options table. The regular settings API is intentionally not used
        // here; form submissions are handled through the custom admin-post flow.
    }

    /**
	 * Register the WordPress admin-post actions used by the settings forms.
	 *
	 * @param LoaderHelper $loader Loader instance used to register hooks.
	 * @return void
	 */
	public function register_admin_post_hooks( LoaderHelper $loader ): void {
		$loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_save_general_settings',
					'callback' => 'handle_general_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_save_access_settings',
					'callback' => 'handle_access_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_save_layout_settings',
					'callback' => 'handle_layout_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_save_security_settings',
					'callback' => 'handle_security_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_accesspress_save_email_settings',
					'callback' => 'handle_email_save',
				),
			)
		)->run();
	}
    /**
	 * Save settings using the direct admin POST flow used by the custom table store.
	 *
	 * @return void
	 */
	public function save_settings(): void {
		$action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
		if ( '' === $action ) {
			return;
		}

		if ( 'accesspress_save_general_settings' === $action ) {
			$this->general_save();
		}

		if ( in_array( $action, array( 'accesspress_save_access_settings' ), true ) ) {
			$this->access_save();
		}

		if ( 'accesspress_save_security_settings' === $action ) {
			$this->security_save();
		}

		if ( 'accesspress_save_email_settings' === $action ) {
			$this->email_save();
		}

		if ( 'accesspress_save_layout_settings' === $action ) {
			$this->layout_save();
		}
	}

	/**
	 * Validate a save nonce against the standard WordPress field and the legacy plugin-specific field.
	 *
	 * @param string $action Nonce action.
	 * @return void
     * @since 1.0.0
	 */
	private function validate_save_nonce( string $action ): void {
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			check_admin_referer( $action, '_wpnonce' );
			return;
		}

		check_admin_referer( $action );
	}
	 /**
     * Redirect back to the settings page with a success or error notification flag.
     *
     * @param string $tab The settings tab to return to.
     * @param bool $success Whether the save succeeded.
     * @param string $message Optional notice message.
     * @param array<string, string> $extra_query Extra query arguments.
     * @return void
     * @since 1.0.0
     */
    private function redirect_after_save( string $tab, bool $success, string $message = '', array $extra_query = array() ): void {
        $query = array(
            'page' => 'accesspress',
            'group' => 'settings',
            'tab' => $tab,
            $success ? 'settings_saved' : 'settings_failed' => '1',
        );

        foreach ( $extra_query as $key => $value ) {
            $query[ $key ] = $value;
        }

        if ( '' !== $message ) {
            $query['settings_message'] = rawurlencode( $message );
        }

        wp_safe_redirect( admin_url( 'admin.php?' . build_query( $query ) ) );
        exit;
    }
	 /**
	 * Save the general settings.
	 *
	 * @return void
     * @since 1.0.0
	 */
	public function general_save(): void {
		LoggerHelper::write_log( 'AccessPress general settings save started.' );
		LoggerHelper::write_log( array(
			'request' => $_POST,
			'action' => 'accesspress_save_general_settings',
		) );
		if ( ! user_can( get_current_user_id(), 'accesspress_settings_general_edit' ) ) {
			LoggerHelper::write_log( 'AccessPress general settings save denied: missing general_edit capability.' );
			$this->redirect_after_save( 'general', false, __( 'You are not allowed to save AccessPress general settings.', 'accesspress' ) );
		}

		$this->validate_save_nonce( 'accesspress_save_general_settings' );
		$input = isset( $_POST['accesspress_general'] ) && is_array( $_POST['accesspress_general'] ) ? wp_unslash( $_POST['accesspress_general'] ) : array();
		LoggerHelper::write_log( array(
			'general_input' => $input,
			'general_input_keys' => array_keys( $input ),
		) );

		try {
			$sanitized = $this->sanitize_general( $input );
			Settings::set_group( Settings::GENERAL, $sanitized );
			foreach ( $sanitized as $key => $value ) {
				Settings::set( $key, $value );
			}
			LoggerHelper::write_log( array(
				'general_sanitized' => $sanitized,
				'general_db_group' => Settings::get_group( Settings::GENERAL, array() ),
			) );
			$this->redirect_after_save( 'general', true, __( 'AccessPress general settings saved successfully.', 'accesspress' ) );
		} catch ( \Throwable $exception ) {
			LoggerHelper::write_log( array(
				'general_save_error' => $exception->getMessage(),
				'general_save_trace' => $exception->getTraceAsString(),
			) );
			$this->redirect_after_save( 'general', false, __( 'Failed to save AccessPress general settings.', 'accesspress' ) );
		}
	}

	/**
	 * Save the access settings.
	 *
	 * @return void
     * @since 1.0.0
	 */
	public function access_save(): void {
		LoggerHelper::write_log( 'AccessPress access settings save started.' );
		LoggerHelper::write_log( array(
			'request' => $_POST,
			'action' => 'accesspress_save_access_settings',
		) );
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'accesspress_settings_access_edit' ) ) {
			LoggerHelper::write_log( 'AccessPress access settings save denied: missing access_edit capability.' );
			$this->redirect_after_save( 'access', false, __( 'You are not allowed to save AccessPress access settings.', 'accesspress' ) );
		}

		$this->validate_save_nonce( 'accesspress_save_access_settings' );
		$input = isset( $_POST['accesspress_access'] ) && is_array( $_POST['accesspress_access'] ) ? wp_unslash( $_POST['accesspress_access'] ) : array();
		LoggerHelper::write_log( array(
			'access_input' => $input,
			'access_input_keys' => array_keys( $input ),
		) );

		try {
			$sanitized = $this->sanitize_access( $input );
			Settings::set_group( Settings::ACCESS, $sanitized );
			foreach ( $sanitized as $key => $value ) {
				Settings::set( $key, $value );
			}
			LoggerHelper::write_log( array(
				'access_sanitized' => $sanitized,
				'access_db_group' => Settings::get_group( Settings::ACCESS, array() ),
			) );
			$this->redirect_after_save( 'access', true, __( 'AccessPress access settings saved successfully.', 'accesspress' ) );
		} catch ( \Throwable $exception ) {
			LoggerHelper::write_log( array(
				'access_save_error' => $exception->getMessage(),
				'access_save_trace' => $exception->getTraceAsString(),
			) );
			$this->redirect_after_save( 'access', false, __( 'Failed to save AccessPress access settings.', 'accesspress' ) );
		}
	}

	/**
	 * Save the layout settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function layout_save(): void {
		LoggerHelper::write_log( 'AccessPress layout settings save started.' );
		LoggerHelper::write_log( array(
			'request' => $_POST,
			'action' => 'accesspress_save_layout_settings',
		) );
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'accesspress_settings_layout_edit' ) ) {
			LoggerHelper::write_log( 'AccessPress layout settings save denied: missing layout_edit capability.' );
			$this->redirect_after_save( 'layout', false, __( 'You are not allowed to save AccessPress layout settings.', 'accesspress' ) );
		}

		$this->validate_save_nonce( 'accesspress_save_layout_settings' );
		$input = isset( $_POST['accesspress_layout'] ) && is_array( $_POST['accesspress_layout'] ) ? wp_unslash( $_POST['accesspress_layout'] ) : array();
		LoggerHelper::write_log( array(
			'layout_input' => $input,
			'layout_input_keys' => array_keys( $input ),
		) );

		try {
			$section = sanitize_key( $input['layout_section'] ?? 'general' );
			$sanitized = $this->sanitize_layout( $input );
			Settings::set_group( Settings::LAYOUT, $sanitized );
			foreach ( $sanitized as $key => $value ) {
				Settings::set( $key, $value );
			}
			LoggerHelper::write_log( array(
				'layout_section' => $section,
				'layout_sanitized' => $sanitized,
				'layout_db_group' => Settings::get_group( Settings::LAYOUT, array() ),
			) );
			$this->redirect_after_save( 'layout', true, __( 'AccessPress layout settings saved successfully.', 'accesspress' ), array( 'layout_section' => $section ) );
		} catch ( \Throwable $exception ) {
			LoggerHelper::write_log( array(
				'layout_save_error' => $exception->getMessage(),
				'layout_save_trace' => $exception->getTraceAsString(),
			) );
			$this->redirect_after_save( 'layout', false, __( 'Failed to save AccessPress layout settings.', 'accesspress' ) );
		}
	}
	public function security_save(): void {
		LoggerHelper::write_log( 'AccessPress security settings save started.' );
		LoggerHelper::write_log( array(
			'request' => $_POST,
			'action' => 'accesspress_save_security_settings',
		) );
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'accesspress_settings_security_edit' ) ) {
			LoggerHelper::write_log( 'AccessPress security settings save denied: missing security_edit capability.' );
			$this->redirect_after_save( 'security', false, __( 'You are not allowed to save AccessPress security settings.', 'accesspress' ) );
		}

		$this->validate_save_nonce( 'accesspress_save_security_settings' );
		$input = isset( $_POST['accesspress_security'] ) && is_array( $_POST['accesspress_security'] ) ? wp_unslash( $_POST['accesspress_security'] ) : array();
		LoggerHelper::write_log( array(
			'security_input' => $input,
			'security_input_keys' => array_keys( $input ),
		) );

		try {
			$section = sanitize_key( $input['security_section'] ?? 'general' );
			$sanitized = $this->sanitize_security( $input );
			Settings::set_group( Settings::SECURITY, $sanitized );
			foreach ( $sanitized as $key => $value ) {
				Settings::set( $key, $value );
			}
			LoggerHelper::write_log( array(
				'security_section' => $section,
				'security_sanitized' => $sanitized,
				'security_db_group' => Settings::get_group( Settings::SECURITY, array() ),
			) );
			$this->redirect_after_save( 'security', true, __( 'AccessPress security settings saved successfully.', 'accesspress' ), array( 'security_section' => $section ) );
		} catch ( \Throwable $exception ) {
			LoggerHelper::write_log( array(
				'security_save_error' => $exception->getMessage(),
				'security_save_trace' => $exception->getTraceAsString(),
			) );
			$this->redirect_after_save( 'security', false, __( 'Failed to save AccessPress security settings.', 'accesspress' ) );
		}
	}
	public function email_save(): void {
		LoggerHelper::write_log( 'AccessPress email settings save started.' );
		LoggerHelper::write_log( array(
			'request' => $_POST,
			'action' => 'accesspress_save_email_settings',
		) );
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'accesspress_settings_email_edit' ) ) {
			LoggerHelper::write_log( 'AccessPress email settings save denied: missing email_edit capability.' );
			$this->redirect_after_save( 'email', false, __( 'You are not allowed to save AccessPress email settings.', 'accesspress' ) );
		}

		$this->validate_save_nonce( 'accesspress_save_email_settings' );
		$input = isset( $_POST['accesspress_email'] ) && is_array( $_POST['accesspress_email'] ) ? wp_unslash( $_POST['accesspress_email'] ) : array();
		LoggerHelper::write_log( array(
			'email_input' => $input,
			'email_input_keys' => array_keys( $input ),
		) );

		try {
			$section = sanitize_key( $input['email_section'] ?? 'general' );
			$sanitized = $this->sanitize_email( $input );
			Settings::set_group( Settings::EMAIL, $sanitized );
			foreach ( $sanitized as $key => $value ) {
				Settings::set( $key, $value );
			}
			LoggerHelper::write_log( array(
				'email_section' => $section,
				'email_sanitized' => $sanitized,
				'email_db_group' => Settings::get_group( Settings::EMAIL, array() ),
			) );
			$this->redirect_after_save( 'email', true, __( 'AccessPress email settings saved successfully.', 'accesspress' ), array( 'email_section' => $section ) );
		} catch ( \Throwable $exception ) {
			LoggerHelper::write_log( array(
				'email_save_error' => $exception->getMessage(),
				'email_save_trace' => $exception->getTraceAsString(),
			) );
			$this->redirect_after_save( 'email', false, __( 'Failed to save AccessPress email settings.', 'accesspress' ) );
		}
	}
	/**
	 * Sanitize the general settings input.
	 *
	 * @param array<string, mixed> $input The input to sanitize.
	 * @return array The sanitized input.
	 * @since 1.0.0
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
	 * @since 1.0.0
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
		foreach ( array(
			'login_registration_title' => 'text',
			'site_logo' => 'text',
			'frontend_user_menu' => 'menu',
			'ajax_submission_user_profile' => 'bool',
			'elementor_integration' => 'bool',
		) as $key => $type ) {
			if ( ! array_key_exists( $key, $input ) ) {
				continue;
			}

			switch ( $type ) {
				case 'text':
					$input[ $key ] = sanitize_text_field( wp_unslash( (string) $input[ $key ] ) );
					break;
				case 'menu':
					$input[ $key ] = in_array( (string) $input[ $key ], array( 'horizontal', 'vertical' ), true ) ? (string) $input[ $key ] : 'horizontal';
					break;
				case 'bool':
				 default:
					$input[ $key ] = ! empty( $input[ $key ] );
					break;
			}

			Settings::set( $key, $input[ $key ] );
		}

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
	public function sanitize_security( $input ): array {
		if ( ! current_user_can( 'accesspress_settings_security_edit' ) ) {
			return (array) Settings::get_group( Settings::SECURITY, array() );
		}
		$input   = is_array( $input ) ? $input : array();
		foreach ( array( 'enable_firewall', 'enable_login_protection' ) as $key ) {
			$input[ $key ] = ! empty( $input[ $key ] );
			Settings::set( $key, $input[ $key ] );
		}
		return $input;
	}
	public function sanitize_email( $input ): array {
		if ( ! current_user_can( 'accesspress_settings_email_edit' ) ) {
			return (array) Settings::get_group( Settings::EMAIL, array() );
		}
		$input   = is_array( $input ) ? $input : array();
		foreach ( array( 'email_from_name', 'email_from_address' ) as $key ) {
			$input[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
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



