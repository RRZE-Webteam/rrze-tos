<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Main
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);

        new Settings();
        new Endpoint();

        NavMenu::addTosMenu();
    }

    /**
     * [enqueueScripts description]
     */
    public function enqueueScripts()
    {
        wp_register_style(
            'rrze-tos-default',
            plugins_url('assets/css/default.min.css', plugin_basename(RRZE_PLUGIN_FILE))
        );
        wp_register_style(
            'rrze-tos-fau',
            plugins_url('assets/css/fau.min.css', plugin_basename(RRZE_PLUGIN_FILE))
        );
        wp_register_style(
            'rrze-tos-rrze',
            plugins_url('assets/css/rrze.min.css', plugin_basename(RRZE_PLUGIN_FILE))
        );
        wp_register_style(
            'rrze-tos-events',
            plugins_url('assets/css/events.min.css', plugin_basename(RRZE_PLUGIN_FILE))
        );

        $stylesheetGroup = Theme::getCurrentStylesheetGroup();
        wp_enqueue_style('rrze-tos-' . $stylesheetGroup);
    }

    /**
     * [adminEnqueueScripts description]
     * @param  string $hook [description]
     * @return void
     */
    public function adminEnqueueScripts($hook)
    {
        if ('settings_page_rrze-tos' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'rrze-tos-admin',
            plugins_url('assets/css/admin.min.css', plugin_basename(RRZE_PLUGIN_FILE))
        );

        wp_enqueue_script(
            'rrze-tos-admin',
            plugins_url('assets/js/admin.min.js', plugin_basename(RRZE_PLUGIN_FILE)),
            ['jquery', 'jquery-ui-tabs', 'jquery-effects-fade', 'jquery-ui-datepicker'],
            false,
            true
        );
    }
}
