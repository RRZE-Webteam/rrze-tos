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

		// $slug_exists = the_slug_exists( 'barrierefreiheit' );!
		$tos_menu_items = Settings::options_page_tabs();

		if ( in_array( $current_theme->get( 'Name' ), $themes_fau, true ) ) {
			if ( has_nav_menu( 'meta-footer' ) ) {
				$menu_name  = 'meta-footer';
				$menu_id    = wp_get_nav_menu_object( get_nav_menu_locations()[ $menu_name ] )->term_id;
				$menu_items = wp_get_nav_menu_items( $menu_id );
				if ( ! $menu_items ) {
					foreach ( $tos_menu_items as $tos_menu_item => $value ) {
						wp_update_nav_menu_item( $menu_id, 0,
							[
								'menu-item-title'   => ucfirst($value),
								'menu-item-classes' => 'tos',
								'menu-item-url'     => home_url( '/' . strtolower( $value ) ),
								'menu-item-status'  => 'publish',
							]
						);
					}
				} else {
					$title_exist = false;
					foreach ( $tos_menu_items as $tos_menu_item => $value ) {
						foreach ( $menu_items as $item ) {
							$title = $item->title;
							if ( ucfirst( $value ) === $title ) {
								$title_exist = true;
							}
						}
						if ( ! $title_exist ) {
							wp_update_nav_menu_item( $menu_id, 0,
								[
									'menu-item-title'   => ucfirst($value),
									'menu-item-classes' => 'tos',
									'menu-item-url'     => home_url( '/' . strtolower( $value ) ),
									'menu-item-status'  => 'publish',
								]
							);
						}
						$title_exist = false;
					}
				}
			} else {
				add_action( 'wp_dashboard_setup',
					'RRZE\Tos\tos_add_dashboard_menu_widgets' );
			}
		}
	}

	add_action( 'init', 'RRZE\Tos\add_page_to_footer_menu' );
}
