<?php
/**
 * Settings access restriction fields.
 *
 * @package TrilBDev
 * @subpackage Admin\Manager\Settings
 */
namespace AccessPress\Admin\Manager\Settings;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsAccess extends SettingsManager {
	/**
	 * Render the access restriction settings fields.
	 *
	 * @param array $values The current values for the access restriction settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$issue_licences  = is_array( $values['issue_licences'] ?? null ) ? $values['issue_licences'] : array( $values['issue_licences'] ?? 'manage_options' );
		$revoke_licences = is_array( $values['revoke_licences'] ?? null ) ? $values['revoke_licences'] : array( $values['revoke_licences'] ?? 'manage_options' );
		$export_data     = is_array( $values['export_data'] ?? null ) ? $values['export_data'] : array( $values['export_data'] ?? 'manage_options' );
		$review_security = is_array( $values['review_security'] ?? null ) ? $values['review_security'] : array( $values['review_security'] ?? 'manage_options' );
		$role_options    = array(
			array( 'value' => 'manage_options', 'label' => __( 'Administrators', 'accesspress' ) ),
			array( 'value' => 'edit_posts', 'label' => __( 'Editors', 'accesspress' ) ),
			array( 'value' => 'publish_posts', 'label' => __( 'Authors', 'accesspress' ) ),
		);
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-access-issue-licences',
							__( 'Who can issue licences?', 'accesspress' ),
							array(
								'description' => __( 'Minimum capability required to issue new licence records.', 'accesspress' ),
								'tooltip'     => __( 'Only trusted administrators or licence managers should issue keys.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::bootstrap_select(
							'accesspress_access[issue_licences]',
							array(
								'data' => $role_options,
								'selected' => sanitize_key( (string) ( $issue_licences[0] ?? 'manage_options' ) ),
								'id' => 'accesspress-access-issue-licences',
								'live_search' => true,
							)
						); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-access-revoke-licences',
							__( 'Who can revoke licences?', 'accesspress' ),
							array(
								'description' => __( 'Minimum capability required to revoke or disable active licences.', 'accesspress' ),
								'tooltip'     => __( 'Revocations are security-sensitive and should be tightly controlled.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::bootstrap_select(
							'accesspress_access[revoke_licences]',
							array(
								'data' => $role_options,
								'selected' => sanitize_key( (string) ( $revoke_licences[0] ?? 'manage_options' ) ),
								'id' => 'accesspress-access-revoke-licences',
								'live_search' => true,
							)
						); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-access-export-data',
							__( 'Who can export licence data?', 'accesspress' ),
							array(
								'description' => __( 'Minimum capability required to export licence records and backups.', 'accesspress' ),
								'tooltip'     => __( 'Exports should require password protection and strong security checks.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::bootstrap_select(
							'accesspress_access[export_data]',
							array(
								'data' => $role_options,
								'selected' => sanitize_key( (string) ( $export_data[0] ?? 'manage_options' ) ),
								'id' => 'accesspress-access-export-data',
								'live_search' => true,
							)
						); ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php echo FormFieldHelper::label(
							'accesspress-access-review-security',
							__( 'Who can review security logs?', 'accesspress' ),
							array(
								'description' => __( 'Minimum capability required to inspect validation and audit activity.', 'accesspress' ),
								'tooltip'     => __( 'Use an administrator-level role for security and compliance review.', 'accesspress' ),
							)
						); ?>
					</th>
					<td>
						<?php echo FormFieldHelper::bootstrap_select(
							'accesspress_access[review_security]',
							array(
								'data' => $role_options,
								'selected' => sanitize_key( (string) ( $review_security[0] ?? 'manage_options' ) ),
								'id' => 'accesspress-access-review-security',
								'live_search' => true,
							)
						); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}



