<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Includes
 *
 * @package AccessPress
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Gutenburg\Includes;
use AccessPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;
use AccessPress\Includes\Plugins\Gutenburg\Includes\Blocks\Blocks;

final class Includes {
    private static ?self $instance = null;
    private Settings $settings;

    private function __construct() {
        $this->settings = new Settings();
    }

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    public function init(): void {
        $this->settings->register();
        Blocks::register();
    }

    public function settings(): Settings {
        return $this->settings;
    }
}
