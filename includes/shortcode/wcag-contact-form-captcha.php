<?php
/**
 * DOC
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

/**
 * Get string value.
 *
 * @return string
 */
function get_salt() {
	return 'Nj,izn_}*)JWzJ-d029=|R&4+VBA. d>:Soy2u+,R+,Tm-}htt/PoL7COrJ|hIX;';
}

/**
 * Generate random number.
 *
 * @return array
 */
function random_numbers() {

	$min_number = 1;
	$max_number = 9;

	$random_number1 = mt_rand( $min_number, $max_number );
	$random_number2 = mt_rand( $min_number, $max_number );

	return array(
		'number1' => $random_number1,
		'number2' => $random_number2,
	);
}

/**
 * Get a random number and return and string
 *
 * @param array $random Mandatory.
 *
 * @return false|int|string
 */
function numbers( array $random ) {

	$arr = array(
		__( 'one', 'rrze-wcag' )   => 1,
		__( 'two', 'rrze-wcag' )   => 2,
		__( 'three', 'rrze-wcag' ) => 3,
		__( 'four', 'rrze-wcag' )  => 4,
		__( 'five', 'rrze-wcag' )  => 5,
		__( 'six', 'rrze-wcag' )   => 6,
		__( 'seven', 'rrze-wcag' ) => 7,
		__( 'eight', 'rrze-wcag' ) => 8,
		__( 'nine', 'rrze-wcag' )  => 9,
	);

	$number2 = $random['number2'];

	$literal = array_search( $number2, $arr, true );

	return $literal;
}

/**
 * Get operator function.
 *
 * @return mixed
 */
function get_operator() {

	$operator = array(
		'+' => 'plus',
		'*' => __( 'times', 'rrze-wcag' ),
	);

	$rand_operator = array_rand( $operator, 1 );

	$single_operator = $operator[ $rand_operator[0] ];

	return $single_operator;

}

/**
 * Calculate function.
 *
 * @param array  $random Used to produce a random output.
 * @param string $operator Perform operation based on operator.
 *
 * @return float|int|mixed
 */
function calculate( array $random, $operator ) {

	switch ( $operator ) {
		case 'plus':
			$output = $random['number1'] + $random['number2'];
			break;
		case __( 'times', 'rrze-wcag' ):
			$output = $random['number1'] * $random['number2'];
			break;
	}

	return $output;

}

/**
 * Used to get captcha.
 *
 * @param array  $random Random number.
 * @param string $lit Extra string.
 * @param string $operator An operator + or *.
 *
 * @return string
 */
function get_captcha_string( array $random, $lit, $operator ) {

	$string = $random['number1'] . ' ' . $operator . ' ' . $lit;

	return $string;

}

/**
 * Used to get captcha.
 *
 * @return array
 */
function get_captcha() {

	$random      = random_numbers();
	$lit         = numbers( $random );
	$operator    = get_operator();
	$task_string = get_captcha_string( $random, $lit, $operator );
	$solution    = calculate( $random, $operator );

	return array(
		'task_string'    => $task_string,
		'task_solution'  => $solution,
		'task_encrypted' => md5( $solution ),
	);
}
