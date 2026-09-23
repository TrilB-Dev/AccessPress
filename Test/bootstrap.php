<?php

// phpcs:disable WordPress.Files.FileName, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize, WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.json_encode_json_encode, WordPress.WP.AlternativeFunctions.parse_url_parse_url, WordPress.WP.AlternativeFunctions.strip_tags_strip_tags, WordPress.WP.GlobalVariablesOverride.Prohibited, Generic.CodeAnalysis.UnusedFunctionParameter, PEAR.NamingConventions.ValidClassName, Squiz.Commenting.VariableComment, Squiz.Commenting.ClassComment, Squiz.Commenting.FileComment, Squiz.Classes.ClassNamePrefix
// phpcs:disable Generic.Files.OneObjectStructurePerFile, Universal.Files.SeparateFunctionsFromOO.Mixed

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 );
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );
		$key = preg_replace( '/[^a-z0-9_\-]+/', '', $key );
		return (string) $key;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return (int) filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = null ) {
		return (string) $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = null ) {
		return (string) $text;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		if ( is_array( $value ) ) {
			return '';
		}
		return trim( strip_tags( (string) $value ) );
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $title ) {
		$title = strtolower( (string) $title );
		$title = preg_replace( '/[^a-z0-9\-_]+/', '-', $title );
		$title = preg_replace( '/-+/', '-', $title );
		$title = trim( $title, '-' );
		return $title;
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $value ) {
		return sanitize_text_field( $value );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return is_string( $url ) ? trim( $url ) : '';
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'get_page_by_path' ) ) {
	function get_page_by_path( $page_path, $output = OBJECT, $post_type = 'page' ) {
		$page_path = sanitize_title( (string) $page_path );
		if ( ! isset( $GLOBALS['__accesspress_pages'] ) || ! is_array( $GLOBALS['__accesspress_pages'] ) ) {
			return null;
		}
		foreach ( $GLOBALS['__accesspress_pages'] as $page ) {
			if ( ( (string) ( $page['post_name'] ?? '' ) ) === $page_path && ( (string) ( $page['post_type'] ?? 'page' ) ) === (string) $post_type ) {
				if ( OBJECT === $output ) {
					return (object) $page;
				}
				return $page['ID'];
			}
		}
		return null;
	}
}

if ( ! function_exists( 'wp_insert_post' ) ) {
	function wp_insert_post( $postarr = array() ) {
		if ( ! isset( $GLOBALS['__accesspress_pages'] ) || ! is_array( $GLOBALS['__accesspress_pages'] ) ) {
			$GLOBALS['__accesspress_pages'] = array();
		}
		$post_name = sanitize_title( (string) ( $postarr['post_name'] ?? '' ) );
		$existing = get_page_by_path( $post_name, OBJECT, $postarr['post_type'] ?? 'page' );
		if ( $existing ) {
			return is_object( $existing ) ? (int) $existing->ID : (int) $existing;
		}
		$id = count( $GLOBALS['__accesspress_pages'] ) + 1;
		$GLOBALS['__accesspress_pages'][ $id ] = array(
			'ID'         => $id,
			'post_name'  => $post_name,
			'post_title' => (string) ( $postarr['post_title'] ?? '' ),
			'post_type'  => (string) ( $postarr['post_type'] ?? 'page' ),
			'post_status' => (string) ( $postarr['post_status'] ?? 'publish' ),
			'post_content' => (string) ( $postarr['post_content'] ?? '' ),
		);
		return $id;
	}
}

if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '' ) {
		return 'http://example.com/wp-admin/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return true;
	}
}

if ( ! function_exists( 'add_menu_page' ) ) {
	function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {
		return true;
	}
}

if ( ! function_exists( 'add_submenu_page' ) ) {
	function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null ) {
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value ) {
		return $value;
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type = 'timestamp', $gmt = 0 ) {
		if ( 'mysql' === $type ) {
			return gmdate( 'Y-m-d H:i:s' );
		}
		return time();
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data ) {
		return json_encode( $data );
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}

if ( ! function_exists( 'maybe_serialize' ) ) {
	function maybe_serialize( $data ) {
		return serialize( $data );
	}
}

if ( ! function_exists( 'maybe_unserialize' ) ) {
	function maybe_unserialize( $data ) {
		if ( is_scalar( $data ) ) {
			$unserialized = @unserialize( (string) $data );
			return false === $unserialized ? $data : $unserialized;
		}
		return $data;
	}
}

