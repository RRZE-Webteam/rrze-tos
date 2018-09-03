<?php

namespace RRZE\Tos {

	/**
	 * Check connection with remote server.
	 *
	 * @return int|string
	 */
	function check_wmp() {
		$host        = $_SERVER['SERVER_NAME'];
		$host        = 'www.map.tf.fau.de';
		$response    = wp_remote_get( esc_url_raw( "https://www.wmp.rrze.fau.de/suche/impressum/$host/format/json" ) );
		$status_code = wp_remote_retrieve_response_code( $response );

		return $status_code;
	}

	/**
	 * Get the json object from remote server and return an array if it is not null.
	 *
	 * @return array|string
	 */
	function get_json_wmp() {
		$host     = $_SERVER['SERVER_NAME'];
		$host     = 'www.map.tf.fau.de';
		$response = wp_remote_get( esc_url_raw( "https://www.wmp.rrze.fau.de/suche/impressum/$host/format/json" ) );
		$json     = wp_remote_retrieve_body( $response );
		$res      = json_decode( $json, true );

		return ( ! empty( $res ) ) ? $res : '';
	}
}