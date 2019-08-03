<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Options {

    protected static $optionName = 'rrze_tos';

    /*-----------------------------------------------------------------------------------*/
    /* Global Options that may be changed by user
    /*-----------------------------------------------------------------------------------*/
    protected static function defaultOptions() {
        $adminMail = is_multisite() ? get_site_option('admin_email') : get_option('admin_email');
        $siteUrl = preg_replace('#^http(s)?://#', '', get_option('siteurl'));

        $options = [
	    'version'				=> 4,
		// Optiontable version
            
            'imprint_websites'                     => $siteUrl,
            'imprint_webmaster_email'              => $adminMail,
            'feedback_receiver_email'              => $adminMail,
            'feedback_subject'                     => __('Barrierefreiheit Feedback-Formular', 'rrze-tos'),
            'feedback_cc_email'                    => '',

        ];
	   
        return $options;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Avaible admin settings that will allow to define and overwrite options
    /*-----------------------------------------------------------------------------------*/
     protected static function defaultAdminSettings() {
	 
         $adminMail = is_multisite() ? get_site_option('admin_email') : get_option('admin_email');
	$siteUrl = preg_replace('#^http(s)?://#', '', get_option('siteurl'));
	
	$settings = [  
	    'imprint'	=> array(
		'tabtitle' =>	 __('Imprint', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(
			
			'rrze_tos_section_imprint_websites' => array(
			    'title'	=> __('Range', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			),
			'rrze_tos_section_imprint_responsible'  => array(
			    'title'	=> __('Responsible person', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Contact data for the responsible person in law for the website.','rrze-tos'),
			),
			'rrze_tos_section_imprint_webmaster'  => array(
			    'title'	=> __('Webmaster', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Contact data for webmaster or content team.','rrze-tos'),
			),
			'rrze_tos_section_imprint_optional'  => array(
			    'title'	=> __('Optional Parts', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('This enables or deactivates optional paragraphs of the imprint.','rrze-tos'),
			    'notice'	=> __('Organisations which are part of the FAU are advised to have all optional parts activated. Websites, that belong to cooperations or external organisations, may deactivate unrelevant parts.','rrze-tos'),
			),
		
			

		    ),
		    'fields' => array(
			'imprint_websites' => array(
			    'title'	=>  __('Websites', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_websites',
			    'type'	=> 'inputTextareaCallback',
			    'desc'	=> __('Add one or more websites referred to in the imprint.','rrze-tos'),
			    'default'	=> $siteUrl,
			    'required'	=> 'required',
			    'rows'        => 4,
			),
			
			
			
			'imprint_webmaster_name'=> array(
			    'title'	=>  __('Name', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Name of webmaster or webteam', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_webmaster_email'=> array(
			    'title'	=>  __('EMail', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Contact email', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			
			'imprint_webmaster_phone'=> array(
			    'title'	=>  __('Phone', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'desc'	=> __('Contact phone number', 'rrze-tos'),
			    'type'	=> 'inputTextCallback',
			    'default'	=> '',
			),
			'imprint_webmaster_fax'=> array(
			    'title'	=>  __('Fax number', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputTextCallback',
			    'default'	=> ''
			),

			
			
			
			'imprint_responsible_name'=> array(
			    'title'	=>  __('Name', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Responsible person for the website.', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_responsible_email'=> array(
			    'title'	=>  __('EMail', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Contact email for responsible person', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_responsible_street'=> array(
			    'title'	=>  __('Street', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> 'Schlossplatz',
			),
			'imprint_responsible_postalcode'=> array(
			    'title'	=>  __('Postal code', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> '91052',
			),
			'imprint_responsible_city'=> array(
			    'title'	=>  __('City', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> 'Erlangen',
			    
			),
			'imprint_responsible_phone'=> array(
			    'title'	=>  __('Phone', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'desc'	=> __('Contact phone number for responsible person', 'rrze-tos'),
			    'type'	=> 'inputTextCallback',
			    'default'	=> '',
			),
			'imprint_responsible_org'=> array(
			    'title'	=>  __('Organisation', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'desc'	=> __('Department name', 'rrze-tos'),
			    'type'	=> 'inputTextCallback',
			    'default'	=> 'Friedrich-Alexander-Universität Erlangen-Nürnberg (FAU)'
			),
			
			
			'display_template_vertretung'   => array(
			    'title'	=>  __('University Management', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Display legal notice for university management', 'rrze-tos'),
			    'default'	=> 1,
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
			     'default'	=> 1,
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
			     'default'	=> 1,
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
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'imprint_section_extra'   => array(
			    'title'	=>  __('Add a new section?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'imprint_section_extra_text'   => array(
			    'title'	=>  __('Content', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Content of the new section', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			)
			
			
		    ),

		)
	    ),

	    'privacy'	=> array(
		'tabtitle'	 => __('Privacy', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_privacy_fauservices'  => array(
			    'title' => __('FAU services', 'rrze-tos'),
			    'desc'	=> __('Check whether you are using FAU services, that are using or working with personal data.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_privacy_externalservices'  => array(
			    'title' => __('External services', 'rrze-tos'),
			    'desc'	=> __('Check whether you are using external services, that are using or working with personal data.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_privacy_optional'  => array(
			    'title'	=> __('Optional Parts', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('This enables or deactivates optional paragraphs of the privacy.','rrze-tos'),
			),

		    ),
		    'fields' => array(
			'display_template_newsletter'   => array(
			    'title'	=>  __('Do you provide a newsletter?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Are you providing a newsletter by using IdM or RRZE mail services?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_contactform'   => array(
			    'title'	=>  __('Contact Form', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Do you use a contact form on this website?', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_register_event'   => array(
			    'title'	=>  __('Register form', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Do you provide a contact form to register for an event?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			
			'display_template_youtube'   => array(
			    'title'	=>  __('YouTube Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Do you embed videos from Youtube?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_slideshare'   => array(
			    'title'	=>  __('Slideshare Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Do you embed slides from Slideshare?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'display_template_vimeo'   => array(
			    'title'	=>  __('Vimeo Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Do you embed videos from Vimeo?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			
			
			
			
			'privacy_section_extra'   => array(
			    'title'	=>  __('Add a new section?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'options' => [
				    '1' => __('Yes', 'rrze-tos'),
				    '0' => __('No', 'rrze-tos')
				]
			),
			'privacy_section_extra_text'   => array(
			    'title'	=>  __('Content', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Content of the new section', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			)
		    ),

		)

	    ),
	    'accessibility' => array(
		'tabtitle'	 => __('Accessibility', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_accessibility_general'  => array(
			    'title' => __('General', 'rrze-tos'),
			    'desc' => __('Public institutions are required by Directive (EU) 2016/2102 of the European Parliament and of the Council to make their websites and/or mobile applications accessible. For public authorities, the directive was implemented in Art. 13 BayBGG and BayBITV.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_accessibility_status'  => array(
			    'title' => __('Konformitätsstatus', 'rrze-tos'),
			    'desc'  => __('Offiziell anzugebender Status des Webauftritts, sowie dessen Inhalten hinsichtlich der Erfüllung der gesetzlichen Anforderungen.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_accessibility_reasonfield'  => array(
			    'title' => __('Erklärung', 'rrze-tos'),
			    'desc'  => __('Auflistung und Erläuterung der Probleme bei der Umsetzung der Barrierefreiheit.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_feedback'  => array(
			    'title' => __(' Feedback-Mechanismus', 'rrze-tos'),
			    'desc'  => __('Möglichkeiten zur Kontaktaufnahme bei Probleen und Fehlern zur Barrierefreiheit.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			

		    ),
		    'fields' => array(
			'accessibility_conformity_val'   => array(
			    'title'	=>  __('Konformitätserklärung', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputSelectCallback',
			    'desc'	=> __('Stand der Konformität gemäß der EU-Richtlinie 2102 und der lokalen Gesetzgebung.', 'rrze-tos'),
			    'default'	=> 1,
			    'addbreak'	=> true,
			    'options' => [
				    '2'    => __('Vollständig konform: Der Inhalt entspricht ohne Ausnahmen vollständig dem Standard für Barrierefreiheit.', 'rrze-tos'),
				    '1'	   => __('Teilweise konform: Einige Teile des Inhalts entsprechen nicht vollständig dem Standard für Barrierefreiheit.', 'rrze-tos'),
				    '0'	   => __('Nicht konform: Der Inhalt entspricht nicht dem Standard für Barrierefreiheit.', 'rrze-tos'),
				    '-1'	   => __('Unbekannt: Der Inhalt wurde nicht bewertet oder die Bewertungsergebnisse sind nicht verfügbar.', 'rrze-tos')
				]
			),
			'accessibility_methodology'   => array(
			    'title'	=>  __('Methodology', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 1,
			    'options' => [
				 '1' => __('Self-evaluation', 'rrze-tos'),
				 '2' => __('Third party evaluation', 'rrze-tos')
			    ]
			),
			'accessibility_creation_date'   => array(
			    'title'	=>  __('Creation date', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputDateCallback',
			    'min'	=> '2019-08-01',
			),
			'accessibility_last_review_date'   => array(
			    'title'	=>  __('Last review date', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputDateCallback',
			    'min'	=> '2019-08-01',
			),
			
			
			'accessibility_non_accessible_content_helper'   => array(
			    'title'	=>  __('Eingabehilfe zu nicht barrierefreie Inhalte', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_reasonfield',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			     'addbreak'	=> true,
			    'options' => [
				    '1' => __('Erklärungen manuell ausfüllen', 'rrze-tos'),
				    '0' => __('Eingabehilfe nutzen und durch manuelle Eingaben ergänzen', 'rrze-tos')
				]
			),
			
			
			'accessibility_non_accessible_content_faillist'   => array(
			    'title'	=>  __('Nicht barrierefrei zugängliche Inhalte (Auswahl)', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_reasonfield',
			    'type'	=> 'inputCheckboxListCallback',
			       'addbreak'	=> true,
			    'options' => [
				    1	=>  __('PDF-Dokumente, die vor dem 23.09.2018 erstellt wurden, konnten noch nicht auf ein barrierefreies Format umgestellt werden.', 'rrze-tos'),
				2	=>  __('PDF-Dokumente, die ab dem 23.09.2018 erstellt wurden, sind noch nicht barrierefrei zugänglich.', 'rrze-tos'),
				3	=>  __('Einige Dokumente wurden von Dritten (z.B. Prüfungsamt, andere Einrichtungen der FAU, Ministerien, u.a.) bereitgestellt. Diese Dokumente liegen nicht in einer barrierefreien Fassung vor.', 'rrze-tos'),
				4	=>  __('Zu eingebundenen Videos stehen derzeit keine Untertitel oder Transkription zur Verfügung.', 'rrze-tos'),
				5	=>  __('Zu mittels Karten oder Kartenbildern eingebundenen Anfahrtsbeschreibungen fehlt derzeit die textuelle Beschreibung.', 'rrze-tos'),
				6	=>  __('In den Seiten enthaltene Grafiken oder Bilder sind derzeit nicht vollständig durch Textbeschreibungen ergänzt worden.', 'rrze-tos'),
				7	=>  __('Es werden Tabellen zum Zwecke der optischen Gestaltung verwendet.', 'rrze-tos'),
				8	=>  __('Bei der Verwendung von mehrsprachigen Inhalten auf einer Seite, werden die Sprachen teilweise nicht korrekt in HTML gekennzeichnet.', 'rrze-tos'),


				]
			),
			
			
			'accessibility_non_accessible_content'   => array(
			    'title'	=>  __('Nicht barrierefrei zugängliche Inhalte', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_reasonfield',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Der Gesetzgeber verpflichtet dazu, alle nicht barrierefreien Bestandteile des Webauftritts und der Inhalte öffentlich aufzulisten. Diese müssen hier angegeben werden.', 'rrze-tos'),
			    'default'	=> '',
			    'height' => 100,
			),
			'accessibility_non_accessible_content_reasons'   => array(
			    'title'	=>  __('Begründung', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_reasonfield',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Neben der reinen Auflistung der nicht barrierefreien Inhalte ist zusätzlich für jeden genannten Punkt eine Begründung anzugeben, warum die Barrierefreiheit nicht geleistet werden konnte. Bitte beachten Sie, daß der Gesetzgeber folgende Begründungen als unberechtigt auflistet: "Mangelnde Prioritäten, Zeit oder Unkenntnis". Diese Punkte dürfen daher nicht als Begründung verwendet werden. ', 'rrze-tos'),
			    'default'	=> '',
			    'height' => 100,
			),
			'accessibility_non_accessible_content_alternatives'   => array(
			    'title'	=>  __('Alternative Zugangswege', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_reasonfield',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Geben Sie hier an, ob und welche Alternativen zur Verfügung stehen, die oben genannten nicht zugänglichen Inhalte zu erlangen. Dies kann beispielsweise die Kontaktaufnahme über das Feedback-Formular sein oder die Angabe einer Stelle, die Hilfe leistet.', 'rrze-tos'),
			    'default'	=> '',
			    'height' => 100,
			),
			
			'accessibility_feedback_contactname'=> array(
			    'title'	=>  __('Ansprechpartner', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'type'	=> 'inputTextCallback',
			    'desc'	=>  __('Geben Sie hier einen Namen für den zuständigen Ansprechpartner für Beschwerden oder Hilfeanfragen über mangelnde Zugänglichkeit an.', 'rrze-tos'),
			),
			'accessibility_feedback_email'=> array(
			    'title'	=>  __('EMail-Adresse', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'desc'	=> __('Empfänger-Mailadresse zu Beschwerden oder Hilfeanfragen über mangelnde Zugänglichkeit. Bitte beachten Sie: Bleibt eine Anfrage über die Kontaktmöglichkeit innerhalb von sechs Wochen ganz oder teilweise unbeantwortet, prüft die zuständige Aufsichtsbehörde auf Antrag des Nutzers, ob im Rahmen der Überwachung gegenüber dem Verpflichteten Maßnahmen erforderlich sind.', 'rrze-tos'),
			    'type'	=> 'inputTextCallback',
			    'default'	=> $adminMail,
			    'required'     => 'required'
			),
			'accessibility_feedback_cc'=> array(
			    'title'	=>  __('E-Mail CC', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'desc'	=> __('Optionale zusätzliche Mailadresse.', 'rrze-tos'),
			    'type'	=> 'inputTextCallback',
			    'default'	=> '',
			),
			'accessibility_feedback_subject'=> array(
			    'title'	=>  __('Subject', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'type'	=> 'inputTextCallback',
			    'default'	=>  __('Accessibility Formular Request', 'rrze-tos'),
			    'required'     => 'required'
			),
			'accessibility_feedback_phone'=> array(
			    'title'	=>  __('Phone', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'type'	=> 'inputTextCallback',
			    'desc'	=>  __('Kontaktnummer für telefonische Hilfestellung.', 'rrze-tos'),
			),
			'accessibility_feedback_address'=> array(
			    'title'	=>  __('Adresse', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'type'	=> 'inputTextareaCallback',
			    'desc'	=>  __('Postadresse als Alternative zur E-Mail.', 'rrze-tos'),
			),
			
			
		    )
		)
	    )
	];
	
	return $settings;
    }

    
    /*-----------------------------------------------------------------------------------*/
    /* gets options from get_option() table and merges them with defaults if needed
    /*-----------------------------------------------------------------------------------*/
    public static function getOptions() {
        $defaults = self::defaultOptions();
    $options = (array) get_option(self::$optionName);
	// $options = array();
	$options = wp_parse_args($options, $defaults);
// $options = array_intersect_key($options, $defaults);
        return (object) $options;
    }

    
    /*-----------------------------------------------------------------------------------*/
    /* get Adminsettings
    /*-----------------------------------------------------------------------------------*/
    public static function getAdminsettings() {
      $defaults = self::defaultAdminSettings();
         return (object) $defaults;
    }
    
    /*-----------------------------------------------------------------------------------*/
    /* getOptionName
    /*-----------------------------------------------------------------------------------*/
    public static function getOptionName() {
        return self::$optionName;
    }


}
