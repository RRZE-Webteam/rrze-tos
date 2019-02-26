<?php
/**
 * Class Test_Helper
 *
 * @package Rrze_Tos
 */

/**
 * Helper test case.
 */
class Test_Helper extends WP_UnitTestCase {

	/**
	 *  Check_wmp test.
	 */
	public function test_check_wmp() {
		$status_code = RRZE\Tos\check_wmp();
		$this->assertTrue( 200 === $status_code );
	}

	/**
	 *  Get_json_wmp test.
	 */
	public function test_get_json_wmp() {
		$host = 'localhost';
		$info = RRZE\Tos\get_json_wmp( $host );
		$this->assertTrue( '' === $info );
	}

	/**
	 *  Get_json_wmp with parameter test.
	 */
	public function test_get_json_wmp_parameter() {
		$host = 'www.map.tf.fau.de';
		$info = RRZE\Tos\get_json_wmp( $host );
		$this->assertTrue( is_array( $info ) );
	}
}
