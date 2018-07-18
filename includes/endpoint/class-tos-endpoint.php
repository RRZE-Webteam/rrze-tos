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
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'template_redirect', array( $this, 'endpoint_template_redirect' ) );

		}

		/**
		 * List of themes
		 *
		 * @var array
		 */
		public static $allowed_stylesheets = [
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
			$this->options = [
				'endpoint_slug' => __( 'accessibility', 'rrze-tos' ),
			];

			return $this->options;
		}

		/**
		 * Create a vector out of options
		 *
		 * @param array $vars Parameter with output variables.
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars[] = $this->options['endpoint_slug'];

			return $vars;
		}

		/**
		 * Change endpoint.
		 */
		public function rewrite() {
			add_rewrite_endpoint( $this->options['endpoint_slug'], EP_ROOT );
		}

		/**
		 * Redirect according to theme.
		 */
		public function endpoint_template_redirect() {

			global $wp_query;

			if ( ! isset( $wp_query->query_vars[ $this->options['endpoint_slug'] ] ) ) {
				return;
			}

			$current_theme = wp_get_theme();

			$styledir = '';
			foreach ( self::$allowed_stylesheets as $dir => $style ) {
				if ( in_array( strtolower( $current_theme->__get( 'stylesheet' ) ), array_map( 'strtolower', $style ), true ) ) {
					$styledir = dirname( __FILE__ ) . "/templates/themes/$dir/";
					break;
				}
			}

			if ( isset( $wp_query->query[ $this->options['endpoint_slug'] ] ) ) {
				include $styledir . 'tos-template.php';
			}

			exit();
		}

	}
}
