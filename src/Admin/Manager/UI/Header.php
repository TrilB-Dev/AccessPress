<?php
/**
 * Header UI component for AccessPress admin pages.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\UI
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\UI;

use AccessPress\Assets\Assets;
use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Header {
	/**
	 * Renders the header for AccessPress admin pages.
	 *
	 * @return void
	 */
	public static function render(): void {
		$links = array(
			array(
				'label' => __( 'Documentation', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress',
			),
			array(
				'label' => __( 'Community', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress/discussions',
			),
			array(
				'label' => __( 'Extensions', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress',
			),
			array(
				'label' => __( 'Support', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress/issues',
			),
			array(
				'label' => __( 'Roadmap', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress/issues',
			),
			array(
				'label' => __( 'Account', 'accesspress' ),
				'url'   => 'https://github.com/TrilB-Dev/AccessPress',
			),
		);
		?>
		<header class="accesspress-header border-bottom">
			<nav class="navbar navbar-expand-lg" aria-label="<?php esc_attr_e( 'AccessPress header navigation', 'accesspress' ); ?>"> 
				<div class="container-fluid accesspress-shell px-3 px-lg-4">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=accesspress' ) ); ?>">
						<img class="navbar-brand d-flex align-items-center gap-2" src="<?php echo esc_url( Assets::get_image( 'logo/AccessPress-Logo.svg' ) ); ?>" alt="" />
					</a>
					<?php echo FormFieldHelper::button(
						'<span class="navbar-toggler-icon" aria-hidden="true"></span>',
						array(
							'class'          => 'navbar-toggler',
							'type'           => 'button',
							'data-bs-toggle' => 'collapse',
							'data-bs-target' => '#accesspress-header-menu',
							'aria-controls'  => 'accesspress-header-menu',
							'aria-expanded'  => 'false',
							'aria-label'     => __( 'Toggle header navigation', 'accesspress' ),
							'raw'            => true,
						)
					); ?>
					<div class="collapse navbar-collapse" id="accesspress-header-menu">
						<ul class="navbar-nav ms-auto align-items-lg-start gap-lg-1">
							<?php foreach ( $links as $link ) : ?>
								<li class="nav-item"><a class="nav-link" href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $link['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		<?php
	}
}



