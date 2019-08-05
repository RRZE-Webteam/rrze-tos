<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

 class NavMenu
 {
     /**
      * [protected description]
      * @var string
      */
     protected static $tosMenuName = 'rrze-tos-menu';

     /**
      * [menuLocations description]
      * @return array [description]
      */
     protected static function menuLocations()
     {
         return [
             'fau' => 'meta-footer',
         ];
     }

     /**
      * [addTosMenu description]
      */
     public static function addTosMenu()
     {
         add_action('init', [__CLASS__, 'setTosMenu']);
     }

     /**
      * [setTosMenu description]
      */
     public static function setTosMenu()
     {
         if (! is_nav_menu(self::$tosMenuName)) {
             self::createTosMenu();
         }
     }

     /**
      * [createTosMenu description]
      */
     protected static function createTosMenu()
     {
         $menuLocations = self::menuLocations();
         $stylesheetGroup = Theme::getCurrentStylesheetGroup();

         $menuItems = Options::getEndPoints();
         $menuName  = self::$tosMenuName;
         $menuLocation = isset($menuLocations[$stylesheetGroup]) ? $menuLocations[$stylesheetGroup] : '';

         self::createNavMenu($menuName, $menuItems, $menuLocation);
     }

     /**
      * [createNavMenu description]
      * @param  [type]  $menuName     [description]
      * @param  [type]  $menuItems    [description]
      * @param  [type]  $menuLocation [description]
      * @return mixed                 [description]
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
