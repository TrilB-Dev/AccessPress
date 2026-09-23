<?php
/**
 * Last name field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LastNameField extends FieldWidget {
	public const SLUG = 'accesspress_last_name_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Last Name Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'last_name';
	}
}
