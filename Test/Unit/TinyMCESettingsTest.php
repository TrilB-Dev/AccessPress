<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\Plugins\TinyMCE\Includes\Settings\Settings;
use PHPUnit\Framework\TestCase;

final class TinyMCESettingsTest extends TestCase {
	public function test_default_tinymce_plugins_include_syntax_highlighting_and_shortcuts(): void {
		$plugins = Settings::plugins();

		$this->assertContains( 'codesample', $plugins );
		$this->assertContains( 'autoresize', $plugins );
		$this->assertContains( 'quickbars', $plugins );
	}

	public function test_profile_fields_include_password_mapping(): void {
		$map = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_wp_field_map();
		$this->assertSame(
			array(
				'user_login',
				'password',
				'first_name',
				'last_name',
				'nickname',
				'display_name',
				'user_email',
				'description',
				'user_url',
				'locale',
				'admin_color',
				'rich_editing',
				'show_admin_bar_front',
				'show_admin_bar_admin',
				'role',
			),
			array_keys( $map )
		);
		$this->assertSame( 'user_pass', $map['password'] );

		$fields = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_builtin_fields();
		$this->assertNotEmpty( array_filter( $fields, static fn( $field ) => ( $field['type'] ?? '' ) === 'password' ) );
	}

	public function test_profile_layout_palette_contains_non_field_blocks(): void {
		$palette = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_palette();
		$this->assertArrayHasKey( 'layout', $palette );
		$this->assertNotEmpty( array_filter( $palette['layout'], static fn( $field ) => in_array( ( $field['type'] ?? '' ), array( 'title', 'text', 'spacer', 'separator', 'heading' ), true ) ) );
	}

	public function test_profile_locale_and_role_fields_include_bootstrap_select_metadata(): void {
		$fields = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_builtin_fields();
		$locale = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'locale' ) );
		$role   = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'role' ) );

		$this->assertNotFalse( $locale );
		$this->assertNotFalse( $role );
		$this->assertNotEmpty( $locale['options'] );
		$this->assertNotEmpty( $role['options'] );
		$this->assertArrayHasKey( 'description', $locale );
		$this->assertArrayHasKey( 'tooltip', $locale );
		$this->assertArrayHasKey( 'country_data', $locale );
		$this->assertTrue( (bool) ( $role['multiple'] ?? false ) );
		$this->assertSame( 'bootstrap_multiselect', $role['renderer'] ?? '' );
	}

	public function test_profile_fields_include_user_and_admin_visibility_options(): void {
		$fields = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_builtin_fields();
		$locale = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'locale' ) );
		$role   = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'role' ) );

		$this->assertNotFalse( $locale );
		$this->assertNotFalse( $role );
		$this->assertSame( 'user', $locale['visibility'] ?? '' );
		$this->assertTrue( (bool) ( $locale['editable'] ?? false ) );
		$this->assertSame( 'admin', $role['visibility'] ?? '' );
		$this->assertFalse( (bool) ( $role['editable'] ?? true ) );
		$this->assertTrue( (bool) ( $role['admin_only'] ?? false ) );
	}

	public function test_profile_forms_respect_visibility_and_editability_runtime_rules(): void {
		$fields = \AccessPress\Includes\UserManagement\Profile\ProfileFields::get_builtin_fields();
		$locale = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'locale' ) );
		$role   = current( array_filter( $fields, static fn( $field ) => ( $field['key'] ?? '' ) === 'role' ) );

		$this->assertNotFalse( $locale );
		$this->assertNotFalse( $role );
		$this->assertTrue( \AccessPress\Includes\UserManagement\Profile\ProfileFields::can_render_field( $locale, false ) );
		$this->assertTrue( \AccessPress\Includes\UserManagement\Profile\ProfileFields::can_edit_field( $locale, false ) );
		$this->assertFalse( \AccessPress\Includes\UserManagement\Profile\ProfileFields::can_render_field( $role, false ) );
		$this->assertTrue( \AccessPress\Includes\UserManagement\Profile\ProfileFields::can_edit_field( $role, true ) );
	}

	public function test_registration_defaults_have_locked_first_page_and_required_fields(): void {
		$tabs = \AccessPress\Includes\UserManagement\Registration\RegistrationTabs::get_default_tabs();

		$this->assertCount( 1, $tabs );
		$this->assertTrue( (bool) ( $tabs[0]['default_page'] ?? false ) );
		$this->assertFalse( (bool) ( $tabs[0]['can_delete'] ?? true ) );

		$keys = array_map(
			static fn( $field ) => (string) ( $field['key'] ?? '' ),
			$tabs[0]['fields'] ?? array()
		);

		$this->assertSame( array( 'username', 'email', 'password', 'confirm_password' ), $keys );
	}
}
