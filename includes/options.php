<?php
/**
 * DOC
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

defined( 'ABSPATH' ) || exit;

/**
 * Class Options
 *
 * @package RRZE\Wcag
 */
class Options {
	/**
	 * Option name variable
	 *
	 * @var string
	 */
	protected $option_name = 'rrze_wcag';

	/**
	 * Constructor function.
	 */
	public function __construct() {
		// delete_option('rrze_wcag'); !
	}

	/**
	 * Standard Einstellungen werden definiert
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

		$options = array(
			'rrze_wcag_field_1'  => __( 'Accessibility statement', 'rrze-wcag' ),
			'rrze_wcag_field_2'  => '2',
			'rrze_wcag_field_3'  => '',
			// Verantwortlicher!
			'rrze_wcag_field_4'  => ( isset( $res['metadata']['verantwortlich']['vorname'] ) ? $res['metadata']['verantwortlich']['vorname'] : '' ),
			'rrze_wcag_field_5'  => ( isset( $res['metadata']['verantwortlich']['nachname'] ) ? $res['metadata']['verantwortlich']['nachname'] : '' ),
			'rrze_wcag_field_6'  => '',
			'rrze_wcag_field_7'  => '',
			'rrze_wcag_field_8'  => '',
			'rrze_wcag_field_9'  => ( isset( $res['metadata']['verantwortlich']['email'] ) ? $res['metadata']['verantwortlich']['email'] : '' ),
			'rrze_wcag_field_10' => '',
			// Webmaster!
			'rrze_wcag_field_11' => ( isset( $res['metadata']['webmaster']['vorname'] ) ? $res['metadata']['webmaster']['vorname'] : '' ),
			'rrze_wcag_field_12' => ( isset( $res['metadata']['webmaster']['nachname'] ) ? $res['metadata']['webmaster']['nachname'] : '' ),
			'rrze_wcag_field_13' => '',
			'rrze_wcag_field_14' => '',
			'rrze_wcag_field_15' => '',
			'rrze_wcag_field_16' => ( isset( $res['metadata']['webmaster']['email'] ) ? $res['metadata']['webmaster']['email'] : '' ),
			'rrze_wcag_field_17' => '',
			'rrze_wcag_field_18' => '',
			'rrze_wcag_field_19' => 'Feedback-Formular Barrierefreiheit',
			'rrze_wcag_field_20' => '',
			// Hier können weitere Felder ('key' => 'value') angelegt werden.
		);

		return $options;
	}

	/**
	 * Gibt die Einstellungen zurück.
	 *
	 * @return object
	 */
	public function get_options() {
		$defaults = self::default_options();

		$options = (array) get_option( $this->option_name );
		$options = wp_parse_args( $options, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return (object) $options;
	}

	/**
	 * Function
	 *
	 * @return string
	 */
	public function get_option_name() {
		return $this->option_name;
	}

}
