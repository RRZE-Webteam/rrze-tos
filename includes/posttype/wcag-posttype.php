<?php

namespace RRZE\Wcag;

function rrze_wcag_post_type() {

    $labels = array(
            'name'                  => _x( 'WCAG', 'Post Type General Name', 'rrze-wcag' ),
            'singular_name'         => _x( 'WCAG', 'Post Type Singular Name', 'rrze-wcag' ),
            'menu_name'             => __( 'WCAG', 'rrze-wcag' ),
            'name_admin_bar'        => __( 'WCAG', 'rrze-wcag' ),
            'archives'              => __( 'Criteria Archives', 'rrze-wcag' ),
            'attributes'            => __( 'Criteria Attributes', 'rrze-wcag' ),
            'parent_item_colon'     => __( 'Parent Criteria:', 'rrze-wcag' ),
            'all_items'             => __( 'All Criteria', 'rrze-wcag' ),
            'add_new_item'          => __( 'Add New Criteria', 'rrze-wcag' ),
            'add_new'               => __( 'Add New', 'rrze-wcag' ),
            'new_item'              => __( 'New Criteria', 'rrze-wcag' ),
            'edit_item'             => __( 'Edit Criteria', 'rrze-wcag' ),
            'update_item'           => __( 'Update Criteria', 'rrze-wcag' ),
            'view_item'             => __( 'View Criteria', 'rrze-wcag' ),
            'view_items'            => __( 'View Criterias', 'rrze-wcag' ),
            'search_items'          => __( 'Search Criteria', 'rrze-wcag' ),
            'not_found'             => __( 'Not found', 'rrze-wcag' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'rrze-wcag' ),
            'filter_items_list'     => __( 'Filter criteria list', 'rrze-wcag' ),
    );
    $args = array(
            'label'                 => __( 'wcag', 'rrze-wcag' ),
            'description'           => __( 'WCAG Criteria', 'rrze-wcag' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'wcag',
    );
    register_post_type( 'wcag', $args );

}

add_action( 'init', 'RRZE\Wcag\rrze_wcag_post_type', 0 );