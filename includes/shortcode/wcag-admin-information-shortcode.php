<?php

namespace RRZE\Wcag;

add_shortcode('admins', 'RRZE\WCag\show_admins'); 

function show_admins ($atts) {

    $atts = shortcode_atts( array(
        'role'      => ' administrator',
        'exclude'   => ''
    ), $atts, 'admins' );
    
    $role = $atts['role'];
    $exclude = $atts['exclude'];
    
    return get_info($role, $exclude);

}

function get_info($role, $exclude) {
    
    $args = array(
        'role'      => $role,
        'exclude'   => ''
    );
    
    //echo '<h3>Für diesen Webauftritt sind folgende Personen verantwortlich:</h3>';
    
    $my_user_query = new \WP_User_Query( $args );
 
    $admins = $my_user_query->get_results();
    
    $count_admins = count($admins);
    
    echo '<h3 style="margin-top:30px;">Für diesen Webauftritt ' . ($count_admins > 1 ? 'sind folgende Personen' : 'ist folgende Person') .' (Webmaster) verantwortlich:</h3>';
    
    if ( ! empty( $admins ) ) {
 
        echo '<ul class="admins-list">';

            foreach ( $admins as $admin ) {

                $admin_info = get_userdata( $admin->ID );
                $sep = substr($admin->user_email, 0, strpos($admin->user_email, '@'));
                $ex = explode('.', $sep);
                $name = '';
                foreach($ex as $key => $value) {
                    $name .= ucfirst($value) . ' ';
                }
               

                echo '<li>' . $name . ' ' . '(E-Mail: <a href="mailto:' . $admin->user_email . '?Subject=Anfrage zur Barrierefreiheit ihres Webauftritts">' . $admin->user_email . ')</a></li>';

            }

        echo '</ul>';
 
    } else {
        
        echo __( 'No editors found!', 'tutsplus' );

    }
    
}
