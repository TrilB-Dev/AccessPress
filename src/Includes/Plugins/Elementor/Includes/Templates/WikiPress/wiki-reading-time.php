<?php
/**
 * Docs Reading Time widget template.
 *
 * @package AccessPress
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;

if ( ! isset( $widget ) || ! $widget instanceof Widget_Base ) {
    return;
}

if ( empty( $context ) || ! is_array( $context ) ) {
    return;
}

if ( empty( $context['has_content'] ) ) {
    return;
}

$accesspress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-docs-reading-time' ];
$accesspress_display_text   = isset( $context['display_text'] ) ? (string) $context['display_text'] : '';

if ( '' === $accesspress_display_text ) {
    return;
}
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $accesspress_wrapper_classes ) ) ) ); ?>"><?php echo esc_html( $accesspress_display_text ); ?></div>


