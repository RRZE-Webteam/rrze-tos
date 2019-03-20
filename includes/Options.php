<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Options
{
    /**
     * Option name
     * @var string
     */
    protected static $option_name = 'rrze_tos';

    /**
     * [default_options description]
     * @return array default options
     */
    protected static function default_options()
    {
        $admin_email = is_multisite() ? get_site_option('admin_email') : get_option('admin_email');
        $siteurl = preg_replace('#^http(s)?://#', '', get_option('siteurl'));

        $options = [
            // Imprint
            'rrze_tos_url'                    => $siteurl,
            'rrze_tos_conformity'             => '1',
            'rrze_tos_no_reason'              => '',
            // Responsible
            'rrze_tos_responsible_name'       => '',
            'rrze_tos_responsible_street'     => '',
            'rrze_tos_responsible_postalcode' => '',
            'rrze_tos_responsible_city'       => '',
            'rrze_tos_responsible_org'        => '',
            'rrze_tos_responsible_email'      => $admin_email,
            'rrze_tos_responsible_phone'      => '',
            'rrze_tos_responsible_id'         => '',
            // Webmaster
            'rrze_tos_webmaster_name'         => '',
            'rrze_tos_webmaster_street'       => '',
            'rrze_tos_webmaster_postalcode'   => '',
            'rrze_tos_webmaster_city'         => '',
            'rrze_tos_webmaster_org'          => '',
            'rrze_tos_webmaster_email'        => '',
            'rrze_tos_webmaster_phone'        => '',
            'rrze_tos_webmaster_fax'          => '',
            'rrze_tos_webmaster_id'           => '',
            // Privacy
            'rrze_tos_protection_newsletter'  => '1',
            'rrze_tos_protection_new_section' => '0',
            'rrze_tos_protection_new_section_text' => '',
            // Feedback email
            'rrze_tos_receiver_email'         => $admin_email,
            'rrze_tos_subject'                => 'Barrierefreiheit Feedback-Formular',
            'rrze_tos_cc_email'               => ''
        ];

        return $options;
    }

    /**
     * [get_options description]
     * @return object settings options
     */
    public static function get_options()
    {
        $defaults = self::default_options();

        $options = (array) get_option(self::$option_name);
        $options = wp_parse_args($options, $defaults);
        $options = array_intersect_key($options, $defaults);

        return (object) $options;
    }

    /**
     * [get_option_name description]
     * @return string option name
     */
    public static function get_option_name()
    {
        return self::$option_name;
    }
}
