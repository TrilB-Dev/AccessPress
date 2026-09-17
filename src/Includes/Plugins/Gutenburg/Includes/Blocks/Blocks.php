<?php
/**
 * Register AccessPress Gutenberg blocks.
 *
 * @package AccessPress
 */
namespace AccessPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use AccessPress\Includes\Plugins\Gutenburg\Includes\Blocks\APMenuUser;
use AccessPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Blocks {
    private const BLOCKS = [
        'accesspress-menu-user' => APMenuUser::class,
    ];

    public static function register(): void {
        foreach ( self::BLOCKS as $block => $renderer ) {
            if ( ! Settings::block_enabled( $block ) ) {
                continue;
            }

            $path = __DIR__ . '/' . $block;
            register_block_type(
                $path,
                [ 'render_callback' => [ $renderer, 'render' ] ]
            );
        }
    }
}

