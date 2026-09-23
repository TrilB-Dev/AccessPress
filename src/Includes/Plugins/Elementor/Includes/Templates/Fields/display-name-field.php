<?php
/**
 * Display name field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$name     = 'display_name';
$label    = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Display Name', 'accesspress' );
$id       = 'accesspress_' . sanitize_key( $name ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';
echo FormFieldHelper::input(
	$name,
	'',
	array(
		'id'       => $id,
		'type'     => 'text',
		'required' => $required,
		'class'    => 'form-control',
	)
);
echo '</div>';
