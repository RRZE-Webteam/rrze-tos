<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Theme
{
    /**
     * Allowed theme stylesheets groups
     * @var array
     */
    protected static $allowedStylesheets = [
        'fau' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Medfak',
            'FAU-RWFak',
            'FAU-Philfak',
            'FAU-Techfak',
            'FAU-Natfak',
        ],
        'rrze' => [
            'rrze-2015',
        ],
        'events' => [
            'FAU-Events',
        ],
    ];

    /**
     * Get the stylesheets groups
     * @return array return the stylesheets groups
     */
    public static function getAllowedStylesheets() {
        return self::$allowedStylesheets;
    }

    /**
     * Get the current theme stylesheet group
     * @return string return the current theme stylesheet group or 'default'
     */
    public static function getCurrentStylesheetGroup() {
        $currentStylesheet = get_stylesheet();

        foreach (self::$allowedStylesheets as $styleGroup => $stylesheets) {
            if (is_array($stylesheets) && in_array(
                strtolower($currentStylesheet),
                array_map('strtolower', $stylesheets),
                true
            )) {
                return $styleGroup;
            }
        }

        return 'default';
    }
}
