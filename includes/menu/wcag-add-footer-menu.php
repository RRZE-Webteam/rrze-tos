<?php
/**
 * DOC
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

/**
 * Widget function
 *
 * @param string $post   Post parameter.
 * @param string $callback_args Callback arguments.
 */
function wcag_dashboard_widget_function( $post, $callback_args ) {
	echo 'Das <strong>WCAG Plugin</strong> kann keinen Menüeintrag in der <strong>Navigation unten</strong> anlegen. Wahrscheinlich haben Sie bereits einen Menüeintrag mit dem Namen <strong>Barrierefreiheit</strong> angelegt. Ändern Sie bitte den Namen Ihrer Seite.';
}

/**
 * Add widget to dashboard
 */
function wcag_add_dashboard_widgets() {
	wp_add_dashboard_widget( 'wcag_dashboard_widget', '<span class="dashicons dashicons-awards"></span>Achtung!', 'RRZE\Wcag\wcag_dashboard_widget_function' );
}

/**
 * Widget menu function
 *
 * @param string $post Post parameter.
 * @param string $callback_args Callback.
 */
function wcag_dashboard_widget_menu_function( $post, $callback_args ) {
	echo 'Das <strong>WCAG Plugin</strong> kann keinen Menüeintrag in der Navigation unten anlegen. Bitte legen Sie ein Footer-Menü an!';
}

/**
 * Add widget menu to dashboard
 */
function wcag_add_dashboard_menu_widgets() {
	wp_add_dashboard_widget( 'wcag_dashboard_widget', '<span class="dashicons dashicons-awards"></span>Achtung!', 'RRZE\Wcag\wcag_dashboard_widget_menu_function' );
}

/**
 * Remove widget from dashboard
 */
function example_remove_dashboard_widget() {
	remove_meta_box( 'wcag_dashboard_widget', 'dashboard', 'normal' );
}

// add_action('wp_dashboard_setup', 'RRZE\Wcag\wcag_add_dashboard_widgets' );!
/**
 * Check if name exit
 *
 * @param string $page_slug Name of the page.
 *
 * @return bool
 */
function the_slug_exists( $page_slug ) {

	$page = get_page_by_path( $page_slug, OBJECT );

	if ( isset( $page ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Add created page to footer menu
 */
function add_page_to_footer_menu() {

	$current_theme     = wp_get_theme();
	$themes_fau        = array(
		__( 'FAU-Institutions', 'rrze-wcag' ),
		'FAU-Natfak',
		'FAU-Philfak',
		'FAU-RWFak',
		'FAU-Techfak',
		'FAU-Medfak',
	);
	$menu_entry_option = 'wcag_menu';

	if ( the_slug_exists( 'barrierefreiheit' ) ) {
		// error_log(print_r('ja', true));!
		$slug_exists = true;
	} else {
		// error_log(print_r('nein', true));!
		$slug_exists = false;
	}

	if ( in_array( $current_theme, $themes_fau, true ) ) {
		if ( has_nav_menu( 'meta-footer' ) ) {
			$menu_name  = 'meta-footer';
			$menu_id    = wp_get_nav_menu_object( get_nav_menu_locations( $menu_name )[ $menu_name ] )->term_id;
			$menu_items = wp_get_nav_menu_items( $menu_id );
			// error_log(print_r($menu_items, true));!
			foreach ( $menu_items as $menu_item ) {
				$title = $menu_item->title;
				if ( 'Barrierefreiheit' === $title || 'Accessibility' === $title ) {
					if ( 'custom' !== $menu_item->object ) {
						add_action( 'wp_dashboard_setup', 'RRZE\Wcag\wcag_add_dashboard_widgets' );
					}
					$used = true;
				} else {
					$used = false;
				}
			}

			if ( ! $used ) {
				wp_update_nav_menu_item(
					$menu_id, 0, array(
						'menu-item-title'   => __( 'Accessibility', 'rrze-wcag' ),
						'menu-item-classes' => 'wcag',
						'menu-item-url'     => home_url( __( '/accessibility', 'rrze-wcag' ) ),
						'menu-item-status'  => 'publish',
					)
				);
			}
		} else {
			add_action( 'wp_dashboard_setup', 'RRZE\Wcag\wcag_add_dashboard_menu_widgets' );
		}
	}
}

add_action( 'init', 'RRZE\Wcag\add_page_to_footer_menu' );
