<?php
/**
 * User registration screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Includes\UserManagement\Profile\ProfileFields;
use AccessPress\Includes\UserManagement\Registration\RegistrationTabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserRegistration extends UserManager {
	/**
	 * Prevent recursive parent initialization when the registration page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the registration management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		if ( isset( $_POST['accesspress_registration_layout'] ) && isset( $_POST['accesspress_registration_builder_nonce'] ) ) {
			if ( check_admin_referer( 'accesspress_registration_builder', 'accesspress_registration_builder_nonce' ) ) {
				$payload = json_decode( wp_unslash( (string) $_POST['accesspress_registration_layout'] ), true );
				if ( is_array( $payload ) ) {
					RegistrationTabs::save_tabs( $payload );
					printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Registration layout saved.', 'accesspress' ) );
				}
			}
		}

		$tabs   = RegistrationTabs::get_tabs();
		$palette = ProfileFields::get_palette();
		$layout = wp_json_encode( $tabs );
		if ( false === $layout ) {
			$layout = '[]';
		}
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Registration settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Build the registration flow using the same field library as the profile, with a locked first page and required account fields.', 'accesspress' ); ?></p>
			</div>
		</div>

		<form method="post" class="accesspress-registration-builder-form">
			<?php wp_nonce_field( 'accesspress_registration_builder', 'accesspress_registration_builder_nonce' ); ?>
			<input type="hidden" name="accesspress_registration_layout" id="accesspress-registration-layout" value="" />

			<div class="accesspress-registration-builder card shadow-sm" data-registration-builder data-registration-config="<?php echo esc_attr( $layout ); ?>">
				<div class="card-body">
					<div class="row g-4">
						<div class="col-lg-4">
							<div class="border rounded p-3 bg-light">
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h3 class="h6 mb-0"><?php esc_html_e( 'Field library', 'accesspress' ); ?></h3>
								</div>

								<div class="accordion" id="accesspress-registration-field-library">
									<div class="accordion-item">
										<h4 class="accordion-header" id="accesspress-registration-fields-builtin-header">
											<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-registration-fields-builtin" aria-expanded="true" aria-controls="accesspress-registration-fields-builtin">
												<?php esc_html_e( 'WordPress profile fields', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-registration-fields-builtin" class="accordion-collapse collapse show" aria-labelledby="accesspress-registration-fields-builtin-header" data-bs-parent="#accesspress-registration-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-registration-field-palette="built_in">
													<?php foreach ( $palette['built_in'] as $field ) : ?>
														<div class="card card-body py-2 px-3 shadow-sm border accesspress-registration-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="built_in" data-field-allow-multiple="false">
															<div class="d-flex justify-content-between align-items-center gap-2">
																<strong class="small"><?php echo esc_html( (string) ( $field['label'] ?? '' ) ); ?></strong>
																<span class="badge bg-secondary-subtle text-secondary"><?php esc_html_e( 'Single', 'accesspress' ); ?></span>
															</div>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>

									<div class="accordion-item">
										<h4 class="accordion-header" id="accesspress-registration-fields-layout-header">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-registration-fields-layout" aria-expanded="false" aria-controls="accesspress-registration-fields-layout">
												<?php esc_html_e( 'Layout blocks', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-registration-fields-layout" class="accordion-collapse collapse" aria-labelledby="accesspress-registration-fields-layout-header" data-bs-parent="#accesspress-registration-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-registration-field-palette="layout">
													<?php foreach ( $palette['layout'] as $field ) : ?>
														<div class="card card-body py-2 px-3 shadow-sm border accesspress-registration-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="layout" data-field-allow-multiple="<?php echo esc_attr( ! empty( $field['multiple'] ) ? 'true' : 'false' ); ?>">
															<div class="d-flex justify-content-between align-items-center gap-2">
																<strong class="small"><?php echo esc_html( (string) ( $field['label'] ?? '' ) ); ?></strong>
																<span class="badge bg-info-subtle text-info-emphasis"><?php esc_html_e( 'Layout', 'accesspress' ); ?></span>
															</div>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h3 class="h6 mb-0"><?php esc_html_e( 'Registration pages', 'accesspress' ); ?></h3>
								<button type="button" class="btn btn-primary btn-sm" data-registration-add-page><?php esc_html_e( 'Add page', 'accesspress' ); ?></button>
							</div>
							<div class="accesspress-registration-pages" data-registration-pages></div>
						</div>
					</div>
					<div class="d-flex justify-content-end mt-4">
						<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Save registration layout', 'accesspress' ); ?></button>
					</div>
				</div>
			</div>
		</form>
		<?php
	}
}