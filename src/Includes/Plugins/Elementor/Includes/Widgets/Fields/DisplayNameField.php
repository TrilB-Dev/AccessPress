<?php
/**
 * Display name field widget for AccessPress forms.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DisplayNameField extends FieldWidget {
	public const SLUG = 'accesspress_display_name_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Display Name Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'display_name';
	}
}
