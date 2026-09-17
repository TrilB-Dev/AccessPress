<?php
/**
 * Docs Search Modal widget template.
 *
 * @package AccessPress
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! isset( $widget ) || ! $widget instanceof Widget_Base ) {
    return;
}

if ( empty( $context ) || ! is_array( $context ) ) {
    return;
}

$accesspress_wrapper_classes   = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-docs-searchmodal' ];
$accesspress_action_url        = isset( $context['action_url'] ) ? (string) $context['action_url'] : home_url( '/' );
$accesspress_open_button_label = isset( $context['open_button_label'] ) ? (string) $context['open_button_label'] : __( 'Search Docs', 'accesspress' );
$accesspress_search_placeholder = isset( $context['search_placeholder'] ) ? (string) $context['search_placeholder'] : __( 'Search docs...', 'accesspress' );
$accesspress_submit_label      = isset( $context['submit_label'] ) ? (string) $context['submit_label'] : __( 'Search', 'accesspress' );
$accesspress_close_label       = isset( $context['close_label'] ) ? (string) $context['close_label'] : '×';
$accesspress_close_aria_label  = __( 'Close search modal', 'accesspress' );
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $accesspress_wrapper_classes ) ) ) ); ?>">
    <button type="button" class="open-search"><?php echo esc_html( $accesspress_open_button_label ); ?></button>
    <div class="overlay" style="display:none;">
        <div class="inner">
            <button type="button" class="close" aria-label="<?php echo esc_attr( $accesspress_close_aria_label ); ?>"><?php echo esc_html( $accesspress_close_label ); ?></button>
            <form role="search" method="get" action="<?php echo esc_url( $accesspress_action_url ); ?>">
                <?php echo wp_kses_post( FormFieldHelper::input( 'post_type', 'docs', [ 'type' => 'hidden' ] ) ); ?>
                <?php echo wp_kses_post( FormFieldHelper::input( 's', '', [ 'type' => 'search', 'placeholder' => $accesspress_search_placeholder ] ) ); ?>
                <button type="submit"><?php echo esc_html( $accesspress_submit_label ); ?></button>
            </form>
        </div>
    </div>
</div>


