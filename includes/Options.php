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
	    'version'				=> 1,
		// Optiontable version
            // imprint
            'imprint_websites'                     => $siteUrl,
            'imprint_websites_extra'               => '0',
            // imprint/responsible
            'imprint_responsible_name'             => '',
            'imprint_responsible_street'           => '',
            'imprint_responsible_postalcode'       => '',
            'imprint_responsible_city'             => '',
            'imprint_tos_responsible_org'          => '',
            'imprint_responsible_email'            => '',
            'imprint_responsible_phone'            => '',
            'imprint_wmp_search_responsible'       => $siteUrl,
            // imprint/webmaster
            'imprint_webmaster_name'               => '',
            'imprint_webmaster_street'             => '',
            'imprint_webmaster_postalcode'         => '',
            'imprint_webmaster_city'               => '',
            'imprint_webmaster_org'                => '',
            'imprint_webmaster_email'              => $adminMail,
            'imprint_webmaster_phone'              => '',
            'imprint_webmaster_fax'                => '',
            'imprint_wmp_search_webmaster'         => $siteUrl,
            // imprint/extra
            'imprint_section_extra_text'           => '',
            // privacy
        //    'privacy_newsletter'                   => '1',
            // privacy/extra
            'privacy_section_extra'                => '0',
            'privacy_section_extra_text'           => '',
            // accessibility
            'accessibility_conformity'             => '',
            'accessibility_non_accessible_content' => '',
            'accessibility_creation_date'          => '',
            'accessibility_methodology'            => '',
            'accessibility_last_review_date'       => '',
            // accessibility/feedback
            'feedback_receiver_email'              => $adminMail,
            'feedback_subject'                     => __('Barrierefreiheit Feedback-Formular', 'rrze-tos'),
            'feedback_cc_email'                    => '',
	    	    
	    'imprint'	=> array(
		'display_template_itsec'		=> 1,
		'display_template_idnumbers'	=> 1,
		'display_template_supervisory'	=> 1,
		'display_template_vertretung'	=> 1,
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_imprint_optional'  => array(
			    'title' => __('Optional Parts', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			)
			
		    ),
		    'fields' => array(
			'display_template_vertretung'   => array(
			    'title'	=>  __('University Management', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Display legal notice for university management', 'rrze-tos'),
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_supervisory'   => array(
			    'title'	=>  __('Supervisory', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Display supervisory for the university', 'rrze-tos'),
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_idnumbers'   => array(
			    'title'	=>  __('ID Numbers', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Display offical and public ID numbers for the university', 'rrze-tos'),
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_itsec'   => array(
			    'title'	=>  __('IT Security Notice', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Display a text for IT abuse contact informations.', 'rrze-tos'),
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			)
		    ),
		    
		)
	    ),
	   
	    'privacy'	=> array(
		'display_template_newsletter'	=> 0,
		'display_template_contactinfos'	=> 1,
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_privacy'  => array(
			    'title' => __('Newsletter', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			)
			
		    ),
		    'fields' => array(
			'display_template_newsletter'   => array(
			    'title'	=>  __('Show the newsletter section?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Are you providing a newsletter?', 'rrze-tos'),
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			)
		    ),
		    
		)
		
	    )
	    
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

    public static function getAccessibilityConformity()
    {
        return [
            '1' => __('fully complies with § 1 BayBITV', 'rrze-tos'),
            '2' => __('is partly in accordance with § 1 BayBITV', 'rrze-tos'),
            '0' => __('does not comply with § 1 BayBITV', 'rrze-tos')
        ];
    }

    public static function getAccessibilityMethodology()
    {
        return [
            '1' => __('Self-evaluation', 'rrze-tos'),
            '2' => __('Third party evaluation', 'rrze-tos')
        ];
    }
}
