<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\Settings\Settings;
use PHPUnit\Framework\TestCase;

final class SettingsManagerTest extends TestCase {
	public function test_general_group_is_stored_with_accesspress_prefix(): void {
		global $wpdb;

		Settings::set_group(
			'general',
			array(
				'registration_page' => '42',
				'login_page'       => '43',
			)
		);

		$this->assertSame( 'accesspress_general', $wpdb->tables['wp_accesspress_settings'][0]['setting_group'] ?? '' );
		$this->assertSame( array( 'registration_page' => '42', 'login_page' => '43' ), Settings::get_group( 'general', array() ) );
	}
}
