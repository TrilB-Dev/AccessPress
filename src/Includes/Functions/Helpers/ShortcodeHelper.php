<?php
/**
 * Convenience methods for defining AccessPress shortcodes.
 *
 * @package AccessPress
 * @subpackage Includes\Functions\Helpers
 * @since 1.0.0
 */
namespace AccessPress\Includes\Functions\Helpers;

use AccessPress\Includes\Core\Shortcodes;
use AccessPress\Includes\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ShortcodeHelper {
	/**
	 * Get the shared shortcode registry instance.
	 *
	 * @return Shortcodes
	 */
	private static function instance(): Shortcodes {
		return Includes::get_instance()->core()->shortcodes();
	}

	/**
	 * Create a shortcode definition for a plugin shortcode list.
	 *
	 * @param array<string, mixed> $metadata Optional descriptor metadata.
	 * @return array<string, mixed>
	 */
	public static function define( string $tag, callable $callback, array $attributes = array(), array $metadata = array() ): array {
		return array_merge(
			array(
				'tag'         => $tag,
				'callback'    => $callback,
				'attributes'  => $attributes,
				'description' => '',
				'category'    => '',
				'enclosing'   => false,
				'tinymce'     => false,
			),
			$metadata
		);
	}

	/**
	 * Register a single shortcode definition.
	 *
	 * @param array<string, mixed> $definition Shortcode definition array.
	 * @param bool $replace Whether to replace an existing shortcode with the same tag.
	 * @return bool True on success, false on failure.
	 * @since 1.0.0
	 */
	public static function register( array $definition, bool $replace = false ): bool {
		return self::instance()->register( $definition, $replace );
	}

	/**
	 * Register a shortcode definition built directly from tag and callback.
	 *
	 * @param string $tag Literal shortcode tag.
	 * @param callable $callback Rendering callback.
	 * @param array<string, mixed> $attributes Default attributes.
	 * @param array<string, mixed> $metadata Optional metadata.
	 * @return bool True on success, false on failure.
	 */
	public static function register_definition( string $tag, callable $callback, array $attributes = array(), array $metadata = array(), bool $replace = false ): bool {
		return self::register( self::define( $tag, $callback, $attributes, $metadata ), $replace );
	}

	/**
	 * Register multiple shortcode definitions at once.
	 *
	 * @param array<int, array<string, mixed>> $definitions Array of shortcode definitions.
	 * @param bool $replace Whether to replace existing shortcodes with the same tags.
	 * @return array<int, string> List of registered shortcode tags.
	 * @since 1.0.0
	 */
	public static function register_many( array $definitions, bool $replace = false ): array {
		return self::instance()->register_many( $definitions, $replace );
	}

	/**
	 * Unregister a shortcode by its tag.
	 *
	 * @param string $tag Shortcode tag to unregister.
	 * @return bool True if successfully unregistered.
	 */
	public static function unregister( string $tag ): bool {
		return self::instance()->unregister( $tag );
	}

	/**
	 * Retrieve all registered shortcode definitions.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function definitions(): array {
		return self::instance()->definitions();
	}

	/**
	 * Get the definition for one shortcode by tag.
	 *
	 * @param string $tag Shortcode tag.
	 * @return array<string, mixed>|null
	 */
	public static function definition( string $tag ): ?array {
		return self::instance()->definition( $tag );
	}

	/**
	 * Check whether a shortcode has been registered.
	 *
	 * @param string $tag Shortcode tag.
	 * @return bool
	 */
	public static function has( string $tag ): bool {
		return self::instance()->has( $tag );
	}

	/**
	 * Directly process a shortcode callback through the shared registry.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @param string|null $content Enclosed content.
	 * @param string $tag Shortcode tag.
	 * @return string
	 */
	public static function process( $atts = array(), $content = null, string $tag = '' ): string {
		return self::instance()->process( $atts, $content, $tag );
	}
}



