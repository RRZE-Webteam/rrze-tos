<?php
/**
 * WordPress TOS captcha
 *
 * @package WordPress
 * @subpackage TOS
 * @since 3.4.0
 */

namespace RRZE\Tos {

	/**
	 *
	 * @return string
	 */
	function get_salt() {
		return 'Nj,izn_}*)JWzJ-d029=|R&4+VBA. d>:Soy2u+,R+,Tm-}htt/PoL7COrJ|hIX;';
	}

	/**
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
	 *
	 * @param array $random
	 *
	 * @return false|int|string
	 */
	function numbers( array $random ) {

		$arr = array(
			__( 'one', 'rrze-tos' )   => 1,
			__( 'two', 'rrze-tos' )   => 2,
			__( 'three', 'rrze-tos' ) => 3,
			__( 'four', 'rrze-tos' )  => 4,
			__( 'five', 'rrze-tos' )  => 5,
			__( 'six', 'rrze-tos' )   => 6,
			__( 'seven', 'rrze-tos' ) => 7,
			__( 'eight', 'rrze-tos' ) => 8,
			__( 'nine', 'rrze-tos' )  => 9,
		);

		$number2 = $random['number2'];

		$literal = array_search( $number2, $arr );

		return $literal;

	}

	/**
	 *
	 * @return mixed
	 */
	function get_operator() {

		$operator = array(
			'+' => 'plus',
			'*' => __( 'times', 'rrze-tos' ),
		);

		$rand_operator = array_rand( $operator, 1 );

		$single_operator = $operator[ $rand_operator[0] ];

		return $single_operator;

	}

	/**
	 *
	 * @param array $random
	 * @param       $operator
	 *
	 * @return float|int|mixed
	 */
	function calculate( array $random, $operator ) {

		switch ( $operator ) {
			case 'plus':
				$output = $random['number1'] + $random['number2'];
				break;
			case __( 'times', 'rrze-tos' ):
				$output = $random['number1'] * $random['number2'];
				break;
		}

		return $output;

	}

	/**
	 *
	 * @param array $random
	 * @param       $lit
	 * @param       $operator
	 *
	 * @return string
	 */
	function get_captcha_string( array $random, $lit, $operator ) {

		$string = $random['number1'] . ' ' . $operator . ' ' . $lit;

		return $string;

	}

	/**
	 *
	 * @return array
	 */
	function getCaptcha() {

		$random      = random_numbers();
		$lit         = numbers( $random );
		$op          = get_operator();
		$task_string = get_captcha_string( $random, $lit, $op );
		$solution    = calculate( $random, $op );

		return array(
			'task_string'    => $task_string,
			'task_solution'  => $solution,
			'task_encrypted' => md5( $solution )
		);

	}
}