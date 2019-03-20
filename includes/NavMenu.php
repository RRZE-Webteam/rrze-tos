<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

 class NavMenu
 {
     /**
      * [protected description]
      * @var string
      */
     protected static $tosFooterMenuName = 'rrze-tos-footer-menu';

     /**
      * [protected description]
      * @var string
      */
     protected static $fauFooterMenuLocation = 'meta-footer';

     /**
      * [addTosFooterMenu description]
      */
     public static function addTosFooterMenu()
     {
         add_action('init', [__CLASS__, 'setTosFooterMenu']);
     }

     /**
      * [setTosFooterMenu description]
      */
     public static function setTosFooterMenu()
     {
         if (! is_nav_menu(self::$tosFooterMenuName)) {
             self::createTosFooterMenu();
         }
     }

     /**
      * [createTosFooterMenu description]
      * @return [type] [description]
      */
     protected static function createTosFooterMenu()
     {
         $stylesheetGroup = Theme::getCurrentStylesheetGroup();

         $menuItems = Endpoint::getEndPoints();
         $menuName  = self::$tosFooterMenuName;
         $menuLocation = $stylesheetGroup == 'fau' ? self::$fauFooterMenuLocation : '';

         self::createNavMenu($menuName, $menuItems, $menuLocation);
     }

     /**
      * [createNavMenu description]
      * @param  [type]  $menuName  [description]
      * @param  [type]  $menuItems [description]
      * @param  [type]  $menuLocation  [description]
      * @param  boolean $activate       [description]
      * @return mixed                  [description]
      */
     protected static function createNavMenu($menuName, $menuItems, $menuLocation = '')
     {
         if (is_nav_menu($menuName)) {
             return null;
         }

         $menuId = wp_create_nav_menu($menuName);
         if (is_wp_error($menuId)) {
             return false;
         }

         $menu = get_term_by('name', $menuName, 'nav_menu');

         foreach ($menuItems as $value) {
             wp_update_nav_menu_item(
                 $menu->term_id,
                 0,
                 [
                    'menu-item-title'   => mb_convert_case($value, MB_CASE_TITLE, 'UTF-8'),
                    'menu-item-classes' => 'tos',
                    'menu-item-url'     => home_url('/' . sanitize_title($value)),
                    'menu-item-status'  => 'publish',
                ]
            );
         }

         if ($menuLocation) {
             $locations = get_theme_mod('nav_menu_locations');
             $locations[$menuLocation] = $menu->term_id;
             set_theme_mod('nav_menu_locations', $locations);
         }

         return $menuId;
     }
 }
