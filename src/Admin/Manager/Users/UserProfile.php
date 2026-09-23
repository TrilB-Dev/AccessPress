<?php
/**
 * User profile screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Includes\UserManagement\Profile\ProfileFields;
use AccessPress\Includes\UserManagement\Profile\ProfileTabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserProfile extends UserManager {
	/**
	 * Prevent recursive parent initialization when the profile page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the profile management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		if ( isset( $_POST['accesspress_profile_layout'] ) && isset( $_POST['accesspress_profile_builder_nonce'] ) ) {
			if ( check_admin_referer( 'accesspress_profile_builder', 'accesspress_profile_builder_nonce' ) ) {
				$payload = json_decode( wp_unslash( (string) $_POST['accesspress_profile_layout'] ), true );
				if ( is_array( $payload ) ) {
					ProfileTabs::save_tabs( $payload );
					printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Profile layout saved.', 'accesspress' ) );
				}
			}
		}

		$tabs   = ProfileTabs::get_tabs();
		$palette = ProfileFields::get_palette();
		$layout = wp_json_encode( $tabs );
		if ( false === $layout ) {
			$layout = '[]';
		}
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Profile settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Define the tabs and fields shown to users on the frontend profile screen.', 'accesspress' ); ?></p>
			</div>
		</div>

		<form method="post" class="accesspress-profile-builder-form">
			<?php wp_nonce_field( 'accesspress_profile_builder', 'accesspress_profile_builder_nonce' ); ?>
			<input type="hidden" name="accesspress_profile_layout" id="accesspress-profile-layout" value="" />

			<div class="accesspress-profile-builder card shadow-sm" data-profile-builder data-profile-config="<?php echo esc_attr( $layout ); ?>">
				<div class="card-body">
					<div class="row g-4">
						<div class="col-lg-4">
							<div class="border rounded p-3 bg-light">
								<div class="d-flex align-items-center justify-content-between mb-3">
									<h3 class="h6 mb-0"><?php esc_html_e( 'Field library', 'accesspress' ); ?></h3>
								</div>

								<div class="accordion" id="accesspress-profile-field-library">
									<div class="accordion-item">
										<h4 class="accordion-header" id="accesspress-profile-fields-builtin-header">
											<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-profile-fields-builtin" aria-expanded="true" aria-controls="accesspress-profile-fields-builtin">
												<?php esc_html_e( 'WordPress profile fields', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-profile-fields-builtin" class="accordion-collapse collapse show" aria-labelledby="accesspress-profile-fields-builtin-header" data-bs-parent="#accesspress-profile-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-profile-field-palette="built_in">
													<?php foreach ( $palette['built_in'] as $field ) : ?>
														<div class="card card-body py-2 px-3 shadow-sm border accesspress-profile-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="built_in" data-field-allow-multiple="false">
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
										<h4 class="accordion-header" id="accesspress-profile-fields-layout-header">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-profile-fields-layout" aria-expanded="false" aria-controls="accesspress-profile-fields-layout">
												<?php esc_html_e( 'Layout blocks', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-profile-fields-layout" class="accordion-collapse collapse" aria-labelledby="accesspress-profile-fields-layout-header" data-bs-parent="#accesspress-profile-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-profile-field-palette="layout">
													<?php foreach ( $palette['layout'] as $field ) : ?>
														<div class="card card-body py-2 px-3 shadow-sm border accesspress-profile-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="layout" data-field-allow-multiple="<?php echo esc_attr( ! empty( $field['multiple'] ) ? 'true' : 'false' ); ?>">
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

									<div class="accordion-item">
										<h4 class="accordion-header" id="accesspress-profile-fields-custom-header">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-profile-fields-custom" aria-expanded="false" aria-controls="accesspress-profile-fields-custom">
												<?php esc_html_e( 'AccessPress custom fields', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-profile-fields-custom" class="accordion-collapse collapse" aria-labelledby="accesspress-profile-fields-custom-header" data-bs-parent="#accesspress-profile-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-profile-field-palette="custom">
													<?php foreach ( $palette['custom'] as $field ) : ?>
														<div class="card card-body py-2 px-3 shadow-sm border accesspress-profile-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="custom" data-field-allow-multiple="true">
															<div class="d-flex justify-content-between align-items-center gap-2">
																<strong class="small"><?php echo esc_html( (string) ( $field['label'] ?? '' ) ); ?></strong>
																<span class="badge bg-success-subtle text-success"><?php esc_html_e( 'Repeatable', 'accesspress' ); ?></span>
															</div>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
										</div>
									</div>

									<div class="accordion-item">
										<h4 class="accordion-header" id="accesspress-profile-fields-third-party-header">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accesspress-profile-fields-third-party" aria-expanded="false" aria-controls="accesspress-profile-fields-third-party">
												<?php esc_html_e( '3rd Party Fields', 'accesspress' ); ?>
											</button>
										</h4>
										<div id="accesspress-profile-fields-third-party" class="accordion-collapse collapse" aria-labelledby="accesspress-profile-fields-third-party-header" data-bs-parent="#accesspress-profile-field-library">
											<div class="accordion-body p-2">
												<div class="d-grid gap-2" data-profile-field-palette="third_party">
													<?php if ( empty( $palette['third_party'] ) ) : ?>
														<div class="text-muted small"><?php esc_html_e( 'No third-party profile fields are currently registered.', 'accesspress' ); ?></div>
													<?php else : ?>
														<?php foreach ( $palette['third_party'] as $field ) : ?>
															<div class="card card-body py-2 px-3 shadow-sm border accesspress-profile-field-card" draggable="true" data-field-type="<?php echo esc_attr( (string) ( $field['type'] ?? 'text' ) ); ?>" data-field-group="third_party" data-field-allow-multiple="<?php echo esc_attr( ! empty( $field['multiple'] ) ? 'true' : 'false' ); ?>">
																<div class="d-flex justify-content-between align-items-center gap-2">
																	<strong class="small"><?php echo esc_html( (string) ( $field['label'] ?? '' ) ); ?></strong>
																	<span class="badge bg-warning-subtle text-warning-emphasis"><?php esc_html_e( 'Plugin', 'accesspress' ); ?></span>
																</div>
															</div>
														<?php endforeach; ?>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h3 class="h6 mb-0"><?php esc_html_e( 'Profile layout', 'accesspress' ); ?></h3>
								<button type="button" class="btn btn-primary btn-sm" data-profile-add-tab><?php esc_html_e( 'Add tab', 'accesspress' ); ?></button>
							</div>
							<div class="accesspress-profile-tabs" data-profile-tabs></div>
						</div>
					</div>
					<div class="d-flex justify-content-end mt-4">
						<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Save profile layout', 'accesspress' ); ?></button>
					</div>
				</div>
			</div>
		</form>
		<?php
	}
}