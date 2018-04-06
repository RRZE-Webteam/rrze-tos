<?php

/* Quit */
defined('ABSPATH') || exit;

global $post;

$args = array( 'post_type' => 'wcag' );

$loop = new WP_Query( $args );

get_header(); ?>

<?php if (!is_front_page()) { ?>
    <div id="sidebar" class="sidebar">
        <?php get_sidebar('page'); ?>
    </div><!-- .sidebar -->
<?php } ?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
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
                  <?php echo do_shortcode('[admins]'); ?>
                  <h3>Probleme bei der Bedienung der Seite?</h3>
                  <p>Sollten Sie Probleme bei der Bedingung der Webseite haben, füllen Sie bitte das Feedback-Formular aus!</p>

          <?php echo do_shortcode('[contact field-one="name,text,name-id" '
                  . 'field-two="email,text,email-id" '
                  . 'field-three="feedback,textarea,textarea-id" '
                  . 'field-four="captcha,text,captcha-id" '
                  . 'field-five="answer,hidden,hidden-id" '
                  . 'field-six="timeout,hidden,timeout-id"]'); ?>

    </div>
</div>
<?php get_footer(); ?>
