<?php

/* Quit */
defined('ABSPATH') || exit;

if(function_exists('fau_initoptions')) {
    $options = fau_initoptions();
} else {
    $options = array();
}

$breadcrumb = '';
if (isset($options['breadcrumb_root'])) {
    if ($options['breadcrumb_withtitle']) {
        $breadcrumb .= '<h3 class="breadcrumb_sitetitle" role="presentation">'.get_bloginfo('title').'</h3>';
        $breadcrumb .= "\n";
    }
    $breadcrumb .= '<nav aria-labelledby="bc-title" class="breadcrumbs">'; 
    $breadcrumb .= '<h4 class="screen-reader-text" id="bc-title">'.__('Sie befinden sich hier:','fau').'</h4>';
    $breadcrumb .= '<a data-wpel-link="internal" href="' . site_url('/') . '">' . $options['breadcrumb_root'] . '</a>';
}

/* Captcha */

$matching_numbers = array(
    'eins'  => 1,
    'zwei'  => 2,
    'drei'  => 3,
    'vier'  => 4,
    'fünf'  => 5,
    'sechs' => 6,
    'sieben'=> 7,
    'acht'  => 8,
    'neun'  => 9
);

$operator = array(
    '+' => 'plus',
    '*' => 'mal'
);

$min_number = 1;
$max_number = 9;

$random_number1 = mt_rand($min_number, $max_number);
$random_number2 = mt_rand($min_number, $max_number);
$random_operator = array_rand($operator, 1);

$figure = array_search($random_number2, $matching_numbers);

$op = $operator[$random_operator[0]];

$solution = $random_number1 . ' ' . $operator[$random_operator[0]] . ' ' . $figure;

$flipped = array_flip($matching_numbers);

$opflipped = array_search($op, $operator);

switch ($op) {
  case 'plus':
    $output = $random_number1 + $random_number2;
    break;
  case 'minus':
    $output = $random_number1 - $random_number2;
    break;
  case 'mal':
    $output = $random_number1 * $random_number2;
    break;
}

$salt = 'Ng<RX12m_i,jN:DSzW J*8lX-8uDmniw!7mIowxigB#+Fb+KcW$?phRDk|<)YGf|';
//delete_option('captcha');
/*if(!isset($_POST['submit'])) {
   $captcha = array(
       'answers' => array(
           md5($output)
        )
   );
   add_option('captcha', $captcha);
}*/

$flag = 0;

global $post;

$args = array( 'post_type' => 'wcag' );

$loop = new WP_Query( $args );

get_header(); ?>

    <section id="hero" class="hero-small">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $breadcrumb; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <h1>WCAG 2.0 AA Prüfung</h1>
                </div>
            </div>
        </div>
    </section>

    <div id="content">
        <div class="container">

            <div class="row">
                <div class="col-xs-12">
                    <main>                        
                        <h2>Prüfungsergebnisse gemäß WCAG 2.0 AA</h2>
                        <p>Diese Webseite wurde gemäß den Konformitätsbedingungen der WCAG geprüft.</p>
                        <h3>Sind die Konformitätskriterien derzeit erfüllt?</h3><br />
                        <?php  
                        while ( $loop->have_posts() ) : $loop->the_post();
                            $complete = get_post_meta($post->ID, 'wcag_complete', true);
                            if($complete == 1) { ?>
                                <p class="wcag-pass">Die Kriterien werden erfüllt.</p>
                            <?php } else { ?>
                                <p class="wcag-fail">Die Kriterien werden nicht erfüllt.</p>
                                <p style="margin-top:20px;margin-bottom:20px"><strong>Begründung:</strong></p>
                                <?php the_content();
                             } 
                        endwhile; ?>
                            <?php 
                            if (isset($_POST['submit'])){
                                $s = 0;
                                $errors = array(); 
                                if(!isset($_POST['feedback'], $_POST['captcha'])) {
                                    //$errors[] = "Bitte benutzen sie unser Formular.";
                                    echo 'deleted';
                                } else {
                                    if($_POST['feedback'] =='') {
                                        $errors['feedback'] = "Bitte geben Sie Ihr Feedback ein.";
                                    }
                                    if($_POST['captcha'] =='') {
                                        $errors['captcha'] = "Bitte geben Sie das Captcha ein.";
                                    }
                                }
                            }
                            
                            if(isset($errors) AND !count($errors)) {
                                $ans = isset($_POST['captcha']);
                                $checksum = md5($ans);
                                $salted = md5($checksum.$salt);
                                //print_r($$_POST['ans']);
                                print_r(get_option('captcha'));
                                //echo $salted;
                                //print_r($ans);
                                /*if(in_array($salted,$_POST['ans'])) { 
                                echo $_POST['ans']*/
                                
                               
                                ?>
                                <p>Vielen Dank für Ihr Feedback! Wir werden uns umgehend bei Ihnen melden.</p> 
                                <?php 
                                $flag = 1;
                                
                                delete_option('captcha'); } else {
                                   if(isset($errors)) {
                                    $message = 'Ihre Benutzereingaben sind nicht korrekt!';
                                     print_r(get_option('captcha'));
                                     //echo $message;
                                    /*foreach($errors as $error) {
                                        echo $error;
                                    }*/
                                     
                                 }
                                }
                                
                                ?> 
                                   
                                <?php
                            //} else { 
                                    if($flag != 1) { ?>
                                <br /><h2>Probleme bei der Bedienung der Seite?</h2>
                                <p>Sollten Sie Probleme bei der Bedingung der Webseite haben, füllen Sie bitte das Feedback-Formular aus!</p><br />
                                <form method="post" id="captchaform">
                                    <p>
                                        <?php if(!empty($message)) echo '<p style="color:red">'. $message . '</p>'; ?>
                                    </p>
                                    <p>
                                        <?php if(get_option('captcha') === false ) {
                                            $captcha = array(
                                                'answers' => array(
                                                    md5($output)
                                                 )
                                            );
                                            add_option('captcha', $captcha);
                                            $captcha = get_option('captcha');
                                            foreach($captcha['answers'] as $checksum)  { 
                                            $salted = md5($checksum.$salt); ?>
                                            <input type="hidden" name="ans[]" value="<?php echo $salted ?>"/>
                                        <?php /*echo $salted; */}
                                        }
                                        ?>
                                    </p>
                                    <p>
                                        <?php if(isset($_POST['submit']) && !empty($errors['feedback']))  { ?> 
                                            <div style="color:red;"><?php echo $errors['feedback'] ?></div>
                                        <?php } ?>
                                        <label for="feedback">Ihr Feedback</label>
                                        <textarea id="feeadback" name="feedback" cols="150" rows="10"><?php if(isset($_POST['submit'])) echo $_POST['feedback'] ?></textarea>
                                    </p>
                                    <p>
                                        <?php if(isset($_POST['submit']) && !empty($errors['captcha']))  { ?> 
                                            <div style="color:red;"><?php echo $errors['captcha'] ?></div>
                                        <?php } ?>
                                        <label for="check">Lösen Sie folgende Aufgabe:</label></p>
                                        <p><?php echo $solution . ' = ' ?> <input type="text" name="captcha" id="check" /></p>
                                </form>
                                 <input type="submit" name="submit" form="captchaform" value="Senden">
                                 <?php 
                                   print_r(get_option('captcha'));
                                 //echo $out;
                           }
                        ?>
                    </main>
                </div>

            </div>

        </div>
    </div>

<?php get_footer(); ?>
