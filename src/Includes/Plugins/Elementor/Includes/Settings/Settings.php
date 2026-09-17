<?php
/**
 * Settings for the AccessPress Elementor integration.
 *
 * @package AccessPress
 * @subpackage Includes\Plugins\Elementor\Includes\Settings
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Settings;

use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Settings {
	public const GROUP = 'elementor';

	public function register(): void {
		BaseSettings::register_group( self::GROUP, [
			'elementor_enabled' => true,
			'elementor_widget_wiki_breadcrumbs' => true,
			'elementor_widget_wiki_list' => true,
			'elementor_widget_wiki_reading_time' => true,
			'elementor_widget_wiki_related' => true,
			'elementor_widget_wiki_toc' => true,
			'elementor_widget_wiki_search_modal' => true,
		] );
	}

	public static function enabled(): bool {
		return BaseSettings::get_bool( 'elementor_enabled', true );
	}

	public static function widget_enabled( string $slug ): bool {
		return self::enabled() && BaseSettings::get_bool( 'elementor_widget_' . SanitizationHelper::key( $slug ), true );
	}

	public function get_settings_page(): array {
		return [
			'slug' => self::GROUP,
			'label' => __( 'Elementor', 'accesspress' ),
			'title' => __( 'Elementor integration', 'accesspress' ),
			'layout' => 'box',
			'fields' => [
				[ 'key' => 'elementor_enabled', 'label' => __( 'Enable AccessPress Elementor widgets', 'accesspress' ), 'description' => __( 'Enable the AccessPress widgets available in Elementor.', 'accesspress' ), 'tooltip' => __( 'Disable this to remove AccessPress widgets from the Elementor editor.', 'accesspress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'accesspress' ), 'description' => __( 'Show the current wiki location and hierarchy.', 'accesspress' ), 'tooltip' => __( 'Adds breadcrumb navigation to Elementor layouts.', 'accesspress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_list', 'label' => __( 'Wiki List', 'accesspress' ), 'description' => __( 'Show a list of AccessPress content.', 'accesspress' ), 'tooltip' => __( 'Use this widget to display wiki entries in an Elementor layout.', 'accesspress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'accesspress' ), 'description' => __( 'Show the estimated reading time for a wiki page.', 'accesspress' ), 'tooltip' => __( 'The estimate is based on the page content.', 'accesspress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_related', 'label' => __( 'Wiki Related', 'accesspress' ), 'description' => __( 'Show related wiki content.', 'accesspress' ), 'tooltip' => __( 'Related content helps visitors continue exploring the wiki.', 'accesspress' ), 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'accesspress' ), 'description' => __( 'Show a table of contents for wiki page headings.', 'accesspress' ), 'tooltip' => __( 'The table of contents is generated from headings in the page.', 'accesspress' ), 'tooltip_type' => 'info', 'default' => true ],
				[ 'key' => 'elementor_widget_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'accesspress' ), 'description' => __( 'Add a modal search interface for wiki content.', 'accesspress' ), 'tooltip' => __( 'Visitors can open the modal from the widget and search without leaving the page.', 'accesspress' ), 'default' => true ],
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


