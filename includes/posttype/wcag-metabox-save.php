<?php

namespace RRZE\Wcag;

function wcag_complete_box_save( $post_id, $post, $update ) {
    
    $post_type = get_post_type($post_id);

    if ( "wcag" != $post_type ) return;

    if ( isset( $_POST['wcag_complete'] ) ) {
        update_post_meta( $post_id, 'wcag_complete', sanitize_text_field( $_POST['wcag_complete'] ) );
    }

    if ( isset( $_POST['wcag_complete'] ) ) {
        $criteria_main = sanitize_text_field( $_POST['wcag_complete'] );
        update_post_meta( $post_id, 'wcag_complete', $criteria_main );
    } else {
        update_post_meta( $post_id, 'wcag_complete', FALSE );
    }
}

add_action( 'save_post', 'RRZE\Wcag\wcag_complete_box_save', 10, 3 );