<?php
/**
 * Dynamic custom meta field widget for AccessPress forms.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

use AccessPress\Includes\UserManagement\Profile\ProfileFields;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CustomMetaField extends FieldWidget {
	public const SLUG = 'accesspress_custom_meta_field';

	protected function field_name(): string {
		return 'custom-meta';
	}

	protected function get_default_title(): string {
		return __( 'AccessPress Custom Meta Field', 'accesspress' );
	}

	protected function get_default_icon(): string {
		return 'eicon-meta-data';
	}

	protected function register_controls(): void {
		parent::register_controls();

		$this->add_control(
			'custom_field_key',
			array(
				'label'       => __( 'Available custom fields', 'accesspress' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => self::get_available_custom_fields(),
				'default'     => self::get_default_field_key(),
				'description' => __( 'Select a registered user meta key to render on the frontend.', 'accesspress' ),
			)
		);

		$this->add_control(
			'custom_field_type',
			array(
				'label'   => __( 'Field type', 'accesspress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'text'     => __( 'Text', 'accesspress' ),
					'email'    => __( 'Email', 'accesspress' ),
					'number'   => __( 'Number', 'accesspress' ),
					'textarea' => __( 'Textarea', 'accesspress' ),
					'url'      => __( 'URL', 'accesspress' ),
				),
				'default' => 'text',
			)
		);
	}

	public static function get_available_custom_fields(): array {
		$options = array();
		$fields  = array_merge(
			ProfileFields::get_custom_fields(),
			ProfileFields::get_third_party_fields()
		);

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$key   = (string) ( $field['key'] ?? $field['id'] ?? '' );
			$label = (string) ( $field['label'] ?? $key );
			if ( '' === $key ) {
				continue;
			}

			$options[ $key ] = sprintf( '%s (%s)', $label, $key );
		}

		if ( empty( $options ) ) {
			$options['custom_meta'] = __( 'Custom meta field', 'accesspress' );
		}

		return $options;
	}

	private static function get_default_field_key(): string {
		$options = self::get_available_custom_fields();
		$keys    = array_keys( $options );
		return $keys[0] ?? 'custom_meta';
	}
}
