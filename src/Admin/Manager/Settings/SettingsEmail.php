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
		$this->render_fields( $values );
	}

	/**
	 * Render the email settings fields.
	 *
	 * @param array $values The current values for the email settings fields.
	 * @return void
	 */
	public function render_fields( array $values ): void {
		$fields = array(
			'from_name' => array(
				'label'       => __( 'From name', 'accesspress' ),
				'description' => __( 'Name used in outbound AccessPress email messages.', 'accesspress' ),
				'type'        => 'text',
				'default'     => get_bloginfo( 'name' ),
			),
			'from_email' => array(
				'label'       => __( 'From email', 'accesspress' ),
				'description' => __( 'Reply-to address used for AccessPress notifications.', 'accesspress' ),
				'type'        => 'email',
				'default'     => get_option( 'admin_email' ),
			),
			'admin_email_notifications' => array(
				'label'       => __( 'Admin notifications', 'accesspress' ),
				'description' => __( 'Send AccessPress admin alerts to site administrators.', 'accesspress' ),
				'type'        => 'switch',
				'default'     => true,
			),
		);

		foreach ( $fields as $key => $field ) {
			$key   = SanitizationHelper::key( $key );
			$id    = 'accesspress-email-' . $key;
			$name  = 'accesspress_email[' . $key . ']';
			$value = $values[ $key ] ?? $field['default'] ?? '';
			?>
			<tr>
				<th scope="row"><?php echo FormFieldHelper::label( $id, $field['label'], $field ); ?></th>
				<td>
					<?php
					if ( 'switch' === ( $field['type'] ?? '' ) ) {
						echo FormFieldHelper::switch(
							$name,
							'1',
							$field['label'],
							array(
								'id'      => $id,
								'checked' => ! empty( $value ),
							)
						);
					} else {
						echo FormFieldHelper::text_input(
							$name,
							is_scalar( $value ) ? (string) $value : '',
							array(
								'id'          => $id,
								'type'        => ( 'email' === ( $field['type'] ?? '' ) ) ? 'email' : 'text',
								'autocomplete' => 'off',
							)
						);
					}
					?>
				</td>
			</tr>
			<?php
		}
	}
}