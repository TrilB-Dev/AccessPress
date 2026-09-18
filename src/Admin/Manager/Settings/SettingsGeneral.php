<?php
/**
 * Settings general fields.
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

final class SettingsGeneral extends SettingsManager {
	/**
	 * Render the general settings fields.
	 *
	 * @param array $values The current values for the settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$this->render_fields( $values );
	}

	/**
	 * Render the general settings fields.
	 *
	 * @param array $values The current values for the settings fields.
	 * @since 1.0.0
	 */
	public function render_fields( array $values ): void {
		$fields = array(
			'registration_page'          => array(
				'label'       => __( 'Registration Page', 'accesspress' ),
				'description' => __( 'Choose the frontend registration page for new users.', 'accesspress' ),
				'tooltip'     => __( 'Create a page if none exists and assign it here.', 'accesspress' ),
				'type'        => 'page_select',
				'page_key'    => 'register',
			),
			'login_page'                 => array(
				'label'       => __( 'Login Page', 'accesspress' ),
				'description' => __( 'Choose the frontend login page for member sign-in.', 'accesspress' ),
				'tooltip'     => __( 'Create a dedicated login page if you have not already done so.', 'accesspress' ),
				'type'        => 'page_select',
				'page_key'    => 'login',
			),
			'my_account_page'            => array(
				'label'       => __( 'My Account Page', 'accesspress' ),
				'description' => __( 'Choose the frontend profile page used for member account management.', 'accesspress' ),
				'tooltip'     => __( 'Create a profile page if one does not yet exist.', 'accesspress' ),
				'type'        => 'page_select',
				'page_key'    => 'profile',
			),
			'lost_password_page'         => array(
				'label'       => __( 'Lost Password Page', 'accesspress' ),
				'description' => __( 'Choose the frontend password recovery page.', 'accesspress' ),
				'tooltip'     => __( 'Create a lost-password page if one does not yet exist.', 'accesspress' ),
				'type'        => 'page_select',
				'page_key'    => 'lost-password',
			),
			'enable_multi_roles'        => array(
				'label'       => __( 'Enable multi roles', 'accesspress' ),
				'description' => __( 'Allow each user to have more than one WordPress role.', 'accesspress' ),
				'type'        => 'switch',
				'default'     => false,
			),
			'enable_membership_groups'   => array(
				'label'       => __( 'Enable Membership Groups', 'accesspress' ),
				'description' => __( 'Enable the creation and assignment of membership groups to users.', 'accesspress' ),
				'type'        => 'switch',
				'default'     => false,
			),
		);

		foreach ( $fields as $key => $field ) {
			$key   = SanitizationHelper::key( $key );
			$id    = 'accesspress-general-' . $key;
			$name  = 'accesspress_general[' . $key . ']';
			$value = $values[ $key ] ?? $field['default'] ?? '';
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
					} elseif ( 'page_select' === ( $field['type'] ?? '' ) ) {
						$page_options = $this->page_options( $field['page_key'] ?? $key );
						$selected     = is_scalar( $value ) ? (string) $value : ( $page_options[0]['value'] ?? '' );
						echo '<div class="input-group">';
						echo FormFieldHelper::select( 
							$name, 
							$page_options, 
							$selected, 
							array( 
								'id' => $id, 
								'class' => 'form-select selectpicker', 
								'data-live-search' => 'true' 
							) 
						);
						echo FormFieldHelper::button(
							'Create page',
							array(
								'type'  => 'button',
								'class' => 'btn btn-outline-secondary accesspress-create-page',
								'data-page-key' => $field['page_key'] ?? $key,
							)
						);
						echo '</div>';
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

	/**
	 * Build a page select list with a create option when the page does not yet exist.
	 *
	 * @param string $page_key The page slug key to look up.
	 * @return array<int, array<string, string>>
	 */
	private function page_options( string $page_key ): array {
		$options = array(
			array(
				'value' => '',
				'label' => __( 'Select a page', 'accesspress' ),
			),
		);

		if ( function_exists( 'get_pages' ) ) {
			$pages = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
			if ( is_array( $pages ) ) {
				foreach ( $pages as $page ) {
					$options[] = array(
						'value' => (string) $page->ID,
						'label' => (string) get_the_title( $page ),
					);
				}
			}
		}

		$create_label = sprintf( __( 'Create %s page', 'accesspress' ), ucfirst( str_replace( '-', ' ', $page_key ) ) );
		$options[] = array(
			'value' => 'create:' . $page_key,
			'label' => $create_label,
		);

		return $options;
	}
}