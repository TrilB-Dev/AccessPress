<?php
/**
 * Register form template for AccessPress Elementor.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Templates\LoginRegister
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = isset( $settings ) && is_array( $settings ) ? $settings : array();
$redirect = ! empty( $settings['redirect_to'] ) ? $settings['redirect_to'] : home_url( '/' );

echo do_shortcode( sprintf( '[accesspress_register redirect_to="%s"]', esc_attr( $redirect ) ) );
