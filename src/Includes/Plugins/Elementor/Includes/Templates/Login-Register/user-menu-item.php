<?php
/**
 * User menu item template for AccessPress Elementor.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Templates\LoginRegister
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	$display_name = $current_user->display_name ?: $current_user->user_login;
	$profile_url  = home_url( '/my-account/' );
	$logout_url   = wp_logout_url( home_url( '/' ) );

	echo '<div class="accesspress-elementor-user-menu-item">';
	echo '<a href="' . esc_url( $profile_url ) . '">' . esc_html( $display_name ) . '</a>';
	echo ' <a href="' . esc_url( $logout_url ) . '">(' . esc_html__( 'Logout', 'accesspress' ) . ')</a>';
	echo '</div>';
	return;
}

echo '<a href="' . esc_url( wp_login_url() ) . '">' . esc_html__( 'Login', 'accesspress' ) . '</a>';
