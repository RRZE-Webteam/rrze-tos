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
			// delete_option('rrze_wcag'); !
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
			$options = array(
				'rrze_tos_field_title'                     => __( 'Accessibility Statement', 'rrze-tos' ),
				'rrze_tos_field_conformity'                     => '2',
				'rrze_tos_field_no_reason'                     => '',
				// Verantwortlicher!
				'rrze_tos_field_responsible_firstname' => ( isset( $res['metadata']['verantwortlich']['vorname'] ) ? $res['metadata']['verantwortlich']['vorname'] : '' ),
				'rrze_tos_field_responsible_lastname'  => ( isset( $res['metadata']['verantwortlich']['nachname'] ) ? $res['metadata']['verantwortlich']['nachname'] : '' ),
				'rrze_tos_field_responsible_street'    => '',
				'rrze_tos_field_responsible_city'      => '',
				'rrze_tos_field_responsible_phone'     => '',
				'rrze_tos_field_responsible_email'     => ( isset( $res['metadata']['verantwortlich']['email'] ) ? $res['metadata']['verantwortlich']['email'] : $admin_email ),
				'rrze_tos_field_responsible_ID'        => '',
				// Webmaster!
				'rrze_tos_field_webmaster_firstname'   => ( isset( $res['metadata']['webmaster']['vorname'] ) ? $res['metadata']['webmaster']['vorname'] : '' ),
				'rrze_tos_field_webmaster_lastname'                    => ( isset( $res['metadata']['webmaster']['nachname'] ) ? $res['metadata']['webmaster']['nachname'] : '' ),
				'rrze_tos_field_webmaster_street'                    => '',
				'rrze_tos_field_webmaster_city'                    => '',
				'rrze_tos_field_webmaster_phone'                    => '',
				'rrze_tos_field_webmaster_email'                    => ( isset( $res['metadata']['webmaster']['email'] ) ? $res['metadata']['webmaster']['email'] : $admin_email ),
				'rrze_tos_field_webmaster_ID'                    => '',
				'rrze_tos_field_receiver_email'                    => '',
				'rrze_tos_field_subject'                    => 'Feedback-Formular Barrierefreiheit',
				'rrze_tos_field_cc'                    => '',
				// Hier können weitere Felder ('key' => 'value') angelegt werden.
			);

			return $options;
		}

		/**
		 * Gibt die Einstellungen zurück.
		 *
		 * @return object Options
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
