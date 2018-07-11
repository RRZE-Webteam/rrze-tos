<?php
/* Quit */
defined( 'ABSPATH' ) || exit;

$values = get_option( 'rrze_tos' );
include WP_PLUGIN_DIR . "/rrze-tos/includes/strings.php";
$strings = $template['a11y'];
get_header();

?>

<div class="content-wrap">
	<div id="blog-wrap" class="blog-wrap cf">
		<div id="primary" class="site-content cf rrze-calendar" role="main">
			<h2><?php echo $strings['title']; ?></h2>
			<p><?php echo $strings['intro']; ?></p>
			<h3>Sind die Konformitätskriterien derzeit erfüllt?</h3>
			<?php

			if ( isset( $values['rrze_tos_field_conformity'] ) && $values['rrze_tos_field_conformity'] == 1 ) { ?>
				<p class="wcag-pass">Die Kriterien werden erfüllt.</p>
			<?php } else { ?>
				<p class="wcag-fail">Die Kriterien werden nicht erfüllt.</p>
				<p style="margin-top:20px;margin-bottom:20px"><strong>Begründung:</strong></p>
				<?php echo $values['rrze_tos_field_no_reason']; ?>
			<?php } ?>
			<h3>Probleme bei der Bedienung der Seite?</h3>
			<h4 class="wcag-h3">Für diesen Webauftritt sind folgende Personen verantwortlich:</h4>
			<?php echo do_shortcode( '[admins]' ); ?>
			<p>Bei Problemen mit der Bedienung der Webseite schreiben Sie eine E-Mail an <a
					href="mailto:<?php echo $values['rrze_tos_field_webmaster_email'] ?>?subject=<?php echo( ! empty( $values['rrze_tos_field_subject'] ) ? $values['rrze_tos_field_subject'] : 'Feedback zur Barrierefreiheit des Webauftritts' ) ?>"><?php echo $values['rrze_tos_field_webmaster_email'] ?></a>
				oder füllen Sie das Feedback-Formular aus!</p>
			<h3>Feedback-Formular</h3>
			<?php echo do_shortcode( '[contact field-name="name,text,name-id,rrze-name" '
			                         . 'field-email="email,text,email-id,rrze-email" '
			                         . 'field-feedback="feedback,textarea,textarea-id" '
			                         . 'field-captcha="captcha,text,captcha-id" '
			                         . 'field-answer="answer,hidden,hidden-id" '
			                         . 'field-timeout="timeout,hidden,timeout-id"]' ); ?>
			<p class="complaint">Sollten Sie den Eindruck haben, dass Ihnen nicht geholfen wird, können Sie sich an die
				<a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html">Schiedsstelle</a>
				wenden.
			<p>

		</div><!-- end #primary -->

		<?php get_sidebar(); ?>

	</div><!-- end .blog-wrap -->
</div><!-- end .content-wrap -->

<?php get_footer(); ?>
