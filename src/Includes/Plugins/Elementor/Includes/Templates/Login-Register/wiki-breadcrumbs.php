<?php
/**
 * Docs Breadcrumbs widget template.
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

$accesspress_crumbs          = isset( $context['crumbs'] ) && is_array( $context['crumbs'] ) ? $context['crumbs'] : [];
$accesspress_delimiter       = isset( $context['delimiter'] ) ? (string) $context['delimiter'] : ' / ';
$accesspress_wrapper_classes = isset( $context['wrapper_classes'] ) && is_array( $context['wrapper_classes'] ) ? $context['wrapper_classes'] : [ 'accesspress-docs-breadcrumbs' ];
$accesspress_aria_label      = isset( $context['nav_aria_label'] ) ? (string) $context['nav_aria_label'] : __( 'Breadcrumbs', 'accesspress' );

if ( empty( $accesspress_crumbs ) ) {
    return;
}

$accesspress_sanitize_class = static function ( $class ) {
    $class = is_string( $class ) ? $class : '';

    $class = sanitize_html_class( $class );

    return '' !== $class ? $class : null;
};

$accesspress_crumb_count = count( $accesspress_crumbs );
?>
<nav class="<?php echo esc_attr( implode( ' ', array_filter( array_map( $accesspress_sanitize_class, $accesspress_wrapper_classes ) ) ) ); ?>" aria-label="<?php echo esc_attr( $accesspress_aria_label ); ?>">
    <ol class="accesspress-breadcrumb-list">
        <?php foreach ( $accesspress_crumbs as $accesspress_index => $accesspress_crumb ) :
            if ( ! is_array( $accesspress_crumb ) || empty( $accesspress_crumb['label'] ) ) {
                continue;
            }

            $accesspress_label      = esc_html( (string) $accesspress_crumb['label'] );
            $accesspress_url        = isset( $accesspress_crumb['url'] ) ? (string) $accesspress_crumb['url'] : '';
            $accesspress_is_current = ! empty( $accesspress_crumb['is_current'] );

            $accesspress_item_classes = [ 'breadcrumb-item' ];
            if ( $accesspress_is_current ) {
                $accesspress_item_classes[] = 'is-current';
            }
            ?>
            <li class="<?php echo esc_attr( implode( ' ', $accesspress_item_classes ) ); ?>">
                <?php if ( $accesspress_is_current || '' === $accesspress_url ) : ?>
                    <span class="breadcrumb-label"<?php echo $accesspress_is_current ? ' aria-current="page"' : ''; ?>><?php echo $accesspress_label; ?></span>
                <?php else : ?>
                    <a class="breadcrumb-link" href="<?php echo esc_url( $accesspress_url ); ?>"><?php echo $accesspress_label; ?></a>
                <?php endif; ?>
            </li>
            <?php if ( $accesspress_index < ( $accesspress_crumb_count - 1 ) ) : ?>
                <li class="breadcrumb-delimiter" aria-hidden="true"><?php echo esc_html( $accesspress_delimiter ); ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>


