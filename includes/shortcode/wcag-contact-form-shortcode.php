<?php
namespace RRZE\Wcag;

add_shortcode('contact', 'RRZE\WCag\show_contact_form'); 

function show_contact_form( $atts ) {

    $rrze_wcag_atts = shortcode_atts( array(
        'email' => '',
    ), $atts );
    
    return create_form($email = '');

}

function create_form($email = '') {
    
    $salt = getSalt();
    
    $captcha_array = getCaptcha();
    
    print_r($captcha_array['task_encrypted']);
    
    if(isset($_POST['submit'])) {
       
        $form_values = $_POST; 
        $errors = checkErrors($form_values);
        
        $ans = $form_values['captcha'];
        echo '<pre>';
        print_r($ans);
        echo '</pre>';
      
        $checksum = md5($ans);
      
        echo '<pre>';
        print_r($checksum);
        echo '</pre>';
        $saltet = md5($checksum.$salt);
        echo '<pre>';
        print_r($saltet);
        echo '</pre>';
        echo '<pre>';
        print_r($form_values['ans']);
        echo '</pre>';
       
    }
    
    $output  =  '<h2>Probleme bei der Bedienung der Seite?</h2>';
    $output .=  '<p>Sollten Sie Probleme bei der Bedingung der Webseite haben, füllen Sie bitte das Feedback-Formular aus!</p><br />';
    $output .=  '<form method="post" id="captchaform">';
    $encry = md5($captcha_array['task_encrypted'].$salt);
    $output .=  '<p><input type="hidden" name="ans[]" value="' . $encry . '"/></p>';
    $output .=  (isset($errors['feedback']) ? '<div style="color:red;">' . $errors['feedback'] . '</div>' : '');
    $output .=  '<p><label for="feedback">Ihr Feedback</label>';
    $output .=  '<textarea id="feeadback" name="feedback" cols="150" rows="10">' . (isset($_POST['submit']) ? htmlspecialchars($form_values['feedback'], ENT_QUOTES) : '') .'</textarea></p>';
    //$output .=  '<textarea id="feeadback" name="feedback" cols="150" rows="10"></textarea></p>';
    $output .=  (isset($errors['captcha']) ? '<div style="color:red;">' . $errors['captcha'] . '</div>' : '');
    $output .=  (isset($errors['tomany']) ? '<div style="color:red;">' . $errors['tomany'] . '</div>' : '');
    $output .=  '<p><label for="check">Lösen Sie folgende Aufgabe:</label></p>';
    $output .=  '<p>' . $captcha_array['task_string'] . ' ' . '<input type="text" name="captcha" id="check"/></p>';
    $output .=  '<input type="submit" name="submit" form="captchaform" value="Senden">';
    $output .=  '</form>';
    $output .=  '</div>';
                                   
  echo $output;                           
    
}   

function checkErrors($values) {
    
    print_r($values);
    
    $hasErrors = array();
    
    $pattern = '/^1-9?/';
    
    if(!isset($_POST['feedback']) && !isset($_POST['captcha'])) {
        $hasErrors[] = "Bitte füllen Sie das Formular aus.";
    } else {
        if($_POST['feedback'] =='') {
            $hasErrors['feedback'] = "Bitte geben Sie Ihr Feedback ein.";
        }
        if($_POST['captcha'] =='') {
            $hasErrors['captcha'] = "Bitte geben Sie das Captcha ein.";
        }
        /*if(!preg_match($pattern, $_POST['captcha'])) {
            $hasErrors['tomany'] = "Sie können nur eine Ziffer eingeben";
        }*/
    }
    
    return $hasErrors;
    
}