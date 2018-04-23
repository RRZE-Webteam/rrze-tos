<?php
/* Quit */
defined('ABSPATH') || exit;

global $post;

$args = array( 'post_type' => 'wcag' );

$loop = new WP_Query( $args );

get_header();

?>

<div class="content-wrap">
    <div id="blog-wrap" class="blog-wrap cf">
        <div id="primary" class="site-content cf rrze-calendar" role="main">
            
            <h2 class="wcag-h2">WCAG-Prüfung der Website</h2>
            <p>Die öffentlichen Stellen sind gemäß der <a href="http://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016L2102&rid=1">EU-Richtline</a> über den barrierefreien Zugang zu den Webseites und mobilen Anwendungen öffentlicher Stellen
            verpflichtet Ihre Websites entsprechend den WCAG Kriterien umzusetzen.
            <p>Diese Webseite wurde gemäß den Konformitätsbedingungen der WCAG geprüft.</p>
            <h3 class="wcag-h3">Sind die Konformitätskriterien derzeit erfüllt?</h3>
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
                    <h3 class="wcag-h3">Probleme bei der Bedienung der Seite?</h3>
                    <p>Sollten Sie Probleme bei der Bedingung der Webseite haben, füllen Sie bitte das Feedback-Formular aus!<br/>
                    Falls Ihnen nicht geholfen wird, wenden Sie sich bitte an die <a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html">Schiedsstelle</a>.</p>

            <?php echo do_shortcode('[contact field-one="name,text,name-id" '
                    . 'field-two="email,text,email-id" '
                    . 'field-three="feedback,textarea,textarea-id" '
                    . 'field-four="captcha,text,captcha-id" '
                    . 'field-five="answer,hidden,hidden-id" '
                    . 'field-six="timeout,hidden,timeout-id"]'); ?>
            
        </div><!-- end #primary -->

        <?php get_sidebar(); ?>

    </div><!-- end .blog-wrap -->
</div><!-- end .content-wrap -->

<?php get_footer(); ?>
