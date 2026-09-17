<?php
/**
 * User Roles Manager AccessPress Plugin Includes
 *
 * @package AccessPress
 * @subpackage UserRolesManager\Includes
 * @since 1.0.0
 */

namespace AccessPress\Includes\Plugins\UserRolesManager\Includes;
use AccessPress\Includes\Plugins\UserRolesManager\Includes\Core\Capabilities;
use AccessPress\Includes\Plugins\UserRolesManager\Includes\Settings\Settings;

final class Includes {
    /**
     * The singleton instance of the Includes class.
     *
     * @since 1.0.0
     * @var self|null
     */
    private static ?self $instance = null;
    /**
     * The settings instance.
     *
     * @since 1.0.0
     * @var Settings
     */
    private Settings $settings;
    /**
     * The capabilities instance.
     *
     * @since 1.0.0
     * @var Capabilities
     */
    private Capabilities $capabilities;
    /**
     * Constructs the Includes instance.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->settings = new Settings();
        $this->capabilities = new Capabilities();
    }
    /**
     * Returns the singleton instance of the Includes class.
     *
     * @since 1.0.0
     * @return self The singleton instance.
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * Initializes the Includes instance.
     *
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        Capabilities::register();
        $this->settings->register();
    }

    /**
     * Returns the settings instance.
     *
     * @since 1.0.0
     * @return Settings The settings instance.
     */
    public function settings(): Settings {
        return $this->settings;
    }

    /**
     * Returns the capabilities instance.
     *
     * @since 1.0.0
     * @return Capabilities The capabilities instance.
     */
    public function capabilities(): Capabilities {
        return $this->capabilities;
    }
}