<?php
/**
 * First name field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FirstNameField extends FieldWidget {
	public const SLUG = 'accesspress_first_name_field';

	protected function get_default_title(): string {
		return __( 'AccessPress First Name Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'first_name';
	}
}
