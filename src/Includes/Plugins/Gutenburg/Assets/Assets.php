<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Assets
 *
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\Demo\Assets
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Gutenburg\Assets;

use AccessPress\Includes\Functions\Helpers\LoaderHelper;
use AccessPress\Includes\Functions\Helpers\ImageHelper;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the Demo plugin assets.
     */
    public function register(): void {
        $this->register_editor_script();
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'accesspress_frontend_assets', 'callback' => 'register_frontend_assets' ],
        ] )->run();
    }

    public function register_frontend_assets( array $assets ): array {
        $assets['scripts'][] = [
            'handle' => 'accesspress-gutenburg-blocks',
            'src' => ACCESSPRESS_URL . 'src/includes/Plugins/Gutenburg/Assets/dist/js/blocks.js',
            'in_footer' => true,
        ];

        return $assets;
    }

    public function register_editor_script(): void {
        wp_register_script(
            'accesspress-gutenburg-blocks',
            ACCESSPRESS_URL . 'src/includes/Plugins/Gutenburg/Assets/dist/js/blocks.js',
            [ 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ],
            ACCESSPRESS_VERSION,
            true
        );
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'accesspress-gutenburg', $file );
    }
}


