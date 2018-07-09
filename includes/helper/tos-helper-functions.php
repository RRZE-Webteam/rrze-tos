<?php

namespace RRZE\Tos {

	function check_wmp() {
		$host        = $_SERVER['SERVER_NAME'];
		$response    = wp_remote_get( 'http://remoter.dev/wcag-test.json' );
		$status_code = wp_remote_retrieve_response_code( $response );

		return $status_code;
	}

	function get_json_wmp() {
		$json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
		$res  = json_decode( $json, true );

		return $res;
	}
}