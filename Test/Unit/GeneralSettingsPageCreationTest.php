<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\Functions\Admin\FunctionsPlugins;
use AccessPress\Includes\Functions\Admin\FunctionsSettings;
use PHPUnit\Framework\TestCase;

final class GeneralSettingsPageCreationTest extends TestCase {
	public function test_sanitize_general_creates_selected_pages_and_persists_page_ids(): void {
		$GLOBALS['__accesspress_pages'] = array();

		$settings = new FunctionsSettings( new FunctionsPlugins() );
		$result = $settings->sanitize_general(
			array(
				'registration_page' => 'create:register',
				'login_page' => 'create:login',
				'my_account_page' => 'create:profile',
				'lost_password_page' => 'create:lost-password',
			)
		);

		$this->assertSame( '1', $result['registration_page'] );
		$this->assertSame( '2', $result['login_page'] );
		$this->assertSame( '3', $result['my_account_page'] );
		$this->assertSame( '4', $result['lost_password_page'] );

		$this->assertSame( 'Register', $GLOBALS['__accesspress_pages'][1]['post_title'] );
		$this->assertSame( 'Login', $GLOBALS['__accesspress_pages'][2]['post_title'] );
		$this->assertSame( 'My Account', $GLOBALS['__accesspress_pages'][3]['post_title'] );
		$this->assertSame( 'Lost Password', $GLOBALS['__accesspress_pages'][4]['post_title'] );
		$this->assertSame( '[accesspress_register]', $GLOBALS['__accesspress_pages'][1]['post_content'] );
		$this->assertSame( '[accesspress_login]', $GLOBALS['__accesspress_pages'][2]['post_content'] );
		$this->assertSame( '[accesspress_profile]', $GLOBALS['__accesspress_pages'][3]['post_content'] );
		$this->assertSame( '[accesspress_lost_password]', $GLOBALS['__accesspress_pages'][4]['post_content'] );
	}
}
