<?php
/**
 * Locale field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$name     = 'locale';
$label    = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Language', 'accesspress' );
$id       = 'accesspress_' . sanitize_key( $name ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );
$options  = array();
$available = function_exists( 'get_available_languages' ) ? get_available_languages() : array();
if ( empty( $available ) ) {
	$locale = function_exists( 'get_locale' ) ? get_locale() : 'en_US';
	$available = array( $locale );
}
foreach ( $available as $locale_code ) {
	$locale_code = (string) $locale_code;
	if ( '' !== $locale_code ) {
		$options[ $locale_code ] = $locale_code;
	}
}
if ( empty( $options ) ) {
	$options = array(
		'en_US' => 'English (United States)',
		'en_GB' => 'English (United Kingdom)',
	);
}

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';
echo FormFieldHelper::select(
	$name,
	$options,
	'',
	array(
		'id'       => $id,
		'required' => $required,
		'class'    => 'form-select',
		'attributes' => array(
			'data-live-search' => 'true',
		),
	)
);
echo '</div>';
