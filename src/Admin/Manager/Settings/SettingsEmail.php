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
	 * Normalize the current values for the default-prefixed general settings fields.
	 *
	 * @param array<string, mixed> $values The incoming values.
	 * @return array<string, mixed> The normalized values.
	 * @since 1.0.0
	 */
	private function normalize_values( array $values ): array {
		return $values;
	}
	/**
	 * Render the email settings page content.
	 *
	 * @param array $values The current values for the email settings fields.
	 * @return void
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$values = $this->normalize_values( $values );
		$from_name = is_scalar( $values['from_name'] ?? '' ) ? (string) $values['from_name'] : get_bloginfo( 'name' );
		$from_email = is_scalar( $values['from_email'] ?? '' ) ? (string) $values['from_email'] : get_option( 'admin_email' );
		$notifications = ! empty( $values['admin_email_notifications'] );
		?>
		<form method="post" action="" class="accesspress-settings-form">
			<?php echo FormFieldHelper::input( 
				'action', 
				'accesspress_save_email_settings', 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
			<?php echo FormFieldHelper::input( 
				'accesspress_email_nonce', 
				wp_create_nonce( 'accesspress_email' ), 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
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
		</form>
		<?php
	}
}