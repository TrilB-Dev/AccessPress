<?php

namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress;

use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiSearchModal extends Widgets {

    public const SLUG = 'accesspress_wiki_search_modal';

    protected function get_default_title(): string {
        return __( 'Wiki Search Modal', 'accesspress' );
    }

    protected function get_default_icon(): string {
        return 'eicon-search';
    }

    protected function get_default_category(): string {
        return 'accesspress-wiki';
    }

    /**
     * @return array<int, string>
     */
    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base', 'search', 'modal' ];
    }

    protected function register_controls(): void {
        // No controls required.
    }

    public function render(): void {
        $settings = (array) $this->get_settings_for_display();
        $context  = $this->prepare_render_context();

        /**
         * Filter the Wiki Search Modal widget render context.
         *
         * @param array<string, mixed> $context  Prepared context array.
         * @param array<string, mixed> $settings Widget settings.
         * @param self                 $widget   Widget instance.
         */
        $context = \apply_filters( 'accesspress/elementor/widgets/wiki_search_modal/context', $context, $settings, $this );

        Templates::render(
            'widgets/wiki/search-modal',
            [
                'widget'   => $this,
                'settings' => $settings,
                'context'  => $context,
            ]
        );
    }

    /**
     * Prepare modal context.
     *
     * @return array<string, mixed>
     */
    private function prepare_render_context(): array {
        $action_url = \home_url( '/' );

        return [
            'wrapper_classes'     => [ 'accesspress-wiki-searchmodal' ],
            'action_url'          => $action_url,
            'open_button_label'   => __( 'Search Wiki', 'accesspress' ),
            'search_placeholder'  => __( 'Search wiki...', 'accesspress' ),
            'submit_label'        => __( 'Search', 'accesspress' ),
            'close_label'         => '×',
        ];
    }
}


