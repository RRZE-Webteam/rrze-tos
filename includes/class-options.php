<?php
/**
 * WordPress TOS Class
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	defined( 'ABSPATH' ) || exit;

	/**
	 * Class Options
	 *
	 * @package RRZE\Tos
	 */
	class Options {
		/**
		 * Option name variable.
		 *
		 * @var string
		 */
		protected $option_name = 'rrze_tos';

		/**
		 * Constructor function.
		 */
		public function __construct() {
		}

		/**
		 * Standard Einstellungen werden definiert.
		 *
		 * @return array
		 */
		public function default_options() {

			$status_code = check_wmp();

			if ( 200 === $status_code ) {
				$res = get_json_wmp();
			} else {
				$res = '';
			}
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				$admin_email = get_site_option( 'admin_email' );
			} else {
				$admin_email = get_option( 'admin_email' );
			}
			$options =
				[
					'rrze_tos_url'                    => preg_replace( '#^https?://#', '', get_option( 'siteurl' ) ),
					'rrze_tos_title'                  => __( 'Accessibility Statement', 'rrze-tos' ),
					'rrze_tos_conformity'             => '2',
					'rrze_tos_no_reason'              => '',
					// Verantwortlicher!
					'rrze_tos_responsible_name'       => ( isset( $res['verantwortlich']['name'] ) ? $res['verantwortlich']['name'] : '' ),
					'rrze_tos_responsible_street'     => ( isset( $res['verantwortlich']['street'] ) ? $res['verantwortlich']['street'] : '' ),
					'rrze_tos_responsible_postalcode' => ( isset( $res['verantwortlich']['postalcode'] ) ? $res['verantwortlich']['postalcode'] : '' ),
					'rrze_tos_responsible_city'       => ( isset( $res['verantwortlich']['city'] ) ? $res['verantwortlich']['city'] : '' ),
					'rrze_tos_responsible_org'        => ( isset( $res['verantwortlich']['org'] ) ? $res['verantwortlich']['org'] : '' ),
					'rrze_tos_responsible_email'      => ( isset( $res['verantwortlich']['email'] ) ? $res['verantwortlich']['email'] : $admin_email ),
					'rrze_tos_responsible_phone'      => '',
					'rrze_tos_responsible_ID'         => '',
					// email data!
					'rrze_tos_receiver_email'         => ( isset( $res['webmaster']['email'] ) ? $res['webmaster']['email'] : $admin_email ),
					'rrze_tos_subject'                => 'Feedback-Formular Barrierefreiheit',
					'rrze_tos_cc_email'               => '',
					// Editor.
//					'rrze_tos_editor_name'            => ( isset( $res['verantwortlich']['name'] ) ? $res['verantwortlich']['name'] : '' ),
//					'rrze_tos_editor_street'          => ( isset( $res['verantwortlich']['street'] ) ? $res['verantwortlich']['street'] : '' ),
//					'rrze_tos_editor_postalcode'      => ( isset( $res['verantwortlich']['postalcode'] ) ? $res['verantwortlich']['postalcode'] : '' ),
//					'rrze_tos_editor_city'            => ( isset( $res['verantwortlich']['city'] ) ? $res['verantwortlich']['city'] : '' ),
//					'rrze_tos_editor_org'             => ( isset( $res['verantwortlich']['org'] ) ? $res['verantwortlich']['org'] : '' ),
					// Content & Webmaster!
					'rrze_tos_webmaster_name'         => ( isset( $res['webmaster']['name'] ) ? $res['webmaster']['name'] : '' ),
					'rrze_tos_webmaster_street'       => ( isset( $res['webmaster']['street'] ) ? $res['webmaster']['street'] : '' ),
					'rrze_tos_webmaster_postalcode'   => ( isset( $res['webmaster']['postalcode'] ) ? $res['webmaster']['postalcode'] : '' ),
					'rrze_tos_webmaster_city'         => ( isset( $res['webmaster']['city'] ) ? $res['webmaster']['city'] : '' ),
					'rrze_tos_webmaster_org'          => ( isset( $res['webmaster']['org'] ) ? $res['webmaster']['org'] : '' ),
					'rrze_tos_webmaster_email'        => ( isset( $res['webmaster']['email'] ) ? $res['webmaster']['email'] : '' ),
					'rrze_tos_webmaster_phone'        => '',
					'rrze_tos_webmaster_fax'          => '',
					'rrze_tos_webmaster_ID'           => '',
					// privacy.
					'rrze_tos_protection_newsletter'  => '2',
					'rrze_tos_protection_new_section' => '',
					// Imprint.
					'rrze_tos_url_list'               => '',
					// Hier können weitere Felder ('key' => 'value') angelegt werden.
				];

			return $options;
		}

		/**
		 * Gibt die Einstellungen zurück.
		 *
		 * @return mixed Options
		 */
		public function get_options() {
			$defaults = self::default_options();

			$options = (array) get_option( $this->option_name );
			$options = wp_parse_args( $options, $defaults );
			$options = array_intersect_key( $options, $defaults );

			return (object) $options;
		}

		/**
		 * Function to get option name.
		 *
		 * @return string
		 */
		public function get_option_name() {
			return $this->option_name;
		}

	}
}
