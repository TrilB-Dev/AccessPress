<?php
/**
 * Bio/description field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DescriptionField extends FieldWidget {
	public const SLUG = 'accesspress_description_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Bio Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'description';
	}
}
