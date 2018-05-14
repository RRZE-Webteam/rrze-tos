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

$values = get_option('rrze_wcag');

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
                    <h1><?php echo (isset($values['rrze_wcag_field_1']) ? $values['rrze_wcag_field_1'] : 'Barrierefreiheitserklärung') ?></h1>
                </div>
            </div>
        </div>
    </section>

    <div id="content">
        <div class="container">

            <div class="row">
                <div class="col-xs-12">
                    <main>                        
                        <h2><?php _e('WCAG review','rrze-wcag') ?></h2>
                        <p>
                            <?php _e('Public authorities are required by the ','rrze-wcag') ?><a href="http://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016L2102&rid=1"><?php _e('EU Directive','rrze-wcag') ?></a><?php _e(' on Accessibility to Sites and Mobile Applications of Public Agencies to implement their websites in accordance with WCAG criteria. This website has been reviewed in accordance with WCAG conformity criteria.','rrze-wcag'); ?>
                        </p>
                        <h3><?php _e('Are the conformity criteria currently fulfilled?','rrze-wcag') ?></h3>
                        <?php  
                       
                            if(isset($values['rrze_wcag_field_2']) && $values['rrze_wcag_field_2'] == 1) { ?>
                                <p class="wcag-pass"><?php _e('The criteria are fulfilled.','rrze-wcag')?></p>
                            <?php } else { ?>
                                <p class="wcag-fail"><?php _e('The criteria are not fulfilled.','rrze-wcag')?></p>
                                <p style="margin-top:20px;margin-bottom:20px"><strong><?php _e('Reason:','rrze-wcag') ?></strong></p>
                                <?php echo $values['rrze_wcag_field_3']; ?>
                               <?php } ?>
                                <h3><?php _e('Problems with the operation of the site?','rrze-wcag')?></h3>
                                <h4 class="wcag-h3"><?php _e('The following people are responsible for this website:','rrze-wcag')?></h4>
                                <?php echo do_shortcode('[admins]'); ?>
                                <p>
                                    <?php _e('If you have any problems using this website, please write an email to ','rrze-wcag') ?><a href="mailto:<?php echo $values['rrze_wcag_field_16'] ?>?subject=<?php echo (!empty($values['rrze_wcag_field_19']) ? $values['rrze_wcag_field_19'] : 'Feedback zur Barrierefreiheit des Webauftritts') ?>"><?php echo $values['rrze_wcag_field_16'] ?></a><?php _e(' or fill out the feedback form!', 'rrze-wcag') ?>
                                </p>
                                <h3><?php _e('Feedback-Form','rrze-wcag')?></h3>
                        
                        <?php echo do_shortcode('[contact field-one="name,text,name-id,rrze-name" '
                                . 'field-two="email,text,email-id,rrze-email" '
                                . 'field-three="feedback,textarea,textarea-id" '
                                . 'field-four="captcha,text,captcha-id" '
                                . 'field-five="answer,hidden,hidden-id" '
                                . 'field-six="timeout,hidden,timeout-id"]'); ?>
                                 <p class="complaint"><?php _e('If you feel that you are not being helped, you can contact the ','rrze-wcag') ?><a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html"> <?php _e('arbitration board.','rrze-wcag') ?> </a><p>
                        
                    </main>
                </div>

            </div>

        </div>
    </div>

<?php get_footer(); ?>
