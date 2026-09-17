<?php
/**
 * TinyMCE Editor Plugin Assets
 *
 * @package AccessPress
 * @subpackage Plugins\TinyMCE\Assets
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\TinyMCE\Assets;

use AccessPress\Includes\Plugins\TinyMCE\Includes\Settings\Settings;
use AccessPress\Includes\Functions\Helpers\ImageHelper;
use AccessPress\Includes\Functions\Helpers\LoaderHelper;

final class Assets {
	/**
	 * The loader helper instance.
	 *
	 * @var LoaderHelper The loader helper instance.
	 */
	private LoaderHelper $loader;
	/**
	 * Constructor for the TinyMCE plugin assets.
	 *
	 * @param LoaderHelper|null $loader The loader helper instance.
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		$this->loader = $loader ?? new LoaderHelper();
	}

	/**
	 * Registers the TinyMCE plugin assets with the core assets manager.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->loader->register_component(
			$this,
			array(
				array(
					'type'          => 'filter',
					'hook'          => 'accesspress_admin_assets',
					'callback'      => 'register_admin_assets',
					'accepted_args' => 2,
				),
			)
		)->run();
	}
	/**
	 * Registers the admin assets for the TinyMCE plugin.
	 *
	 * @param array  $assets The current assets.
	 * @param string $context The context (e.g., 'frontend', 'admin').
	 * @return array The updated assets with TinyMCE assets included.
	 */
	public function register_admin_assets( $assets = array(), string $context = 'admin' ): array {
		if ( ! is_array( $assets ) ) {
			$assets = array();
		}

		if ( 'admin' !== strtolower( $context ) && 'frontend' !== strtolower( $context ) ) {
			return $assets;
		}

		$base_url = ACCESSPRESS_URL . 'src/Includes/Plugins/TinyMCE/Assets/tinymce/';

		$assets['styles'][]  = array(
			'handle' => 'accesspress-tinymce-skin',
			'src'    => $base_url . 'skins/ui/' . Settings::ui_skin() . '/skin.min.css',
		);
		$assets['scripts'][] = array(
			'handle'    => 'accesspress-tinymce',
			'src'       => $base_url . 'tinymce.min.js',
			'in_footer' => true,
		);
		$assets['scripts'][] = array(
			'handle'    => 'accesspress-tinymce-boot',
			'src'       => ACCESSPRESS_URL . 'src/Includes/Plugins/TinyMCE/Assets/js/tinymce.js',
			'deps'      => array( 'accesspress-tinymce' ),
			'in_footer' => true,
			'localize'  => array(
				'object_name' => 'accesspressTinyMCE',
				'data'        => array(
					'mediaTitle'   => __( 'Insert media', 'accesspress' ),
					'mediaButton'  => __( 'Insert into editor', 'accesspress' ),
					'mediaTooltip' => __( 'Insert media', 'accesspress' ),
				),
			),
		);

		$assets['enqueue_media'] = true;

		return $assets;
	}
	/**
	 * Get an image asset URL from the core Images directory.
	 *
	 * @param string $file The image path relative to Assets/images.
	 * @return string The image URL, or an empty string when the path is invalid.
	 */
	public static function get_image( string $file ): string {
		return ImageHelper::get_image_url( 'accesspress-tinymce', $file );
	}
}



