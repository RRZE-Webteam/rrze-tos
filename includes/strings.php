<?php
/**
 * WordPress TOS Strings for templates
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

$a11y_email = "<a href=\"mailto:" . $values['rrze_tos_field_webmaster_email']
        . "?subject=" . ( !empty( $values['rrze_tos_field_subject'] ) ? $values['rrze_tos_field_subject'] : 'Feedback zur Barrierefreiheit des Webauftritts' ) . "\">" . $values['rrze_tos_field_webmaster_email'] . "</a>";

$template = [
    'a11y' => [
	'title'        => __('Accessibility Statement', 'rrze-tos'),
        'intro'         => sprintf(__('Public authorities are required by the %1sEU Directive%2s on Accessibility to Sites and Mobile Applications of Public Agencies to implement their websites in accordance with WCAG criteria. This website has been reviewed in accordance with WCAG conformity criteria.', 'rrze-tos' ),'<a href="http://eur-lex.europa.eu/legal-content/DE/TXT/HTML/?uri=CELEX:32016L2102&rid=1">', '</a>'),
	'conformity'    => __('Are the conformity criteria currently fulfilled?', 'rrze-tos'),
	'fulfilled'      => __('The criteria are fulfilled.', 'rrze-tos'),
	'not_fulfilled'   => __('The criteria are not fulfilled.', 'rrze-tos'),
	'reason'        => __('Reason', 'rrze-tos'),
	'problems'      => __('Problems with the operation of the site?', 'rrze-tos'),
	'responsible'   => __('The following people are responsible for this website:', 'rrze-tos'),
	'email'         => sprintf(__('If you have any problems using this website, please write an email to %1s or fill out the feedback form!', 'rrze-tos'), $a11y_email),
	'feedback'      => __('Feedback-Form', 'rrze-tos'),
	'contact'       => __('If you feel that you are not being helped, you can contact the ', 'rrze-tos')
    ],
    'imprint' => [],
    'privacy' => []
];