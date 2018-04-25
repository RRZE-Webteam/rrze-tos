<?php

namespace RRZE\Wcag;

add_shortcode('admins', 'RRZE\WCag\show_admins'); 

function show_admins ($atts) {
    return get_info();
}

function get_info() {
    
    global $post;
    
    $host = $_SERVER['SERVER_NAME'];
   
    echo '<h3 class="wcag-h3">Für diesen Webauftritt sind folgende Personen verantwortlich:</h3>';
    
    $response = wp_remote_get('http://remoter.dev/wcag-test.json');
    $status_code = wp_remote_retrieve_response_code( $response );

    if ( 200 === $status_code ) {
        $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
        $res = json_decode($json, TRUE);
        
        $the_query = new \WP_Query( array( 'post_type' => array('wcag') ) );

        $values1 = get_post_meta($post->ID, 'wcag_resp_person', true);

        foreach($values1 as $key => $value) {
            $store['verantwortlich']['strasse']   =  $values1['item_3'];
            $store['verantwortlich']['ort']       =  $values1['item_4'];
            $store['verantwortlich']['telefon']   =  $values1['item_5'];
            $store['verantwortlich']['homepage']  =  $values1['item_7'];
        }

        $values2 = get_post_meta($post->ID, 'wcag_webmaster', true);

        foreach($values2 as $key => $value) {
            $store['webmaster']['strasse']   =  $values2['item_3'];
            $store['webmaster']['ort']       =  $values2['item_4'];
            $store['webmaster']['telefon']   =  $values2['item_5'];
            $store['webmaster']['homepage']  =  $values2['item_7'];
        }
        
        foreach($store as $key => $value) {
            $role = ucfirst($key);
            if ($key == 'verantwortlich') {
                $role .= 'e/er';
            }
            $heading[] = $role;
        }
        
        $html = '<div class="table-wrapper">';
        $html .= '<div class="scrollable">';
        $html .= '<table width="" border="1">';
        $html .= '<tbody><tr>';
        $html .= '<th>' . $heading[0] . '</th><th>' . $heading[1] . '</th></tr><tr><td>';
        $html .=  $res['metadata']['verantwortlich']['vorname'] . ' ' . $res['metadata']['verantwortlich']['nachname'] .'<br/>';
        $html .= (!empty($store['verantwortlich']['strasse']) ?  $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>': '');
        $html .= (!empty($store['verantwortlich']['telefon']) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '');
        $html .= '<strong>E-Mail:</strong> ' . $res['metadata']['verantwortlich']['email'] . '</br>';
        $html .= (!empty($store['verantwortlich']['homepage']) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '');
        $html .= '</td><td>';
        $html .=  $res['metadata']['webmaster']['vorname'] . ' ' .$res['metadata']['webmaster']['nachname'] .'<br/>';
        $html .= (!empty($store['webmaster']['strasse']) ? $store['webmaster']['strasse'] . '<br/>' . $store['webmaster']['ort'] . '<br/>': '');
        $html .= (!empty($store['webmaster']['telefon']) ? '<strong>Telefon:</strong> ' . $store['webmaster']['telefon'] . '<br/>': '');
        $html .= '<strong>E-Mail:</strong> ' . $res['metadata']['webmaster']['email'] . '</br>';
        $html .= (!empty($store['webmaster']['homepage']) ? '<strong>Website:</strong> ' . $store['webmaster']['homepage'] . '<br/>':'');
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table></div></div>';
        echo $html;
        
    } else {
    
        $the_query = new \WP_Query( array( 'post_type' => array('wcag') ) );

        $values1 = get_post_meta($post->ID, 'wcag_resp_person', true);

        foreach($values1 as $key => $value) {
            $store['verantwortlich']['vorname']   =  $values1['item_1'];
            $store['verantwortlich']['nachname']  =  $values1['item_2'];
            $store['verantwortlich']['strasse']   =  $values1['item_3'];
            $store['verantwortlich']['ort']       =  $values1['item_4'];
            $store['verantwortlich']['telefon']   =  $values1['item_5'];
            $store['verantwortlich']['email']     =  $values1['item_6'];
            $store['verantwortlich']['homepage']  =  $values1['item_7'];
        }

        $values2 = get_post_meta($post->ID, 'wcag_webmaster', true);

        foreach($values2 as $key => $value) {
            $store['webmaster']['vorname']   =  $values2['item_1'];
            $store['webmaster']['nachname']  =  $values2['item_2'];
            $store['webmaster']['strasse']   =  $values2['item_3'];
            $store['webmaster']['ort']       =  $values2['item_4'];
            $store['webmaster']['telefon']   =  $values2['item_5'];
            $store['webmaster']['email']     =  $values2['item_6'];
            $store['webmaster']['homepage']  =  $values2['item_7'];
        }

        foreach($store as $key => $value) {
            $role = ucfirst($key);
            if ($key == 'verantwortlich') {
                $role .= 'e/er';
            }
            $heading[] = $role;
        }


        $html = '<div class="table-wrapper">';
        $html .= '<div class="scrollable">';
        $html .= '<table width="" border="1">';
        $html .= '<tbody><tr>';
        $html .= '<th>' . $heading[0] . '</th><th>' . $heading[1] . '</th></tr><tr><td>';
        $html .=  $store['verantwortlich']['vorname'] . ' ' . $store['verantwortlich']['nachname'] .'<br/>';
        $html .= (!empty($store['verantwortlich']['strasse']) ? $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>': '');
        $html .= (!empty($store['verantwortlich']['telefon']) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '');
        $html .= '<strong>E-Mail:</strong> ' . $store['verantwortlich']['email'] . '</br>';
        $html .= (!empty($store['verantwortlich']['homepage']) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '');
        $html .= '</td><td>';
        $html .=  $store['webmaster']['vorname'] . ' ' . $store['webmaster']['nachname'] .'<br/>';
        $html .= (!empty($store['webmaster']['strasse']) ? $store['webmaster']['strasse'] . '<br/>' . $store['webmaster']['ort'] . '<br/>': '');
        $html .= (!empty($store['webmaster']['telefon']) ? '<strong>Telefon:</strong> ' . $store['webmaster']['telefon'] . '<br/>': '');
        $html .= '<strong>E-Mail:</strong> ' . $store['webmaster']['email'] . '</br>';
        $html .= (!empty($store['webmaster']['homepage']) ? '<strong>Website:</strong> ' . $store['webmaster']['homepage'] . '<br/>':'');
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table></div></div>';
        echo $html;
    
    }
}