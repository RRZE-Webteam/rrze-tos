<?php

namespace RRZE\Wcag;

function wcag_webmaster_create_metabox() {
    add_meta_box(
        'wcag_webmaster_metabox', // Metabox ID
        'Webmaster', // Title to display
        'RRZE\Wcag\wcag_webmaster_render_metabox', // Function to call that contains the metabox content
        'wcag', // Post type to display metabox on
        'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
        'default' // Priority relative to other metaboxes
    );
}
add_action( 'add_meta_boxes', 'RRZE\Wcag\wcag_webmaster_create_metabox' );


function wcag_webmaster_metabox_defaults() {
    return array(
        'item_1' => '',
        'item_2' => '',
        'item_3' => '',
        'item_4' => '',
        'item_5' => '',
        'item_6' => '',
        'item_7' => '',
    );
}

function wcag_webmaster_render_metabox() {

    global $post; // Get the current post data
    $saved = get_post_meta( $post->ID, 'wcag_webmaster', true ); // Get the saved values
    $defaults = wcag_webmaster_metabox_defaults(); // Get the default values
    $details = wp_parse_args( $saved, $defaults ); // Merge the two in case any fields don't exist in the saved data
    
    $host = $_SERVER['SERVER_NAME'];
    #$response = 'https://www.wmp.rrze.fau.de/api/domain/metadata/www.'. $host;
    
    $response = wp_remote_get('http://remoter.dev/wcag-test.json');
    $status_code = wp_remote_retrieve_response_code( $response );

    if ( 200 === $status_code ) {
        $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
        #$res = json_decode($json, TRUE);
    }

    ?>
    <fieldset>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_1">
            <?php
                _e( 'Firstname:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_1]"
                id="wcag_webmaster_custom_metabox_item_1"
                value="<?php echo (isset($res['metadata']['webmaster']['vorname']) ? $res['metadata']['webmaster']['vorname'] : esc_attr( $details['item_1'] )); ?>"
                <?php echo (isset($res['metadata']['webmaster']['vorname']) ? "readonly" : '') ?>
            >
        </div>
         <div>
            <label for="wcag_webmaster_custom_metabox_item_2">
            <?php
                _e( 'Lastname:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_2]"
                id="wcag_webmaster_custom_metabox_item_2"
                value="<?php echo (isset($res['metadata']['webmaster']['nachname']) ? $res['metadata']['webmaster']['nachname'] : esc_attr( $details['item_2'] )); ?>"
                <?php echo (isset($res['metadata']['webmaster']['nachname']) ? "readonly" : '') ?>
            >
        </div>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_3">
            <?php
                _e( 'Street:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_3]"
                id="wcag_webmaster_custom_metabox_item_3"
                value="<?php echo esc_attr( $details['item_3'] ); ?>"
            >
        </div>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_4">
            <?php
                _e( 'City:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_4]"
                id="wcag_webmaster_custom_metabox_item_4"
                value="<?php echo esc_attr( $details['item_4'] ); ?>"
            >
        </div>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_5">
            <?php
                _e( 'Phone:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_5]"
                id="wcag_webmaster_custom_metabox_item_5"
                value="<?php echo esc_attr( $details['item_5'] ); ?>"
            >
        </div>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_6">
            <?php
                _e( 'E-Mail:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_6]"
                id="wcag_webmaster_custom_metabox_item_6"
                value="<?php echo (isset($res['metadata']['webmaster']['email']) ? $res['metadata']['webmaster']['email'] : esc_attr( $details['item_6'] )); ?>"
                <?php echo (isset($res['metadata']['webmaster']['email']) ? "readonly" : '') ?>
            >
        </div>
        <div>
            <label for="wcag_webmaster_custom_metabox_item_7">
            <?php
                _e( 'Homepage:', 'wcag_webmaster' );
            ?>
            </label>
            <input
                type="text"
                name="wcag_webmaster_custom_metabox[item_7]"
                id="wcag_webmaster_custom_metabox_item_7"
                value="<?php echo esc_attr( $details['item_7'] ); ?>"
            >
        </div>
    </fieldset>
    <?php
    wp_nonce_field( 'wcag_webmaster_form_metabox_nonce', 'wcag_webmaster_form_metabox_process' );
}

function wcag_webmaster_save_metabox( $post_id, $post ) {

    if ( !isset( $_POST['wcag_webmaster_form_metabox_process'] ) ) return;

    if ( !wp_verify_nonce( $_POST['wcag_webmaster_form_metabox_process'], 'wcag_webmaster_form_metabox_nonce' ) ) {
            return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID )) {
            return $post->ID;
    }

    if ( !isset( $_POST['wcag_webmaster_custom_metabox'] ) ) {
            return $post->ID;
    }

    $sanitized = array();

    foreach ( $_POST['wcag_webmaster_custom_metabox'] as $key => $detail ) {
            $sanitized[$key] = wp_filter_post_kses( $detail );
    }

    update_post_meta( $post->ID, 'wcag_webmaster', $sanitized );

}
add_action( 'save_post', 'RRZE\Wcag\wcag_webmaster_save_metabox', 1, 2 );