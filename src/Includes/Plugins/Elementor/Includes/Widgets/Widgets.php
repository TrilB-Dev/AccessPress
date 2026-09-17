<?php
/**
 * 
 * AccessPress Elementor Widget Integration Class
 * 
 * A Class that handles the integration of the AccessPress plugin with Elementor Widgets.
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 * @since 1.0.0
 */
namespace AccessPress\Includes\Plugins\Elementor\Includes\Widgets;

use Elementor\Widget_Base;
use AccessPress\Includes\Plugins\Elementor\Includes\Settings\Settings;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiBreadcrumbs;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiList;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiReadingTime;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiRelated;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiTOC;
use AccessPress\Includes\Plugins\Elementor\Includes\Widgets\AccessPress\WikiSearchModal;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Widgets extends Widget_Base {
    abstract protected function get_default_title(): string;
    abstract protected function get_default_icon(): string;
    abstract protected function get_default_category(): string;

    protected function get_default_keywords(): array {
        return [ 'wiki', 'knowledge base' ];
    }

    public function get_name(): string {
        return (string) constant( static::class . '::SLUG' );
    }

    public function get_title(): string {
        return $this->get_default_title();
    }

    public function get_icon(): string {
        return $this->get_default_icon();
    }

    public function get_categories(): array {
        return [ $this->get_default_category() ];
    }

    public function get_keywords(): array {
        return $this->get_default_keywords();
    }

    public static function register( $manager ): void {
        $classes = [
            WikiBreadcrumbs::class,
            WikiList::class,
            WikiReadingTime::class,
            WikiRelated::class,
            WikiTOC::class,
            WikiSearchModal::class,
        ];

        foreach ( $classes as $class ) {
            $slug = (string) constant( $class . '::SLUG' );
            $setting_slug = str_replace( 'accesspress_', '', $slug );
            if ( Settings::widget_enabled( $setting_slug ) ) {
                $manager->register( new $class() );
            }
        }
    }
}

