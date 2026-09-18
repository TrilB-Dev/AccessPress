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
		$this->render_fields( $values );
	}

	/**
	 * Render the layout settings fields.
	 *
	 * @param array $values The current values for the layout settings fields.
	 * @since 1.0.0
	 */
	public function render_fields( array $values ): void {
		$fields = array(
			'login_registration_title' => array(
				'label'       => __( 'Login & Registration Title', 'accesspress' ),
				'description' => __( 'Set the title displayed across the frontend login and registration experience.', 'accesspress' ),
				'type'        => 'text',
				'default'     => get_bloginfo( 'name' ) ?: __( 'AccessPress', 'accesspress' ),
			),
			'site_logo'                => array(
				'label'       => __( 'Site Logo', 'accesspress' ),
				'description' => __( 'Choose a logo for the frontend member UI.', 'accesspress' ),
				'type'        => 'media',
			),
			'frontend_user_menu'       => array(
				'label'       => __( 'Frontend user UI menu', 'accesspress' ),
				'description' => __( 'Choose whether the user menu is displayed horizontally or vertically.', 'accesspress' ),
				'type'        => 'select',
				'options'     => array(
					'horizontal' => __( 'Horizontal', 'accesspress' ),
					'vertical'   => __( 'Vertical', 'accesspress' ),
				),
			),
			'ajax_submission_user_profile' => array(
				'label'       => __( 'Ajax Submission user profile', 'accesspress' ),
				'description' => __( 'Submit profile updates via AJAX without a full page reload.', 'accesspress' ),
				'type'        => 'switch',
				'default'     => true,
			),
		);

		foreach ( $fields as $key => $field ) {
			$key   = SanitizationHelper::key( $key );
			$id    = 'accesspress-layout-' . $key;
			$name  = 'accesspress_layout[' . $key . ']';
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
					} elseif ( 'media' === ( $field['type'] ?? '' ) ) {
						echo '<div class="input-group">';
						echo FormFieldHelper::text_input( $name, is_scalar( $value ) ? (string) $value : '', array( 'id' => $id, 'placeholder' => __( 'Select a logo image', 'accesspress' ) ) );
						echo FormFieldHelper::button(
							__( 'Select image', 'accesspress' ),
							array(
								'type'  => 'button',
								'class' => 'btn btn-outline-secondary accesspress-media-button',
								'data-target' => $id,
							)
						);
						echo '</div>';
					} elseif ( 'select' === ( $field['type'] ?? '' ) ) {
						echo FormFieldHelper::select(
							$name,
							(array) ( $field['options'] ?? array() ),
							is_scalar( $value ) ? (string) $value : '',
							array( 'id' => $id, 'class' => 'form-select selectpicker', 'data-live-search' => 'true' )
						);
					} else {
						echo FormFieldHelper::text_input(
							$name,
							is_scalar( $value ) ? (string) $value : '',
							array( 'id' => $id )
						);
					}
					?>
				</td>
			</tr>
			<?php
		}
	}
}
