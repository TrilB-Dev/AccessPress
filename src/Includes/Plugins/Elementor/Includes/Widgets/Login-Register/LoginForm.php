<?php
/**
 * Elementor Login Form widget for AccessPress.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\LoginRegister
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\LoginRegister;

use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LoginForm extends Widgets {
	public const SLUG = 'accesspress_login_form';

	protected function get_default_title(): string {
		return __( 'AccessPress Login Form', 'accesspress' );
	}

	protected function get_default_icon(): string {
		return 'eicon-lock-user';
	}

	protected function get_default_category(): string {
		return 'accesspress-user-management';
	}

	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'accesspress' ),
			)
		);

		$this->add_control(
			'redirect_to',
			array(
				'label'       => __( 'Redirect after login', 'accesspress' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => home_url( '/' ),
				'placeholder' => __( 'https://example.com/account', 'accesspress' ),
			)
		);

		$this->end_controls_section();

        
	}

	public function render(): void {
		$settings = $this->get_settings_for_display();
		Templates::render(
			'Login-Register/login-form',
			array(
				'widget'   => $this,
				'settings' => $settings,
			)
		);
	}
}