if ( ! function_exists( 'dbDelta' ) ) {
	function dbDelta( $sql ) {
		return true;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		if ( ! isset( $GLOBALS['__accesspress_options'] ) || ! is_array( $GLOBALS['__accesspress_options'] ) ) {
			$GLOBALS['__accesspress_options'] = array();
		}
		return array_key_exists( $option, $GLOBALS['__accesspress_options'] ) ? $GLOBALS['__accesspress_options'][ $option ] : $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value, $autoload = null ) {
		if ( ! isset( $GLOBALS['__accesspress_options'] ) || ! is_array( $GLOBALS['__accesspress_options'] ) ) {
			$GLOBALS['__accesspress_options'] = array();
		}
		$GLOBALS['__accesspress_options'][ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'get_user_meta' ) ) {
	function get_user_meta( $user_id, $key, $single = false ) {
		if ( ! isset( $GLOBALS['__accesspress_user_meta'] ) || ! is_array( $GLOBALS['__accesspress_user_meta'] ) ) {
			$GLOBALS['__accesspress_user_meta'] = array();
		}
		if ( ! isset( $GLOBALS['__accesspress_user_meta'][ $user_id ] ) ) {
			return $single ? '' : array();
		}
		$value = $GLOBALS['__accesspress_user_meta'][ $user_id ][ $key ] ?? ( $single ? '' : array() );
		return $value;
	}
}

if ( ! function_exists( 'update_user_meta' ) ) {
	function update_user_meta( $user_id, $key, $value ) {
		if ( ! isset( $GLOBALS['__accesspress_user_meta'] ) || ! is_array( $GLOBALS['__accesspress_user_meta'] ) ) {
			$GLOBALS['__accesspress_user_meta'] = array();
		}
		if ( ! isset( $GLOBALS['__accesspress_user_meta'][ $user_id ] ) ) {
			$GLOBALS['__accesspress_user_meta'][ $user_id ] = array();
		}
		$GLOBALS['__accesspress_user_meta'][ $user_id ][ $key ] = $value;
		return true;
	}
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	function sanitize_hex_color( $color ) {
		$color = trim( (string) $color );
		if ( preg_match( '/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $color ) ) {
			return $color;
		}
		return null;
	}
}

if ( ! function_exists( 'flush_rewrite_rules' ) ) {
	function flush_rewrite_rules() {
		return true;
	}
}

if ( ! defined( 'ARRAY_A' ) ) {
	define( 'ARRAY_A', 1 );
}

if ( ! defined( 'OBJECT' ) ) {
	define( 'OBJECT', 1 );
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile
if ( ! class_exists( 'wpdb' ) ) {
	class wpdb {
		public string $prefix = 'wp_';
		public int $insert_id = 0;
		public array $tables  = array();

		public function prepare( $query, ...$args ) {
			$formatted = $query;
			foreach ( $args as $arg ) {
				$replacement = is_numeric( $arg ) ? (string) $arg : "'" . addslashes( (string) $arg ) . "'";
				$formatted   = preg_replace( '/%s|%d/', $replacement, $formatted, 1 );
			}
			return $formatted;
		}

		public function get_row( $query, $output = ARRAY_A ) {
			$rows = $this->get_results( $query, $output );
			return is_array( $rows ) && ! empty( $rows ) ? $rows[0] : null;
		}

		public function get_var( $query ) {
			$matches = array();
			if ( preg_match( '/SHOW TABLES LIKE\s+[\'\"]?([A-Za-z0-9_]+)[\'\"]?/i', $query, $matches ) ) {
				$table = $matches[1];
				return isset( $this->tables[ $table ] ) ? $table : null;
			}

			if ( preg_match( '/FROM\s+`?([A-Za-z0-9_]+)`?/i', $query, $matches ) ) {
				$table = $matches[1];
				$rows  = $this->tables[ $table ] ?? array();
				if ( empty( $rows ) ) {
					return null;
				}
				if ( stripos( $query, 'WHERE' ) !== false && stripos( $query, 'setting_group' ) !== false ) {
					preg_match( "/setting_group\s*=\s*'([^']+)'/i", $query, $matches );
					$expected = $matches[1] ?? '';
					foreach ( $rows as $row ) {
						if ( ( $row['setting_group'] ?? '' ) === $expected ) {
							return $row['setting_value'];
						}
					}
					return null;
				}
				foreach ( $rows as $row ) {
					if ( isset( $row['setting_group'] ) ) {
						return $row['setting_value'];
					}
				}
			}
			return null;
		}

		public function get_results( $query, $output = ARRAY_A ) {
			$matches = array();
			if ( ! preg_match( '/FROM\s+`?([A-Za-z0-9_]+)`?/i', $query, $matches ) ) {
				return array();
			}

			$table = $matches[1];
			$rows  = $this->tables[ $table ] ?? array();
			if ( empty( $rows ) ) {
				return array();
			}

			if ( stripos( $query, 'WHERE' ) !== false && stripos( $query, 'event_key' ) !== false ) {
				preg_match( "/event_key\s*=\s*'([^']+)'/i", $query, $matches );
				$expected = $matches[1] ?? '';
				$filtered = array();
				foreach ( $rows as $row ) {
					if ( ( $row['event_key'] ?? '' ) === $expected ) {
						$filtered[] = $row;
					}
				}
				return $filtered;
			}

			if ( stripos( $query, 'COUNT(*)' ) !== false && stripos( $query, 'GROUP BY' ) !== false ) {
				$grouped = array();
				foreach ( $rows as $row ) {
					$key = (string) ( $row['event_key'] ?? '' );
					if ( '' === $key ) {
						continue;
					}
					$grouped[ $key ][] = $row;
				}

				$result = array();
				foreach ( $grouped as $event_key => $event_rows ) {
					$result[] = array(
						'event_key' => $event_key,
						'count' => count( $event_rows ),
						'last_seen' => max( array_column( $event_rows, 'created_at' ) ),
					);
				}
				return $result;
			}

			if ( stripos( $query, 'WHERE' ) !== false && stripos( $query, 'token_hash' ) !== false ) {
				preg_match( "/token_hash\s*=\s*'([^']+)'/i", $query, $matches );
				$expected = $matches[1] ?? '';
				foreach ( $rows as $row ) {
					if ( ( $row['token_hash'] ?? '' ) === $expected ) {
						return array( $row );
					}
				}
				return array();
			}

			if ( stripos( $query, 'WHERE' ) !== false && stripos( $query, 'customer_id' ) !== false ) {
				preg_match( "/customer_id\s*=\s*'([^']+)'/i", $query, $matches );
				$expected = $matches[1] ?? '';
				$filtered = array();
				foreach ( $rows as $row ) {
					if ( ( $row['customer_id'] ?? '' ) === $expected ) {
						$filtered[] = $row;
					}
				}
				return $filtered;
			}

			return $rows;
		}

		public function insert( $table, $data, $format = array() ) {
			$rows   = $this->tables[ $table ] ?? array();
			$id     = count( $rows ) + 1;
			$record = array( 'id' => $id );
			foreach ( $data as $key => $value ) {
				$record[ $key ] = $value;
			}
			$rows[]                 = $record;
			$this->tables[ $table ] = $rows;
			$this->insert_id        = $id;
			return $id;
		}

		public function update( $table, $data, $where, $format = array(), $where_format = array() ) {
			$rows = $this->tables[ $table ] ?? array();
			foreach ( $rows as $index => $row ) {
				if ( (int) ( $where['id'] ?? 0 ) === (int) ( $row['id'] ?? 0 ) ) {
					foreach ( $data as $key => $value ) {
						$rows[ $index ][ $key ] = $value;
					}
					$this->tables[ $table ] = $rows;
					return 1;
				}
			}
			return 0;
		}

		public function replace( $table, $data, $format = array() ) {
			$rows  = $this->tables[ $table ] ?? array();
			$found = false;
			foreach ( $rows as $index => $row ) {
				if ( ( $row['setting_group'] ?? '' ) === ( $data['setting_group'] ?? '' ) ) {
					$rows[ $index ] = array_merge( $row, $data );
					$found          = true;
					break;
				}
			}
			if ( ! $found ) {
				$rows[] = $data;
			}
			$this->tables[ $table ] = $rows;
			return 1;
		}

		public function delete( $table, $where, $where_format = array() ) {
			$rows     = $this->tables[ $table ] ?? array();
			$filtered = array();
			foreach ( $rows as $row ) {
				if ( ( $where['setting_group'] ?? '' ) !== ( $row['setting_group'] ?? '' ) ) {
					$filtered[] = $row;
				}
			}
			$this->tables[ $table ] = $filtered;
			return 1;
		}
	}

	global $wpdb;
	$wpdb = new wpdb();
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';



