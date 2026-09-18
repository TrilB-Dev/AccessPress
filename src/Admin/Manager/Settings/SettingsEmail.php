<?php
/**
 * Email settings fields.
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

final class SettingsEmail extends SettingsManager {
	/**
	 * Render the email settings page content.
	 *
	 * @param array $values The current values for the email settings fields.
	 * @return void
	 */
	public function render_page_content( array $values ): void {
		$from_name = is_scalar( $values['from_name'] ?? '' ) ? (string) $values['from_name'] : get_bloginfo( 'name' );
		$from_email = is_scalar( $values['from_email'] ?? '' ) ? (string) $values['from_email'] : get_option( 'admin_email' );
		$notifications = ! empty( $values['admin_email_notifications'] );
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-email-from-name',
							__( 'From name', 'accesspress' ),
							array(
								'description' => __( 'Name used in outbound AccessPress email messages.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::text_input(
							'accesspress_email[from_name]',
							$from_name,
							array(
								'id' => 'accesspress-email-from-name',
								'autocomplete' => 'off',
							)
						); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-email-from-email',
							__( 'From email', 'accesspress' ),
							array(
								'description' => __( 'Reply-to address used for AccessPress notifications.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::input(
							'accesspress_email[from_email]',
							$from_email,
							array(
								'id' => 'accesspress-email-from-email',
								'type' => 'email',
								'autocomplete' => 'off',
							)
						); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-email-admin-notifications',
							__( 'Admin notifications', 'accesspress' ),
							array(
								'description' => __( 'Send AccessPress admin alerts to site administrators.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::switch(
							'accesspress_email[admin_email_notifications]',
							'1',
							__( 'Admin notifications', 'accesspress' ),
							array(
								'id' => 'accesspress-email-admin-notifications',
								'checked' => $notifications,
							)
						); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}