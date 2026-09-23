<?php
/**
 * Reusable field widget base for AccessPress forms.
 *
 * @package AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Fields;

use AccessPress\Includes\Plugins\Elementor\Includes\Templates\Templates;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class FieldWidget extends Widgets {
    /**
     * The slug for the field widget.
     * 
     * @var string The slug for the field widget.
     * @since 1.0.0
     */
	public const SLUG = 'accesspress_field_widget';
    /**
     * Get the field name.
     *
     * @since 1.0.0
     * @return string
     */
	abstract protected function field_name(): string;
    /**
     * Get the field type.
     *
     * @since 1.0.0
     * @return string
     */
	protected function field_type(): string {
		return 'text';
	}
    /**
     * Get the default title for the field widget.
     *
     * @since 1.0.0
     * @return string
     */
	protected function get_default_title(): string {
		return __( 'AccessPress Field', 'accesspress' );
	}
    /**
     * Get the default icon for the field widget.
     *
     * @since 1.0.0
     * @return string
     */
	protected function get_default_icon(): string {
		return 'eicon-form-horizontal';
	}

    /**
     * Get the default category for the field widget.
     *
     * @since 1.0.0
     * @return string
     */
	protected function get_default_category(): string {
		return 'accesspress-user-management';
	}
    /**
     * Register the controls for the field widget.
     *
     * @since 1.0.0
     * @return void
     */
	protected function register_controls(): void {
        $this->start_controls_section(
            'section_content',
            [ 
                'label' => __( 'Content', 'trilbdev' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
			'field_context',
			array(
				'label'   => __( 'Context', 'accesspress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'login_form'    => __( 'Login form', 'accesspress' ),
					'registration_form' => __( 'Registration form', 'accesspress' ),
					'profile'       => __( 'Profile field', 'accesspress' ),
					'profile_form'  => __( 'Profile form field', 'accesspress' ),
				),
				'default' => 'login_form',
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'accesspress' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Field', 'accesspress' ),
			)
		);

		$this->add_control(
			'required',
			array(
				'label'        => __( 'Required', 'accesspress' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'accesspress' ),
				'label_off'    => __( 'No', 'accesspress' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [ 
                'label' => __( 'Style', 'trilbdev' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

		$this->end_controls_section();
	}

	public function render(): void {
		$settings = $this->get_settings_for_display();
		Templates::render(
			'Fields/' . sanitize_title( $this->field_name() ) . '-field',
			array(
				'widget'   => $this,
				'settings' => $settings,
			)
		);
	}
}
