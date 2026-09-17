<?php
/**
 * Docs Related widget template.
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
$accesspress_heading         = isset( $context['heading'] ) ? (string) $context['heading'] : __( 'Related Docs', 'accesspress' );
$accesspress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-related-docs' ];

if ( ! $accesspress_has_posts ) {
    return;
}
?>
<section class="<?php echo esc_attr( implode( ' ', array_filter( array_map( 'sanitize_html_class', $accesspress_wrapper_classes ) ) ) ); ?>">
    <h3 class="related-docs__heading"><?php echo esc_html( $accesspress_heading ); ?></h3>
    <?php $accesspress_views_label = esc_attr__( 'View count', 'accesspress' ); ?>
    <ul class="related-docs__list">
        <?php foreach ( $accesspress_posts as $accesspress_post_item ) :
            if ( ! is_array( $accesspress_post_item ) ) {
                continue;
            }

            $accesspress_title     = isset( $accesspress_post_item['title'] ) ? (string) $accesspress_post_item['title'] : '';
            $accesspress_permalink = isset( $accesspress_post_item['permalink'] ) ? (string) $accesspress_post_item['permalink'] : '';
            $accesspress_views     = isset( $accesspress_post_item['views'] ) ? (int) $accesspress_post_item['views'] : 0;

            if ( '' === $accesspress_title ) {
                continue;
            }
            ?>
            <li class="related-docs__item">
                <a class="related-docs__link" href="<?php echo esc_url( $accesspress_permalink ); ?>"><?php echo esc_html( $accesspress_title ); ?></a>
                <span class="related-docs__views" aria-label="<?php echo $accesspress_views_label; ?>">
                    (<?php echo esc_html( number_format_i18n( $accesspress_views ) ); ?>)
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</section>


