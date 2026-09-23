<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\UserManagement\Groups\Groups;
use AccessPress\Includes\UserManagement\UserManagement;
use PHPUnit\Framework\TestCase;

final class GroupsTest extends TestCase {
	public function test_default_groups_are_loaded_from_the_schema_layer(): void {
		$groups = Groups::get_group_definitions();

		$this->assertNotEmpty( $groups );
		$this->assertArrayHasKey( 'free', $groups );
		$this->assertSame( 'Free', $groups['free']['label'] );
		$this->assertSame( 'Free', UserManagement::get_instance()->get_group( 'free' )['label'] );
	}
}
