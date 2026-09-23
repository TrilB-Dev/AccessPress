<?php
/**
 * Elementor user profile widget for AccessPress.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Profile
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Profile;

use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class UserProfile extends Widgets {
	public const SLUG = 'accesspress_user_profile';

	protected function get_default_title(): string {
		return __( 'AccessPress User Profile', 'accesspress' );
	}

	protected function get_default_icon(): string {
		return 'eicon-user-circle';
	}

	protected function get_default_category(): string {
		return 'accesspress-user-management';
	}

	public function render(): void {
		$settings = $this->get_settings_for_display();
		Templates::render(
			'Profile/user-profile',
			array(
				'widget'   => $this,
				'settings' => $settings,
			)
		);
	}
}
