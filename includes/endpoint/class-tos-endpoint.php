<?php
/**
 * WordPress TOS endpoint Class
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

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
//			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'template_redirect',
				array( $this, 'endpoint_template_redirect' ) );

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
			$this->options = Settings::options_page_tabs();

			return $this->options;
		}

		/**
		 * Include content part into base template endpoint.
		 */
		public function get_tos_content() {
			global $wp_query, $locale;
			$this->default_options();
			foreach ( $this->options as $key => $value ) {
				if ( isset( $wp_query->query[ $value ] ) ) {
//					$template_part = dirname( __FILE__ ) . '/templates/' . substr( $locale, 0, 2 ) . '/' . $key . '-template.php';
					$template_part = dirname( __FILE__ ) . '/templates/' . $key . '-template.php';
					if ( file_exists( $template_part ) ) {
						include_once $template_part;
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
				if ( in_array( strtolower( $current_theme->__get( 'stylesheet' ) ),
					array_map( 'strtolower', $style ), true ) ) {
					$styledir = dirname( __FILE__ ) . "/templates/themes/$dir/";
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
