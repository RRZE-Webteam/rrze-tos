<?php

namespace RRZE\Wcag;

function wcag_main_box() {
  add_meta_box(
    'wcag_main_box',
    __( 'WCAG Criteria accomplished?', 'rrze-wcag' ),
    'RRZE\Wcag\wcag_criteria_complete',
    'wcag',
    'normal',
    'high'
  );
}

add_action( 'add_meta_boxes', 'RRZE\Wcag\wcag_main_box' );

function wcag_criteria_complete ( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ),
  'wcag_criteria_complete_inhalt_nonce' );
  $value = get_post_meta( $post->ID, 'wcag_complete', true ); ?>
  <label for="Yes"><?php _e('Yes', 'rrze-wcag') ?></label>
  <input type="radio" id="radio-yes" name="wcag_complete" value="Yes" <?php echo ($value == 'Yes')? 'checked="checked"':''; ?>>
  <label for="No"><?php _e('No', 'rrze-wcag') ?></label>
  <input type="radio" id="radio-no" name="wcag_complete" value="No" <?php echo ($value == 'No')? 'checked="checked"':''; ?>>
  <?php
}