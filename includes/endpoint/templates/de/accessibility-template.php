/**
* WordPress TOS accessibility Template
* Open php tag has been deleted on purpose to eval php code
*
* @package    WordPress
* @subpackage TOS
* @since      3.4.0
*/

namespace RRZE\Tos
?>

<?php
$tos_erros = isset( $GLOBALS['tos_erros'] ) ? $GLOBALS['tos_erros'] : '';
if ( $tos_erros instanceof \WP_Error ) {
	if ( ! empty( $tos_erros->get_error_codes() ) ) {
		\RRZE\Tos\my_contact_form_generate_response( 'error', $tos_erros );
	} else {
		\RRZE\Tos\my_contact_form_generate_response( 'success', __( 'The mail has been sent successfully.', 'rrze-tos' ) );
	}
}

?>
<p>
	Die öffentlichen Stellen sind gemäß der
	<a href="https://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32018D1523" target="_blank"> EU
		Directive</a> über den barrierefreien Zugang zu den Webseites und mobilen Anwendungen öffentlicher Stellen
		verpflichtet Ihre Websites entsprechend den WCAG Kriterien umzusetzen. Diese Webseite wurde gemäß den
		Konformitätsbedingungen der WCAG geprüft.<br>
</p>

<h3>Sind die Konformitätskriterien derzeit erfüllt?</h3>
{{ if rrze_tos_conformity
<div class="alert alert-success" role="alert">
	<span class="wcag-pass"></span>Die Kriterien werden erfüllt.
</div>

elseif

<div class="alert alert-info" role="alert">
	<span class="wcag-fail"></span>Die Kriterien werden nicht erfüllt.<br>
	<div>
		<h4>Begründung:</h4>
		{{ rrze_tos_no_reason }}
	</div>
</div>

endif }}

<h3>Probleme bei der Bedienung der Seite?</h3>
<h4 class="wcag-h3">Für diesen Webauftritt sind folgende Personen verantwortlich:</h4>

<?php echo do_shortcode( '[admins]' ); ?>

<p>
	Wenn Sie Probleme bei der Benutzung dieser Website haben, schreiben Sie eine E-Mail an <a
		href="mailto:{{ rrze_tos_receiver_email }}?subject={{ rrze_tos_subject }}">{{ rrze_tos_receiver_email }}</a>
	oder füllen Sie das Feedback-Formular aus!
	<br>
</p>
<h3>Feedback-Form</h3>
<?php
echo do_shortcode( '[tos-contact-form]' );
?>
<p class="complaint">Wenn Sie das Gefühl, dass Sie nicht wird geholfen werden, können Sie den Kontakt
	<a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html" target="_blank">arbitration board.</a>
</p>
