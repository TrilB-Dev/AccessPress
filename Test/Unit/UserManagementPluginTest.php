<?php

namespace {
	if ( ! function_exists( 'add_shortcode' ) ) {
		function add_shortcode( $tag, $callback ) {
			$GLOBALS['accesspress_test_shortcodes'][ (string) $tag ] = $callback;
			return true;
		}
	}

	if ( ! function_exists( 'shortcode_exists' ) ) {
		function shortcode_exists( $tag ) {
			return isset( $GLOBALS['accesspress_test_shortcodes'][ (string) $tag ] );
		}
	}

	if ( ! function_exists( 'remove_shortcode' ) ) {
		function remove_shortcode( $tag ) {
			unset( $GLOBALS['accesspress_test_shortcodes'][ (string) $tag ] );
			return true;
		}
	}

	if ( ! function_exists( 'add_action' ) ) {
		function add_action( $hook, $callback ) {
			$GLOBALS['accesspress_test_actions'][ (string) $hook ] = $callback;
			return true;
		}
	}

	if ( ! function_exists( 'has_action' ) ) {
		function has_action( $hook, $callback = null ) {
			return isset( $GLOBALS['accesspress_test_actions'][ (string) $hook ] );
		}
	}

	if ( ! function_exists( 'wp_schedule_event' ) ) {
		function wp_schedule_event( $timestamp, $recurrence, $hook, $args = array() ) {
			$GLOBALS['accesspress_test_crons'][ (string) $hook ] = array(
				'timestamp' => $timestamp,
				'recurrence' => $recurrence,
				'args' => $args,
			);
			return true;
		}
	}

	if ( ! function_exists( 'wp_next_scheduled' ) ) {
		function wp_next_scheduled( $hook, $args = array() ) {
			return isset( $GLOBALS['accesspress_test_crons'][ (string) $hook ] ) ? time() : false;
		}
	}

	if ( ! function_exists( 'wp_clear_scheduled_hook' ) ) {
		function wp_clear_scheduled_hook( $hook ) {
			unset( $GLOBALS['accesspress_test_crons'][ (string) $hook ] );
			return true;
		}
	}
}

namespace AccessPress\Test\Unit {

use AccessPress\Admin\Admin;
use AccessPress\Admin\Manager\Dashboard\DashboardManager;
use AccessPress\Admin\Manager\Reports\ReportsManager;
use AccessPress\Includes\Core\CronJobs;
use AccessPress\Includes\Core\Menus;
use AccessPress\Includes\Core\Shortcodes;
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

	public function test_batch_shortcode_registry_syncs_with_shared_definitions(): void {
		$GLOBALS['accesspress_test_shortcodes'] = array();
		$callback = static function ( $atts ) {
			return 'ok';
		};

		Shortcodes::create_registry(
			array(
				array(
					'tag'      => 'accesspress_banner',
					'callback' => $callback,
				),
			)
		);

		$this->assertArrayHasKey( 'accesspress_banner', Shortcodes::definitions() );
		$this->assertTrue( shortcode_exists( 'accesspress_banner' ) );
	}

	public function test_batch_cron_registry_syncs_with_shared_definitions(): void {
		$GLOBALS['accesspress_test_actions'] = array();
		$GLOBALS['accesspress_test_crons'] = array();

		CronJobs::create_registry(
			array(
				array(
					'scope' => 'core',
					'hook' => 'cleanup',
					'schedule' => 'hourly',
					'callback' => static function () {
						return true;
					},
				),
			)
		);

		$this->assertArrayHasKey( 'accesspress_core_cleanup', CronJobs::definitions() );
		$this->assertTrue( has_action( 'accesspress_core_cleanup' ) );
	}
}

}
