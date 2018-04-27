<?php

namespace RRZE\Wcag;

function wcag_dashboard_widget_function( $post, $callback_args ) {
	echo "Das <strong>WCAG Plugin</strong> kann einen Menueintrag im Menü-Footer nicht anlegen. Wahrscheinlich haben Sie bereits einen Menüeintrag mit dem Namen <strong>Barrierefreiheit</strong>. Ändern Sie bitte den Namen Ihrer Seite.";
}

function wcag_add_dashboard_widgets() {
	wp_add_dashboard_widget('wcag_dashboard_widget', '<span class="dashicons dashicons-awards"></span>Achtung!', 'RRZE\Wcag\wcag_dashboard_widget_function');
}

function example_remove_dashboard_widget() {
 	remove_meta_box( 'wcag_dashboard_widget', 'dashboard', 'side' );
} 
 
function add_page_to_footer_menu() {
    
    $current_theme = wp_get_theme();
    $themes_fau = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
    
    if(in_array($current_theme, $themes_fau)) {
        if(has_nav_menu('meta-footer')) {
            $menu_name = 'meta-footer';
            $menu_id = wp_get_nav_menu_object( get_nav_menu_locations( $menu_name )[ $menu_name ] )->term_id;
           
            $menu_items = wp_get_nav_menu_items($menu_id);
            #error_log(print_r($menu_items, true));
            foreach($menu_items as $menu_item) {
                $title = $menu_item->title;
                if($title == 'Barrierefreiheit') {
                    $used = true;
                    add_action('wp_dashboard_setup', 'RRZE\Wcag\wcag_add_dashboard_widgets' );
                } else {
                    $used = false;
                    #add_action('wp_dashboard_setup', 'example_remove_dashboard_widget' );
                }
            }
    
            if(!$used) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' =>  __('Barrierefreiheit','rrze-wcag'),
                    'menu-item-classes' => 'wcag',
                    'menu-item-url' => home_url('/barrierefreiheit'), 
                    'menu-item-status' => 'publish')
                );
            }
        }
    }  
}

add_action('init', 'RRZE\Wcag\add_page_to_footer_menu');