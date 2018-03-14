<?php

namespace RRZE\Wcag;

Class WCAGEndpoint {
    
    function __construct() {
        add_action( 'init', array( $this, 'default_options' ) );
        add_action( 'init', array( $this, 'rewrite' ) );
        add_filter( 'query_vars', array( $this , 'add_query_vars'));
        add_action( 'template_redirect', array( $this, 'endpoint_template_redirect' ) );
      
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
            'endpoint_slug' => 'fa7b70763d9d18f3fd32481907f4b687e4a7ca71a62582ad0cc00f5840cfa9c3',
        ];
        return $this->options;
    }

    function add_query_vars($vars) {
            $vars[] = $this->options['endpoint_slug'];
            return $vars;
    }
    
    function rewrite() {
        add_rewrite_endpoint($this->options['endpoint_slug'], EP_PERMALINK );
    }
    
    function endpoint_template_redirect() {
        
        global $wp_query;
        
        if (!isset($wp_query->query_vars[$this->options['endpoint_slug']])) {
            return;
        }
        
        $current_theme = wp_get_theme();
        
        $styledir = '';
        foreach (self::$allowed_stylesheets as $dir => $style) {
            if (in_array(strtolower($current_theme->stylesheet), array_map('strtolower', $style))) {
                $styledir = dirname(__FILE__) . "/templates/themes/$dir/";
                break;
            }
        }
        
        if(isset( $wp_query->query[$this->options['endpoint_slug']] )) {
            include $styledir . 'wcag-template.php';
        }
        
        exit();
    }
    
}

?>