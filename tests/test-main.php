<?php
/**
 * Class Test_Main
 *
 * @package Rrze_Tos
 */

/**
 * Main test case.
 */
class Test_Main extends WP_UnitTestCase {

	/**
	 * Settings construct test.
	 */
	public function test_construct() {
		new RRZE\Tos\Main();
		$has_admin_menu = has_action( 'admin_menu' );
		$has_admin_init = has_action( 'admin_init' );
		$result         = $has_admin_menu && $has_admin_init;
		$this->assertTrue( $result );
	}
}