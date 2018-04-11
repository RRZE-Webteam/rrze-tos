<?php

namespace RRZE\Wcag;

function add_page_to_footer_menu() {
    
    $current_theme = wp_get_theme();
    $themes_fau = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
    
    if(in_array($current_theme, $themes_fau)) {
        if(has_nav_menu('meta-footer')) {
            $menu_name = 'meta-footer';
            $menu_id = wp_get_nav_menu_object( get_nav_menu_locations( $menu_name )[ $menu_name ] )->term_id;
            $menu_items = wp_get_nav_menu_items($menu_id);
            foreach($menu_items as $menu_item) {
                $title = $menu_item->title;
                if($title == 'Barrierefreiheit') {
                    $used = true;
                } else {
                    $used = false;
                }
            }
    
            if(!$used) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' =>  __('Barrierefreiheit'),
                    'menu-item-classes' => 'wcag',
                    'menu-item-url' => home_url('/fa7b70763d9d18f3fd32481907f4b687e4a7ca71a62582ad0cc00f5840cfa9c3'), 
                    'menu-item-status' => 'publish')
                );
            }
        }
    }  
}

add_action('init', 'RRZE\Wcag\add_page_to_footer_menu');