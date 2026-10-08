<?php
/**
 * PostTypes class for registering plugin post types.
 *
 * @package AccessPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace AccessPress\Includes\Pages;

use AccessPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostTypes {
	/**
	 * Register all page-layer post types.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::accesspress_posttype();
	}

	/**
	 * Register the core AccessPress mod post type.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function accesspress_posttype(): bool {
		return PostType::create(
			'accesspress_accesspress',
			array(
				'label'           => __( 'AccessPress', 'accesspress' ),
				'description'     => __( 'An AccessPress entry managed by AccessPress.', 'accesspress' ),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
				'taxonomies'      => array( 'accesspress_category', 'accesspress_tag' ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => false,
				'menu_position'   => 20,
				'menu_icon'       => 'dashicons-archive',
				'has_archive'     => true,
				'show_in_rest'    => true,
				'rewrite'         => array( 'slug' => 'accesspress' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);
	}
}



