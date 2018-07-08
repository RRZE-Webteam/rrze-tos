<?php

namespace RRZE\Tos {

	function tos_dashboard_widget_function( $post, $callback_args ) {
		echo "Das <strong>WCAG Plugin</strong> kann keinen Menüeintrag in der <strong>Navigation unten</strong> anlegen. Wahrscheinlich haben Sie bereits einen Menüeintrag mit dem Namen <strong>Barrierefreiheit</strong> angelegt. Ändern Sie bitte den Namen Ihrer Seite.";
	}

	function tos_add_dashboard_widgets() {
		wp_add_dashboard_widget('tos_dashboard_widget', '<span class="dashicons dashicons-awards"></span>Achtung!', 'RRZE\Tos\tos_dashboard_widget_function');
	}

	function tos_dashboard_widget_menu_function( $post, $callback_args ) {
		echo "Das <strong>WCAG Plugin</strong> kann keinen Menüeintrag in der Navigation unten anlegen. Bitte legen Sie ein Footer-Menü an!";
	}

	function tos_add_dashboard_menu_widgets() {
		wp_add_dashboard_widget('tos_dashboard_widget', '<span class="dashicons dashicons-awards"></span>Achtung!', 'RRZE\Tos\tos_dashboard_widget_menu_function');
	}

	function example_remove_dashboard_widget() {
		remove_meta_box( 'tos_dashboard_widget', 'dashboard', 'normal' );
	}

#add_action('wp_dashboard_setup', 'RRZE\Tos\tos_add_dashboard_widgets' );

	function the_slug_exists($page_slug) {

		$page = get_page_by_path( $page_slug , OBJECT );

		if ( isset($page) )
			return true;
		else
			return false;
	}

	function add_page_to_footer_menu() {

		$current_theme = wp_get_theme();
		$themes_fau = array(__('FAU-Institutions','rrze-tos'), 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
		$menu_entry_option = 'tos_menu';

		if (the_slug_exists('barrierefreiheit')) {
			#error_log(print_r('ja', true));
			$slug_exists = true;
		} else {
			#error_log(print_r('nein', true));
			$slug_exists = false;
		}

		if(in_array($current_theme, $themes_fau)) {
			if(has_nav_menu('meta-footer')) {
				$menu_name = 'meta-footer';
				$menu_id = wp_get_nav_menu_object( get_nav_menu_locations( $menu_name )[ $menu_name ] )->term_id;
				$menu_items = wp_get_nav_menu_items($menu_id);
				#error_log(print_r($menu_items, true));
				foreach($menu_items as $menu_item) {
					$title = $menu_item->title;
					if($title == 'Barrierefreiheit' || $title == 'Accessibility') {
						if($menu_item->object != 'custom') {
							add_action('wp_dashboard_setup', 'RRZE\Tos\tos_add_dashboard_widgets' );
						}
						$used = true;
					} else {
						$used = false;
					}
				}

				if(!$used) {
					wp_update_nav_menu_item($menu_id, 0, array(
							'menu-item-title' =>  __('Accessibility','rrze-tos'),
							'menu-item-classes' => 'tos',
							'menu-item-url' => home_url(__('/accessibility','rrze-tos')),
							'menu-item-status' => 'publish')
					);
				}
			} else {
				add_action('wp_dashboard_setup', 'RRZE\Tos\tos_add_dashboard_menu_widgets' );
			}
		}
	}

	add_action('init', 'RRZE\Tos\add_page_to_footer_menu');
}