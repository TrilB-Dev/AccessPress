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

final class SettingsAccess {
	/**
	 * Render the access restriction settings fields.
	 *
	 * @param array $values The current values for the access restriction settings fields.
	 * @since 1.0.0
	 */
	public function render( array $values ): void {
		$fields = array(
			'issue_licences'  => array(
				'label'       => __( 'Who can issue licences?', 'accesspress' ),
				'description' => __( 'Minimum capability required to issue new licence records.', 'accesspress' ),
				'tooltip'     => __( 'Only trusted administrators or licence managers should issue keys.', 'accesspress' ),
			),
			'revoke_licences' => array(
				'label'       => __( 'Who can revoke licences?', 'accesspress' ),
				'description' => __( 'Minimum capability required to revoke or disable active licences.', 'accesspress' ),
				'tooltip'     => __( 'Revocations are security-sensitive and should be tightly controlled.', 'accesspress' ),
			),
			'export_data'     => array(
				'label'       => __( 'Who can export licence data?', 'accesspress' ),
				'description' => __( 'Minimum capability required to export licence records and backups.', 'accesspress' ),
				'tooltip'     => __( 'Exports should require password protection and strong security checks.', 'accesspress' ),
			),
			'review_security' => array(
				'label'       => __( 'Who can review security logs?', 'accesspress' ),
				'description' => __( 'Minimum capability required to inspect validation and audit activity.', 'accesspress' ),
				'tooltip'     => __( 'Use an administrator-level role for security and compliance review.', 'accesspress' ),
			),
		);

		foreach ( $fields as $key => $field ) {
			$key      = SanitizationHelper::key( $key );
			$id       = 'accesspress-access-' . $key;
			$name     = 'accesspress_access[' . $key . ']';
			$options  = array(
				array(
					'value' => 'manage_options',
					'label' => __( 'Administrators', 'accesspress' ),
				),
				array(
					'value' => 'edit_posts',
					'label' => __( 'Editors', 'accesspress' ),
				),
				array(
					'value' => 'publish_posts',
					'label' => __( 'Authors', 'accesspress' ),
				),
			);
			$current  = $values[ $key ] ?? 'manage_options';
			$current  = is_array( $current ) ? $current : array( $current );
			$current  = array_values( array_filter( array_map( 'sanitize_key', $current ) ) );
			$selected = array();
			foreach ( $options as $option ) {
				if ( in_array( $option['value'], $current, true ) ) {
					$selected[] = $option;
				}
			}
			if ( empty( $selected ) ) {
				$selected[] = $options[0];
			}
			?>
			<tr>
				<th scope="row">
					<?php echo FormFieldHelper::label( 
						$id, 
						$field['label'], 
						$field 
					); ?>
				</th>
				<td>
					<?php echo FormFieldHelper::select( 
						$name, 
						$options, 
						$selected[0]['value'], 
						array( 'id' => $id ) 
					); ?>
				</td>
			</tr>
			<?php
		}
	}
}



