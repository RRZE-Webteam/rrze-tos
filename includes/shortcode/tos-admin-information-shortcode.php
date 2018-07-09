<?php
/**
 * WordPress TOS shortcode
 *
 * @package WordPress
 * @subpackage TOS
 * @since 3.4.0
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

		global $post;

		$host = $_SERVER['SERVER_NAME'];
		$wmp  = 'https://www.wmp.rrze.fau.de/api/domain/metadata/www.' . $host;

		$status_code = check_wmp();

		if ( 200 === $status_code ) {
			$json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
			$res  = json_decode( $json, true );

			$values = get_option( 'rrze_tos' );

			if ( ! empty( $values ) ) {
				if ( $values ) {
					foreach ( $values as $key => $value ) {
						$store['verantwortlich']['strasse'] = $values['rrze_tos_field_responsible_street'];
						$store['verantwortlich']['ort']     = $values['rrze_tos_field_responsible_city'];
						$store['verantwortlich']['telefon'] = $values['rrze_tos_field_responsible_phone'];
						//$store['verantwortlich']['email']     =  $values['rrze_tos_field_responsible_email'];!
						//$store['verantwortlich']['personid']  =  $values['rrze_tos_field_responsible_ID'];!
						$store['webmaster']['strasse'] = $values['rrze_tos_field_webmaster_street'];
						$store['webmaster']['ort']     = $values['rrze_tos_field_webmaster_city'];
						$store['webmaster']['telefon'] = $values['rrze_tos_field_webmaster_phone'];
						//$store['webmaster']['email']     =  $values['rrze_tos_field_webmaster_email'];!
						//$store['webmaster']['personid']  =  $values['rrze_tos_field_webmaster_ID'];!
					}

					if ( ! empty( $store ) ) {
						foreach ( $store as $key => $value ) {
							$role = ucfirst( $key );
							if ( 'verantwortlich' === $key ) {
								$role .= 'e/er';
								$role  = __( 'Responsible', 'rrze-tos' );
							}
							$heading[] = $role;
						}
					}
				}
			}

			$html  = '<div class="table-wrapper">';
			$html .= '<div class="scrollable">';
			$html .= '<table width="" border="1">';
			$html .= '<tbody><tr>';
			$html .= '<th>' . ( isset( $heading[0] ) ? $heading[0] : 'Verantwortliche/er' ) . '</th><th>' . ( isset( $heading[1] ) ? $heading[1] : 'Webmaster' ) . '</th></tr><tr><td>';
			$html .= $res['metadata']['verantwortlich']['vorname'] . ' ' . $res['metadata']['verantwortlich']['nachname'] . '<br/>';
			$html .= ( ! empty( $store['verantwortlich']['strasse'] ) && ! empty( $store['verantwortlich']['ort'] ) ? $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>' : '' );
			$html .= ( ! empty( $store['verantwortlich']['telefon'] ) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '' );
			$html .= '<strong>E-Mail:</strong> ' . $res['metadata']['verantwortlich']['email'] . '</br>';
			$html .= ( ! empty( $store['verantwortlich']['homepage'] ) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '' );
			$html .= '</td><td>';
			$html .= $res['metadata']['webmaster']['vorname'] . ' ' . $res['metadata']['webmaster']['nachname'] . '<br/>';
			$html .= ( ! empty( $store['webmaster']['strasse'] ) && ! empty( $store['webmaster']['ort'] ) ? $store['webmaster']['strasse'] . '<br/>' . $store['webmaster']['ort'] . '<br/>' : '' );
			$html .= ( ! empty( $store['webmaster']['telefon'] ) ? '<strong>Telefon:</strong> ' . $store['webmaster']['telefon'] . '<br/>' : '' );
			$html .= '<strong>E-Mail:</strong> ' . $res['metadata']['webmaster']['email'] . '</br>';
			$html .= ( ! empty( $store['webmaster']['homepage'] ) ? '<strong>Website:</strong> ' . $store['webmaster']['homepage'] . '<br/>' : '' );
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
			$html .= '</table></div></div>';
			echo  $html ;

		} else {

			$values = get_option( 'rrze_tos' );

			if ( ! empty( $values ) ) {
				foreach ( $values as $key => $value ) {
					$store['verantwortlich']['vorname']  = $values['rrze_tos_field_responsible_firstname'];
					$store['verantwortlich']['nachname'] = $values['rrze_tos_field_responsible_lastname'];
					$store['verantwortlich']['strasse']  = $values['rrze_tos_field_responsible_street'];
					$store['verantwortlich']['ort']      = $values['rrze_tos_field_responsible_city'];
					$store['verantwortlich']['telefon']  = $values['rrze_tos_field_responsible_phone'];
					$store['verantwortlich']['email']    = $values['rrze_tos_field_responsible_email'];
					//$store['verantwortlich']['personid']  =  $values['rrze_tos_field_responsible_ID'];!
					$store['webmaster']['vorname']  = $values['rrze_tos_field_webmaster_firstname'];
					$store['webmaster']['nachname'] = $values['rrze_tos_field_webmaster_lastname'];
					$store['webmaster']['strasse']  = $values['rrze_tos_field_webmaster_street'];
					$store['webmaster']['ort']      = $values['rrze_tos_field_webmaster_city'];
					$store['webmaster']['telefon']  = $values['rrze_tos_field_webmaster_phone'];
					$store['webmaster']['email']    = $values['rrze_tos_field_webmaster_email'];
					//$store['webmaster']['personid']  =  $values['rrze_tos_field_webmaster_ID'];!
				}
			}

			if ( ! empty( $store ) ) {
				foreach ( $store as $key => $value ) {
					$role = ucfirst( $key );
					if ( 'verantwortlich' === $key ) {
						$role .= 'e/er';
						$role  = __( 'Responsible', 'rrze-tos' );
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
				$html .= $store['verantwortlich']['vorname'] . ' ' . $store['verantwortlich']['nachname'] . '<br/>';
			}
			$html .= ( ! empty( $store['verantwortlich']['strasse'] ) ? $store['verantwortlich']['strasse'] . '<br/>' . $store['verantwortlich']['ort'] . '<br/>' : '' );
			$html .= ( ! empty( $store['verantwortlich']['telefon'] ) ? '<strong>Telefon:</strong> ' . $store['verantwortlich']['telefon'] . '<br/>' : '' );
			if ( ! empty( $store ) ) {
				$html .= '<strong>E-Mail:</strong> ' . $store['verantwortlich']['email'] . '</br>';
			}
			$html .= ( ! empty( $store['verantwortlich']['homepage'] ) ? '<strong>Website:</strong> ' . $store['verantwortlich']['homepage'] . '<br/>' : '' );
			$html .= '</td><td>';
			if ( ! empty( $store ) ) {
				$html .= $store['webmaster']['vorname'] . ' ' . $store['webmaster']['nachname'] . '<br/>';
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