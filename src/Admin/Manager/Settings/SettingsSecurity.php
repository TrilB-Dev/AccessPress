<?php
/**
 * Security settings fields.
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

final class SettingsSecurity extends SettingsManager {
	/**
	 * Render the security settings fields.
	 *
	 * @param array $values The current values for the security settings fields.
	 * @since 1.0.0
	 */
	public function render_page_content( array $values ): void {
		$disable_wp_admin = is_array( $values['disable_wp_admin_access'] ?? null ) ? array_map( 'strval', $values['disable_wp_admin_access'] ) : array( is_scalar( $values['disable_wp_admin_access'] ?? '' ) ? (string) $values['disable_wp_admin_access'] : '' );
		$disable_toolbar  = is_array( $values['disable_admin_toolbar'] ?? null ) ? array_map( 'strval', $values['disable_admin_toolbar'] ) : array( is_scalar( $values['disable_admin_toolbar'] ?? '' ) ? (string) $values['disable_admin_toolbar'] : '' );
		$form_protection  = (string) ( $values['form_protection'] ?? '' );
		$recaptcha_version = (string) ( $values['recaptcha_version'] ?? 'v2_checkbox' );
		$recaptcha_site_key = is_scalar( $values['recaptcha_site_key'] ?? '' ) ? (string) $values['recaptcha_site_key'] : '';
		$recaptcha_secret_key = is_scalar( $values['recaptcha_secret_key'] ?? '' ) ? (string) $values['recaptcha_secret_key'] : '';
		$turnstile_site_key = is_scalar( $values['turnstile_site_key'] ?? '' ) ? (string) $values['turnstile_site_key'] : '';
		$turnstile_secret_key = is_scalar( $values['turnstile_secret_key'] ?? '' ) ? (string) $values['turnstile_secret_key'] : '';
		$protection_options = array(
			'' => __( 'None', 'accesspress' ),
			'recaptcha' => __( 'reCAPTCHA', 'accesspress' ),
			'turnstile' => __( 'Cloudflare Turnstile', 'accesspress' ),
		);
		$recaptcha_options = array(
			'v2_checkbox' => __( 'v2 Checkbox', 'accesspress' ),
			'v3_invisible' => __( 'v3 Invisible', 'accesspress' ),
		);
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-disable-wp-admin-access', __( 'Disable wp-admin access to:', 'accesspress' ), array( 'description' => __( 'Restrict WordPress admin access for selected roles.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::select( 'accesspress_security[disable_wp_admin_access][]', $this->role_options(), $disable_wp_admin, array( 'id' => 'accesspress-security-disable-wp-admin-access', 'multiple' => true, 'class' => 'form-select selectpicker', 'data-live-search' => 'true' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-disable-admin-toolbar', __( 'Disable admin toolbar for:', 'accesspress' ), array( 'description' => __( 'Hide the WordPress toolbar for selected roles.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::select( 'accesspress_security[disable_admin_toolbar][]', $this->role_options(), $disable_toolbar, array( 'id' => 'accesspress-security-disable-admin-toolbar', 'multiple' => true, 'class' => 'form-select selectpicker', 'data-live-search' => 'true' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-form-protection', __( 'Form protection', 'accesspress' ), array( 'description' => __( 'Choose the anti-bot protection used on frontend forms.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::select( 'accesspress_security[form_protection]', $protection_options, $form_protection, array( 'id' => 'accesspress-security-form-protection', 'class' => 'form-select selectpicker', 'data-live-search' => 'true' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-recaptcha-version', __( 'reCAPTCHA version', 'accesspress' ), array( 'description' => __( 'Choose the version of Google reCAPTCHA to enforce.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::select( 'accesspress_security[recaptcha_version]', $recaptcha_options, $recaptcha_version, array( 'id' => 'accesspress-security-recaptcha-version', 'class' => 'form-select selectpicker', 'data-live-search' => 'true' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-recaptcha-site-key', __( 'reCAPTCHA Site Key', 'accesspress' ), array( 'description' => __( 'Paste the reCAPTCHA site key generated in Google Admin.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::text_input( 'accesspress_security[recaptcha_site_key]', $recaptcha_site_key, array( 'id' => 'accesspress-security-recaptcha-site-key' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-recaptcha-secret-key', __( 'reCAPTCHA Secret Key', 'accesspress' ), array( 'description' => __( 'This value is encrypted before being stored.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::input( 'accesspress_security[recaptcha_secret_key]', $recaptcha_secret_key, array( 'id' => 'accesspress-security-recaptcha-secret-key', 'type' => 'password', 'autocomplete' => 'new-password' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-turnstile-site-key', __( 'Turnstile Site Key', 'accesspress' ), array( 'description' => __( 'Paste the Cloudflare Turnstile site key.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::text_input( 'accesspress_security[turnstile_site_key]', $turnstile_site_key, array( 'id' => 'accesspress-security-turnstile-site-key' ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo FormFieldHelper::label( 'accesspress-security-turnstile-secret-key', __( 'Turnstile Secret Key', 'accesspress' ), array( 'description' => __( 'This value is encrypted before being stored.', 'accesspress' ) ) ); ?></th>
					<td><?php echo FormFieldHelper::input( 'accesspress_security[turnstile_secret_key]', $turnstile_secret_key, array( 'id' => 'accesspress-security-turnstile-secret-key', 'type' => 'password', 'autocomplete' => 'new-password' ) ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Build the available WordPress role options.
	 *
	 * @return array<string, string>
	 */
	private function role_options(): array {
		$options = array();

		if ( function_exists( 'wp_roles' ) ) {
			$roles = wp_roles()->roles;
			if ( is_array( $roles ) ) {
				foreach ( $roles as $role_key => $role ) {
					$options[ $role_key ] = $role['name'] ?? ucfirst( str_replace( array( '_', '-' ), ' ', $role_key ) );
				}
			}
		}

		if ( empty( $options ) ) {
			$options = array(
				'administrator' => __( 'Administrator', 'accesspress' ),
				'editor'        => __( 'Editor', 'accesspress' ),
				'author'        => __( 'Author', 'accesspress' ),
				'contributor'   => __( 'Contributor', 'accesspress' ),
				'subscriber'    => __( 'Subscriber', 'accesspress' ),
			);
		}

		return $options;
	}
}
