<?php

namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress;

use Elementor\Controls_Manager;
use AccessPress\Includes\Functions\Helpers\PostHelper;
use AccessPress\Includes\Functions\Helpers\ContentHelper;
use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiTOC extends Widgets {

    public const SLUG = 'accesspress_wiki_toc';

    protected function get_default_title(): string {
        return __( 'Wiki TOC', 'accesspress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-table-of-contents';
    }

    protected function get_default_category(): string {
        return 'accesspress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'toc', 'table of contents' ];
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'content_section', [ 'label' => __( 'Content', 'accesspress' ) ] );

        $this->add_control(
            'min',
            [
                'label'   => __( 'Minimum Heading Level', 'accesspress' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 2,
                'min'     => 2,
                'max'     => 6,
            ]
        );

        $this->add_control(
            'max',
            [
                'label'   => __( 'Maximum Heading Level', 'accesspress' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 2,
                'max'     => 6,
            ]
        );

        $this->end_controls_section();
    }

    public function render(): void {
        if ( ! \is_singular( 'wiki' ) ) {
            return;
        }

        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context( $settings );

        if ( empty( $context['has_items'] ) ) {
            return;
        }

        /**
         * Filter the Wiki TOC widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'accesspress/elementor/widgets/wiki_toc/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/toc',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare TOC context.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context( array $settings ): array {
        $levels   = $this->resolve_levels( $settings );
        $headings = $this->collect_headings( $levels['min'], $levels['max'] );

        if ( empty( $headings ) ) {
            return [ 'has_items' => false ];
        }

        $this->persist_levels( $levels['min'], $levels['max'] );

            return [
                'has_items'       => true,
                'items'           => $headings,
                'min_level'       => $levels['min'],
                'max_level'       => $levels['max'],
                'heading'         => __( 'Table of Contents', 'accesspress' ),
                'wrapper_classes' => [ 'accesspress-wiki-toc' ],
            ];
    }

    /**
     * Resolve the heading levels to include.
     *
     * @param array<string, mixed> $settings Widget settings.
     *
     * @return array{min:int,max:int}
     */
    private function resolve_levels( array $settings ): array {
        $options = \get_option( 'accesspress_wiki_settings', [] );
        if ( empty( $options ) ) {
            $options = \get_option( 'accesspress_docs_settings', [] );
        }

        $min = $this->sanitize_level( $settings['min'] ?? ( $options['toc_min_level'] ?? 2 ) );
        $max = $this->sanitize_level( $settings['max'] ?? ( $options['toc_max_level'] ?? 4 ) );

        if ( $min > $max ) {
            $max = $min;
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }

    /**
     * Collect heading items within the specified levels.
     *
     * @param int $min Minimum heading level.
     * @param int $max Maximum heading level.
     *
     * @return array<int, array<string, mixed>>
     */
    private function collect_headings( int $min, int $max ): array {
        $post_id = PostHelper::current_id();

        if ( ! $post_id ) {
            return [];
        }

        $content = \get_post_field( 'post_content', $post_id );

        if ( ! is_string( $content ) || '' === $content ) {
            return [];
        }

        $processed = \apply_filters( 'the_content', $content );

        $pattern = sprintf( '/<h([%1$d-%2$d])([^>]*)>(.*?)<\/h\\1>/isu', $min, $max );

        if ( ! preg_match_all( $pattern, (string) $processed, $matches, PREG_SET_ORDER ) ) {
            return [];
        }

        $items = [];

        foreach ( $matches as $match ) {
            $level   = (int) $match[1];
            $attrs   = $match[2];
            $heading = \wp_strip_all_tags( $match[3] );

            if ( '' === $heading ) {
                continue;
            }

            $id = $this->extract_heading_id( $attrs, $heading );

            $items[] = [
                'level' => $level,
                'title' => $heading,
                'id'    => $id,
            ];
        }

        return $items;
    }

    /**
     * Persist selected heading levels to options for consistency with shortcode behaviour.
     */
    private function persist_levels( int $min, int $max ): void {
        $options = \get_option( 'accesspress_wiki_settings', [] );

        if ( ! is_array( $options ) ) {
            $options = [];
        }

        $updated = array_merge( $options, [
            'toc_min_level' => $min,
            'toc_max_level' => $max,
        ] );

        \update_option( 'accesspress_wiki_settings', $updated, false );
    }

    /**
     * Sanitize a heading level between 2 and 6 inclusive.
     *
     * @param mixed $level Raw level value.
     */
    private function sanitize_level( $level ): int {
        $value = (int) $level;

        if ( $value < 2 ) {
            return 2;
        }

        if ( $value > 6 ) {
            return 6;
        }

        return $value;
    }

    /**
     * Determine an ID for the heading, using the attribute if present.
     */
    private function extract_heading_id( string $attributes, string $title ): string {
        if ( preg_match( '/id="([^"]+)"/i', $attributes, $matches ) ) {
            return ContentHelper::heading_id( $matches[1] );
        }

        return ContentHelper::heading_id( $title );
    }
}


