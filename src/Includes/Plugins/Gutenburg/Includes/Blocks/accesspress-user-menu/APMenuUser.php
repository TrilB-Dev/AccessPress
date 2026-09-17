<?php
/**
 * Renderer for the AP Menu User block.
 *
 * @package AccessPress
 */
namespace AccessPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use AccessPress\Includes\Functions\Helpers\PostHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class APMenuUser {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post = PostHelper::current();
        if ( ! $post ) {
            return '';
        }

        $label = get_the_title( $post );
        return '<nav class="accesspress-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'accesspress' ) . '"><ol><li class="accesspress-breadcrumbs__item"><span aria-current="page">' . esc_html( $label ) . '</span></li></ol></nav>';
    }
}


