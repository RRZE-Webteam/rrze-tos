<?php

/* Quit */
defined('ABSPATH') || exit;

$values = get_option('rrze_tos');

get_header(); ?>

<?php if (!is_front_page()) { ?>
    <div id="sidebar" class="sidebar">
        <?php get_sidebar('page'); ?>
    </div><!-- .sidebar -->
<?php } ?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">
        <h2>WCAG-Prüfung der Website</h2>
        <p>Die öffentlichen Stellen sind gemäß der <a href="http://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016L2102&rid=1">EU-Richtline</a> über den barrierefreien Zugang zu den Webseites und mobilen Anwendungen öffentlicher Stellen
           verpflichtet Ihre Websites entsprechend den WCAG Kriterien umzusetzen.
        Diese Webseite wurde gemäß den Konformitätsbedingungen der WCAG geprüft.</p>
        <h3>Sind die Konformitätskriterien derzeit erfüllt?</h3>
        <?php
        if(isset($values['rrze_tos_field_2']) && $values['rrze_tos_field_2'] == 1) { ?>
        <p class="wcag-pass">Die Kriterien werden erfüllt.</p>
        <?php } else { ?>
        <p class="wcag-fail">Die Kriterien werden nicht erfüllt.</p>
        <p style="margin-top:20px;margin-bottom:20px"><strong>Begründung:</strong></p>
        <?php echo $values['rrze_tos_field_3']; ?>
       <?php } ?>
        <h3>Probleme bei der Bedienung der Seite?</h3>
        <h4 class="wcag-h3">Für diesen Webauftritt sind folgende Personen verantwortlich:</h4>
        <?php echo do_shortcode('[admins]'); ?>
        <p>Bei Problemen mit der Bedienung der Webseite schreiben Sie eine E-Mail an <a href="mailto:<?php echo $values['rrze_tos_field_16'] ?>?subject=<?php echo (!empty($values['rrze_tos_field_19']) ? $values['rrze_tos_field_19'] : 'Feedback zur Barrierefreiheit des Webauftritts') ?>"><?php echo $values['rrze_tos_field_16'] ?></a> oder füllen Sie das Feedback-Formular aus!</p>
        <h3>Feedback-Formular</h3>
        <?php echo do_shortcode('[contact field-one="name,text,name-id,rrze-name" '
        . 'field-two="email,text,email-id,rrze-email" '
        . 'field-three="feedback,textarea,textarea-id" '
        . 'field-four="captcha,text,captcha-id" '
        . 'field-five="answer,hidden,hidden-id" '
        . 'field-six="timeout,hidden,timeout-id"]'); ?>
        <p class="complaint">Sollten Sie den Eindruck haben, dass Ihnen nicht geholfen wird, können Sie sich an die <a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html">Schiedsstelle</a> wenden.<p>
    </div>
</div>
<?php get_footer(); ?>
