<?php
/**
 * Locale field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LocaleField extends FieldWidget {
	public const SLUG = 'accesspress_locale_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Locale Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'locale';
	}
}
