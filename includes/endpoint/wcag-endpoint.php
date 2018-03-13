<?php

namespace RRZE\Wcag;

Class WCAGEndpoint {
    
    function __construct() {
        add_action( 'init', array( $this, 'default_options' ) );
        add_action( 'init', array( $this, 'rewrite' ) );
        add_action( 'template_include', array( $this, 'endpoint_template_redirect' ) );
    }
    
    public static $allowed_stylesheets = [
        'fau' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Medfak',
            'FAU-RWFak',
            'FAU-Philfak',
            'FAU-Techfak',
            'FAU-Natfak'
        ],
        'rrze' => [
            'rrze-2015'
        ],
        'fau-events' => [
            'FAU-Events'
        ]
    ];
    
    function default_options() {
        $this->options = [
            'endpoint_slug' => 'dump',
        ];
        return $this->options;
    }
    
    function rewrite() {
        add_rewrite_endpoint($this->options['endpoint_slug'], EP_ROOT );
        //print_r($this->options['endpoint_slug']);
    }
    
    /*function endpoint_template_redirect( $template ) {
    
        $wcag_template = plugin_dir_path( __FILE__ ) . 'template/wcag-template.php';
        return $template;
       
    }*/
    
    function endpoint_template_redirect() {
        
        //$calendar_endpoint_url = self::endpoint_url();
        //$endpoint_name = self::endpoint_name();
        //$calendar_endpoint_name = mb_strtoupper(mb_substr($endpoint_name, 0, 1)) . mb_substr($endpoint_name, 1);
        $current_theme = wp_get_theme();
        
        $styledir = '';
        foreach (self::$allowed_stylesheets as $dir => $style) {
            if (in_array(strtolower($current_theme->stylesheet), array_map('strtolower', $style))) {
                $styledir = dirname(__FILE__) . "/includes/templates/themes/$dir/";
                break;
            }
        }
        $styledir = is_dir($styledir) ? $styledir : dirname(__FILE__) . '/includes/templates/';
        
        /*if (empty($slug)) {
            include $styledir . 'events.php';
        } else {
            include $styledir . 'single-event.php';
        }*/
        include $styledir . 'wcag-template.php';
        exit();
    }
    
}

?>