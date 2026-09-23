<?php
/**
 * Password field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$name     = 'password';
$label    = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Password', 'accesspress' );
$id       = 'accesspress_' . sanitize_key( $name ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';
echo FormFieldHelper::input(
	$name,
	'',
	array(
		'id'       => $id,
		'type'     => 'password',
		'required' => $required,
		'class'    => 'form-control',
	)
);
echo '</div>';
