<?php
/**
 * Docs Table of Contents widget template.
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

$accesspress_items          = isset( $context['items'] ) && is_array( $context['items'] ) ? $context['items'] : [];
$accesspress_has_items      = ! empty( $context['has_items'] ) && ! empty( $accesspress_items );
$accesspress_heading        = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Table of Contents', 'accesspress' );
$accesspress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-docs-toc' ];

if ( ! $accesspress_has_items ) {
    return;
}

?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $accesspress_wrapper_classes ) ) ) ); ?>">
    <strong class="docs-toc__heading"><?php echo esc_html( $accesspress_heading ); ?></strong>
    <ul class="docs-toc__list">
        <?php foreach ( $accesspress_items as $accesspress_item ) :
            if ( ! is_array( $accesspress_item ) || empty( $accesspress_item['title'] ) ) {
                continue;
            }

            $accesspress_level = isset( $accesspress_item['level'] ) ? (int) $accesspress_item['level'] : 0;
            $accesspress_title = (string) $accesspress_item['title'];
            $accesspress_id    = isset( $accesspress_item['id'] ) ? (string) $accesspress_item['id'] : '';

            $accesspress_classes = [ 'docs-toc__item' ];
            if ( $accesspress_level >= 2 && $accesspress_level <= 6 ) {
                $accesspress_classes[] = 'level-' . $accesspress_level;
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $accesspress_classes ) ); ?>">
                <a class="docs-toc__link" href="<?php echo esc_url( '#' . $accesspress_id ); ?>"><?php echo esc_html( $accesspress_title ); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


