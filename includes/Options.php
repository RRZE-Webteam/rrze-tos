<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Options
{
    /**
     * Option name
     * @var string
     */
    protected static $optionName = 'rrze_tos';

    /**
     * [defaultOptions description]
     * @return array default options
     */
    protected static function defaultOptions()
    {
        $adminMail = is_multisite() ? get_site_option('admin_email') : get_option('admin_email');
        $siteUrl = preg_replace('#^http(s)?://#', '', get_option('siteurl'));

        $options = [
            // Imprint
            'rrze_tos_wmp_search_term'          => $siteUrl,
            'rrze_tos_websites'                 => $siteUrl,
            'rrze_tos_websites_more'            => '0',
            'rrze_tos_conformity'               => '1',
            'rrze_tos_no_reason'                => '',
            // Responsible
            'rrze_tos_responsible_name'         => '',
            'rrze_tos_responsible_street'       => '',
            'rrze_tos_responsible_postalcode'   => '',
            'rrze_tos_responsible_city'         => '',
            'rrze_tos_responsible_org'          => '',
            'rrze_tos_responsible_email'        => $adminMail,
            'rrze_tos_responsible_phone'        => '',
            'rrze_tos_responsible_id'           => '',
            // Webmaster
            'rrze_tos_webmaster_name'           => '',
            'rrze_tos_webmaster_street'         => '',
            'rrze_tos_webmaster_postalcode'     => '',
            'rrze_tos_webmaster_city'           => '',
            'rrze_tos_webmaster_org'            => '',
            'rrze_tos_webmaster_email'          => '',
            'rrze_tos_webmaster_phone'          => '',
            'rrze_tos_webmaster_fax'            => '',
            'rrze_tos_webmaster_id'             => '',
            'rrze_tos_webmaster_more'           => '',
            // Privacy
            'rrze_tos_privacy_newsletter'       => '1',
            'rrze_tos_privacy_new_section'      => '0',
            'rrze_tos_privacy_new_section_text' => '',
            // Feedback email
            'rrze_tos_receiver_email'           => $adminMail,
            'rrze_tos_subject'                  => 'Barrierefreiheit Feedback-Formular',
            'rrze_tos_cc_email'                 => ''
        ];

        return $options;
    }

    /**
     * [getOptions description]
     * @return object settings options
     */
    public static function getOptions()
    {
        $defaults = self::defaultOptions();

        $options = (array) get_option(self::$optionName);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return (object) $options;
    }

    /**
     * [getOptionName description]
     * @return string option name
     */
    public static function getOptionName()
    {
        return self::$optionName;
    }
}
