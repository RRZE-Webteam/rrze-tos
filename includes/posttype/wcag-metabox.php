<?php

namespace RRZE\Wcag;

function remove_metabox_wcag_main_box( $post ) {
    
    do_meta_boxes( null, 'wcag-main-metabox-holder', $post );
}

add_action( 'edit_form_after_title', 'RRZE\Wcag\remove_metabox_wcag_main_box' );

function wcag_add_main_meta_box() {
 
    add_meta_box(
        'wcag_main_metabox_id',
         __( 'WCAG Criteria accomplished?', 'rrze-wcag' ),
        'RRZE\Wcag\wcag_criteria_complete',
        'wcag',
        'wcag-main-metabox-holder'
    );
    
}

add_action( 'add_meta_boxes', 'RRZE\Wcag\wcag_add_main_meta_box' );
 
function wcag_criteria_complete( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'wcag_complete_nonce' );
    $value = get_post_meta( $post->ID, 'wcag_complete', true ); ?>
    <label for="Yes"><?php _e('Yes', 'rrze-wcag') ?></label>
    <input type="radio" id="radio-yes" name="wcag_complete" value="1" <?php echo ($value == 1)? 'checked="checked"':''; ?>>
    <label for="No"><?php _e('No', 'rrze-wcag') ?></label>
    <input type="radio" id="radio-no" name="wcag_complete" value="0" <?php echo ($value == 0)? 'checked="checked"':''; ?>>

<?php 
}