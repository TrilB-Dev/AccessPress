<?php

namespace AccessPress\Test\Unit;

use AccessPress\Admin\Admin;
use AccessPress\Admin\Manager\Dashboard\DashboardManager;
use AccessPress\Admin\Manager\Reports\ReportsManager;
use AccessPress\Includes\Core\Menus;
use AccessPress\Includes\Functions\Admin\FunctionsSidebar;
use AccessPress\Includes\Functions\Helpers\MenuHelper;
use AccessPress\Includes\UserManagement\UserManagement;
use PHPUnit\Framework\TestCase;

final class UserManagementPluginTest extends TestCase {
	public function test_core_user_management_exists_and_is_not_a_plugin_extension(): void {
		$this->assertTrue( class_exists( UserManagement::class ) );
		$this->assertFalse( class_exists( '\\AccessPress\\Includes\\Plugins\\UserManagement\\UserManagement' ) );
		$this->assertSame( 'accesspress-user-management', UserManagement::get_instance()->get_slug() );
	}

	public function test_pmpro_style_admin_pages_exist_and_are_registered(): void {
		$this->assertTrue( class_exists( DashboardManager::class ) );
		$this->assertTrue( class_exists( ReportsManager::class ) );
		$this->assertTrue( method_exists( Admin::class, 'route_manager' ) );
		$this->assertTrue( method_exists( Admin::class, 'render_users' ) );
		$this->assertArrayHasKey( 'user-management', FunctionsSidebar::get_sidebar_groups() );
		$this->assertArrayHasKey( 'reports', FunctionsSidebar::get_sidebar_groups() );
	}

	public function test_menu_registration_contract_exists(): void {
		$this->assertTrue( class_exists( Menus::class ) );
		$this->assertTrue( class_exists( MenuHelper::class ) );

		$menus = new Menus();
		$menus->register_location( 'header', 'Header navigation' );
		$this->assertArrayHasKey( 'header', $menus->get_locations() );

		$helper = MenuHelper::register_location( 'footer', 'Footer navigation' );
		$this->assertArrayHasKey( 'footer', $helper->get_locations() );
	}

	public function test_user_management_tab_aliases_are_normalized(): void {
		$manager = new \AccessPress\Admin\Manager\Users\UserManager( false );
		$method = new \ReflectionMethod( $manager, 'normalize_tab' );
		$method->setAccessible( true );

		$this->assertSame( 'dashboard', $method->invoke( $manager, 'overview' ) );
		$this->assertSame( 'groups', $method->invoke( $manager, 'user-groups' ) );
		$this->assertSame( 'roles', $method->invoke( $manager, 'user-roles' ) );
		$this->assertSame( 'login', $method->invoke( $manager, 'user-login' ) );
	}
}
