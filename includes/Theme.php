<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Theme
{
    /**
     * [allowedStylesheets description]
     * @return array [description]
     */
    protected static function allowedStylesheets()
    {
        return [
	    
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
    }

    /**
     * Get the current theme stylesheet group
     * @return string return the current theme stylesheet group or 'default'
     */
    public static function getCurrentStylesheetGroup()
    {
        $currentStylesheet = get_stylesheet();
        $allowedStylesheets = self::allowedStylesheets();

        foreach ($allowedStylesheets as $styleGroup => $stylesheets) {
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
    
     /**
     * Enqueue scripts and JS for the theme
     */
    public function enqueueScripts() {
        $stylesheetGroup = Theme::getCurrentStylesheetGroup();
	if ($stylesheetGroup !== 'none') {
	    wp_enqueue_style('rrze-tos-' . $stylesheetGroup);
	}
    }
}
