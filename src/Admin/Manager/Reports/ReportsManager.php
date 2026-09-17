<?php
/**
 * Reports admin page manager.
 *
 * @package AccessPress\Admin\Manager\Reports
 */
namespace AccessPress\Admin\Manager\Reports;

use AccessPress\Admin\Manager\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ReportsManager extends Manager {
	public function render(): void {
		$this->header( __( 'Reports', 'accesspress' ) );
		?>
		<div class="row g-4">
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h6 text-muted"><?php esc_html_e( 'Revenue', 'accesspress' ); ?></h2>
						<p class="display-6 mb-0"><?php esc_html_e( '$0.00', 'accesspress' ); ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h6 text-muted"><?php esc_html_e( 'New members', 'accesspress' ); ?></h2>
						<p class="display-6 mb-0"><?php esc_html_e( '0', 'accesspress' ); ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h6 text-muted"><?php esc_html_e( 'Cancelled', 'accesspress' ); ?></h2>
						<p class="display-6 mb-0"><?php esc_html_e( '0', 'accesspress' ); ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm h-100">
					<div class="card-body">
						<h2 class="h6 text-muted"><?php esc_html_e( 'Conversion rate', 'accesspress' ); ?></h2>
						<p class="display-6 mb-0"><?php esc_html_e( '0%', 'accesspress' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
		$this->footer();
	}
}
