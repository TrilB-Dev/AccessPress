<?php
/**
 * Settings for the AccessPress Gutenberg blocks.
 *
 * @package AccessPress
 */
namespace AccessPress\Includes\Plugins\Gutenburg\Includes\Settings;

use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    public const GROUP = 'gutenburg';

    public function register(): void {
        BaseSettings::register_group( self::GROUP, [
            'gutenburg_enabled' => true,
            'gutenburg_block_wiki_breadcrumbs' => true,
            'gutenburg_block_wiki_list' => true,
            'gutenburg_block_wiki_reading_time' => true,
            'gutenburg_block_wiki_related' => true,
            'gutenburg_block_wiki_toc' => true,
            'gutenburg_block_wiki_search_modal' => true,
        ] );
    }

    public static function enabled(): bool {
        return BaseSettings::get_bool( 'gutenburg_enabled', true );
    }

    public static function block_enabled( string $slug ): bool {
        return self::enabled() && BaseSettings::get_bool( 'gutenburg_block_' . SanitizationHelper::key( $slug ), true );
    }

    public function get_settings_page(): array {
        return [
            'slug' => self::GROUP,
            'label' => __( 'Gutenberg', 'accesspress' ),
            'title' => __( 'Gutenberg integration', 'accesspress' ),
            'layout' => 'box',
            'fields' => [
                [ 'key' => 'gutenburg_enabled', 'label' => __( 'Enable AccessPress Gutenberg blocks', 'accesspress' ), 'description' => __( 'Enable the AccessPress blocks available in the block editor.', 'accesspress' ), 'tooltip' => __( 'Disable this to remove AccessPress blocks from the Gutenberg editor.', 'accesspress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'accesspress' ), 'description' => __( 'Show the current wiki location and hierarchy.', 'accesspress' ), 'tooltip' => __( 'Adds breadcrumb navigation to block-based layouts.', 'accesspress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_list', 'label' => __( 'Wiki List', 'accesspress' ), 'description' => __( 'Show a list of AccessPress content.', 'accesspress' ), 'tooltip' => __( 'Use this block to display wiki entries in a page or post.', 'accesspress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'accesspress' ), 'description' => __( 'Show the estimated reading time for a wiki page.', 'accesspress' ), 'tooltip' => __( 'The estimate is based on the page content.', 'accesspress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_related', 'label' => __( 'Wiki Related', 'accesspress' ), 'description' => __( 'Show related wiki content.', 'accesspress' ), 'tooltip' => __( 'Related content helps visitors continue exploring the wiki.', 'accesspress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'accesspress' ), 'description' => __( 'Show a table of contents for wiki page headings.', 'accesspress' ), 'tooltip' => __( 'The table of contents is generated from headings in the page.', 'accesspress' ), 'tooltip_type' => 'info', 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'accesspress' ), 'description' => __( 'Add a modal search interface for wiki content.', 'accesspress' ), 'tooltip' => __( 'Visitors can open the modal and search without leaving the page.', 'accesspress' ), 'default' => true ],
            ],
        ];
    }

    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( array_column( $this->get_settings_page()['fields'], 'key' ) as $key ) {
            $input[ $key ] = ! empty( $input[ $key ] );
            BaseSettings::set( $key, $input[ $key ] );
        }
        return $input;
    }
}

