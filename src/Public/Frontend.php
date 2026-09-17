<?php
/**
 * Frontend class for the AccessPress plugin.
 *
 * @package AccessPress\Public
 */
namespace AccessPress\Public;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Frontend {
	/**
	 * Filter the post content for frontend rendering.
	 *
	 * @param string $content The post content.
	 * @return string
	 */
	public function filter_content( string $content ): string {
		return $content;
	}

	/**
	 * Append AccessPress body classes.
	 *
	 * @param array<int, string> $classes Existing body classes.
	 * @return array<int, string>
	 */
	public function body_classes( array $classes ): array {
		return $classes;
	}
}



