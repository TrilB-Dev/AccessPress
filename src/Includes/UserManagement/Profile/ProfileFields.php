<?php
/**
 * Field definitions and configuration helpers for profile and registration.
 *
 * @package AccessPress\Includes\UserManagement\Profile
 * @since 1.0.0
 */
namespace AccessPress\Includes\UserManagement\Profile;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProfileFields {
	/**
	 * WordPress field names mapped to the built-in profile field definitions.
	 *
	 * These values must match the underlying user table and user meta keys so the
	 * built-in profile fields save to the correct WP User fields.
	 *
	 * @return array<string, string>
	 * @since 1.0.0
	 */
	public static function get_wp_field_map(): array {
		return array(
			'user_login'           => 'user_login',
			'password'             => 'user_pass',
			'first_name'           => 'first_name',
			'last_name'            => 'last_name',
			'nickname'             => 'nickname',
			'display_name'         => 'display_name',
			'user_email'           => 'user_email',
			'description'          => 'description',
			'user_url'             => 'user_url',
			'locale'               => 'locale',
			'admin_color'          => 'admin_color',
			'rich_editing'         => 'rich_editing',
			'show_admin_bar_front' => 'show_admin_bar_front',
			'show_admin_bar_admin' => 'show_admin_bar_admin',
			'role'                 => 'role',
		);
	}
	/**
	 * Resolve a profile field key to the native WordPress field name used in wp_users.
	 *
	 * @param string $key Field key.
	 * @param string $fallback Fallback key when no mapping exists.
	 * @return string
	 * @since 1.0.0
	 */
	public static function resolve_wp_field_name( string $key, string $fallback = '' ): string {
		$key = sanitize_key( $key );
		if ( '' === $key ) {
			return $fallback;
		}

		$map = self::get_wp_field_map();
		if ( isset( $map[ $key ] ) ) {
			return $map[ $key ];
		}

		$aliases = array(
			'username'     => 'user_login',
			'password'    => 'user_pass',
			'email'        => 'user_email',
			'bio'          => 'description',
			'website'      => 'user_url',
			'capabilities' => 'role',
		);

		if ( isset( $aliases[ $key ] ) ) {
			return $aliases[ $key ];
		}

		return '' !== $fallback ? $fallback : $key;
	}
	/**
	 * Built-in WordPress profile fields that can be added once per profile.
	 *
	 * @return array<int, array<string, mixed>>
	 * @since 1.0.0
	 */
	public static function get_builtin_fields(): array {
		$locale_options = self::get_wordpress_locale_options();
		$role_options   = self::get_wordpress_role_options();

		$definitions = array(
			array(
				'id'          => 'user_login',
				'type'        => 'user_login',
				'label'       => __( 'Username', 'accesspress' ),
				'key'         => 'user_login',
				'wp_field'    => 'user_login',
				'placeholder' => '',
				'required'    => true,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'password',
				'type'        => 'password',
				'label'       => __( 'Password', 'accesspress' ),
				'key'         => 'password',
				'wp_field'    => 'user_pass',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'display_name',
				'type'        => 'display_name',
				'label'       => __( 'Display name', 'accesspress' ),
				'key'         => 'display_name',
				'wp_field'    => 'display_name',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'first_name',
				'type'        => 'first_name',
				'label'       => __( 'First name', 'accesspress' ),
				'key'         => 'first_name',
				'wp_field'    => 'first_name',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'last_name',
				'type'        => 'last_name',
				'label'       => __( 'Last name', 'accesspress' ),
				'key'         => 'last_name',
				'wp_field'    => 'last_name',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'nickname',
				'type'        => 'nickname',
				'label'       => __( 'Nickname', 'accesspress' ),
				'key'         => 'nickname',
				'wp_field'    => 'nickname',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'user_email',
				'type'        => 'user_email',
				'label'       => __( 'Email', 'accesspress' ),
				'key'         => 'user_email',
				'wp_field'    => 'user_email',
				'placeholder' => '',
				'required'    => true,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'description',
				'type'        => 'description',
				'label'       => __( 'Bio', 'accesspress' ),
				'key'         => 'description',
				'wp_field'    => 'description',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'user_url',
				'type'        => 'user_url',
				'label'       => __( 'Website', 'accesspress' ),
				'key'         => 'user_url',
				'wp_field'    => 'user_url',
				'placeholder' => 'https://',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'locale',
				'type'        => 'locale',
				'label'       => __( 'Language', 'accesspress' ),
				'key'         => 'locale',
				'wp_field'    => 'locale',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
				'visibility'  => 'user',
				'editable'    => true,
				'options'     => $locale_options,
				'description' => __( 'Choose the language used for the WordPress interface and user locale.', 'accesspress' ),
				'tooltip'     => __( 'This value is stored as the user locale in WordPress.', 'accesspress' ),
				'renderer'    => 'bootstrap_select',
				'country_data' => array(
					'type'  => 'language',
					'group' => 'locale',
					'flags' => false,
				),
				'attributes' => array(
					'data-live-search' => 'true',
				),
			),
			array(
				'id'          => 'admin_color',
				'type'        => 'admin_color',
				'label'       => __( 'Admin color scheme', 'accesspress' ),
				'key'         => 'admin_color',
				'wp_field'    => 'admin_color',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'wp_user',
			),
			array(
				'id'          => 'role',
				'type'        => 'role',
				'label'       => __( 'Role', 'accesspress' ),
				'key'         => 'role',
				'wp_field'    => 'role',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'wp_user',
				'visibility'  => 'admin',
				'editable'    => false,
				'admin_only'  => true,
				'options'     => $role_options,
				'description' => __( 'Assign one or more WordPress roles to this user.', 'accesspress' ),
				'tooltip'     => __( 'Roles determine the capabilities and access granted to the user.', 'accesspress' ),
				'renderer'    => 'bootstrap_multiselect',
				'attributes' => array(
					'data-live-search' => 'true',
				),
			),
		);

		$normalized = array();
		foreach ( $definitions as $index => $definition ) {
			$normalized[] = self::normalize_field( $definition, $index );
		}

		return $normalized;
	}
	/**
	 * AccessPress custom fields that may be used multiple times within a layout.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function get_wordpress_locale_options(): array {
		$options = array();
		$available = function_exists( 'get_available_languages' ) ? get_available_languages() : array();
		$translations = function_exists( 'wp_get_available_translations' ) ? wp_get_available_translations() : array();

		if ( empty( $available ) ) {
			$active_locale = function_exists( 'get_locale' ) ? get_locale() : 'en_US';
			$available     = array( $active_locale );
		}

		foreach ( $available as $locale ) {
			$locale = (string) $locale;
			$label  = $translations[ $locale ]['native_name'] ?? self::format_locale_label( $locale );
			if ( '' !== $locale ) {
				$options[ $locale ] = $label;
			}
		}

		if ( empty( $options ) ) {
			$options = array(
				'en_US' => 'English (United States)',
				'en_GB' => 'English (United Kingdom)',
			);
		}

		return $options;
	}
	/**
	 * Get WordPress role options.
	 *
	 * @return array<string, string> The role options.
	 * @since 1.0.0
	 */
	private static function get_wordpress_role_options(): array {
		if ( ! function_exists( 'wp_roles' ) ) {
			return array(
				'administrator' => 'Administrator',
				'editor'        => 'Editor',
				'author'        => 'Author',
				'contributor'   => 'Contributor',
				'subscriber'    => 'Subscriber',
			);
		}

		$roles = wp_roles();
		if ( ! is_object( $roles ) || ! isset( $roles->roles ) || ! is_array( $roles->roles ) ) {
			return array(
				'administrator' => 'Administrator',
				'editor'        => 'Editor',
				'author'        => 'Author',
				'contributor'   => 'Contributor',
				'subscriber'    => 'Subscriber',
			);
		}

		$options = array();
		foreach ( $roles->roles as $slug => $role ) {
			$name = is_array( $role ) && isset( $role['name'] ) ? (string) $role['name'] : ucfirst( (string) $slug );
			$options[ (string) $slug ] = $name;
		}

		return $options;
	}
	/**
	 * Format a locale label for display.
	 *
	 * @param string $locale The locale code.
	 * @return string The formatted locale label.
	 * @since 1.0.0
	 */
	private static function format_locale_label( string $locale ): string {
		$locale = trim( $locale );
		if ( '' === $locale ) {
			return '';
		}

		$parts = explode( '_', $locale );
		$language = strtolower( $parts[0] ?? '' );
		$country = strtoupper( $parts[1] ?? '' );
		if ( '' === $language ) {
			return $locale;
		}

		if ( '' !== $country ) {
			return ucfirst( $language ) . ' (' . $country . ')';
		}

		return ucfirst( $language );
	}
	/**
	 * Get layout fields for the profile.
	 *
	 * @return array The layout fields.
	 * @since 1.0.0
	 */
	public static function get_layout_fields(): array {
		return array(
			array(
				'id'          => 'title',
				'type'        => 'title',
				'label'       => __( 'Title', 'accesspress' ),
				'key'         => 'title',
				'wp_field'    => '',
				'placeholder' => __( 'Section title', 'accesspress' ),
				'required'    => false,
				'multiple'    => false,
				'source'      => 'layout',
			),
			array(
				'id'          => 'text_block',
				'type'        => 'text',
				'label'       => __( 'Text', 'accesspress' ),
				'key'         => 'text',
				'wp_field'    => '',
				'placeholder' => __( 'Paragraph text', 'accesspress' ),
				'required'    => false,
				'multiple'    => false,
				'source'      => 'layout',
			),
			array(
				'id'          => 'spacer',
				'type'        => 'spacer',
				'label'       => __( 'Spacer', 'accesspress' ),
				'key'         => 'spacer',
				'wp_field'    => '',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'layout',
			),
			array(
				'id'          => 'separator',
				'type'        => 'separator',
				'label'       => __( 'Separator', 'accesspress' ),
				'key'         => 'separator',
				'wp_field'    => '',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => false,
				'source'      => 'layout',
			),
			array(
				'id'          => 'heading',
				'type'        => 'heading',
				'label'       => __( 'Heading', 'accesspress' ),
				'key'         => 'heading',
				'wp_field'    => '',
				'placeholder' => __( 'Heading text', 'accesspress' ),
				'required'    => false,
				'multiple'    => false,
				'source'      => 'layout',
			),
		);
	}
	/**
	 * Get layout fields for the profile.
	 *
	 * @return array The layout fields.
	 * @since 1.0.0
	 */
	public static function get_custom_fields(): array {
		return array(
			array(
				'id'          => 'text',
				'type'        => 'text',
				'label'       => __( 'Text', 'accesspress' ),
				'key'         => 'text',
				'wp_field'    => '',
				'placeholder' => __( 'Enter a value', 'accesspress' ),
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'email',
				'type'        => 'email',
				'label'       => __( 'Email', 'accesspress' ),
				'key'         => 'email',
				'wp_field'    => '',
				'placeholder' => 'name@example.com',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'textarea',
				'type'        => 'textarea',
				'label'       => __( 'Textarea', 'accesspress' ),
				'key'         => 'textarea',
				'wp_field'    => '',
				'placeholder' => __( 'Message', 'accesspress' ),
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'number',
				'type'        => 'number',
				'label'       => __( 'Number', 'accesspress' ),
				'key'         => 'number',
				'wp_field'    => '',
				'placeholder' => '0',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'select',
				'type'        => 'select',
				'label'       => __( 'Select', 'accesspress' ),
				'key'         => 'select',
				'wp_field'    => '',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'radio',
				'type'        => 'radio',
				'label'       => __( 'Radio', 'accesspress' ),
				'key'         => 'radio',
				'wp_field'    => '',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
			array(
				'id'          => 'checkbox',
				'type'        => 'checkbox',
				'label'       => __( 'Checkbox', 'accesspress' ),
				'key'         => 'checkbox',
				'wp_field'    => '',
				'placeholder' => '',
				'required'    => false,
				'multiple'    => true,
				'source'      => 'user_meta',
			),
		);
	}
	/**
	 * A third-party field registry for plugin-added profile fields.
	 *
	 * Plugins can add definitions through the `accesspress_profile_third_party_fields`
	 * filter or a helper registration call. This keeps the UI list dynamic without
	 * requiring direct access to each plugin's profile HTML.
	 *
	 * Example:
	 * add_filter(
	 *     'accesspress_profile_third_party_fields',
	 *     static function ( $fields ) {
	 *         $fields[] = array(
	 *             'id' => 'my_field',
	 *             'type' => 'text',
	 *             'label' => 'My field',
	 *             'key' => 'my_field',
	 *             'wp_field' => 'my_field',
	 *             'source' => 'user_meta',
	 *         );
	 *         return $fields;
	 *     }
	 * );
	 *
	 * @return array<int, array<string, mixed>>
	 * @since 1.0.0
	 */
	public static function get_third_party_fields(): array {
		$fields = apply_filters( 'accesspress_profile_third_party_fields', array() );
		if ( ! is_array( $fields ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $fields as $index => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$field['group']    = 'third_party';
			$field['source']   = isset( $field['source'] ) ? sanitize_key( (string) $field['source'] ) : 'user_meta';
			$field['wp_field'] = isset( $field['wp_field'] ) ? sanitize_key( (string) $field['wp_field'] ) : self::resolve_wp_field_name( (string) ( $field['key'] ?? '' ), (string) ( $field['type'] ?? 'text' ) );
			$normalized[] = self::normalize_field( $field, $index );
		}

		return $normalized;
	}
	/**
	 * Register a third-party field definition for the profile field library.
	 *
	 * @param array<string, mixed> $field Field definition.
	 * @return void
	 * @example
	 * AccessPress\Includes\UserManagement\Profile\ProfileFields::register_third_party_field(
	 *     array(
	 *         'id' => 'my_field',
	 *         'type' => 'text',
	 *         'label' => 'My field',
	 *         'key' => 'my_field',
	 *         'wp_field' => 'my_field',
	 *         'source' => 'user_meta',
	 *     )
	 * );
	 * @since 1.0.0
	 */
	public static function register_third_party_field( array $field ): void {
		add_filter(
			'accesspress_profile_third_party_fields',
			static function ( array $fields ) use ( $field ): array {
				$fields[] = $field;
				return $fields;
			}
		);
	}
	/**
	 * Return the field palette used by the admin profile builder.
	 *
	 * @return array<string, array<int, array<string, mixed>>>
	 * @example
	 * AccessPress\Includes\UserManagement\Profile\ProfileFields::get_palette();
	 * @since 1.0.0
	 */
	public static function get_palette(): array {
		return array(
			'built_in'    => self::get_builtin_fields(),
			'layout'      => self::get_layout_fields(),
			'custom'      => self::get_custom_fields(),
			'third_party' => self::get_third_party_fields(),
		);
	}
	/**
	 * Normalize a field object before it is stored in the tab layout.
	 *
	 * @param array<string, mixed> $field Raw field payload.
	 * @param int                  $index Order index.
	 * @return array<string, mixed>
	 * @since 1.0.0
	 */
	public static function normalize_field( array $field, int $index = 0 ): array {
		$type = isset( $field['type'] ) ? sanitize_key( (string) $field['type'] ) : 'text';
		$key  = isset( $field['key'] ) ? sanitize_key( (string) $field['key'] ) : $type . '-' . ( $index + 1 );
		if ( '' === $key ) {
			$key = $type . '-' . ( $index + 1 );
		}

		$wp_field = isset( $field['wp_field'] ) ? sanitize_key( (string) $field['wp_field'] ) : self::resolve_wp_field_name( $key, $key );
		$source    = isset( $field['source'] ) ? sanitize_key( (string) $field['source'] ) : ( '' === $wp_field ? 'user_meta' : 'wp_user' );

		$visibility = self::normalize_visibility( $field );
		$editable   = self::normalize_editable( $field, $visibility );
		$admin_only = self::normalize_admin_only( $field, $visibility );

		return array(
			'id'          => isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : $key,
			'type'        => $type,
			'label'       => ! empty( $field['label'] ) ? (string) $field['label'] : ucwords( str_replace( array( '-', '_' ), ' ', $type ) ),
			'key'         => $key,
			'wp_field'    => $wp_field,
			'source'      => $source,
			'placeholder' => isset( $field['placeholder'] ) ? (string) $field['placeholder'] : '',
			'required'    => ! empty( $field['required'] ),
			'order'       => absint( $field['order'] ?? $index ),
			'group'       => sanitize_key( (string) ( $field['group'] ?? 'custom' ) ),
			'visibility'  => $visibility,
			'editable'    => $editable,
			'admin_only'  => $admin_only,
			'multiple'    => ! empty( $field['multiple'] ),
			'options'     => is_array( $field['options'] ?? null ) ? $field['options'] : array(),
			'selected'    => is_array( $field['selected'] ?? null ) ? $field['selected'] : ( isset( $field['selected'] ) ? (string) $field['selected'] : array() ),
			'description' => isset( $field['description'] ) ? (string) $field['description'] : '',
			'tooltip'     => isset( $field['tooltip'] ) ? (string) $field['tooltip'] : '',
			'help'        => isset( $field['help'] ) ? (string) $field['help'] : '',
			'class'       => isset( $field['class'] ) ? (string) $field['class'] : '',
			'attributes'  => is_array( $field['attributes'] ?? null ) ? $field['attributes'] : array(),
			'validation'  => is_array( $field['validation'] ?? null ) ? $field['validation'] : array(),
			'country_data' => is_array( $field['country_data'] ?? null ) ? $field['country_data'] : array(),
			'renderer'    => isset( $field['renderer'] ) ? sanitize_key( (string) $field['renderer'] ) : '',
			'meta'        => is_array( $field['meta'] ?? null ) ? $field['meta'] : array(),
		);
	}

	private static function normalize_visibility( array $field ): string {
		$visibility = isset( $field['visibility'] ) ? strtolower( (string) $field['visibility'] ) : 'all';
		if ( in_array( $visibility, array( 'all', 'user', 'admin', 'readonly', 'hidden' ), true ) ) {
			return $visibility;
		}

		return 'all';
	}

	private static function normalize_editable( array $field, string $visibility ): bool {
		if ( isset( $field['editable'] ) ) {
			return ! empty( $field['editable'] );
		}

		if ( isset( $field['user_editable'] ) ) {
			return ! empty( $field['user_editable'] );
		}

		if ( 'admin' === $visibility ) {
			return false;
		}

		return 'readonly' !== $visibility && 'hidden' !== $visibility;
	}

	private static function normalize_admin_only( array $field, string $visibility ): bool {
		if ( isset( $field['admin_only'] ) ) {
			return ! empty( $field['admin_only'] );
		}

		return 'admin' === $visibility;
	}

	/**
	 * Determine whether a field should be shown in the rendered profile form.
	 *
	 * @param array<string, mixed> $field Field definition.
	 * @param bool                 $is_admin Whether the current viewer is an admin.
	 * @return bool
	 */
	public static function can_render_field( array $field, bool $is_admin = false ): bool {
		$visibility = self::normalize_visibility( $field );
		if ( 'hidden' === $visibility ) {
			return false;
		}

		if ( 'admin' === $visibility && ! $is_admin ) {
			return false;
		}

		if ( ! empty( $field['admin_only'] ) && ! $is_admin ) {
			return false;
		}

		return true;
	}

	/**
	 * Determine whether a field is editable by the current viewer.
	 *
	 * @param array<string, mixed> $field Field definition.
	 * @param bool                 $is_admin Whether the current viewer is an admin.
	 * @return bool
	 */
	public static function can_edit_field( array $field, bool $is_admin = false ): bool {
		if ( ! self::can_render_field( $field, $is_admin ) ) {
			return false;
		}

		if ( ! empty( $field['admin_only'] ) ) {
			return $is_admin;
		}

		if ( isset( $field['editable'] ) ) {
			return ! empty( $field['editable'] );
		}

		if ( isset( $field['user_editable'] ) ) {
			return ! empty( $field['user_editable'] );
		}

		return 'readonly' !== self::normalize_visibility( $field );
	}

	/**
	 * Filter field definitions down to those that should be shown to the current viewer.
	 *
	 * @param array<int, array<string, mixed>> $fields Field definitions.
	 * @param bool                            $is_admin Whether the current viewer is an admin.
	 * @return array<int, array<string, mixed>>
	 */
	public static function filter_visible_fields( array $fields, bool $is_admin = false ): array {
		$filtered = array();
		foreach ( $fields as $field ) {
			if ( is_array( $field ) && self::can_render_field( $field, $is_admin ) ) {
				$filtered[] = $field;
			}
		}

		return $filtered;
	}
	/**
	 * Return the default field definition for a provided field type.
	 *
	 * @param string $type Field type key.
	 * @return array<string, mixed>
	 * @since 1.0.0
	 */
	public static function get_field_defaults( string $type ): array {
		$type = sanitize_key( $type );
		$palette = self::get_palette();
		foreach ( array_merge( $palette['built_in'], $palette['custom'], $palette['third_party'] ) as $field ) {
			if ( (string) ( $field['type'] ?? '' ) === $type ) {
				return self::normalize_field( $field );
			}
		}

		return self::normalize_field(
			array(
				'type'  => 'text',
				'label' => __( 'Text', 'accesspress' ),
			)
		);
	}
}