<?php
/**
 * Plugin Name:     WACG RRZE-TOS
 * Plugin URI:      https://gitlab.rrze.fau.de/rrze-webteam/rrze-wcag.git
 * Description:     WordPress-Plugin: Prüfung einer Website aus dem FAU-Netzwerk gemäß den Konformitätskriterien der WCAG.
 * Version:         0.1.2
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-wcag
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

use RRZE\Wcag\Main;

defined( 'ABSPATH' ) || exit;

const RRZE_PHP_VERSION = '7.1';
const RRZE_WP_VERSION  = '4.9';

register_activation_hook( __FILE__, 'RRZE\Wcag\activation' );
register_deactivation_hook( __FILE__, 'RRZE\Wcag\deactivation' );

add_action( 'plugins_loaded', 'RRZE\Wcag\loaded' );

/**
 * Einbindung der Sprachdateien.
 *
 * @return void
 */
function load_textdomain() {
	load_plugin_textdomain( 'rrze-wcag', false, sprintf( '%s/languages/', dirname( plugin_basename( __FILE__ ) ) ) );
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

	require_once __DIR__ . '/includes/endpoint/wcag-endpoint.php';
	$obj = new WCAGEndpoint();
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
		/* translators: %s: search term */
		$error = sprintf( __( 'Your server is running PHP version %1$s. Please upgrade at least to PHP version %2$s.', 'cms-basis' ), PHP_VERSION, RRZE_PHP_VERSION );
	}

	if ( version_compare( $GLOBALS['wp_version'], RRZE_WP_VERSION, '<' ) ) {
		/* translators: %s: search term */
		$error = sprintf( __( 'Your WordPress version is %1$s. Please upgrade at least to WordPress version %2$s.', 'cms-basis' ), $GLOBALS['wp_version'], RRZE_WP_VERSION );
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
	add_action( 'wp_enqueue_scripts', 'RRZE\Wcag\rrze_wcag_scripts' );
	add_action( 'admin_enqueue_scripts', 'RRZE\Wcag\rrze_wcag_admin_scripts' );
	load_textdomain();
	require_once __DIR__ . '/includes/helper/wcag-helper-functions.php';
	autoload();
	require_once __DIR__ . '/includes/shortcode/wcag-contact-form-captcha.php';
	require_once __DIR__ . '/includes/shortcode/wcag-contact-form-shortcode.php';
	require_once __DIR__ . '/includes/shortcode/wcag-admin-information-shortcode.php';
	require_once __DIR__ . '/includes/endpoint/wcag-endpoint.php';
	new WCAGEndpoint();
	require_once __DIR__ . '/includes/menu/wcag-add-footer-menu.php';

	// Ab hier können weitere Funktionen bzw. Klassen angelegt werden.
}

/**
 * Function rrze_wcag_scripts
 *
 * @return void
 */
function rrze_wcag_scripts() {
	wp_register_style( 'wcag_styles', plugins_url( 'rrze-wcag/assets/css/styles.css', dirname( __FILE__ ) ) );
	wp_register_style( 'wcag_styles_rrze', plugins_url( 'rrze-wcag/assets/css/rrze-styles.css', dirname( __FILE__ ) ) );
	wp_register_style( 'wcag_styles_events', plugins_url( 'rrze-wcag/assets/css/events-styles.css', dirname( __FILE__ ) ) );

	$current_theme = wp_get_theme();
	$themes_fau    = array(
		__( 'FAU-Institutions', 'rrze-wcag' ),
		'FAU-Natfak',
		'FAU-Philfak',
		'FAU-RWFak',
		'FAU-Techfak',
		'FAU-Medfak',
	);

	if ( in_array( $current_theme, $themes_fau, true ) ) {
		wp_enqueue_style( 'wcag_styles' );
	} elseif ( 'RRZE 2015' === $current_theme ) {
		wp_enqueue_style( 'wcag_styles_rrze' );
	} else {
		wp_enqueue_style( 'wcag_styles_events' );
	}
}

/**
 * Function rrze_wcag_admin_scripts
 *
 * @return void
 */
function rrze_wcag_admin_scripts() {
	wp_enqueue_style( 'admin-styles', plugins_url( 'rrze-wcag/assets/css/admin.css', dirname( __FILE__ ) ) );
}

/**
 * Automatische Laden von Klassen.
 *
 * @return void
 */
function autoload() {
	require __DIR__ . '/includes/autoload.php';
	$main = new Main();
	$main->init( plugin_basename( __FILE__ ) );
}
//TODO: create array for template text to be included
//TODO: loco translate plugin
