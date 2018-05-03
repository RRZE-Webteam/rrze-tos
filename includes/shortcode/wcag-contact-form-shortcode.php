<?php
namespace RRZE\Wcag;

add_shortcode('contact', 'RRZE\WCag\contact_shortcode'); 

function contact_shortcode( $atts ) {

$atts = shortcode_atts( 
    array(
      'field-one'   => '',
      'field-two'   => '',
      'field-three' => '',
      'field-four'  => '',
      'field-five'  => '',
      'field-six'   => '',
      'timeout'     =>  3, 
    ), $atts);

    $filter = array_filter($atts);

    foreach($filter as $key => $value) {
      $fields[] = explode(",", $value);
    }
    
    /*echo '<pre>';
    print_r($fields);
    echo '</pre>';*/

    return generateForm($fields);
}

function generateForm($values) {
    
    $salt = getSalt();
    $captcha_array = getCaptcha();
    $encrypted = md5($captcha_array['task_encrypted'].$salt);
    $flag = 0;
    
    $fields = $values;
    $timeout = array_pop($fields);
    
    if(isset($_POST['submit'])) {

        $values = assignPostValues($_POST);
        
       /* echo '<pre>';
        print_r($values);
        echo '</pre>';*/
        
        $current_time = time();
        $submitted = $values['timeout'] + $timeout[0];
        
        $hasErrors = checkErrors($values);
        $ans = $values['captcha'];
        $checksum = md5($ans);
        $salted = md5($checksum.$salt);
        $clean = array_filter($hasErrors);
        
        if(isset($clean) && !count($clean)) {
            if ($current_time < $submitted) {
                echo 'Du bist ein Bot!';
            } elseif ($salted === $values['answer']) { 
                echo '<h2>Vielen Dank! Wir werden uns umgehend bei Ihnen melden!</h2>';
                sendMail($values['feedback'], $values['email'], $values['name']);
                $flag = 1;
            } else {
                $flag = 0;
                $hasErrors['captcha'] = 'Falsches Captcha! Versuchen Sie es erneut.';
            }

        }
    }
    
    if($flag == 0) {
        ?>
        <form method="post" id="feedback_form">
        <?php 
            for($i = 0; $i < sizeof($fields); $i++) {
              switch($fields[$i][1]) {
                case 'text': 
                    if($fields[$i][0] == 'captcha') { ?>
                    <p>
                        <?php if(isset($_POST['submit']) && isset($hasErrors) && array_key_exists($fields[$i][0], $hasErrors)) {  ?>
                        <div class="error"><?php echo $hasErrors[$fields[$i][0]] ?></div>
                        <?php } ?>
                        <label for="check">Lösen Sie folgende Aufgabe:</label><br />
                        <?php echo $captcha_array['task_string'] . ' '?><input type="text" name="captcha" id="check" >
                    </p>
                   <?php } else {?>
                    <p>
                        <?php if(isset($_POST['submit']) && isset($hasErrors) && array_key_exists($fields[$i][0], $hasErrors)) {  ?>
                        <div class="error"><?php echo $hasErrors[$fields[$i][0]] ?></div>
                        <?php } ?>
                        <label for=<?php echo $fields[$i][2] ?>><?php echo ($fields[$i][0] == 'email' ? 'E-Mail' : ucfirst($fields[$i][0])) ?>:</label><br />
                        <input type="text" name=<?php echo $fields[$i][0] ?> id=<?php echo $fields[$i][2] ?> placeholder=<?php echo ($fields[$i][0] == 'email' ? 'E-Mail' : ucfirst($fields[$i][0])) ?> value=<?php echo (isset($_POST['submit'])) ? $values[$fields[$i][0]] : ''?> >
                   </p><?php } break;
                case 'textarea': ?>
                    <p>
                        <?php if(isset($_POST['submit']) && isset($hasErrors) && array_key_exists($fields[$i][0], $hasErrors)) {  ?>
                        <div class="error"><?php echo $hasErrors[$fields[$i][0]] ?></div>
                        <?php } ?>
                        <label for=<?php echo $fields[$i][2] ?>><?php echo ucfirst($fields[$i][0]) ?>:</label>
                        <textarea name=<?php echo $fields[$i][0] ?>  id=<?php echo $fields[$i][2] ?> cols="150" rows="10"><?php echo (isset($_POST['submit'])) ? $values[$fields[$i][0]] : ''?></textarea>
                    </p><?php break;
                case 'hidden': 
                    if($fields[$i][0] == 'answer') { ?>
                        <p>
                            <input type="hidden" class="form-control" name=<?php echo $fields[$i][0].'[]'?> value=<?php echo $encrypted ?>>
                        </p><?php
                    } else { ?>
                        <p>
                            <input type="hidden" class="form-control" name=<?php echo $fields[$i][0] ?> value=<?php echo time() ?>>
                        </p><?php break;
                    }
              }
            }?>
            <input type="submit" class="submit_wcag_form" name="submit" form="feedback_form"value="Senden" >
        </form>
        <?php 
    } 
}

function assignPostValues($post) {

    foreach($post as $key => $value) {
        if($key != 'answer') {
            $a[$key] = strip_tags(htmlspecialchars($value));
        } else {
            $a[$key] = strip_tags(htmlspecialchars($value[0]));
        }
    }

    return $a;
}

function checkErrors($a) {
    foreach($a as $key1 => $value1) {
        if($value1 === '') {
            $hasErrors[$key1] = 'Bitte ' . ucfirst($key1) . ' eingeben.';
        }elseif($key1 == 'email' && !filter_var($value1, FILTER_VALIDATE_EMAIL)) {
            $hasErrors[$key1] = 'Falsches ' . ucfirst($key1).'-Format.';
        }elseif($key1 == 'captcha' && !preg_match('/^[0-9]{1,2}$/', $_POST['captcha'])) {
            $hasErrors[$key1] = 'Sie können maximal zwei Ziffern eingeben';
        }else{
           $hasErrors['error'] = '';
        }
    }

    return $hasErrors;
}

function sendMail($feedback, $from, $name) {
    
    $values = get_option('rrze_wcag');
    
    if(!$values) {
        
        $response = wp_remote_get('http://remoter.dev/wcag-test.json');
        $status_code = wp_remote_retrieve_response_code( $response );

        if ( 200 === $status_code ) {
            $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
            $res = json_decode($json, TRUE);
        }
        
    }
    
    /*$to = (!empty($values['rrze_wcag_field_18']) ? $values['rrze_wcag_field_18'] : $res['metadata']['webmaster']['email']);
    $subject = $values['rrze_wcag_field_19'];
    $message = $feedback;
    $headers[] = "From: <$from>";
    $cc = (!empty($values['rrze_wcag_field_20']) ? $values['rrze_wcag_field_20'] : '');
    if(!empty($cc)) {
        $headers[] = "CC: <$cc>";
    }
    
    wp_mail( $to, $subject, $message, $headers );*/
    
    $to = (!empty($values['rrze_wcag_field_18']) ? $values['rrze_wcag_field_18'] : $res['metadata']['webmaster']['email']);
    $subject = $values['rrze_wcag_field_19'];
    $message = $feedback;
    $headers[] = "From: <$from>";
    $cc = (!empty($values['rrze_wcag_field_20']) ? $values['rrze_wcag_field_20'] : '');
    $cc_addr = explode(",", $cc);
    if(!empty($cc)) {
        foreach($cc_addr as $cc => $value) {
            $headers[] = "CC: <$value>";
        }
    }
    
    wp_mail( $to, $subject, $message, $headers );
    
}