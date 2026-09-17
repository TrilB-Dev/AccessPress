<?php

namespace AccessPress\Includes\Plugins\UserRolesManager\Includes\Core;

use AccessPress\Includes\Core\Capabilities as CoreCapabilities;

final class Capabilities extends CoreCapabilities {
	/**
	 * Register User Roles Manager capabilities with AccessPress core.
	 *
	 * @return void
	 */
	public static function register(): void {
		parent::extend( self::plugin_definitions() );
	}

	/**
	 * Return User Roles Manager capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	private static function plugin_definitions(): array {
		return [
			'accesspress_roles_view' => [ 'group' => 'AccessPress User Roles', 'label' => __( 'View User Roles Manager', 'accesspress' ), 'description' => __( 'Allows viewing the AccessPress User Roles Manager.', 'accesspress' ) ],
			'accesspress_roles_create' => [ 'group' => 'AccessPress User Roles', 'label' => __( 'Create User Roles', 'accesspress' ), 'description' => __( 'Allows creating user roles.', 'accesspress' ) ],
			'accesspress_roles_edit' => [ 'group' => 'AccessPress User Roles', 'label' => __( 'Edit User Roles', 'accesspress' ), 'description' => __( 'Allows editing user roles.', 'accesspress' ) ],
			'accesspress_roles_delete' => [ 'group' => 'AccessPress User Roles', 'label' => __( 'Delete User Roles', 'accesspress' ), 'description' => __( 'Allows deleting user roles.', 'accesspress' ) ],
		];
	}
}