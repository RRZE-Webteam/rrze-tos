<?php

namespace CMS\Basis;

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
        $this->admin_settings_page = add_options_page(__('CMS Basis', 'cms-basis'), __('CMS Basis', 'cms-basis'), 'manage_options', 'cms-basis', array($this, 'settings_page'));
        add_action('load-' . $this->admin_settings_page, array($this, 'admin_help_menu'));        
    }
    
    /*
     * Die Ausgabe der Optionsseite.
     * @return void
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo __('Settings &rsaquo; CMS Basis', 'cms-basis'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('cms_basis_options');
                do_settings_sections('cms_basis_options');
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
        register_setting('cms_basis_options', $this->option_name, array($this, 'options_validate'));
        add_settings_section('cms_basis_section_1', false, '__return_false', 'cms_basis_options');
        add_settings_field('cms_basis_field_1', __('Field 1', 'cms-basis'), array($this, 'cms_basis_field_1'), 'cms_basis_options', 'cms_basis_section_1');
    }

    /*
     * Validiert die Eingabe der Optionsseite.
     * @param array $input
     * @return array
     */
    public function options_validate($input) {
        $input['cms_basis_text'] = !empty($input['cms_basis_field_1']) ? $input['cms_basis_field_1'] : '';
        return $input;
    }

    /*
     * Erstes Feld der Optionsseite
     * @return void
     */
    public function cms_basis_field_1() {
        ?>
        <input type='text' name="<?php printf('%s[cms_basis_field_1]', $this->option_name); ?>" value="<?php echo $this->options->cms_basis_field_1; ?>">
        <?php
    }

    /*
     * Erstellt die Kontexthilfe der Optionsseite.
     * @return void
     */
    public function admin_help_menu() {

        $content = array(
            '<p>' . __('Here comes the Context Help content.', 'cms-basis') . '</p>',
        );


        $help_tab = array(
            'id' => $this->admin_settings_page,
            'title' => __('Overview', 'cms-basis'),
            'content' => implode(PHP_EOL, $content),
        );

        $help_sidebar = sprintf('<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __('For more information', 'cms-basis'), __('RRZE Webteam on Github', 'cms-basis'));

        $screen = get_current_screen();

        if ($screen->id != $this->admin_settings_page) {
            return;
        }

        $screen->add_help_tab($help_tab);

        $screen->set_help_sidebar($help_sidebar);
    }
}