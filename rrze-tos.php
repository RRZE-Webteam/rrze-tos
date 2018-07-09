<?php
/**
 * Plugin Name:     RRZE TOS
 * Plugin URI:      https://github.com/RRZE-Webteam/rrze-tos.git
 * Description:     WordPress-Plugin: Prüfung einer Website aus dem FAU-Netzwerk gemäß den Konformitätskriterien der TOS.
 * Version:         0.1.2
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-tos
 *
 * @package WordPress
 * @subpackage TOS
 */

namespace RRZE\Tos {

	use RRZE\Tos\Main;

	defined( 'ABSPATH' ) || exit;

	const RRZE_PHP_VERSION = '7.1';
	const RRZE_WP_VERSION  = '4.9';

	register_activation_hook( __FILE__, 'RRZE\Tos\activation' );
	register_deactivation_hook( __FILE__, 'RRZE\Tos\deactivation' );

	add_action( 'plugins_loaded', 'RRZE\Tos\loaded' );

	/**
	 * Einbindung der Sprachdateien.
	 *
	 * @return void
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'rrze-tos', false, sprintf( '%s/languages/', dirname( plugin_basename( __FILE__ ) ) ) );
	}

	/**
	 * Wird durchgeführt, nachdem das Plugin aktiviert wurde.
	 *
	 * @return void
	 */
	function activation() {
		// Sprachdateien werden eingebunden.
		load_textdomain();

		// Überprüft die minimal erforderliche PHP- u. WP-Version.
		system_requirements();

		include_once __DIR__ . '/includes/endpoint/class-tos-endpoint.php';
		$obj = new Tos_Endpoint();
		$obj->default_options();
		$obj->rewrite();

		flush_rewrite_rules();

		// Ab hier können die Funktionen hinzugefügt werden,
		// die bei der Aktivierung des Plugins aufgerufen werden müssen.
		// Bspw. wp_schedule_event, flush_rewrite_rules, etc.
	}

	/**
	 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
	 *
	 * @return void
	 */
	function deactivation() {
		flush_rewrite_rules();
		// Hier können die Funktionen hinzugefügt werden, die
		// bei der Deaktivierung des Plugins aufgerufen werden müssen.
		// Bspw. wp_clear_scheduled_hook, flush_rewrite_rules, etc.
	}

	/**
	 * Überprüft die minimal erforderliche PHP- u. WP-Version.
	 *
	 * @return void
	 */
	function system_requirements() {
		$error = '';

		if ( version_compare( PHP_VERSION, RRZE_PHP_VERSION, '<' ) ) {
			$error = sprintf( __( 'Your server is running PHP version %1$s. Please upgrade at least to PHP version %2$s.', 'rrze-tos' ), PHP_VERSION, RRZE_PHP_VERSION );
		}

		if ( version_compare( $GLOBALS['wp_version'], RRZE_WP_VERSION, '<' ) ) {
			$error = sprintf( __( 'Your WordPress version is %1$s. Please upgrade at least to WordPress version %2$s.', 'rrze-tos' ), $GLOBALS['wp_version'], RRZE_WP_VERSION );
		}

		// Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
		if ( ! empty( $error ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ), false, true );
			wp_die( esc_html( $error ) );
		}
	}

	/**
	 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
	 * und alle Plugins eingebunden wurden.
	 *
	 * @return void
	 */
	function loaded() {
		// Sprachdateien werden eingebunden.
		add_action( 'wp_enqueue_scripts', 'RRZE\Tos\rrze_tos_scripts' );
		add_action( 'admin_enqueue_scripts', 'RRZE\Tos\rrze_tos_admin_scripts' );
		load_textdomain();
		include_once __DIR__ . '/includes/helper/tos-helper-functions.php';
		autoload();
		include_once __DIR__ . '/includes/shortcode/tos-contact-form-captcha.php';
		include_once __DIR__ . '/includes/shortcode/tos-contact-form-shortcode.php';
		include_once __DIR__ . '/includes/shortcode/tos-admin-information-shortcode.php';
		include_once __DIR__ . '/includes/endpoint/class-tos-endpoint.php';
		new Tos_Endpoint();
		include_once __DIR__ . '/includes/menu/tos-add-footer-menu.php';

		// Ab hier können weitere Funktionen bzw. Klassen angelegt werden.
	}

	/**
	 * Register Scripts.
	 *
	 * @return void
	 */
	function rrze_tos_scripts() {
		wp_register_style( 'tos_styles', plugins_url( 'rrze-tos/assets/css/styles.css', dirname( __FILE__ ) ) );
		wp_register_style( 'tos_styles_rrze', plugins_url( 'rrze-tos/assets/css/rrze-styles.css', dirname( __FILE__ ) ) );
		wp_register_style( 'tos_styles_events', plugins_url( 'rrze-tos/assets/css/events-styles.css', dirname( __FILE__ ) ) );

		$current_theme = wp_get_theme();
		$themes_fau    = array(
			__( 'FAU-Institutions', 'rrze-tos' ),
			'FAU-Natfak',
			'FAU-Philfak',
			'FAU-RWFak',
			'FAU-Techfak',
			'FAU-Medfak',
		);

		if ( in_array( $current_theme, $themes_fau ) ) {
			//error_log(print_r($current_theme, true));!
			wp_enqueue_style( 'tos_styles' );
		} elseif ( 'RRZE 2015' === $current_theme ) {
			wp_enqueue_style( 'tos_styles_rrze' );
		} else {
			wp_enqueue_style( 'tos_styles_events' );
		}
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @return void
	 */
	function rrze_tos_admin_scripts() {
		wp_enqueue_style( 'admin-styles', plugins_url( 'rrze-tos/assets/css/admin.css', dirname( __FILE__ ) ) );
	}

	/**
	 * Automatische Laden von Klassen.
	 *
	 * @return void
	 */
	function autoload() {
		include __DIR__ . '/includes/autoload.php';
		$main = new Main();
		$main->init( plugin_basename( __FILE__ ) );
	}
}
