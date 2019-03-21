<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Locale
{
    /**
     * [protected description]
     * @var array
     */
    protected static $localeFallback = [
        'de' => 'de_DE',
        'en' => 'en_US',
        'es' => 'es_ES'
    ];

    /**
     * [getLocaleFallback description]
     * @return array [description]
     */
    public static function getLocaleFallback()
    {
        return self::$localeFallback;
    }

    /**
     * [getLocale description]
     * @return string [description]
     */
    public static function getLocale()
    {
        return get_locale();
    }

    /**
     * [getLangCode description]
     * @return string [description]
     */
    public static function getLangCode()
    {
        return substr(self::getLocale(), 0, 2);
    }
}
