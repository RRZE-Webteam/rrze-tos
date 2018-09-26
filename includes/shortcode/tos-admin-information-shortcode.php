<?php
/**
 * WordPress TOS shortcode
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	add_shortcode( 'admins', 'RRZE\Tos\show_admins' );
	/**
	 * Admin function.
	 *
	 * @param array $atts Attributes.
	 */
	function show_admins( $atts ) {
		return get_info();
	}

	/**
	 * Show tos information.
	 */
	function get_info() {
		$values = (array) get_option( 'rrze_tos' );

		if ( ! empty( $values ) ) {
			$store['verantwortlich']['name']    = isset( $values['rrze_tos_responsible_name'] ) ? $values['rrze_tos_responsible_name'] : '';
			$store['verantwortlich']['strasse'] = isset( $values['rrze_tos_responsible_street'] ) ? $values['rrze_tos_responsible_street'] : '';
			$store['verantwortlich']['ort']     = isset( $values['rrze_tos_responsible_city'] ) ? $values['rrze_tos_responsible_city'] : '';
			$store['verantwortlich']['telefon'] = isset( $values['rrze_tos_responsible_phone'] ) ? $values['rrze_tos_responsible_phone'] : '';
			$store['verantwortlich']['email']   = isset( $values['rrze_tos_responsible_email'] ) ? $values['rrze_tos_responsible_email'] : '';
			$store['webmaster']['name']         = isset( $values['rrze_tos_webmaster_name'] ) ? $values['rrze_tos_webmaster_name'] : '';
			$store['webmaster']['strasse']      = isset( $values['rrze_tos_webmaster_street'] ) ? $values['rrze_tos_webmaster_street'] : '';
			$store['webmaster']['ort']          = isset( $values['rrze_tos_webmaster_city'] ) ? $values['rrze_tos_webmaster_city'] : '';
			$store['webmaster']['telefon']      = isset( $values['rrze_tos_webmaster_phone'] ) ? $values['rrze_tos_webmaster_phone'] : '';
			$store['webmaster']['email']        = isset( $values['rrze_tos_webmaster_email'] ) ? $values['rrze_tos_webmaster_email'] : '';

			if ( ! empty( $store ) ) {
				foreach ( $store as $key => $value ) {
					$role = ucfirst( $key );
					if ( 'verantwortlich' === $key ) {
						$role .= 'e/er';
						$role = __( 'Responsible', 'rrze-tos' );
					}
					$heading[] = $role;
				}
			}

			$html  = '<div class="table-wrapper">';
			$html .= '<div class="scrollable">';
			$html .= '<table width="" border="1">';
			$html .= '<tbody><tr>';
			if ( ! empty( $heading ) ) {
				$html .= '<th>' . $heading[0] . '</th><th>' . $heading[1] . '</th></tr><tr><td>';
			}
			if ( ! empty( $store ) ) {
				$html .= $store['verantwortlich']['name'] . '<br/>';
			}
			$html .= ( ! empty( $store['verantwortlich']['strasse'] ) ? $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>' : '' );
			$html .= ( ! empty( $store['verantwortlich']['telefon'] ) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '' );
			if ( ! empty( $store ) ) {
				$html .= '<strong>E-Mail:</strong> ' . $store['verantwortlich']['email'] . '</br>';
			}
			$html .= ( ! empty( $store['verantwortlich']['homepage'] ) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '' );
			$html .= '</td><td>';
			if ( ! empty( $store ) ) {
				$html .= $store['webmaster']['name'] . '<br/>';
			}
			$html .= ( ! empty( $store['webmaster']['strasse'] ) ? $store['webmaster']['strasse'] . '<br/>' . $store['webmaster']['ort'] . '<br/>' : '' );
			$html .= ( ! empty( $store['webmaster']['telefon'] ) ? '<strong>Telefon:</strong> ' . $store['webmaster']['telefon'] . '<br/>' : '' );
			if ( ! empty( $store ) ) {
				$html .= '<strong>E-Mail:</strong> ' . $store['webmaster']['email'] . '</br>';
			}
			$html .= ( ! empty( $store['webmaster']['homepage'] ) ? '<strong>Website:</strong> ' . $store['webmaster']['homepage'] . '<br/>' : '' );
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
			$html .= '</table></div></div>';
			echo $html;
		}
	}
}