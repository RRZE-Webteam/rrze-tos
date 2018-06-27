<?php
/**
 * DOC
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

/**
 * Check remote.
 *
 * @return int|string
 */
function check_wmp() {
	$host        = esc_url( wp_unslash( $_SERVER['SERVER_NAME'] ) );
	$response    = wp_remote_get( 'http://remoter.dev/wcag-test.json' );
	$status_code = wp_remote_retrieve_response_code( $response );

	return $status_code;
}

/**
 * Retrieve json.
 *
 * @return array|mixed|object
 */
function get_json_wmp() {
	$json = wp_remote_get( 'http://remoter.dev/wcag-test.json' );
	$res  = json_decode( $json, true );

	return $res;
}
