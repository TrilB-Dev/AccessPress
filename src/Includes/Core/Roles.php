<?php
/**
 * Role registration for AccessPress.
 *
 * @package AccessPress\Includes\Core
 * @since 1.0.0
 */
namespace AccessPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Roles {
	/**
	 * Role identifiers for AccessPress.
	 * 
	 * @since 1.0.0
	 * @var string The role identifier for the manager role.
	 */
	public const MANAGER_ROLE = 'accesspress_manager';


	/**
	 * Install the default AccessPress roles used by AccessPress.
	 *
	 * @return void
	 */
	public static function install(): void {
		if ( ! function_exists( 'add_role' ) || ! function_exists( 'get_role' ) ) {
			return;
		}

		self::manager_role();
	}
	/**
	 * Get the manager role identifier.
	 *
	 * @since 1.0.0
	 * @return string The manager role identifier.
	 */
	public static function manager_role(): string {
		if ( ! get_role( self::MANAGER_ROLE ) ) {
			add_role(
				self::MANAGER_ROLE,
				__( 'User Manager', 'accesspress' ),
				array(
					'read'                       => true,
					'add_users'       			 => true,
					'edit_users'        		 => true,
					'create_users'      		 => true,
					'delete_users'  			 => true,
					'accesspress_suspend_users'  => true,
					'accesspress_ban_users'  	 => true,
				)
			);
		}
		return self::MANAGER_ROLE;
	}
}
