<?php
/**
 * Layout settings fields.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Settings;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsLayout extends SettingsManager {
	/**
	 * Render the layout settings fields.
	 *
	 * @param array $values The current values for the layout settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$login_title = is_scalar( $values['login_registration_title'] ?? '' ) ? (string) $values['login_registration_title'] : ( get_bloginfo( 'name' ) ?: __( 'AccessPress', 'accesspress' ) );
		$site_logo   = is_scalar( $values['site_logo'] ?? '' ) ? (string) $values['site_logo'] : '';
		$user_menu   = (string) ( $values['frontend_user_menu'] ?? 'horizontal' );
		$ajax_profile = ! empty( $values['ajax_submission_user_profile'] );
		$user_menu_options = array(
			'horizontal' => __( 'Horizontal', 'accesspress' ),
			'vertical'   => __( 'Vertical', 'accesspress' ),
		);
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-layout-login-registration-title', __( 'Login & Registration Title', 'accesspress' ), array( 'description' => __( 'Set the title displayed across the frontend login and registration experience.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::text_input( 'accesspress_layout[login_registration_title]', $login_title, array( 'id' => 'accesspress-layout-login-registration-title' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-layout-site-logo', __( 'Site Logo', 'accesspress' ), array( 'description' => __( 'Choose a logo for the frontend member UI.', 'accesspress' ) ) ); ?></th>
					<td>
						<div class="input-group">
							<?php echo FormFieldHelper::text_input( 'accesspress_layout[site_logo]', $site_logo, array( 'id' => 'accesspress-layout-site-logo', 'placeholder' => __( 'Select a logo image', 'accesspress' ) ) ); ?>
							<?php echo FormFieldHelper::button( __( 'Select image', 'accesspress' ), array( 'type' => 'button', 'class' => 'btn btn-outline-secondary accesspress-media-button', 'data-target' => 'accesspress-layout-site-logo' ) ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-layout-frontend-user-menu', __( 'Frontend user UI menu', 'accesspress' ), array( 'description' => __( 'Choose whether the user menu is displayed horizontally or vertically.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::select( 'accesspress_layout[frontend_user_menu]', $user_menu_options, $user_menu, array( 'id' => 'accesspress-layout-frontend-user-menu', 'class' => 'form-select selectpicker', 'data-live-search' => 'true' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-layout-ajax-submission-user-profile', __( 'Ajax Submission user profile', 'accesspress' ), array( 'description' => __( 'Submit profile updates via AJAX without a full page reload.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::switch( 'accesspress_layout[ajax_submission_user_profile]', '1', __( 'Ajax Submission user profile', 'accesspress' ), array( 'id' => 'accesspress-layout-ajax-submission-user-profile', 'checked' => $ajax_profile ) ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}
