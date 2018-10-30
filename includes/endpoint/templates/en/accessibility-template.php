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
	Public authorities are required by the
	<a target="_blank" href="https://eur-lex.europa.eu/legal-content/EN/TXT/HTML/?uri=CELEX:32018D1523">EU
		Directive</a> on Accessibility to Sites and Mobile Applications of Public
	Agencies to implement their websites in accordance with WCAG criteria. This website has been reviewed in accordance
	with WCAG conformity criteria.<br>
</p>

<h3>Are the conformity criteria currently fulfilled?</h3>
{{ if rrze_tos_conformity
<div class="alert alert-success" role="alert">
	<span class="wcag-pass"></span>The criteria are fulfilled.
</div>

elseif

<div class="alert alert-info" role="alert">
	<span class="wcag-fail"></span>The criteria are not fulfilled.<br>
	<div>
		<h4>Justification:</h4>
		{{ rrze_tos_no_reason }}
	</div>
</div>

endif }}

<h3>Problems with the operation of the site?</h3>
<h4 class="wcag-h3">The following people are responsible for this website:</h4>
<?php echo do_shortcode( '[admins]' ); ?>

<p>
	If you have any problems using this website, please write an email to
	<a href="mailto:{{ rrze_tos_receiver_email }}?subject={{ rrze_tos_subject }}">{{ rrze_tos_receiver_email }}</a>
	or fill out the feedback form!
	<br>
</p>

<?php
echo do_shortcode( '[tos-contact-form]' );
?>

<p class="complaint">If you feel that you are not being helped, you can contact the
	<a href="https://www.behindertenbeauftragte.de/DE/SchlichtungsstelleBGG/SchlichtungsstelleBGG_node.html" target="_blank">arbitration board.</a>
</p>
