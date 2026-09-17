<?php
/**
 * Docs List widget template.
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

$accesspress_posts           = isset( $context['posts'] ) && is_array( $context['posts'] ) ? $context['posts'] : [];
$accesspress_has_posts       = ! empty( $context['has_posts'] ) && ! empty( $accesspress_posts );
$accesspress_pagination      = isset( $context['pagination'] ) ? (string) $context['pagination'] : '';
$accesspress_no_results      = isset( $context['no_results_message'] ) ? (string) $context['no_results_message'] : __( 'No docs found.', 'accesspress' );
$accesspress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-docs-list-widget' ];
?>
<div class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $accesspress_wrapper_classes ) ) ) ); ?>">
    <?php if ( $has_posts ) : ?>
        <div class="docs-items">
            <?php foreach ( $accesspress_posts as $accesspress_post_item ) :
                if ( ! is_array( $accesspress_post_item ) ) {
                    continue;
                }

                $accesspress_title     = isset( $accesspress_post_item['title'] ) ? (string) $accesspress_post_item['title'] : '';
                $accesspress_permalink = isset( $accesspress_post_item['permalink'] ) ? (string) $accesspress_post_item['permalink'] : '';
                $accesspress_excerpt   = isset( $accesspress_post_item['excerpt'] ) ? (string) $accesspress_post_item['excerpt'] : '';

                if ( '' === $accesspress_title ) {
                    continue;
                }
                ?>
                <article class="docs-item">
                    <h3 class="docs-item__title">
                        <a href="<?php echo esc_url( $accesspress_permalink ); ?>"><?php echo esc_html( $accesspress_title ); ?></a>
                    </h3>
                    <?php if ( '' !== $accesspress_excerpt ) : ?>
                        <div class="docs-item__excerpt"><?php echo wp_kses_post( $accesspress_excerpt ); ?></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ( '' !== $accesspress_pagination ) : ?>
            <div class="docs-pagination"><?php echo wp_kses_post( $accesspress_pagination ); ?></div>
        <?php endif; ?>
    <?php else : ?>
        <p class="docs-no-results"><?php echo esc_html( $accesspress_no_results ); ?></p>
    <?php endif; ?>
</div>


