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
	 * Normalize the current values for the default-prefixed general settings fields.
	 *
	 * @param array<string, mixed> $values The incoming values.
	 * @return array<string, mixed> The normalized values.
	 */
	private function normalize_values( array $values ): array {
		return $values;
	}
	/**
	 * Render the general settings fields.
	 *
	 * @param array $values The current values for the settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$values = $this->normalize_values( $values );
		$registration_page = (string) ( $values['registration_page'] ?? '' );
		$login_page        = (string) ( $values['login_page'] ?? '' );
		$my_account_page   = (string) ( $values['my_account_page'] ?? '' );
		$lost_password_page = (string) ( $values['lost_password_page'] ?? '' );
		$multi_roles       = ! empty( $values['enable_multi_roles'] );
		$membership_groups = ! empty( $values['enable_membership_groups'] );
		$register_options  = $this->page_options( 'register' );
		$login_options     = $this->page_options( 'login' );
		$profile_options   = $this->page_options( 'profile' );
		$password_options  = $this->page_options( 'lost-password' );
		?>
		<form method="post" action="" class="accesspress-settings-form">
			<?php echo FormFieldHelper::input( 
				'action', 
				'accesspress_save_general_settings', 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
			<?php echo FormFieldHelper::input( 
				'accesspress_general_nonce', 
				wp_create_nonce( 'accesspress_general' ), 
				array( 
					'type' => 'hidden' 
				) 
			); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'accesspress-general-registration-page', 
								__( 'Registration Page', 'accesspress' ), 
								array( 
									'description' => __( 'Choose the frontend registration page for new users.', 'accesspress' ), 
									'tooltip' => __( 'Create a page if none exists and assign it here.', 'accesspress' ) 
								) 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_general[registration_page]',
								array(
									'data' => $register_options,
									'selected' => $registration_page,
									'id' => 'accesspress-general-registration-page',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-general-login-page',
								__( 'Login Page', 'accesspress' ),
								array(
									'description' => __( 'Choose the frontend login page for member sign-in.', 'accesspress' ),
									'tooltip'     => __( 'Create a dedicated login page if you have not already done so.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_general[login_page]',
								array(
									'data' => $login_options,
									'selected' => $login_page,
									'id' => 'accesspress-general-login-page',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-general-my-account-page',
								__( 'My Account Page', 'accesspress' ),
								array(
									'description' => __( 'Choose the frontend profile page used for member account management.', 'accesspress' ),
									'tooltip'     => __( 'Create a profile page if one does not yet exist.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_general[my_account_page]',
								array(
									'data' => $profile_options,
									'selected' => $my_account_page,
									'id' => 'accesspress-general-my-account-page',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-general-lost-password-page',
								__( 'Lost Password Page', 'accesspress' ),
								array(
									'description' => __( 'Choose the frontend password recovery page.', 'accesspress' ),
									'tooltip'     => __( 'Create a lost-password page if one does not yet exist.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_general[lost_password_page]',
								array(
									'data' => $password_options,
									'selected' => $lost_password_page,
									'id' => 'accesspress-general-lost-password-page',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-general-enable-multi-roles',
								__( 'Enable multi roles', 'accesspress' ),
								array(
									'description' => __( 'Allow each user to have more than one WordPress role.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch(
								'accesspress_general[enable_multi_roles]',
								'1',
								__( 'Enable multi roles', 'accesspress' ),
								array(
									'id' => 'accesspress-general-enable-multi-roles',
									'checked' => $multi_roles,
								)
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-general-enable-membership-groups',
								__( 'Enable Membership Groups', 'accesspress' ),
								array(
									'description' => __( 'Enable the creation and assignment of membership groups to users.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch(
								'accesspress_general[enable_membership_groups]',
								'1',
								__( 'Enable Membership Groups', 'accesspress' ),
								array(
									'id' => 'accesspress-general-enable-membership-groups',
									'checked' => $membership_groups,
								)
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
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