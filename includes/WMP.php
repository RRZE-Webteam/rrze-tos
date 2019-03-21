<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class WMP
{
    protected static $wmpApiUrl = 'https://www.wmp.rrze.fau.de/suche/impressum';

    /**
     * Check connection with remote server.
     * @param null|string $host Hostname to check connection
     * @return int|string
     */
    public static function checkApiResponse($host = null)
    {
        if (is_null($host)) {
            return 0;
        }

        $response = wp_remote_get(esc_url_raw(sprintf('%1$s/%2$s/format/json', self::$wmpApiUrl, $host)));
        $status_code = wp_remote_retrieve_response_code($response);

        return $status_code;
    }

    /**
     * Get the json object from remote server and return an array if it is not null.
     * @param null|string $host Hostname to retrieve information.
     * @return array|string
     */
    public static function getJsonApiResponse($host = null)
    {
        if (is_null($host)) {
            return 0;
        }
        $response = wp_remote_get(esc_url_raw(sprintf('%1$s/%2$s/format/json', self::$wmpApiUrl, $host)));
        $returns = json_decode(wp_remote_retrieve_body($response), true);

        return (! empty($returns)) ? $returns : '';
    }

    /**
     * [getWmpApiUrl description]
     * @return string [description]
     */
    public static function getWmpApiUrl()
    {
        return self::$wmpApiUrl;
    }
}
