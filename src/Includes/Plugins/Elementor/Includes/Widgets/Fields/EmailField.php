<?php
/**
 * Email field widget for AccessPress forms.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class EmailField extends FieldWidget {
	public const SLUG = 'accesspress_email_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Email Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'user_email';
	}

	protected function field_type(): string {
		return 'email';
	}
}
