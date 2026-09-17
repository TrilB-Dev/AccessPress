<?php
/**
 * Footer UI component for AccessPress admin pages.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\UI
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\UI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Footer {
	/**
	 * Renders the footer for the admin interface.
	 *
	 * @since 1.0.0
	 */
	public static function render(): void {
		?>
					</section>
				</div>
			</div>
		</main>
		<footer class="accesspress-footer border-top">
			<div class="container-fluid px-3 px-lg-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
				<span class="small text-secondary">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> AccessPress</span>
				<span class="small text-secondary"><?php esc_html_e( 'Powered by', 'accesspress' ); ?> <a class="fw-semibold text-decoration-none" href="https://github.com/TrilB-Dev/AccessPress" target="_blank" rel="noopener noreferrer">AccessPress</a></span>
			</div>
		</footer>
		<?php
	}
}



