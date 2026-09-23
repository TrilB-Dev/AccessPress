<?php
/**
 * Role field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RoleField extends FieldWidget {
	public const SLUG = 'accesspress_role_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Role Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'role';
	}
}
