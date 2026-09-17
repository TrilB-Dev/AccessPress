<?php
/**
 * Export class for AccessPress plugin.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Tools;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Export extends ToolsManager {
	/**
	 * Constructor for the Export class.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}
	/**
	 * Render the export form.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Export licence data', 'accesspress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Download a protected JSON export of licence records, customer meta, and validation data.', 'accesspress' ); ?></p>
				<?php echo wp_kses_post(
					FormFieldHelper::button(
						esc_html__( 'Export licence JSON', 'accesspress' ),
						array(
							'href'  => UrlHelper::admin_action_nonce( 'accesspress_export', 'accesspress_export' ),
							'class' => 'btn-outline-primary',
						)
					)
				); ?>
			</div>
		</div>
		<?php
	}
	/**
	 * Render the export page content.
	 *
	 * @return void
	 */
	public function render(): void {
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post(
				FormFieldHelper::label(
					'accesspress-export',
					esc_html__( 'Import and export', 'accesspress' ),
					array(
						'description' => __( 'Export or import AccessPress data as a password-protected JSON archive.', 'accesspress' ),
						'tooltip'     => __( 'Exports are protected with a WordPress nonce and should use a password whenever shared with partners.', 'accesspress' ),
					)
				)
			); ?></th>
			<td><?php echo wp_kses_post(
				FormFieldHelper::button(
					esc_html__( 'Export licence JSON', 'accesspress' ),
					array(
						'href'  => UrlHelper::admin_action_nonce( 'accesspress_export', 'accesspress_export' ),
						'class' => 'btn-outline-primary',
					)
				)
			); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo FormFieldHelper::label(
				'accesspress-database-manager',
				esc_html__( 'Database manager', 'accesspress' ),
				array(
					'description' => __( 'Licence records are kept in the plugin database tables and managed through the core lifecycle.', 'accesspress' ),
					'tooltip'     => __( 'Manual database changes are not required for normal licence operations.', 'accesspress' ),
				)
			); ?></th>
			<td><?php esc_html_e( 'Managed automatically', 'accesspress' ); ?></td>
		</tr>
		<?php
	}
}
