<?php

namespace AccessPress\Includes\Plugins\Demo\Includes\Core;

use AccessPress\Includes\Functions\Helpers\ShortcodeHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes supplied by the Demo AccessPress plugin.
 */
final class Shortcodes {
    /**
     * Return this plugin's shortcode definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function definitions(): array {
        return [
            ShortcodeHelper::define(
                'accesspress_demo',
                [ self::class, 'render_demo' ],
                [ 'message' => __( 'AccessPress Demo', 'accesspress' ) ],
                [
                    'description' => __( 'Render a message from the AccessPress Demo plugin.', 'accesspress' ),
                    'category' => 'demo',
                ]
            ),
        ];
    }

    /**
     * Render the Demo shortcode.
     *
     * @param array<string, mixed> $atts Shortcode attributes.
     * @param string|null $content Enclosed content.
     * @param string $tag Shortcode tag.
     */
    public static function render_demo( array $atts = [], ?string $content = null, string $tag = '' ): string {
        return esc_html( (string) $atts['message'] );
    }
}