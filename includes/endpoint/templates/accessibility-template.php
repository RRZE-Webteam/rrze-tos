<?php
/**
 * WordPress TOS accessibility Template
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

$option_values = (array) get_option( 'rrze_tos' );
$url           = 'http://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016L2102&rid=1';
$email         = isset( $option_values['rrze_tos_receiver_email'] ) ? $option_values['rrze_tos_receiver_email'] : '' ;
$subject       = !empty( $option_values['rrze_tos_subject'] ) ? $option_values['rrze_tos_subject'] : __( 'Feedback zur Barrierefreiheit des Webauftritts', 'rrze-tos' );
?>
<h2><?php _e('TOS review of the site WCAG-Prüfung der Website','rrze-tos'); ?></h2>
<p>
	<?php printf( __( 'Public authorities are required by the <a target="_blank" href="%s">EU Directive</a> on Accessibility to Sites and Mobile Applications of Public Agencies to implement their websites in accordance with WCAG criteria. This website has been reviewed in accordance with WCAG conformity criteria.',
		'rrze-tos' ), esc_url( $url ) ); ?><br>
</p>

<h3><?php _e( 'Are the conformity criteria currently fulfilled?', 'rrze-tos' ); ?></h3>
<?php
if ( isset( $option_values['rrze_tos_conformity'] ) && $option_values['rrze_tos_conformity'] === '1' ) { ?>
	<div class="alert alert-success" role="alert">
		<span class="wcag-pass"></span><?php _e( 'The criteria are fulfilled.', 'rrze-tos' ) ?>
	</div>
<?php } else { ?>
	<div class="alert alert-danger" role="alert">
		<span class="wcag-fail"></span><?php _e( 'The criteria are not fulfilled.', 'rrze-tos' ); ?><br>
	</div>
	<div>
		<strong><?php _e( 'Reason:', 'rrze-tos' ) ?></strong><br>
		<?php echo $option_values['rrze_tos_no_reason']; ?>
	</div>
<?php } ?>

<h3><?php _e( 'Problems with the operation of the site?', 'rrze-tos' ) ?></h3>
<h4 class="wcag-h3"><?php _e( 'The following people are responsible for this website:', 'rrze-tos' ); ?></h4>
<?php echo do_shortcode( '[admins]' ); ?>

<p>
	<?php printf( __( 'If you have any problems using this website, please write an email to <a href="mailto:%1$s?subject=%2$s" >%1$s</a> or fill out the feedback form!',
		'rrze-tos' ), $email, $subject); ?><br>
</p>

<h3><?php _e( 'Feedback-Form', 'rrze-tos' ) ?></h3>
<?php
echo do_shortcode( '[contact 
field-name="name,text,name-id,rrze-name" 
field-email="email,text,email-id,rrze-email" 
field-feedback="feedback,textarea,textarea-id"
field-captcha="captcha,text,captcha-id"
field-answer="answer,hidden,hidden-id"
field-timeout="timeout,hidden,timeout-id"
]'); ?>
<p class="complaint"><?php _e( 'If you feel that you are not being helped, you can contact the ', 'rrze-tos' ) ?>
	<a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html"> <?php _e( 'arbitration board.', 'rrze-tos' ) ?> </a>
<p>
