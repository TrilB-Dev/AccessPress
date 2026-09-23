<?php
/**
 * Dynamic custom meta field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$meta_key = isset( $settings['custom_field_key'] ) ? (string) $settings['custom_field_key'] : 'custom_meta';
$field_type = isset( $settings['custom_field_type'] ) ? sanitize_key( (string) $settings['custom_field_type'] ) : 'text';
$label = ! empty( $settings['label'] ) ? $settings['label'] : ucwords( str_replace( array( '-', '_' ), ' ', $meta_key ) );
$id    = 'accesspress_' . sanitize_key( $meta_key ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );
$value = '';

if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	if ( $current_user instanceof WP_User ) {
		$value = (string) get_user_meta( $current_user->ID, $meta_key, true );
	}
}

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';

if ( 'textarea' === $field_type ) {
	echo FormFieldHelper::textarea(
		'custom_meta_' . sanitize_key( $meta_key ),
		$value,
		array(
			'id'       => $id,
			'required' => $required,
			'rows'     => 4,
			'class'    => 'form-control',
		)
	);
} elseif ( 'email' === $field_type ) {
	echo FormFieldHelper::input(
		'custom_meta_' . sanitize_key( $meta_key ),
		$value,
		array(
			'id'       => $id,
			'type'     => 'email',
			'required' => $required,
			'class'    => 'form-control',
		)
	);
} elseif ( 'number' === $field_type ) {
	echo FormFieldHelper::input(
		'custom_meta_' . sanitize_key( $meta_key ),
		$value,
		array(
			'id'       => $id,
			'type'     => 'number',
			'required' => $required,
			'class'    => 'form-control',
		)
	);
} elseif ( 'url' === $field_type ) {
	echo FormFieldHelper::input(
		'custom_meta_' . sanitize_key( $meta_key ),
		$value,
		array(
			'id'       => $id,
			'type'     => 'url',
			'required' => $required,
			'class'    => 'form-control',
		)
	);
} else {
	echo FormFieldHelper::input(
		'custom_meta_' . sanitize_key( $meta_key ),
		$value,
		array(
			'id'       => $id,
			'type'     => 'text',
			'required' => $required,
			'class'    => 'form-control',
		)
	);
}

echo '</div>';
