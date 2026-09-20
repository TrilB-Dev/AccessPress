<?php
/**
 * Core component for AccessPress.
 *
 * @package AccessPress\Includes\Core
 */
namespace AccessPress\Includes\Core;

use AccessPress\Includes\Core\WP\WPLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinate AccessPress core registration and reusable WordPress services.
 *
 * Post types and taxonomies remain separate components; this class provides a
 * single lifecycle boundary for AccessPress and extension plugins.
 */
final class Core {
	/**
	 * @var PostType Post type registrar.
	 */
	private PostType $post_types;
	/**
	 * @var Taxonomy Taxonomy registrar.
	 */
	private Taxonomy $taxonomy;
	/**
	 * @var Shortcodes Shortcode registrar.
	 */
	private Shortcodes $shortcodes;
	/**
	 * @var Menus Menu registry.
	 */
	private Menus $menus;
	/**
	 * @var Editor Editor service.
	 */
	private Editor $editor;
	/**
	 * @var Schema Database schema service.
	 */
	private Schema $schema;
	/**
	 * @var bool Whether core registration has run.
	 */
	private bool $registered = false;

	/**
	 * Create the core coordinator with optional component instances.
	 *
	 * @param PostType|null $post_types Post type registrar.
	 * @param Taxonomy|null $taxonomy Taxonomy registrar.
	 * @param Shortcodes|null $shortcodes Shortcode registrar.
	 * @param Menus|null $menus Menu registrar.
	 * @param Editor|null $editor Editor service.
	 * @param Schema|null $schema Schema service.
	 */
	public function __construct( ?PostType $post_types = null, ?Taxonomy $taxonomy = null, ?Shortcodes $shortcodes = null, ?Menus $menus = null, ?Editor $editor = null, ?Schema $schema = null ) {
		$this->post_types = $post_types ?? new PostType();
		$this->taxonomy   = $taxonomy ?? new Taxonomy();
		$this->shortcodes = $shortcodes ?? new Shortcodes();
		$this->menus      = $menus ?? new Menus();
		$this->editor     = $editor ?? new Editor();
		$this->schema     = $schema ?? new Schema();
	}

	/**
	 * Register the core components once.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( $this->registered ) {
			return;
		}

		$this->install_capabilities();
		$this->install_roles();
		//$this->post_types->register();
		//$this->taxonomies->register();
		$this->registered = true;
	}

	/**
	 * Attach core registration to a WordPress loader.
	 *
	 * @param WPLoader $loader Hook loader.
	 * @param string   $hook WordPress action name.
	 * @param int      $priority Registration priority.
	 * @return self
	 */
	public function register_hooks( WPLoader $loader, string $hook = 'init', int $priority = 10 ): self {
		$loader->add_action( $hook, $this, 'register', $priority, 0 );
		return $this;
	}

	/**
	 * Get the post type registrar.
	 *
	 * @return PostType Post type registrar.
	 */
	//public function post_types(): PostType {
	//	return $this->post_types;
	//}

	/**
	 * Get the taxonomy registrar.
	 *
	 * @return Taxonomy Taxonomy registrar.
	 */
	//public function taxonomies(): Taxonomy {
	//	return $this->taxonomies;
	//}

	public function shortcodes(): Shortcodes {
		return $this->shortcodes;
	}

	/**
	 * Get the post type registrar.
	 *
	 * @return PostType Post type registrar.
	 */
	public function post_types(): PostType {
		return $this->post_types;
	}

	/**
	 * Get the taxonomy registrar.
	 *
	 * @return Taxonomy Taxonomy registrar.
	 */
	public function taxonomies(): Taxonomy {
		return $this->taxonomy;
	}

	/**
	 * Alias for the taxonomy registrar in singular form.
	 *
	 * @return Taxonomy Taxonomy registrar.
	 */
	public function taxonomy(): Taxonomy {
		return $this->taxonomy;
	}

	/**
	 * Get the core menu registry.
	 *
	 * @return Menus Menu registry.
	 */
	public function menus(): Menus {
		return $this->menus;
	}

	/**
	 * Get the editor helper service.
	 *
	 * @return Editor Editor service.
	 */
	public function editor(): Editor {
		return $this->editor;
	}

	/**
	 * Get the schema helper service.
	 *
	 * @return Schema Schema helper service.
	 */
	public function schema(): Schema {
		return $this->schema;
	}

	/**
	 * Return the cron service class handle.
	 *
	 * @return string
	 */
	public function cron_jobs(): string {
		return CronJobs::class;
	}

	/**
	 * Return the capabilities service class handle.
	 *
	 * @return string
	 */
	public function capabilities(): string {
		return Capabilities::class;
	}

	/**
	 * Register a shortcode definition directly through the core registry.
	 *
	 * @param array<string, mixed> $definition Shortcode definition.
	 * @param bool $replace Whether to replace an existing shortcode.
	 * @return bool
	 */
	public function register_shortcode( array $definition, bool $replace = false ): bool {
		return $this->shortcodes->register( $definition, $replace );
	}

	/**
	 * Register multiple shortcode definitions through the core registry.
	 *
	 * @param array<int, array<string, mixed>> $definitions Shortcode definitions.
	 * @param bool $replace Whether to replace existing shortcodes.
	 * @return array<int, string>
	 */
	public function register_shortcodes( array $definitions, bool $replace = false ): array {
		return $this->shortcodes->register_many( $definitions, $replace );
	}

	/**
	 * Register a WordPress menu location through the core registry.
	 *
	 * @param string $location Menu location slug.
	 * @param string $label Human-readable label.
	 * @param bool $overwrite Whether to replace an existing location.
	 * @return self
	 */
	public function register_menu_location( string $location, string $label, bool $overwrite = false ): self {
		$this->menus->register_location( $location, $label, $overwrite );
		return $this;
	}

	/**
	 * Register multiple menu locations through the core registry.
	 *
	 * @param array<string, string> $locations Menu slugs and labels.
	 * @param bool $overwrite Whether to replace existing values.
	 * @return self
	 */
	public function register_menu_locations( array $locations, bool $overwrite = false ): self {
		foreach ( $locations as $location => $label ) {
			$this->register_menu_location( (string) $location, (string) $label, $overwrite );
		}

		return $this;
	}

	/**
	 * Install the core AccessPress capabilities.
	 *
	 * @return void
	 */
	public function install_capabilities(): void {
		Capabilities::install();
	}

	/**
	 * Install the core AccessPress roles.
	 *
	 * @return void
	 */
	public function install_roles(): void {
		Roles::install();
	}

	/**
	 * Return whether core registration has run.
	 *
	 * @return bool Registration state.
	 */
	public function is_registered(): bool {
		return $this->registered;
	}
}



