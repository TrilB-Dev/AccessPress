<?php
/**
 * Demo Plugin Assets
 *
 * @package AccessPress
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Demo\Assets;

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
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'accesspress_frontend_assets', 'callback' => 'register_frontend_assets' ],
        ] )->run();
    }

    public function register_frontend_assets( array $assets ): array {
        $assets['scripts'][] = [
            'handle' => 'accesspress-demo',
            'src' => ACCESSPRESS_PLUGINS_URL . '/Demo/Assets/dist/js/demo.js',
            'in_footer' => true,
        ];

        return $assets;
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'accesspress-demo', $file );
    }
}