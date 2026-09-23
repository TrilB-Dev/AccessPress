<?php
/**
 * Public profile template.
 *
 * @package AccessPress\Public\Templates
 */
namespace AccessPress\Public\Templates;

use AccessPress\Includes\Functions\Helpers\FormFieldHelper;
use AccessPress\Includes\Settings\Settings;
use AccessPress\Includes\UserManagement\Profile\ProfileFields;
use AccessPress\Includes\UserManagement\Profile\ProfileTabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Profile {
	/**
	 * Render the frontend profile template.
	 *
	 * @param array<string, mixed> $args Template args.
	 * @return string
	 */
	public static function render( array $args = array() ): string {
		$current_user = wp_get_current_user();
		if ( ! is_user_logged_in() || ! $current_user instanceof \WP_User ) {
			return '<p>' . esc_html__( 'Please log in to manage your profile.', 'accesspress' ) . '</p>';
		}

		if ( Settings::get_bool( 'elementor_integration', false ) ) {
			return '';
		}

		$template_path = self::locate_theme_template( 'profile' );
		if ( '' !== $template_path ) {
			ob_start();
			include $template_path;
			return (string) ob_get_clean();
		}

		$is_admin = current_user_can( 'manage_options' );
		$tabs     = ProfileTabs::get_tabs();
		$fields   = array();
		foreach ( $tabs as $tab ) {
			if ( isset( $tab['fields'] ) && is_array( $tab['fields'] ) ) {
				$fields = array_merge( $fields, $tab['fields'] );
			}
		}

		if ( empty( $fields ) ) {
			$fields = ProfileFields::get_builtin_fields();
		}

		$fields = ProfileFields::filter_visible_fields( $fields, $is_admin );
		if ( empty( $fields ) ) {
			$fields = array(
				array(
					'key'       => 'display_name',
					'wp_field'  => 'display_name',
					'label'     => __( 'Display name', 'accesspress' ),
					'type'      => 'text',
					'editable'  => true,
					'required'  => true,
					'visibility' => 'user',
				),
				array(
					'key'       => 'user_email',
					'wp_field'  => 'user_email',
					'label'     => __( 'Email', 'accesspress' ),
					'type'      => 'email',
					'editable'  => true,
					'required'  => true,
					'visibility' => 'user',
				),
			);
		}

		ob_start();
		?>
		<form method="post" class="accesspress-user-management-form accesspress-profile-form">
			<?php foreach ( $fields as $field ) : ?>
				<?php
				if ( ! is_array( $field ) ) {
					continue;
				}
				$field = ProfileFields::normalize_field( $field );
				if ( ! ProfileFields::can_render_field( $field, $is_admin ) ) {
					continue;
				}
				$input_name = $field['wp_field'] ?: $field['key'];
				$value      = self::resolve_field_value( $current_user, $field );
				$attributes = array(
					'id'      => 'accesspress-profile-' . sanitize_key( (string) ( $field['key'] ?? $input_name ) ),
					'required' => ! empty( $field['required'] ),
				);
				if ( ! ProfileFields::can_edit_field( $field, $is_admin ) ) {
					$attributes['readonly'] = 'readonly';
				}
				$rendered = self::render_field_input( $field, $input_name, $value, $attributes );
				if ( '' === $rendered ) {
					continue;
				}
				?>
				<div class="mb-3">
					<?php echo FormFieldHelper::label( $attributes['id'], (string) ( $field['label'] ?? ucfirst( str_replace( array( '-', '_' ), ' ', (string) $input_name ) ) ) ); ?>
					<?php echo $rendered; ?>
				</div>
			<?php endforeach; ?>
			<?php wp_nonce_field( 'accesspress_user_management', 'accesspress_user_management_nonce' ); ?>
			<input type="hidden" name="accesspress_profile" value="1" />
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Save profile', 'accesspress' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Resolve the current value of a field for the viewing user.
	 *
	 * @param \WP_User               $user  Current user.
	 * @param array<string, mixed> $field Field definition.
	 * @return string
	 */
	private static function resolve_field_value( \WP_User $user, array $field ): string {
		$key = $field['wp_field'] ?? $field['key'] ?? '';
		if ( '' === $key ) {
			return '';
		}

		if ( isset( $user->$key ) ) {
			return (string) $user->$key;
		}

		if ( 'user_email' === $key ) {
			return (string) $user->user_email;
		}

		if ( 'display_name' === $key ) {
			return (string) $user->display_name;
		}

		return (string) get_user_meta( $user->ID, $key, true );
	}

	/**
	 * Render a single field input according to its configured type.
	 *
	 * @param array<string, mixed> $field      Field definition.
	 * @param string               $input_name Field name attribute.
	 * @param string               $value      Current value.
	 * @param array<string, mixed> $attributes Additional attributes.
	 * @return string
	 */
	private static function render_field_input( array $field, string $input_name, string $value, array $attributes ): string {
		$type = sanitize_key( (string) ( $field['type'] ?? 'text' ) );
		if ( 'password' === $type ) {
			return FormFieldHelper::input( $input_name, '', array_merge( $attributes, array( 'type' => 'password' ) ) );
		}

		if ( 'description' === $type || 'textarea' === $type ) {
			return FormFieldHelper::textarea( $input_name, $value, array_merge( $attributes, array( 'rows' => 4 ) ) );
		}

		if ( 'user_email' === $type || 'email' === $type ) {
			return FormFieldHelper::input( $input_name, $value, array_merge( $attributes, array( 'type' => 'email' ) ) );
		}

		if ( ! empty( $field['options'] ) && ( 'locale' === $type || 'role' === $type || ! empty( $field['multiple'] ) ) ) {
			if ( ! empty( $field['multiple'] ) ) {
				return FormFieldHelper::bootstrap_multiselect( $input_name, array(
					'data'       => $field['options'],
					'selected'   => (array) $value,
					'country_data' => $field['country_data'] ?? array(),
					'class'      => 'w-100',
				) + $attributes );
			}

			return FormFieldHelper::bootstrap_select( $input_name, array(
				'data'       => $field['options'],
				'selected'   => $value,
				'country_data' => $field['country_data'] ?? array(),
				'class'      => 'w-100',
			) + $attributes );
		}

		return FormFieldHelper::input( $input_name, $value, $attributes );
	}

	/**
	 * Locate an active theme override for the template.
	 *
	 * @param string $template_name Template slug.
	 * @return string
	 */
	private static function locate_theme_template( string $template_name ): string {
		$paths = array( get_stylesheet_directory(), get_template_directory() );
		$paths = array_values( array_unique( array_filter( $paths ) ) );
		foreach ( $paths as $path ) {
			foreach ( array(
				$path . '/accesspress/templates/' . $template_name . '.php',
				$path . '/accesspress/' . $template_name . '.php',
				$path . '/templates/accesspress/' . $template_name . '.php',
			) as $candidate ) {
				if ( file_exists( $candidate ) ) {
					return $candidate;
				}
			}
		}

		return '';
	}
}