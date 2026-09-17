<?php
/**
 * Settings for the Font Awesome AccessPress plugin.
 *
 * @package    AccessPress
 * @subpackage AccessPress/Includes
 */
namespace AccessPress\Includes\Plugins\FontAwesome\Includes\Settings;

use AccessPress\Includes\Settings\Settings as BaseSettings;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
	public function register(): void {
		BaseSettings::register_group(
			'fontawesome',
			array(
				'fontawesome_source'  => 'base',
				'fontawesome_kit_id'  => '',
				'fontawesome_version' => '7.0.0',
			)
		);
	}

	public static function source(): string {
		$source = BaseSettings::get_key( 'fontawesome_source', 'base' );
		return in_array( $source, array( 'base', 'kit' ), true ) ? $source : 'base';
	}

	public static function kit_id(): string {
		return BaseSettings::get_string( 'fontawesome_kit_id' );
	}

	public static function version(): string {
		return BaseSettings::get_string( 'fontawesome_version', '7.0.0' );
	}

	public function get_settings_page(): array {
		return array(
			'slug'           => 'fontawesome',
			'settings_group' => 'fontawesome',
			'label'          => __( 'Font Awesome', 'accesspress' ),
			'title'          => __( 'Font Awesome integration', 'accesspress' ),
			'layout'         => 'table',
			'fields'         => array(
				array(
					'key'          => 'fontawesome_source',
					'label'        => __( 'Icon source', 'accesspress' ),
					'description'  => __( 'Choose how AccessPress loads Font Awesome icons.', 'accesspress' ),
					'tooltip'      => __( 'Use the base package for the bundled icons or a Kit when you need a custom Font Awesome configuration.', 'accesspress' ),
					'tooltip_type' => 'info',
					'type'         => 'select',
					'options'      => array(
						'base' => __( 'Base package', 'accesspress' ),
						'kit'  => __( 'Font Awesome Kit', 'accesspress' ),
					),
					'default'      => 'base',
				),
				array(
					'key'         => 'fontawesome_kit_id',
					'label'       => __( 'Kit ID', 'accesspress' ),
					'description' => __( 'Enter the ID of your Font Awesome Kit.', 'accesspress' ),
					'tooltip'     => __( 'This value is used only when Icon source is set to Font Awesome Kit.', 'accesspress' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'key'          => 'fontawesome_version',
					'label'        => __( 'Base package version', 'accesspress' ),
					'description'  => __( 'Set the version of the bundled Font Awesome package to load.', 'accesspress' ),
					'tooltip'      => __( 'Use a version supported by the installed Font Awesome assets.', 'accesspress' ),
					'tooltip_type' => 'info',
					'type'         => 'text',
					'default'      => '7.0.0',
				),
			),
		);
	}

	public function sanitize( $input ): array {
		$input                        = is_array( $input ) ? $input : array();
		$source                       = SanitizationHelper::key( $input['fontawesome_source'] ?? 'base', 'base' );
		$input['fontawesome_source']  = in_array( $source, array( 'base', 'kit' ), true ) ? $source : 'base';
		$input['fontawesome_kit_id']  = SanitizationHelper::text( $input['fontawesome_kit_id'] ?? '' );
		$input['fontawesome_version'] = SanitizationHelper::text( $input['fontawesome_version'] ?? '7.0.0', '7.0.0' );
		BaseSettings::set_group( 'fontawesome', $input );
		return $input;
	}
}



