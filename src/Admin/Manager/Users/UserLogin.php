<?php
/**
 * User login screen UI for AccessPress.
 *
 * @package AccessPress
 * @subpackage Admin\Manager\Users
 */
namespace AccessPress\Admin\Manager\Users;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserLogin extends UserManager {
	/**
	 * Prevent recursive parent initialization when the login page is instantiated.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( false );
	}

	/**
	 * Render the login management screen.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		if ( isset( $_POST['accesspress_save_login_settings'] ) && isset( $_POST['accesspress_login_settings_nonce'] ) ) {
			if ( check_admin_referer( 'accesspress_login_settings', 'accesspress_login_settings_nonce' ) ) {
				$mode = isset( $_POST['accesspress_general']['login_identifier_mode'] ) ? sanitize_key( wp_unslash( $_POST['accesspress_general']['login_identifier_mode'] ) ) : 'default';
				if ( ! in_array( $mode, array( 'default', 'username', 'email' ), true ) ) {
					$mode = 'default';
				}
				Settings::set( 'login_identifier_mode', $mode );
				printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html__( 'Login settings saved.', 'accesspress' ) );
			}
		}

		$login_mode = in_array( (string) ( Settings::get( 'login_identifier_mode', 'default' ) ), array( 'default', 'username', 'email' ), true ) ? (string) Settings::get( 'login_identifier_mode', 'default' ) : 'default';
		$login_mode_options = array(
			array(
				'value' => 'default',
				'label' => __( 'Username or email', 'accesspress' ),
			),
			array(
				'value' => 'username',
				'label' => __( 'Username only', 'accesspress' ),
			),
			array(
				'value' => 'email',
				'label' => __( 'Email only', 'accesspress' ),
			),
		);
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5 mb-3"><?php esc_html_e( 'Login settings', 'accesspress' ); ?></h2>
				<p class="text-secondary mb-0"><?php esc_html_e( 'Configure the frontend login experience, redirect rules, and default login behavior.', 'accesspress' ); ?></p>
			</div>
		</div>

		<form method="post" action="" class="accesspress-settings-form">
			<?php echo FormFieldHelper::input( 'action', 'accesspress_save_login_settings', array( 'type' => 'hidden' ) ); ?>
			<?php wp_nonce_field( 'accesspress_login_settings', 'accesspress_login_settings_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'accesspress-login-identifier-mode',
								__( 'Login Identifier Mode', 'accesspress' ),
								array(
									'description' => __( 'Control how the frontend login and password reset forms accept user identifiers. This keeps the native WordPress auth stack and installed plugins active.', 'accesspress' ),
								)
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_select(
								'accesspress_general[login_identifier_mode]',
								array(
									'data' => $login_mode_options,
									'selected' => $login_mode,
									'id' => 'accesspress-login-identifier-mode',
									'live_search' => true,
								)
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="mt-3 d-flex justify-content-end">
				<?php echo FormFieldHelper::button(
					__( 'Save Login Settings', 'accesspress' ),
					array(
						'type'  => 'submit',
						'class' => 'btn-primary',
						'name'  => 'accesspress_save_login_settings',
					)
				); ?>
			</div>
		</form>
		<?php
	}
}