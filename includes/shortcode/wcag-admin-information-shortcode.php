<?php

namespace RRZE\Wcag;

add_shortcode('admins', 'RRZE\WCag\show_admins'); 

function show_admins ($atts) {
    return get_info();
}

function get_info() {
    
    global $post;
    
    $host = $_SERVER['SERVER_NAME'];
    
    $response = wp_remote_get('http://remoter.dev/wcag-test.json');
    $status_code = wp_remote_retrieve_response_code( $response );

    if ( 200 === $status_code ) {
        $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
        $res = json_decode($json, TRUE);
        
        $values = get_option('rrze_wcag');
        
        if($values) {

            foreach($values as $key => $value) {
                $store['verantwortlich']['strasse']   =  $values['rrze_wcag_field_6'];
                $store['verantwortlich']['ort']       =  $values['rrze_wcag_field_7'];
                $store['verantwortlich']['telefon']   =  $values['rrze_wcag_field_8'];
                #$store['verantwortlich']['email']     =  $values['rrze_wcag_field_9'];
                #$store['verantwortlich']['personid']  =  $values['rrze_wcag_field_10'];
                $store['webmaster']['strasse']   =  $values['rrze_wcag_field_13'];
                $store['webmaster']['ort']       =  $values['rrze_wcag_field_14'];
                $store['webmaster']['telefon']   =  $values['rrze_wcag_field_15'];
                #$store['webmaster']['email']     =  $values['rrze_wcag_field_16'];
                #$store['webmaster']['personid']  =  $values['rrze_wcag_field_17'];
            }

            foreach($store as $key => $value) {
                $role = ucfirst($key);
                if ($key == 'verantwortlich') {
                    $role .= 'e/er';
                }
                $heading[] = $role;
            }
        }
        
        $html = '<div class="table-wrapper">';
        $html .= '<div class="scrollable">';
        $html .= '<table width="" border="1">';
        $html .= '<tbody><tr>';
        $html .= '<th>' . (isset($heading[0]) ? $heading[0] : 'Verantwortliche/er') . '</th><th>' .(isset($heading[1]) ? $heading[1] : 'Webmaster') . '</th></tr><tr><td>';
        $html .=  $res['metadata']['verantwortlich']['vorname'] . ' ' . $res['metadata']['verantwortlich']['nachname'] .'<br/>';
        $html .= (!empty($store['verantwortlich']['strasse']) && !empty($store['verantwortlich']['ort']) ? $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>': '');
        $html .= (!empty($store['verantwortlich']['telefon']) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '');
        $html .= '<strong>E-Mail:</strong> ' . $res['metadata']['verantwortlich']['email'] . '</br>';
        $html .= (!empty($store['verantwortlich']['homepage']) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '');
        $html .= '</td><td>';
        $html .=  $res['metadata']['webmaster']['vorname'] . ' ' .$res['metadata']['webmaster']['nachname'] .'<br/>';
        $html .= (!empty($store['webmaster']['strasse']) && !empty($store['webmaster']['ort']) ? $store['webmaster']['strasse'] . '<br/>' . $store['webmaster']['ort'] . '<br/>': '');
        $html .= (!empty($store['webmaster']['telefon']) ? '<strong>Telefon:</strong> ' . $store['webmaster']['telefon'] . '<br/>': '');
        $html .= '<strong>E-Mail:</strong> ' . $res['metadata']['webmaster']['email'] . '</br>';
        $html .= (!empty($store['webmaster']['homepage']) ? '<strong>Website:</strong> ' . $store['webmaster']['homepage'] . '<br/>':'');
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table></div></div>';
        echo $html;
        
    } else {
        
        $values = get_option('rrze_wcag');

        foreach($values as $key => $value) {
            $store['verantwortlich']['vorname']   =  $values['rrze_wcag_field_4'];
            $store['verantwortlich']['nachname']  =  $values['rrze_wcag_field_5'];
            $store['verantwortlich']['strasse']   =  $values['rrze_wcag_field_6'];
            $store['verantwortlich']['ort']       =  $values['rrze_wcag_field_7'];
            $store['verantwortlich']['telefon']   =  $values['rrze_wcag_field_8'];
            $store['verantwortlich']['email']     =  $values['rrze_wcag_field_9'];
            #$store['verantwortlich']['personid']  =  $values['rrze_wcag_field_10'];
            $store['webmaster']['vorname']   =  $values['rrze_wcag_field_11'];
            $store['webmaster']['nachname']  =  $values['rrze_wcag_field_12'];
            $store['webmaster']['strasse']   =  $values['rrze_wcag_field_13'];
            $store['webmaster']['ort']       =  $values['rrze_wcag_field_14'];
            $store['webmaster']['telefon']   =  $values['rrze_wcag_field_15'];
            $store['webmaster']['email']     =  $values['rrze_wcag_field_16'];
            #$store['webmaster']['personid']  =  $values['rrze_wcag_field_17'];
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