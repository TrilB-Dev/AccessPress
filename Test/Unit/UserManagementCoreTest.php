<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\UserManagement\Groups\Groups;
use AccessPress\Includes\UserManagement\Login\Login;
use AccessPress\Includes\UserManagement\Login\LoginTemplate;
use AccessPress\Includes\UserManagement\Login\LostPassword;
use AccessPress\Includes\UserManagement\Profile\Profile;
use AccessPress\Includes\UserManagement\Profile\ProfileTemplate;
use AccessPress\Includes\UserManagement\Registration\Registration;
use AccessPress\Includes\UserManagement\Registration\RegistrationTemplate;
use AccessPress\Includes\UserManagement\Registration\UserActivation;
use AccessPress\Includes\UserManagement\Roles\Roles;
use AccessPress\Includes\UserManagement\Security\Security;
use AccessPress\Includes\UserManagement\Security\Turnstile\Turnstile;
use AccessPress\Includes\UserManagement\Security\reCAPTCHA\reCAPTCHA;
use PHPUnit\Framework\TestCase;

final class UserManagementCoreTest extends TestCase {
	public function test_membership_group_api_is_available_and_has_default_definitions(): void {
		$this->assertTrue( method_exists( Groups::class, 'get_default_group_definitions' ) );
		$this->assertTrue( method_exists( Groups::class, 'register_default_groups' ) );
		$this->assertTrue( method_exists( Groups::class, 'get_user_groups' ) );
		$this->assertTrue( method_exists( Groups::class, 'sync_user_groups' ) );

		$definitions = Groups::get_default_group_definitions();
		$this->assertNotEmpty( $definitions );
		$this->assertArrayHasKey( 'free', $definitions );
		$this->assertArrayHasKey( 'bronze', $definitions );
		$this->assertArrayHasKey( 'silver', $definitions );
		$this->assertArrayHasKey( 'gold', $definitions );
		$this->assertArrayNotHasKey( 'grants', $definitions['gold'] );
		$this->assertArrayHasKey( 'label', $definitions['gold'] );
		$this->assertSame( 'Gold', $definitions['gold']['label'] );
	}

	public function test_login_components_expose_functional_api_surface(): void {
		$this->assertTrue( method_exists( Login::class, 'render_form' ) );
		$this->assertTrue( method_exists( Login::class, 'process_login' ) );
		$this->assertTrue( method_exists( LoginTemplate::class, 'render' ) );
		$this->assertTrue( method_exists( LostPassword::class, 'render_link' ) );
		$this->assertTrue( method_exists( LostPassword::class, 'render_form' ) );
	}

	public function test_profile_components_expose_functional_api_surface(): void {
		$this->assertTrue( method_exists( Profile::class, 'render_form' ) );
		$this->assertTrue( method_exists( Profile::class, 'process_profile_update' ) );
		$this->assertTrue( method_exists( ProfileTemplate::class, 'render' ) );
	}

	public function test_registration_and_activation_components_expose_functional_api_surface(): void {
		$this->assertTrue( method_exists( Registration::class, 'render_form' ) );
		$this->assertTrue( method_exists( Registration::class, 'process_registration' ) );
		$this->assertTrue( method_exists( RegistrationTemplate::class, 'render' ) );
		$this->assertTrue( method_exists( UserActivation::class, 'register_user' ) );
		$this->assertTrue( method_exists( UserActivation::class, 'activate_user' ) );
		$this->assertTrue( method_exists( UserActivation::class, 'is_user_activated' ) );
	}

	public function test_roles_components_expose_functional_api_surface(): void {
		$this->assertTrue( method_exists( Roles::class, 'get_available_roles' ) );
		$this->assertTrue( method_exists( Roles::class, 'get_user_roles' ) );
		$this->assertTrue( method_exists( Roles::class, 'sync_user_roles' ) );
		$this->assertTrue( method_exists( Roles::class, 'add_user_role' ) );
		$this->assertTrue( method_exists( Roles::class, 'remove_user_role' ) );
		$this->assertTrue( method_exists( Roles::class, 'get_max_roles' ) );
		$this->assertTrue( method_exists( Roles::class, 'set_max_roles' ) );
	}

	public function test_security_components_expose_functional_api_surface(): void {
		$this->assertTrue( method_exists( Security::class, 'render_recaptcha' ) );
		$this->assertTrue( method_exists( Security::class, 'verify_recaptcha' ) );
		$this->assertTrue( method_exists( Security::class, 'render_turnstile' ) );
		$this->assertTrue( method_exists( Security::class, 'verify_turnstile' ) );
		$this->assertTrue( method_exists( reCAPTCHA::class, 'render' ) );
		$this->assertTrue( method_exists( reCAPTCHA::class, 'verify' ) );
		$this->assertTrue( method_exists( Turnstile::class, 'render' ) );
		$this->assertTrue( method_exists( Turnstile::class, 'verify' ) );
	}
}
