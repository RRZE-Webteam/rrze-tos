<?php
/**
 * WordPress TOS Class
 *
 * @package WordPress
 * @subpackage TOS
 * @since 3.4.0
 */

namespace RRZE\Tos {

	use RRZE\Tos\Options;
	use RRZE\Tos\Settings;

	defined( 'ABSPATH' ) || exit;

	/**
	 * Class Main
	 *
	 * @package RRZE\Tos
	 */
	class Main {
		/**
		 * Options object type
		 *
		 * @var object Options
		 */
		public $options;
		/**
		 * Settings object type
		 *
		 * @var object Settings
		 */
		public $settings;

		/**
		 * Constructor function
		 *
		 * @param string $plugin_basename directory path.
		 */
		public function init( $plugin_basename ) {
			$this->options  = new Options();
			$this->settings = new Settings( $this );

			add_action( 'admin_menu', array( $this->settings, 'admin_settings_page' ) );
			add_action( 'admin_init', array( $this->settings, 'admin_settings' ) );

		}

	}
}
