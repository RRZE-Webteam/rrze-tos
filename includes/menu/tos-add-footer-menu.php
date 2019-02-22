<?php
/**
 * WordPress TOS Footer menu.
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	/**
	 *
	 * @param $post
	 * @param $callback_args
	 */
	function tos_dashboard_widget_function( $post, $callback_args ) {
		echo 'Das <strong>TOS Plugin</strong> kann keinen Menüeintrag in der <strong>Navigation unten</strong> anlegen. Wahrscheinlich haben Sie bereits einen Menüeintrag mit dem Namen <strong>Barrierefreiheit</strong> angelegt. Ändern Sie bitte den Namen Ihrer Seite.';
	}

	/**
	 *
	 */
	function tos_add_dashboard_widgets() {
		wp_add_dashboard_widget( 'tos_dashboard_widget',
			'<span class="dashicons dashicons-awards"></span>Achtung!',
			'RRZE\Tos\tos_dashboard_widget_function' );
	}

	/**
	 *
	 * @param $post
	 * @param $callback_args
	 */
	function tos_dashboard_widget_menu_function( $post, $callback_args ) {
		echo 'Das <strong>TOS Plugin</strong> kann keinen Menüeintrag in der Navigation unten anlegen. Bitte legen Sie ein Footer-Menü an!';
	}

	/**
	 *
	 */
	function tos_add_dashboard_menu_widgets() {
		wp_add_dashboard_widget( 'tos_dashboard_widget',
			'<span class="dashicons dashicons-awards"></span>Achtung!',
			'RRZE\Tos\tos_dashboard_widget_menu_function' );
	}

	/**
	 *
	 */
	function example_remove_dashboard_widget() {
		remove_meta_box( 'tos_dashboard_widget', 'dashboard', 'normal' );
	}

//add_action('wp_dashboard_setup', 'RRZE\Tos\tos_add_dashboard_widgets' );

	/**
	 *
	 * @param $page_slug
	 *
	 * @return bool
	 */
	function the_slug_exists( $page_slug ) {

		$page = get_page_by_path( $page_slug, OBJECT );

		return isset( $page );
	}

	/**
	 * Check if footer items exist and create links.
	 *
	 * Workflow
	 * 1. Create TOS basic menu (3 tabs) for FAU themes.
	 * 3. Activate new menu.
	 */
	function add_page_to_footer_menu() {

		$current_theme = wp_get_theme();
		$themes_fau    = [
			__( 'FAU-Einrichtungen', 'rrze-tos' ),
			'FAU-Natfak',
			'FAU-Philfak',
			'FAU-RWFak',
			'FAU-Techfak',
			'FAU-Medfak',
		];

		$tos_menu_items = Settings::options_pages();
		$tos_menu_name  = 'rrze-tos-menu-footer';
		$menu_location  = 'meta-footer';
		//
		// Create TOS menu for FAU themes.
		//
		if ( in_array( $current_theme->get( 'Name' ), $themes_fau, true ) ) {
			//
			// - Create TOS basic menu (3 tabs) for FAU themes if not exit.
			// - Activate menu.
			//
			tos_create_nav_menu( $tos_menu_name, $tos_menu_items, $menu_location, true );

		} else {
			//
			// - Create TOS basic menu (3 tabs) for general themes if not exit.
			// - No activate menu.
			//
			tos_create_nav_menu( $tos_menu_name, $tos_menu_items, $menu_location );
		}
	}


	/**
	 * Create nav menu, add items and activate it.
	 *
	 * @param      $tos_menu_name
	 * @param      $tos_menu_items
	 * @param      $menu_location
	 *
	 * @param bool $activate
	 *
	 * @return int|\WP_Error
	 */
	function tos_create_nav_menu( $tos_menu_name, $tos_menu_items, $menu_location, $activate = false ) {
		$menu_id = null;
		if ( ! is_nav_menu( $tos_menu_name ) ) {
			$menu_id = wp_create_nav_menu( $tos_menu_name );
			$menu    = get_term_by( 'name', $tos_menu_name, 'nav_menu' );

			foreach ( $tos_menu_items as $tos_menu_item => $value ) {
				wp_update_nav_menu_item( $menu->term_id, 0,
					[
						'menu-item-title'   => ucfirst( $value ),
						'menu-item-classes' => 'tos',
						'menu-item-url'     => home_url( '/' . strtolower( $value ) ),
						'menu-item-status'  => 'publish',
					]
				);
			}

			if ( true === $activate ) {
				$locations                   = get_theme_mod( 'nav_menu_locations' );
				$locations[ $menu_location ] = $menu->term_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}

		return $menu_id;
	}

	add_action( 'init', 'RRZE\Tos\add_page_to_footer_menu' );
}
