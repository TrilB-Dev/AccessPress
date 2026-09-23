<?php
/**
 * Bio field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$name     = 'description';
$label    = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Bio', 'accesspress' );
$id       = 'accesspress_' . sanitize_key( $name ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';
echo FormFieldHelper::textarea(
	$name,
	'',
	array(
		'id'       => $id,
		'required' => $required,
		'rows'     => 4,
		'class'    => 'form-control',
	)
);
echo '</div>';
