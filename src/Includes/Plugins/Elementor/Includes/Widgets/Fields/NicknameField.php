<?php
/**
 * Nickname field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class NicknameField extends FieldWidget {
	public const SLUG = 'accesspress_nickname_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Nickname Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'nickname';
	}
}
