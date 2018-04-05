<?php

namespace RRZE\Wcag;

add_shortcode('admins', 'RRZE\WCag\show_admins'); 

function show_admins ($atts) {

    $atts = shortcode_atts( array(
        'role'      => '',
        'exclude'   => ''
    ), $atts, 'admins' );
    
    $role = $atts['role'];
    $exclude = $atts['exclude'];
    
    return get_info($role, $exclude);

}

function get_info($role, $exclude) {
    
    $host = $_SERVER['SERVER_NAME'];
    echo 'https://www.wmp.rrze.fau.de/api/domain/metadata/www.'. $host;
    
    $response = file_get_contents( 'http://remoter.dev/wcag-test.json' );
    $res = json_decode($response, TRUE);
   
    echo '<h3>Für diesen Webauftritt sind folgende Personen verantwortlich:</h3>';
    $i = 0;
    if ( !empty( $res ) ) {
 
        echo '<ul class="admins-list">';

            foreach ( $res['metadata'] as $admin ) {
               
               echo '<li>' . $admin['vorname'] . ' ' . $admin['nachname'] . ($i == 0 ? ' (Verantwortliche/er)' : ' (Webmaster)') . ',' . ' E-Mail: <a href="mailto:' . $admin['email'] . '?Subject=Anfrage zur Barrierefreiheit ihres Webauftritts">' .  $admin['email'] . '</a></li>';
               $i++;
           }

        echo '</ul>';
 
    } else {
        
        echo __( 'No persons found!', 'rrze-wcag' );

    }
    
}
