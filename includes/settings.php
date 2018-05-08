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
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $response = wp_remote_get('http://remoter.dev/wcag-test.json');
        $status_code = wp_remote_retrieve_response_code( $response );

        if ( 200 === $status_code ) {
            $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
            $this->res = json_decode($json, TRUE);
        } else {
            $this->res = '';
        }
    }
    
    /*
     * Füge eine Optionsseite in das Menü "Einstellungen" hinzu.
     * @return void
     */
    public function admin_settings_page() {
        $this->admin_settings_page = add_options_page(__('Accessibility', 'rrze-wcag'), __('Accessibility', 'rrze-wcag'), 'manage_options', 'rrze-wcag', array($this, 'settings_page'));
        add_action('load-' . $this->admin_settings_page, array($this, 'admin_help_menu'));        
    }
    
    /*
     * Die Ausgabe der Optionsseite.
     * @return void
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo __('Settings &rsaquo; Accessible', 'rrze-wcag'); ?></h2>
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
        add_settings_section('rrze_wcag_section_1', 'Allgemein', '__return_false', 'rrze_wcag_options');
        add_settings_field('rrze_wcag_field_1', __('Title', 'rrze-wcag'), array($this, 'rrze_wcag_field_1'), 'rrze_wcag_options', 'rrze_wcag_section_1');
        add_settings_field('rrze_wcag_field_2', __('Are the conformity conditions of the WCAG 2.0 AA fulfilled?', 'rrze-wcag'), array($this, 'rrze_wcag_field_2'), 'rrze_wcag_options', 'rrze_wcag_section_1'); 
        add_settings_field('rrze_wcag_field_3', __('If not, with what reason', 'rrze-wcag'), array($this, 'rrze_wcag_field_3'), 'rrze_wcag_options', 'rrze_wcag_section_1'); 
        add_settings_section('rrze_wcag_section_2', 'Verantwortliche/er', '__return_false', 'rrze_wcag_options');
        add_settings_field('rrze_wcag_field_4', __('Firstname', 'rrze-wcag'), array($this, 'rrze_wcag_field_4'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        add_settings_field('rrze_wcag_field_5', __('Lastname', 'rrze-wcag'), array($this, 'rrze_wcag_field_5'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        add_settings_field('rrze_wcag_field_6', __('Street', 'rrze-wcag'), array($this, 'rrze_wcag_field_6'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        add_settings_field('rrze_wcag_field_7', __('City', 'rrze-wcag'), array($this, 'rrze_wcag_field_7'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        add_settings_field('rrze_wcag_field_8', __('Phone', 'rrze-wcag'), array($this, 'rrze_wcag_field_8'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        add_settings_field('rrze_wcag_field_9', __('E-Mail', 'rrze-wcag'), array($this, 'rrze_wcag_field_9'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        if(is_plugin_active('fau-person/fau-person.php')) {
            add_settings_field('rrze_wcag_field_10', __('Person-ID', 'rrze-wcag'), array($this, 'rrze_wcag_field_10'), 'rrze_wcag_options', 'rrze_wcag_section_2'); 
        }
        add_settings_section('rrze_wcag_section_3', 'Webmaster', '__return_false', 'rrze_wcag_options');
        add_settings_field('rrze_wcag_field_11', __('Firstname', 'rrze-wcag'), array($this, 'rrze_wcag_field_11'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        add_settings_field('rrze_wcag_field_12', __('Lastname', 'rrze-wcag'), array($this, 'rrze_wcag_field_12'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        add_settings_field('rrze_wcag_field_13', __('Street', 'rrze-wcag'), array($this, 'rrze_wcag_field_13'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        add_settings_field('rrze_wcag_field_14', __('City', 'rrze-wcag'), array($this, 'rrze_wcag_field_14'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        add_settings_field('rrze_wcag_field_15', __('Phone', 'rrze-wcag'), array($this, 'rrze_wcag_field_15'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        add_settings_field('rrze_wcag_field_16', __('E-Mail', 'rrze-wcag'), array($this, 'rrze_wcag_field_16'), 'rrze_wcag_options', 'rrze_wcag_section_3'); 
        if(is_plugin_active('fau-person/fau-person.php')) {
            add_settings_field('rrze_wcag_field_17', __('Person-ID', 'rrze-wcag'), array($this, 'rrze_wcag_field_17'), 'rrze_wcag_options', 'rrze_wcag_section_3');
        }
        add_settings_section('rrze_wcag_section_4', 'E-Mail Settings', '__return_false', 'rrze_wcag_options');
        add_settings_field('rrze_wcag_field_18', __('Receiver E-Mail', 'rrze-wcag'), array($this, 'rrze_wcag_field_18'), 'rrze_wcag_options', 'rrze_wcag_section_4'); 
        add_settings_field('rrze_wcag_field_19', __('Subject', 'rrze-wcag'), array($this, 'rrze_wcag_field_19'), 'rrze_wcag_options', 'rrze_wcag_section_4'); 
        add_settings_field('rrze_wcag_field_20', __('CC', 'rrze-wcag'), array($this, 'rrze_wcag_field_20'), 'rrze_wcag_options', 'rrze_wcag_section_4'); 
    }

    /*
     * Validiert die Eingabe der Optionsseite.
     * @param array $input
     * @return array
     */
    public function options_validate($input) {
        $input['rrze_wcag_text'] = !empty($input['rrze_wcag_field_1']) ? $input['rrze_wcag_field_1'] : '';
        $input['rrze_wcag_text'] = !empty($input['rrze_wcag_field_3']) ? $input['rrze_wcag_field_3'] : '';
        return $input;
    }

    /*
     * Erstes Feld der Optionsseite
     * @return void
     */
    public function rrze_wcag_field_1() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_1]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_1; ?>" readonly>
        <?php
    }
    
    public function rrze_wcag_field_2() {
    ?>
            <input type="radio" name="<?php printf('%s[rrze_wcag_field_2]', $this->option_name) ?>" value="1" <?php checked(1, $this->options->rrze_wcag_field_2, true); ?>>Ja
            <input type="radio" name="<?php printf('%s[rrze_wcag_field_2]', $this->option_name) ?>" value="2" <?php checked(2, $this->options->rrze_wcag_field_2, true); ?>>Nein
       <?php
    }
    
    public function rrze_wcag_field_3() {
        ?>
            <textarea rows="8" cols="50" size="50" name="<?php printf('%s[rrze_wcag_field_3]', $this->option_name); ?>"><?php echo $this->options->rrze_wcag_field_3; ?></textarea>
        <?php
    }
    
    public function rrze_wcag_field_4() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_4]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_4; ?>" <?php echo isset($this->res['metadata']['verantwortlich']['vorname']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_5() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_5]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_5; ?>" <?php echo isset($this->res['metadata']['verantwortlich']['nachname']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_6() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_6]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_6; ?>">
        <?php
    }
    
    public function rrze_wcag_field_7() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_7]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_7; ?>">
        <?php
    }
    
    public function rrze_wcag_field_8() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_8]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_8; ?>">
        <?php
    }
    
    public function rrze_wcag_field_9() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_9]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_9; ?>" <?php echo isset($this->res['metadata']['verantwortlich']['email']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_10() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_10]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_10; ?>">
        <?php
    }
    
     public function rrze_wcag_field_11() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_11]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_11; ?>" <?php echo isset($this->res['metadata']['webmaster']['vorname']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_12() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_12]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_12; ?>" <?php echo isset($this->res['metadata']['webmaster']['nachname']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_13() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_13]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_13; ?>">
        <?php
    }
    
    public function rrze_wcag_field_14() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_14]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_14; ?>">
        <?php
    }
    
    public function rrze_wcag_field_15() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_15]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_15; ?>">
        <?php
    }
    
    public function rrze_wcag_field_16() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_16]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_16; ?>" <?php echo isset($this->res['metadata']['webmaster']['email']) ? 'readonly' : '' ?>>
        <?php
    }
    
    public function rrze_wcag_field_17() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_17]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_17; ?>">
        <?php
    }
    
    public function rrze_wcag_field_18() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_18]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_18; ?>">
        <?php
    }
    
    public function rrze_wcag_field_19() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_19]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_19; ?>">
        <?php
    }
    
    public function rrze_wcag_field_20() {
        ?>
        <input size="50" type='text' name="<?php printf('%s[rrze_wcag_field_20]', $this->option_name); ?>" value="<?php echo $this->options->rrze_wcag_field_20; ?>">
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