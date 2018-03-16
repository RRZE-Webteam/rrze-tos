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
    $flag = 0;
    $output = '';
    
    if(isset($_POST['submit'])) {
       
        $form_values = $_POST; 
        $errors = checkErrors($form_values);
        $ans = $form_values['captcha'];
        $checksum = md5($ans);
        $salted = md5($checksum.$salt);
        
        if(isset($errors) AND !count($errors)) {
            if (in_array($salted, $form_values['ans'])) { 
                echo '<h2>Vielen Dank! Wir werden uns umgehend bei Ihnen melden!</h2>';
                $flag = 1;
            } else {
                $errors['newcaptcha'] = 'Falsches Captcha! Versuchen Sie es erneut.';
                $flag = 0;
            }
        
        }
       
    }
    
    if($flag == 0) {
        $output  =  '<h2>Probleme bei der Bedienung der Seite?</h2>';
        $output .=  '<p>Sollten Sie Probleme bei der Bedingung der Webseite haben, füllen Sie bitte das Feedback-Formular aus!</p><br />';
        $output .=  '<form method="post" id="captchaform">';
        $output .=  (isset($errors['name']) ? '<div style="color:red;">' . $errors['name'] . '</div>' : '');
        $output .=  '<p><label for="name">Name:</label></p><p><input type="text" name="name" id="name" value="' . (isset($_POST['submit']) ? htmlspecialchars($form_values['name'], ENT_QUOTES) : '') . '"/></p>';
        $output .=  (isset($errors['email']) ? '<div style="color:red;">' . $errors['email'] . '</div>' : '');
        $output .=  (isset($errors['validaddress']) ? '<div style="color:red;">' . $errors['validaddress'] . '</div>' : '');
        $output .=  '<p><label for="email">E-Mail:</label></p><p><input type="text" name="email" id="email" value="' . (isset($_POST['submit']) ? htmlspecialchars($form_values['email'], ENT_QUOTES) : '') . '"/></p>';
        $encry = md5($captcha_array['task_encrypted'].$salt);
        $output .=  '<p><input type="hidden" name="ans[]" value="' . $encry . '"/></p>';
        $output .=  (isset($errors['feedback']) ? '<div style="color:red;">' . $errors['feedback'] . '</div>' : '');
        $output .=  '<p><label for="feedback">Ihr Feedback</label>';
        $output .=  '<textarea id="feeadback" name="feedback" cols="150" rows="10">' . (isset($_POST['submit']) ? htmlspecialchars($form_values['feedback'], ENT_QUOTES) : '') .'</textarea></p>';
        //$output .=  '<textarea id="feeadback" name="feedback" cols="150" rows="10"></textarea></p>';
        $output .=  (isset($errors['captcha']) ? '<div style="color:red;">' . $errors['captcha'] . '</div>' : '');
        $output .=  (isset($errors['validcaptcha']) ? '<div style="color:red;">' . $errors['validcaptcha'] . '</div>' : '');
        $output .=  (isset($errors['newcaptcha']) ? '<div style="color:red;">' . $errors['newcaptcha'] . '</div>' : '');
        $output .=  '<p><label for="check">Lösen Sie folgende Aufgabe:</label></p>';
        $output .=  '<p>' . $captcha_array['task_string'] . ' ' . '<input type="text" name="captcha" id="check"/></p>';
        $output .=  '<input type="submit" name="submit" form="captchaform" value="Senden">';
        $output .=  '</form>';
        $output .=  '</div>';
    }
                                   
  echo $output;  
}

function checkErrors($values) {
    
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
        if($_POST['name'] =='') {
            $hasErrors['name'] = "Bitte geben Sie Ihren Namen ein.";
        }
        if($_POST['email'] =='') {
            $hasErrors['email'] = "Bitte geben Sie Ihre E-Mail ein.";
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            if(!empty($_POST['email'])) {
                $hasErrors['validaddress'] = "Bitte geben Sie das richtige Format ein. (name@domain.de)";
            }
        }
        if(!preg_match('/^[0-9]{1,2}$/', $_POST['captcha'])) {
            if(!empty($_POST['captcha'])) {
                $hasErrors['validcaptcha'] = "Sie können maximal zwei Ziffern eingeben";
            }
        }
    }
    
    return $hasErrors;
    
}