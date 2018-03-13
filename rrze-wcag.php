<?php

/**
 * Plugin Name:     CMS Basis
 * Plugin URI:      https://gitlab.rrze.fau.de/rrze-webteam/cms-basis
 * Description:     Grundlegende Vorlage für alle WordPress-CMS-Plugins.
 * Version:         3.0.0
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     cms-basis
 */

/*
  Verzeichnisschema:
  cms-basis
  |-- languages                     Verzeichnis der Sprachdateien
  |   +-- cms-basis.pot             Vorlagedatei falls Übersetzungen in andere Sprachen nötig werden
  |   +-- cms-basis-de_DE.po        Deutsche Übersetzungsdatei (kann mit poedit angepasst werden)
  |   +-- cms-basis-de_DE.mo        Deutsche Übersetzungsdatei (wird beim Speichern in poedit aktualisiert)
  |   +-- cms-basis-de_DE_formal.po Deutsche (Sie) Übersetzungsdatei (kann mit poedit angepasst werden)
  |   +-- cms-basis-de_DE_formal.mo Deutsche (Sie) Übersetzungsdatei (wird beim Speichern in poedit aktualisiert)
  |-- includes                      (Optional)
      +-- autoload.php              Automatische Laden von Klassen
      +-- main.php                  Main-Klasse
      +-- options.php               Optionen-Klasse
      +-- settings.php              Settings-Klasse
  +-- README.md                     Anweisungen
  +-- cms-basis.php                 Hauptdatei des Plugins
 */

namespace CMS\Basis;

use CMS\Basis\Main;

defined('ABSPATH') || exit;

const RRZE_PHP_VERSION = '5.5';
const RRZE_WP_VERSION = '4.8';

register_activation_hook(__FILE__, 'CMS\Basis\activation');
register_deactivation_hook(__FILE__, 'CMS\Basis\deactivation');

add_action('plugins_loaded', 'CMS\Basis\loaded');

/*
 * Einbindung der Sprachdateien.
 * @return void
 */
function load_textdomain() {
    load_plugin_textdomain('cms-basis', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/*
 * Wird durchgeführt, nachdem das Plugin aktiviert wurde.
 * @return void
 */
function activation() {
    // Sprachdateien werden eingebunden.
    load_textdomain();

    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    system_requirements();

    // Ab hier können die Funktionen hinzugefügt werden, 
    // die bei der Aktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_schedule_event, flush_rewrite_rules, etc.    
}

/*
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 * @return void
 */
function deactivation() {
    // Hier können die Funktionen hinzugefügt werden, die
    // bei der Deaktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_clear_scheduled_hook, flush_rewrite_rules, etc.
}

/*
 * Überprüft die minimal erforderliche PHP- u. WP-Version.
 * @return void
 */
function system_requirements() {
    $error = '';

    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'cms-basis'), PHP_VERSION, RRZE_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'cms-basis'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }

    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), FALSE, TRUE);
        wp_die($error);
    }
}

/*
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 * @return void
 */
function loaded() {
    // Sprachdateien werden eingebunden.
    load_textdomain();
    
    // Ab hier können weitere Funktionen bzw. Klassen angelegt werden.
    //autoload();
}

/*
 * Automatische Laden von Klassen.
 * @return void
 */
function autoload() {
    require __DIR__ . '/includes/autoload.php';
    $main = new Main();
    $main->init(plugin_basename(__FILE__));
}
