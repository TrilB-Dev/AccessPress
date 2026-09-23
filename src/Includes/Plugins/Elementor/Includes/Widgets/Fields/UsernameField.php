<?php
/**
 * Username field widget for AccessPress forms.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UsernameField extends FieldWidget {
	public const SLUG = 'accesspress_username_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Username Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'username';
	}
}
