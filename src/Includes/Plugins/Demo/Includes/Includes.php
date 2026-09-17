<?php
/**
 * Demo Wiki Plugin Includes
 *
 * @package AccessPress
 * @subpackage Plugins\Demo\Includes
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\Demo\Includes;
use AccessPress\Includes\Plugins\Demo\Includes\Settings\Settings;
use AccessPress\Includes\Plugins\Demo\Includes\Shortcodes\Shortcodes;

final class Includes {
    /**
     * Singleton instance of the Includes class.
     * 
     * @var ?self The singleton instance of the Includes class.
     * @since 1.0.0
     */
    private static ?self $instance = null;
    /**
     * Settings instance for the Demo plugin.
     *
     * @var Settings
     * @since 1.0.0
     */
    private Settings $settings;
    /**
     * Shortcodes instance for the Demo plugin.
     *
     * @var Shortcodes
     * @since 1.0.0
     */
    private Shortcodes $shortcodes;
    /**
     * Constructor for the Includes class.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->settings = new Settings();
        $this->shortcodes = new Shortcodes();
    }
    /**
     * Initializes the Includes class instance.
     *
     * @since 1.0.0
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * Initializes the Demo plugin includes.
     *
     * @since 1.0.0
     */
    public function init(): void {
        $this->settings->register();
    }
    /**
     * Retrieves the Settings instance for the Demo plugin.
     *
     * @return Settings The Settings instance.
     * @since 1.0.0
     */
    public function settings(): Settings {
        return $this->settings;
    }
    /**
     * Retrieves the Shortcodes instance for the Demo plugin.
     *
     * @return Shortcodes The Shortcodes instance.
     * @since 1.0.0
     */
    public function shortcodes(): Shortcodes {
        return $this->shortcodes;
    }
}