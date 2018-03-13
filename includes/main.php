<?php

namespace RRZE\Wcag;

use RRZE\Wcag\Options;
use RRZE\Wcag\Settings;

defined('ABSPATH') || exit;

class Main {
    
    public $options;
    
    public $settings;

    public function init($plugin_basename) {
        $this->options = new Options();
        $this->settings = new Settings($this);       
        
        add_action('admin_menu', array($this->settings, 'admin_settings_page'));
        add_action('admin_init', array($this->settings, 'admin_settings'));         
        
    }
    
}
