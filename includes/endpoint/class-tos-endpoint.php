<?php
/**
 * WordPress TOS endpoint Class
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	defined( 'ABSPATH' ) || exit;
	require_once plugin_dir_path( __DIR__ ) . 'class-settings.php';

	/**
	 * Class TosEndpoint
	 *
	 * @property array options
	 * @package RRZE\Tos
	 */
	class Tos_Endpoint {
		/**
		 * Tos_Endpoint constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'default_options' ) );
			add_action( 'init', array( $this, 'rewrite' ) );
			add_action( 'template_redirect', array( $this, 'endpoint_template_redirect' ) );
		}

		/**
		 * List of themes
		 *
		 * @var array
		 */
		public static $allowed_stylesheets
			= [
				'fau'        => [
					'FAU-Einrichtungen',
					'FAU-Einrichtungen-BETA',
					'FAU-Medfak',
					'FAU-RWFak',
					'FAU-Philfak',
					'FAU-Techfak',
					'FAU-Natfak',
				],
				'rrze'       => [
					'rrze-2015',
				],
				'fau-events' => [
					'FAU-Events',
				],
			];

		/**
		 * Define default values.
		 *
		 * @return array
		 */
		public function default_options() {
			$this->options = Settings::options_pages();

			return $this->options;
		}

		/**
		 * Callback get_tos_content function to replace variable from template.
		 *
		 * @param array $matches Variable found in template.
		 *
		 * @return string
		 */
		public function get_option_values( $matches ) {
			$option_values = (array) get_option( 'rrze_tos' );
			$value         = isset( $option_values[ $matches[1] ] ) ? $option_values[ $matches[1] ] : '';

			return $value;
		}

		/**
		 * Callback get_tos_content function to check if-else condition.
		 *
		 * @param array $matches if variable found in template.
		 *
		 * @return string
		 */
		public function check_if_else_condition( $matches ) {
			$option_values = (array) get_option( 'rrze_tos' );
			if ( isset( $option_values[ $matches[1] ] ) && '1' === $option_values[ $matches[1] ] ) {
				return $matches[2];
			} elseif ( isset( $matches[3] ) ) {
				return $matches[3];
			}

			return '';
		}

		/**
		 * Include content part into base template endpoint.
		 */
		public function get_tos_content() {
			global $wp_query, $locale;

			$this->default_options();
			foreach ( $this->options as $key => $value ) {
				if ( isset( $wp_query->query[ $value ] ) ) {
					$template_part = plugin_dir_path( __FILE__ ) . 'templates/' . substr( $locale, 0, 2 ) . "/$key-template.php";
					if ( file_exists( $template_part ) ) {
						$template = file_get_contents( $template_part );
						$content  = preg_replace_callback(
							'/{{[\s]*?([\w]+)[\s]*?}}/',
							[ $this, 'get_option_values' ],
							$template
						);
						$content  = preg_replace_callback(
							'/{{[\s]*?if[\s]+?([\w]+)(.*?)(?:elseif(.*?))*?endif[\s]*?}}/s',
							[ $this, 'check_if_else_condition' ],
							$content
						);
						eval( $content );
					}
				}
			}
		}

		/**
		 * Create a vector out of options
		 *
		 * @param array $vars Parameter with output variables.
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars = array_merge( $vars, $this->options );

			return $vars;
		}

		/**
		 * Change endpoint.
		 */
		public function rewrite() {
			foreach ( $this->options as $key => $value ) {
				add_rewrite_endpoint( $value, EP_ROOT, true );
			}
		}

		/**
		 * Redirect according to theme and load base template.
		 */
		public function endpoint_template_redirect() {

			global $wp_query;

			$is_option_set = false;
			foreach ( $this->options as $key => $value ) {
				if ( isset( $wp_query->query_vars[ $value ] ) ) {
					$is_option_set = true;
				}
			}
			if ( ! $is_option_set ) {
				return;
			}

			$current_theme = wp_get_theme();

			// Find the correct FAU template to be included.
			$styledir = '';
			foreach ( self::$allowed_stylesheets as $dir => $style ) {
				if ( in_array( strtolower( $current_theme->__get( 'stylesheet' ) ), array_map( 'strtolower', $style ), true ) ) {
					$styledir = plugin_dir_path( __FILE__ ) . "/templates/themes/$dir/";
					break;
				}
			}

			foreach ( $this->options as $key => $value ) {
				if ( isset( $wp_query->query[ $value ] ) ) {
					$base_template = $styledir . 'tos-template.php';

					if ( file_exists( $base_template ) ) {
						// Pass title to base template.
						$title = ucfirst( $value );
						include $base_template;
					}
				}
			}
			exit();
		}
	}
}
