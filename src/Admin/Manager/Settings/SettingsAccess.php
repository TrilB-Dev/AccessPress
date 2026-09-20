<?php
/**
 * Settings access restriction fields.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace AccessPress\Admin\Manager\Settings;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsAccess extends SettingsManager {
	/**
	 * Normalize the current values for the default-prefixed general settings fields.
	 *
	 * @param array<string, mixed> $values The incoming values.
	 * @return array<string, mixed> The normalized values.
	 */
	private function normalize_values( array $values ): array {
		return $values;
	}
	/**
	 * Render the access restriction settings fields.
	 *
	 * @param array $values The current values for the access restriction settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$values = $this->normalize_values( $values );
		$manage_users    = is_array( $values['issue_licences'] ?? null ) ? $values['issue_licences'] : array( $values['issue_licences'] ?? 'manage_options' );
		$manage_roles    = is_array( $values['revoke_licences'] ?? null ) ? $values['revoke_licences'] : array( $values['revoke_licences'] ?? 'manage_options' );
		$export_users    = is_array( $values['export_data'] ?? null ) ? $values['export_data'] : array( $values['export_data'] ?? 'manage_options' );
		$review_security = is_array( $values['review_security'] ?? null ) ? $values['review_security'] : array( $values['review_security'] ?? 'manage_options' );
		$role_options    = array(
			array( 
				'value' => 'manage_options', 
				'label' => __( 'Administrators', 'accesspress' ) 
			),
			array( 
				'value' => 'edit_posts', 
				'label' => __( 'Editors', 'accesspress' ) 
			),
			array( 
				'value' => 'publish_posts', 
				'label' => __( 'Authors', 'accesspress' ) 
			),
		);
		?>
		<form method="post" action="" class="accesspress-settings-form">
			<?php echo FormFieldHelper::input( 
				'action', 
				'accesspress_save_access_settings', 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
			<?php echo FormFieldHelper::input( 
				'accesspress_access_nonce', 
				wp_create_nonce( 'accesspress_access' ), 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-access-manage-users',
								__( 'Who can manage users?', 'accesspress' ),
								array(
									'description' => __( 'Minimum capability required to create, edit, and deactivate user accounts.', 'accesspress' ),
									'tooltip'     => __( 'Restrict user management to trusted roles only.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_access[issue_licences]',
								array(
									'data' => $role_options,
									'selected' => sanitize_key( (string) ( $manage_users[0] ?? 'manage_options' ) ),
									'id' => 'accesspress-access-manage-users',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-access-manage-roles',
								__( 'Who can manage user roles?', 'accesspress' ),
								array(
									'description' => __( 'Minimum capability required to assign or change WordPress roles for users.', 'accesspress' ),
									'tooltip'     => __( 'Role changes are sensitive and should be restricted to site administrators.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_access[revoke_licences]',
								array(
									'data' => $role_options,
									'selected' => sanitize_key( (string) ( $manage_roles[0] ?? 'manage_options' ) ),
									'id' => 'accesspress-access-manage-roles',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-access-export-users',
								__( 'Who can export user data?', 'accesspress' ),
								array(
									'description' => __( 'Minimum capability required to export user records and account data.', 'accesspress' ),
									'tooltip'     => __( 'Exports should be limited to trusted administrators and data managers.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_access[export_data]',
								array(
									'data' => $role_options,
									'selected' => sanitize_key( (string) ( $export_users[0] ?? 'manage_options' ) ),
									'id' => 'accesspress-access-export-users',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-access-review-activity',
								__( 'Who can review activity logs?', 'accesspress' ),
								array(
									'description' => __( 'Minimum capability required to inspect account validation and security activity.', 'accesspress' ),
									'tooltip'     => __( 'Security review should be restricted to administrators or trusted compliance roles.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_access[review_security]',
								array(
									'data' => $role_options,
									'selected' => sanitize_key( (string) ( $review_security[0] ?? 'manage_options' ) ),
									'id' => 'accesspress-access-review-activity',
									'live_search' => true,
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



