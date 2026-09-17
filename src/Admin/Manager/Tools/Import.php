<?php
/**
 * Import class for AccessPress plugin.
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

final class Import extends ToolsManager {
	/**
	 * Render the JSON import form below the tools settings form.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}
	/**
	 * Render the import form.
	 *
	 * @return void
	 */
	public function render(): void {
		?>
		<form
			method="post"
			action="<?php echo esc_url( UrlHelper::admin_action( 'accesspress_import' ) ); ?>"
			enctype="multipart/form-data"
			class="card accesspress-import-form shadow-sm mt-4"
		>
			<?php echo wp_kses_post( FormFieldHelper::input( 'action', 'accesspress_import', array( 'type' => 'hidden' ) ) ); ?>
			<?php wp_nonce_field( 'accesspress_import' ); ?>
			<div class="card-body">
				<?php
				echo wp_kses_post(
					FormFieldHelper::label(
						'accesspress-import-file',
						__( 'Import licence JSON', 'accesspress' ),
						array(
							'description'  => __( 'Select a JSON export containing AccessPress records, customer data, and validation metadata.', 'accesspress' ),
							'tooltip'      => __( 'Import should use a valid archive and, where required, a password-protected file to preserve security.', 'accesspress' ),
							'tooltip_icon' => 'fa-file-import',
						)
					)
				);
				echo wp_kses_post(
					FormFieldHelper::input(
						'accesspress_import_file',
						'',
						array(
							'id'       => 'accesspress-import-file',
							'type'     => 'file',
							'class'    => 'mb-3',
							'accept'   => 'application/json,.json',
							'required' => true,
						)
					)
				);
				echo wp_kses_post(
					FormFieldHelper::button(
						__( 'Import JSON', 'accesspress' ),
						array(
							'type'  => 'submit',
							'class' => 'btn-primary',
						)
					)
				);
				?>
			</div>
		</form>
		<?php
	}
}
