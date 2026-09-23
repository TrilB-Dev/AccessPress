<?php
/**
 * Elementor user menu widget for AccessPress.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\LoginRegister
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\LoginRegister;

use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserMenuItem extends Widgets {
	public const SLUG = 'accesspress_user_menu_item';

	protected function get_default_title(): string {
		return __( 'AccessPress User Menu Item', 'accesspress' );
	}

	protected function get_default_icon(): string {
		return 'eicon-user-menu';
	}

	protected function get_default_category(): string {
		return 'accesspress-user-management';
	}

	public function render(): void {
		$settings = $this->get_settings_for_display();
		Templates::render(
			'Login-Register/user-menu-item',
			array(
				'widget'   => $this,
				'settings' => $settings,
			)
		);
	}
}
