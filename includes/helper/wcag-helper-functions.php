<?php

namespace RRZE\Wcag;

function checkWMP() {
    $host = $_SERVER['SERVER_NAME'];
    $response = wp_remote_get('http://remoter.dev/wcag-test.json');
    $status_code = wp_remote_retrieve_response_code( $response );
    return $status_code;
}

function getJsonWMP() {
    $json = file_get_contents( 'http://remoter.dev/wcag-test.json' );
    $res = json_decode($json, TRUE);
    return $res;
}