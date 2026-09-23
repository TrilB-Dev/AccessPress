<?php
/**
 * Role field template for AccessPress Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$name     = 'role';
$label    = ! empty( $settings['label'] ) ? $settings['label'] : __( 'Role', 'accesspress' );
$id       = 'accesspress_' . sanitize_key( $name ) . '_' . ( isset( $widget ) ? $widget->get_id() : wp_generate_uuid4() );
$required = ! empty( $settings['required'] );
$options  = array();
if ( function_exists( 'wp_roles' ) ) {
	$roles = wp_roles();
	if ( is_object( $roles ) && isset( $roles->roles ) && is_array( $roles->roles ) ) {
		foreach ( $roles->roles as $slug => $role ) {
			$name_value = is_array( $role ) && isset( $role['name'] ) ? $role['name'] : ucfirst( (string) $slug );
			$options[ (string) $slug ] = (string) $name_value;
		}
	}
}
if ( empty( $options ) ) {
	$options = array(
		'administrator' => __( 'Administrator', 'accesspress' ),
		'editor'        => __( 'Editor', 'accesspress' ),
		'author'        => __( 'Author', 'accesspress' ),
		'subscriber'    => __( 'Subscriber', 'accesspress' ),
	);
}

echo '<div class="accesspress-elementor-field">';
echo '<label for="' . esc_attr( $id ) . '" class="form-label">' . esc_html( $label ) . '</label>';
echo FormFieldHelper::select(
	$name,
	$options,
	array(),
	array(
		'id'       => $id,
		'required' => $required,
		'multiple' => true,
		'class'    => 'form-select',
		'attributes' => array(
			'data-live-search' => 'true',
		),
	)
);
echo '</div>';
