<?php

namespace RRZE\Wcag;

defined('ABSPATH') || exit;

class Settings {
    
    /*
     * Main-Klasse
     * object
     */
    protected $main;
    
    protected $option_name;
    
    protected $options;
    
    /*
     * "Screen ID" der Einstellungsseite
     * string
     */
    protected $admin_settings_page;
    
    public function __construct(Main $main) {
        $this->main = $main;
        $this->option_name = $this->main->options->get_option_name();
        $this->options = $this->main->options->get_options();
    }
    
    /*
     * Füge eine Optionsseite in das Menü "Einstellungen" hinzu.
     * @return void
     */
    public function admin_settings_page() {
        $this->admin_settings_page = add_options_page(__('WCAG', 'rrze-wcag'), __('WCAG', 'rrze-wcag'), 'manage_options', 'rrze-wcag', array($this, 'settings_page'));
        add_action('load-' . $this->admin_settings_page, array($this, 'admin_help_menu'));        
    }
    
    /*
     * Die Ausgabe der Optionsseite.
     * @return void
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo __('Settings &rsaquo; WCAG', 'rrze-wcag'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('rrze_wcag_options');
                do_settings_sections('rrze_wcag_options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /*
     * Legt die Einstellungen der Optionsseite fest.
     * @return void
     */
    public function admin_settings() {
        register_setting('rrze_wcag_options', $this->option_name, array($this, 'options_validate'));
        add_settings_section('rrze_wcag_section_1', false, '__return_false', 'rrze_wcag_options');
        add_settings_field('rrze_wcag_field_1', __('Field 1', 'rrze-wcag'), array($this, 'rrze_wcag_field_1'), 'rrze_wcag_options', 'rrze_wcag_section_1');
    }

    /*
     * Validiert die Eingabe der Optionsseite.
     * @param array $input
     * @return array
     */
    public function options_validate($input) {
        $input['rrze_wcag_text'] = !empty($input['rrze_wcag_field_1']) ? $input['rrze_wcag_field_1'] : '';
        return $input;
    }

    /*
     * Erstes Feld der Optionsseite
     * @return void
     */
    public function rrze_wcag_field_1() {
        ?>
        <input type='text' name="<?php printf('%s[rrze_wcag_field_1]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_1; ?>">
        <?php
    }

    /*
     * Erstellt die Kontexthilfe der Optionsseite.
     * @return void
     */
    public function admin_help_menu() {

        $content = array(
            '<p>' . __('Here comes the Context Help content.', 'rrze-wcag') . '</p>',
        );


        $help_tab = array(
            'id' => $this->admin_settings_page,
            'title' => __('Overview', 'rrze-wcag'),
            'content' => implode(PHP_EOL, $content),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('For more information', 'rrze-wcag'), __('RRZE Webteam on Github', 'rrze-wcag'));

        $screen = get_current_screen();

        if ($screen->id != $this->admin_settings_page) {
            return;
        }

        $screen->add_help_tab($help_tab);

        $screen->set_help_sidebar($help_sidebar);
    }
}