<?php

namespace AccessPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	/**
	 * Capability definitions contributed by AccessPress extensions.
	 *
	 * @var array<string, array{group: string, label: string, description: string}>
	 */
	private static array $extensions = array();

	/**
	 * Return the core and registered extension capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	public static function definitions(): array {
		return array_merge(
			array(
				'accesspress_admin_view'                => array(
					'group'       => 'AccessPress',
					'label'       => __( 'View AccessPress Administration', 'accesspress' ),
					'description' => __( 'Allows access to the AccessPress dashboard and admin pages.', 'accesspress' ),
				),
				'accesspress_dashboard_view'            => array(
					'group'       => 'AccessPress',
					'label'       => __( 'View AccessPress Dashboard', 'accesspress' ),
					'description' => __( 'Allows viewing the AccessPress dashboard and summary status.', 'accesspress' ),
				),
				'accesspress_settings_general_view'     => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View AccessPress Settings', 'accesspress' ),
					'description' => __( 'Allows viewing the general AccessPress settings.', 'accesspress' ),
				),
				'accesspress_settings_general_edit'     => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'Edit AccessPress Settings', 'accesspress' ),
					'description' => __( 'Allows editing the AccessPress settings.', 'accesspress' ),
				),
				'accesspress_settings_access_view'      => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View Access Controls', 'accesspress' ),
					'description' => __( 'Allows viewing who can do what inside AccessPress.', 'accesspress' ),
				),
				'accesspress_settings_access_edit'      => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'Edit Access Controls', 'accesspress' ),
					'description' => __( 'Allows changing AccessPress access roles and permission boundaries.', 'accesspress' ),
				),
				'accesspress_settings_security_view'    => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View Security Settings', 'accesspress' ),
					'description' => __( 'Allows viewing security and export protection settings.', 'accesspress' ),
				),
				'accesspress_settings_security_edit'    => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'Edit Security Settings', 'accesspress' ),
					'description' => __( 'Allows editing export passwords, encryption controls, and security flags.', 'accesspress' ),
				),
				'accesspress_tools_import'              => array(
					'group'       => 'AccessPress Tools',
					'label'       => __( 'Import AccessPress Data', 'accesspress' ),
					'description' => __( 'Allows importing AccessPress exports into the system securely.', 'accesspress' ),
				),
				'accesspress_tools_export'              => array(
					'group'       => 'AccessPress Tools',
					'label'       => __( 'Export AccessPress Data', 'accesspress' ),
					'description' => __( 'Allows exporting AccessPress records using encryption and a password.', 'accesspress' ),
				),
				'accesspress_tools_debug'               => array(
					'group'       => 'AccessPress Tools',
					'label'       => __( 'View Debug Tools', 'accesspress' ),
					'description' => __( 'Allows using AccessPress debug and diagnostics tools.', 'accesspress' ),
				),
				'accesspress_tools_reset'               => array(
					'group'       => 'AccessPress Tools',
					'label'       => __( 'Reset AccessPress Data', 'accesspress' ),
					'description' => __( 'Allows resetting or clearing AccessPress records and related data.', 'accesspress' ),
				),
				'accesspress_settings_plugins_view'     => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View Plugin Settings', 'accesspress' ),
					'description' => __( 'Allows viewing AccessPress plugin settings.', 'accesspress' ),
				),
				'accesspress_settings_plugins_int_view' => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View Internal Plugin Settings', 'accesspress' ),
					'description' => __( 'Allows viewing settings for internal AccessPress plugins.', 'accesspress' ),
				),
				'accesspress_settings_plugins_int_edit' => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'Edit Internal Plugin Settings', 'accesspress' ),
					'description' => __( 'Allows editing settings for internal AccessPress plugins.', 'accesspress' ),
				),
				'accesspress_settings_plugins_ext_view' => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'View External Plugin Settings', 'accesspress' ),
					'description' => __( 'Allows viewing settings for external AccessPress plugins.', 'accesspress' ),
				),
				'accesspress_settings_plugins_ext_edit' => array(
					'group'       => 'AccessPress Settings',
					'label'       => __( 'Edit External Plugin Settings', 'accesspress' ),
					'description' => __( 'Allows editing settings for external AccessPress plugins.', 'accesspress' ),
				),
				'accesspress_roles_view' => [ 
					'group' => 'AccessPress User Roles', 
					'label' => __( 'View User Roles Manager', 'accesspress' ), 
					'description' => __( 'Allows viewing the AccessPress User Roles Manager.', 'accesspress' ) 
				],
				'accesspress_roles_create' => [ 
					'group' => 'AccessPress User Roles', 
					'label' => __( 'Create User Roles', 'accesspress' ), 
					'description' => __( 'Allows creating user roles.', 'accesspress' ) 
				],
				'accesspress_roles_edit' => [ 
					'group' => 'AccessPress User Roles', 
					'label' => __( 'Edit User Roles', 'accesspress' ), 
					'description' => __( 'Allows editing user roles.', 'accesspress' ) 
				],
				'accesspress_roles_delete' => [ 
					'group' => 'AccessPress User Roles', 
					'label' => __( 'Delete User Roles', 'accesspress' ), 
					'description' => __( 'Allows deleting user roles.', 'accesspress' ) 
				],
				'accesspress_user_management_dashboard' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'View User Management Dashboard', 'accesspress' ),
					'description' => __( 'Allows viewing the AccessPress User Management Dashboard.', 'accesspress' ),
				),
				'accesspress_user_management_roles' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'Manage User Roles', 'accesspress' ),
					'description' => __( 'Allows managing user roles within AccessPress User Management.', 'accesspress' ),
				),
				'accesspress_user_management_groups_view' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'View User Groups', 'accesspress' ),
					'description' => __( 'Allows viewing user groups within AccessPress User Management.', 'accesspress' ),
				),
				'accesspress_user_management_groups_edit' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'Edit User Groups', 'accesspress' ),
					'description' => __( 'Allows editing user groups within AccessPress User Management.', 'accesspress' ),
				),
				'accesspress_user_management_login' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'Manage User Login', 'accesspress' ),
					'description' => __( 'Allows managing user login within AccessPress User Management.', 'accesspress' ),
				),
				'accesspress_user_management_registration' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'Manage User Registration', 'accesspress' ),
					'description' => __( 'Allows managing user registration within AccessPress User Management.', 'accesspress' ),
				),
				'accesspress_user_management_profile' => array(
					'group'       => 'AccessPress User Management',
					'label'       => __( 'Manage User Profile', 'accesspress' ),
					'description' => __( 'Allows managing user profile within AccessPress User Management.', 'accesspress' ),
				),
			),
			self::$extensions
		);
	}

	/**
	 * Register definitions contributed by a plugin and install any missing caps.
	 *
	 * @param array<string, array{group: string, label: string, description: string}> $definitions Definitions to add.
	 * @return void
	 */
	public static function extend( array $definitions ): void {
		self::$extensions = array_merge( self::$extensions, $definitions );
		self::install();
	}

	/**
	 * Install missing capabilities without removing administrator customizations.
	 *
	 * @return void
	 */
	public static function install(): void {
		$administrator = get_role( 'administrator' );
		if ( ! $administrator ) {
			return;
		}

		foreach ( array_keys( self::definitions() ) as $capability ) {
			if ( ! $administrator->has_cap( $capability ) ) {
				$administrator->add_cap( $capability );
			}
		}
	}
}



