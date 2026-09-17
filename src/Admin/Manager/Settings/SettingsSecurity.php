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

final class SettingsSecurity {
	/**
	 * Render the security settings fields.
	 *
	 * @param array $values The current values for the security settings fields.
	 * @since 1.0.0
	 */
	public function render( array $values ): void {
		$fields = array(
			'disable_wp_admin_access' => array(
				'label'       => __( 'Disable wp-admin access to:', 'accesspress' ),
				'description' => __( 'Restrict WordPress admin access for selected roles.', 'accesspress' ),
				'type'        => 'multi_select_roles',
			),
			'disable_admin_toolbar'   => array(
				'label'       => __( 'Disable admin toolbar for:', 'accesspress' ),
				'description' => __( 'Hide the WordPress toolbar for selected roles.', 'accesspress' ),
				'type'        => 'multi_select_roles',
			),
			'form_protection'         => array(
				'label'       => __( 'Form protection', 'accesspress' ),
				'description' => __( 'Choose the anti-bot protection used on frontend forms.', 'accesspress' ),
				'type'        => 'select',
				'options'     => array(
					'' => __( 'None', 'accesspress' ),
					'recaptcha' => __( 'reCAPTCHA', 'accesspress' ),
					'turnstile' => __( 'Cloudflare Turnstile', 'accesspress' ),
				),
			),
			'recaptcha_version'       => array(
				'label'       => __( 'reCAPTCHA version', 'accesspress' ),
				'description' => __( 'Choose the version of Google reCAPTCHA to enforce.', 'accesspress' ),
				'type'        => 'select',
				'options'     => array(
					'v2_checkbox' => __( 'v2 Checkbox', 'accesspress' ),
					'v3_invisible' => __( 'v3 Invisible', 'accesspress' ),
				),
			),
			'recaptcha_site_key'      => array(
				'label'       => __( 'reCAPTCHA Site Key', 'accesspress' ),
				'description' => __( 'Paste the reCAPTCHA site key generated in Google Admin.', 'accesspress' ),
				'type'        => 'text',
			),
			'recaptcha_secret_key'    => array(
				'label'       => __( 'reCAPTCHA Secret Key', 'accesspress' ),
				'description' => __( 'This value is encrypted before being stored.', 'accesspress' ),
				'type'        => 'password',
			),
			'turnstile_site_key'      => array(
				'label'       => __( 'Turnstile Site Key', 'accesspress' ),
				'description' => __( 'Paste the Cloudflare Turnstile site key.', 'accesspress' ),
				'type'        => 'text',
			),
			'turnstile_secret_key'    => array(
				'label'       => __( 'Turnstile Secret Key', 'accesspress' ),
				'description' => __( 'This value is encrypted before being stored.', 'accesspress' ),
				'type'        => 'password',
			),
		);

		foreach ( $fields as $key => $field ) {
			$key   = SanitizationHelper::key( $key );
			$id    = 'accesspress-security-' . $key;
			$name  = 'accesspress_security[' . $key . ']';
			$value = $values[ $key ] ?? $field['default'] ?? '';
			?>
			<tr>
				<th scope="row"><?php echo FormFieldHelper::label( $id, $field['label'], $field ); ?></th>
				<td>
					<?php
					if ( 'select' === ( $field['type'] ?? '' ) ) {
						echo FormFieldHelper::select(
							$name,
							(array) ( $field['options'] ?? array() ),
							is_scalar( $value ) ? (string) $value : '',
							array( 'id' => $id, 'class' => 'form-select selectpicker', 'data-live-search' => 'true' )
						);
					} elseif ( 'multi_select_roles' === ( $field['type'] ?? '' ) ) {
						$selected = is_array( $value ) ? array_map( 'strval', $value ) : array( is_scalar( $value ) ? (string) $value : '' );
						echo FormFieldHelper::select(
							$name . '[]',
							$this->role_options(),
							$selected,
							array(
								'id' => $id,
								'multiple' => true,
								'class' => 'form-select selectpicker',
								'data-live-search' => 'true',
							)
						);
					} elseif ( 'password' === ( $field['type'] ?? '' ) ) {
						echo FormFieldHelper::input(
							$name,
							is_scalar( $value ) ? (string) $value : '',
							array( 'id' => $id, 'type' => 'password', 'autocomplete' => 'new-password' )
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
