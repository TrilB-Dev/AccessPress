<?php
/**
 * PostType class for managing custom post types in the plugin.
 *
 * @package AccessPress\Includes\Core
 */
namespace AccessPress\Includes\Core;

use AccessPress\Includes\Settings\Settings;
use AccessPress\Includes\Functions\Helpers\PermalinkHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostType {

	/**
	 * AccessPress page post type identifier.
	 *
	 * @var string
	 */
	public const ACCESSPRESS = 'accesspress';
	/**
	 * Register the custom post types.
	 * 
	 * @since 1.0.0
	 */
	public function register(): void {
		register_post_type( self::ACCESSPRESS, self::page_args() );
		add_filter( 'post_type_link', array( PermalinkHelper::class, 'filter_page_permalink' ), 10, 2 );
		PermalinkHelper::rewrite_rule();
	}
	/**
	 * Get the AccessPress container post type identifier.
	 *
	 * @return string
	 */
	public static function get_accesspress_post_type_name(): string {
		return self::ACCESSPRESS;
	}
	/**
	 * Get the public AccessPress page post type identifier.
	 *
	 * @return string
	 */
	public static function get_post_type_name(): string {
		return self::ACCESSPRESS;
	}
	/**
	 * Get the rewrite slug for the public AccessPress page post type.
	 *
	 * @return string
	 */
	public static function page_rewrite_slug(): string {
		return self::setting_slug( 'root_slug', 'accesspress' );
	}
	/**
	 * Build the public AccessPress page post type definition.
	 *
	 * @return array<string, mixed> Registration arguments.
	 */
	public static function page_args(): array {
		return apply_filters(
			'accesspress_page_post_type_args',
			array(
				'labels'          => array(
					'name'          => __( 'AccessPress Pages', 'accesspress' ),
					'singular_name' => __( 'AccessPress Page', 'accesspress' ),
					'add_new_item'  => __( 'Add New AccessPress Page', 'accesspress' ),
					'edit_item'     => __( 'Edit AccessPress Page', 'accesspress' ),
				),
				'public'          => true,
				'show_ui'         => false,
				'show_in_rest'    => true,
				'has_archive'     => false,
				'rewrite'         => array( 'slug' => self::page_rewrite_slug() ),
				'supports'        => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
				'capability_type' => array(  ),
				'map_meta_cap'    => true,
			),
			self::ACCESSPRESS
		);
	}

	public static function get_post_type_names(): array {
		return array( self::ACCESSPRESS );
	}

	private static function setting_slug( string $key, string $fallback ): string {
		$value = sanitize_title( (string) Settings::get( $key, $fallback ) );
		return $value !== '' ? $value : $fallback;
	}
}



