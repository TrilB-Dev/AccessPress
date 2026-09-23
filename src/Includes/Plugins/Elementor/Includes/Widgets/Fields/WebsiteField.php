<?php
/**
 * Website field widget for AccessPress forms.
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class WebsiteField extends FieldWidget {
	public const SLUG = 'accesspress_website_field';

	protected function get_default_title(): string {
		return __( 'AccessPress Website Field', 'accesspress' );
	}

	protected function field_name(): string {
		return 'user_url';
	}
}
