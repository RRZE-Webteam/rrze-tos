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

	    'imprint_websites'                  => $siteUrl,
	    'imprint_webmaster_email'           => $adminMail,
	    'accessibility_feedback_email'	    => $adminMail,
	    'display_template_contactinfos'	    => 1,
	    'accessibility_region'		    => 2,
	    'accessibility_conformity_val'	    => -1,
	    'accessibility_feedback_subject'	=> __('Feedback-Formular Barrierefreiheit', 'rrze-tos'),
	    'display_template_supervisory'  => 1,
	    'display_template_idnumbers'  => 1,
	    'display_template_itsec'  => 1,
	    'display_template_betroffenenrechte'  => 1,
	    'display_template_vertretung'   => 1,
	    'display_template_coronakontaktverfolgung'   => 1,
	    'imprint_responsible_org'	=> get_bloginfo('name'),


        ];

        return $options;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Avaible admin settings that will allow to define and overwrite options
    /*-----------------------------------------------------------------------------------*/
     protected static function defaultAdminSettings() {

         $adminMail = is_multisite() ? get_site_option('admin_email') : get_option('admin_email');
	$siteUrl = preg_replace('#^http(s)?://#', '', get_option('siteurl'));
	$rechtsraumliste = self::getRechtsraumData();
	foreach ($rechtsraumliste as $key => $bereich) {
	    $rechtsraumindex[$key] = $bereich['region'];
	}


	$settings = [
	    'imprint'	=> array(
		'endpoint'  => array(
		    'de'    => 'impressum',
		    'en'    => 'imprint'
		),
		'tabtitle' => __('Impressum', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(

			'rrze_tos_section_imprint_websites' => array(
			    'title'	=> __('Umfang', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			),
			'rrze_tos_section_imprint_responsible'  => array(
			    'title'	=> __('Verantwortliche Person', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Daten für die Kontaktaufnahme in rechtlicher Hinsicht.','rrze-tos'),
			),
			'rrze_tos_section_imprint_webmaster'  => array(
			    'title'	=> __('Webmaster', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Daten zur Kontaktaufnahme hinsichtlich der Inhalte des Webauftritts.','rrze-tos'),
			),
			'rrze_tos_section_imprint_optional'  => array(
			    'title'	=> __('Optionale Angaben', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Diese Option erlaubt das Ändern von vorgegebenen Absätzen, sowie das Hinzufügen eines weiteren selbst formulierten Absatzes.','rrze-tos'),
			    'notice'	=> __('Hinweis: Offizielle Einrichtungen der FAU sollten alle folgenden Optionen aktiviert haben.','rrze-tos'),
			),



		    ),
		    'fields' => array(
			'imprint_websites' => array(
			    'title'	=>  __('Webauftritte', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_websites',
			    'type'	=> 'inputTextareaCallback',
			    'desc'	=> __('Wenn dieses Impressum für mehr als einen Webauftritt gilt, fügen Sie hier die Adressen der weiteren Webauftritte ein. Bitte jeweils pro Zeile eine Adresse eingeben.','rrze-tos'),
			    'default'	=> $siteUrl,
			    'required'	=> 'required',
			    'rows'        => 4,
			),



			'imprint_webmaster_name'=> array(
			    'title'	=>  __('Name', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Name des Webmasters oder der zuständigen Webredaktion.', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_webmaster_email'=> array(
			    'title'	=>  __('E-Mail', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputEMailCallback',
			    'default'	=> '',
			    'required'	=> 1,
			),

			'imprint_webmaster_phone'=> array(
			    'title'	=>  __('Telefon', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_webmaster',
			    'type'	=> 'inputTextCallback',
			    'default'	=> '',
			),




			'imprint_responsible_name'=> array(
			    'title'	=>  __('Name', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'desc'	=> __('Rechtlich verantwortliche Person für den Webauftritt. (In der Regel ist dies der Lehrstuhlinhaber oder Einrichtungsleiter)', 'rrze-tos'),
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_responsible_email'=> array(
			    'title'	=>  __('E-Mail', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputEMailCallback',
			    'default'	=> '',
			    'required'	=> 1,
			),
			'imprint_responsible_street'=> array(
			    'title'	=>  __('Straße und Hausnummer', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> 'Schlossplatz 1',
			),
			'imprint_responsible_postalcode'=> array(
			    'title'	=>  __('Postleitzahl', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> '91052',
			),
			'imprint_responsible_city'=> array(
			    'title'	=>  __('Stadt', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> 'Erlangen',

			),
			'imprint_responsible_phone'=> array(
			    'title'	=>  __('Telefon', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> '',
			),
			'imprint_responsible_fax'=> array(
			    'title'	=>  __('Fax number', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=> ''
			),

			'imprint_responsible_org'=> array(
			    'title'	=>  __('Organisation', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_responsible',
			    'type'	=> 'inputTextCallback',
			    'default'	=>  get_bloginfo('name'),
			),


			'display_template_vertretung'   => array(
			    'title'	=>  __('Verweis auf die Universitätsleitung', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Offizieller Vertreter der Universität und ihrer Einrichtungen nach Außen ist der Präsident. Dazu wird hiermit ein entsprechenden Absatz angezeigt.', 'rrze-tos'),
			    'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_supervisory'   => array(
			    'title'	=>  __('Aufsichtsbehörde', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Zeigt die Aufsichtsbehörde an.', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_idnumbers'   => array(
			    'title'	=>  __('Identifikationsnummern', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Anzeige der öffentlichen und offiziellen Identifikationsnummern der Universität', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_itsec'   => array(
			    'title'	=>  __('IT Sicherheit', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Hinweis und Kontaktangaben zur Meldung von Vorfällen zur IT Sicherheit.', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'imprint_section_bildrechte'   => array(
			    'title'	=>  __('Freitextfeld für Bildrechte einfügen?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'imprint_section_bildrechte_text'   => array(
			    'title'	=>  __('Bildrechte', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Optionaler Absatz für die Beschreibung etwaiger verwendeter Bildrechte.', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			),
			
			
			'imprint_section_extra'   => array(
			    'title'	=>  __('Neuen Abschnitt hinzufügen?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'imprint_section_extra_text'   => array(
			    'title'	=>  __('Inhalt', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_imprint_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Inhalt des neuen, zusätzlichen Abschnitts.', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			)


		    ),

		)
	    ),

	    'privacy'	=> array(
		'endpoint'  => array(
		    'de'    => 'datenschutz',
		    'en'    => 'privacy'
		),
		'tabtitle'	 => __('Datenschutz', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_privacy_fauservices'  => array(
			    'title' => __('Dienste', 'rrze-tos'),
			    'desc'	=> __('Falls einer der folgenden Dienste verwendet wird, aktivieren Sie diesen um einen entsprechenden Hinweis in der Datenschutzerklärung zu erzeugen.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_privacy_externalservices'  => array(
			    'title' => __('Externe Dienstleister', 'rrze-tos'),
			    'desc'	=> __('Wenn externe Dienstleister verwendet werden, um Inhalte in der Webseite einzubinden, müssen diese ebenfalls in der Datenschutzerklärung aufgenommen werden.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),
			'rrze_tos_section_privacy_optional'  => array(
			    'title'	=> __('Optionale Angaben', 'rrze-tos'),
			    'page'	=> 'rrze_tos_options',
			    'desc'	=> __('Zusätzliche Angaben zur Datenschutzerklärung.','rrze-tos'),
			),

		    ),
		    'fields' => array(
			'display_template_newsletter'   => array(
			    'title'	=>  __('Newsletter oder Mailverteiler', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Bieten Sie einen Newsletter oder Mailverteiler an?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_contactform'   => array(
			    'title'	=>  __('Kontaktformular', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Verwenden Sie ein Kontaktformular auf dieser Webseite? (Die Barrierefreiheitserklärung bietet ein solches an. Daher ist die Antwort in der Regel "ja").', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_registrationform'   => array(
			    'title'	=>  __('Registrierungs- und Anmeldeformulare', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Verwenden Sie Formulare für die Anmeldung zu Veranstaltungen oder anderen Funktionen, bei denen man sich Registrieren muss?', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_coronakontaktverfolgung'   => array(
			    'title'	=>  __('Corona Kontaktverfolgung', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_fauservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Anzeigen der Hinweise zur Corona Kontaktverfolgung für Veranstaltungen', 'rrze-tos'),
			     'default'	=> 1,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),

			'display_template_youtube'   => array(
			    'title'	=>  __('YouTube Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Wenn Sie YouTube Videos in der Webseite einbinden, aktivieren Sie diese Option.', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_slideshare'   => array(
			    'title'	=>  __('Slideshare Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Wenn Sie Vortragsfolien auf Slideshare anbieten und in der Webseite embedden, aktivieren Sie diese Option.', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_vimeo'   => array(
			    'title'	=>  __('Vimeo Embeds', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Wenn Sie Videos vom Onlinedienst Vimeo in der Webseite einbinden, aktivieren Sie diese Option.', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_vgwort'   => array(
			    'title'	=>  __('VG Wort Zählpixel', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Für den Fall, dass auf der Webseite das Messverfahren der VG Wort eingesetzt wird, sollte diese Option aktiviert werden', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_siteimprove'   => array(
			    'title'	=>  __('Siteimprove', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Für den Fall, dass auf der Webseite Siteimprove Analytics eingesetzt wird, sollte diese Option aktiviert werden', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'display_template_varifast'   => array(
			    'title'	=>  __('Varifast', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_externalservices',
			    'type'	=> 'inputRadioCallback',
			    'desc'	=> __('Für den Fall, dass auf der Webseite Varifast Werbung eingesetzt wird, sollte diese Option aktiviert werden', 'rrze-tos'),
			     'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),



			'privacy_section_extra'   => array(
			    'title'	=>  __('Neuen Abschnitt hinzufügen?', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'privacy_section_extra_text'   => array(
			    'title'	=>  __('Inhalt', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Inhalt des neuen, zusätzlichen Abschnitts.', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			),
			'privacy_section_owndsb'   => array(
			    'title'	=>  __('Text Datenschutzbeauftragter', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 0,
			    'desc'	=>  __('Ersetze den Standardext mit der Einleitung und den Kontaktdaten zum Datenschutzbeauftragten durch einen eigenen Text.', 'rrze-tos'),
			    'options' => [
				    '1' => __('Ja', 'rrze-tos'),
				    '0' => __('Nein', 'rrze-tos')
				]
			),
			'privacy_section_owndsb_text'   => array(
			    'title'	=>  __('Inhalt', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_privacy_optional',
			    'type'	=> 'inputWPEditor',
			    'desc'	=>  __('Eigener Text für Einleitung und Angabe eines Datenschutzbeauftragten', 'rrze-tos'),
			    'default'	=> '',
			     'height' => 200,
			)


		    ),

		)

	    ),
	    'accessibility' => array(
		'endpoint'  => array(
		    'de'    => 'barrierefreiheit',
		    'en'    => 'accessibility'
		),
		'tabtitle'	 => __('Barrierefreiheit', 'rrze-tos'),
		'settings'  => array(
		    'sections'	=> array(
			'rrze_tos_section_accessibility_general'  => array(
			    'title' => __('Allgemeine Hinweise', 'rrze-tos'),
			    'desc' => __('Alle öffentlichen Stellen sind gemäß der Richtlinie (EU) 2016/2102 des Europäischen Parlaments und des Rates, bzw. der Umsetzung in der jeweiligen Landesgesetzgebung dazu verpflichtet, ihre Webauftritte und/ oder mobilen Anwendungen barrierefrei zugänglich zu machen. HIerzu gehört auch die Bereitstellung einer Konformitätserklärung zur Barrierefreiheit, in der alle Betreiber von Webauftritten und Apps den Status der Webseite öffentlich angeben und erläutern müssen, aus welchen Gründen welche Barrieren vorhanden sind.', 'rrze-tos'),
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
			    'desc'  => __('Möglichkeiten zur Kontaktaufnahme bei Problemen und Fehlern zur Barrierefreiheit.', 'rrze-tos'),
			    'page'  => 'rrze_tos_options',
			),


		    ),
		    'fields' => array(
			'accessibility_region'   => array(
			    'title'	=>  __('Rechtsraum', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_general',
			    'type'	=> 'inputSelectCallback',
			    'desc'	=> __('Auswahl des Rechtsraumes, zu welcher der Betreiber des Webangebots gehört.', 'rrze-tos'),
			    'default'	=> 2,
			    'options'	=> $rechtsraumindex
			),
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
			    'title'	=>  __('Methode', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputRadioCallback',
			    'default'	=> 1,
			    'options' => [
				 '1' => __('Selbstbewertung', 'rrze-tos'),
				 '2' => __('Bewertung durch Dritte', 'rrze-tos')
			    ]
			),
			'accessibility_creation_date'   => array(
			    'title'	=>  __('Erstellungsdatum', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputDateCallback',
			    'min'	=> '2018-01-01',
			),
			'accessibility_last_review_date'   => array(
			    'title'	=>  __('Letzte Überprüfung', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputDateCallback',
			    'min'	=> '2018-01-01',
			),
			'accessibility_testurl'   => array(
			    'title'	=>  __('Bericht', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_accessibility_status',
			    'type'	=> 'inputURLCallback',
			    'desc'	=> __('Falls es einen ausführlichen Testbericht gibt, kann dieser hier verlinkt werden.', 'rrze-tos'),
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
				2	=>  __('PDF-Dokumente, die nach dem 23.09.2018 erstellt wurden, sind noch nicht barrierefrei zugänglich.', 'rrze-tos'),
				3	=>  __('Einige Dokumente wurden von Dritten (z.B. Prüfungsamt, andere Einrichtungen der FAU, Ministerien, u.a.) bereitgestellt. Diese Dokumente liegen nicht in einer barrierefreien Fassung vor.', 'rrze-tos'),
				4	=>  __('Zu eingebundenen Videos stehen derzeit keine Untertitel oder Transkription zur Verfügung.', 'rrze-tos'),
				5	=>  __('Zu mittels Karten oder Kartenbildern eingebundenen Anfahrtsbeschreibungen fehlt derzeit die textuelle Beschreibung.', 'rrze-tos'),
				6	=>  __('In den Seiten enthaltene Grafiken oder Bilder sind derzeit nicht vollständig durch Textbeschreibungen ergänzt worden.', 'rrze-tos'),
				7	=>  __('Es werden Tabellen zum Zwecke der optischen Gestaltung verwendet.', 'rrze-tos'),
				8	=>  __('Bei der Verwendung von mehrsprachigen Inhalten auf einer Seite, werden die Sprachen teilweise nicht korrekt in HTML gekennzeichnet.', 'rrze-tos'),
				9	=>  __('Die Schriftfarbe im Logo mit dem ausgeschriebenen Titel des Webauftritts ist nicht kontrastreich genug.', 'rrze-tos'),


				],
			    'desc'  => __('Auswahl der gängsten Mängel, die eine Webseite aufweisen kann. Bitte geben Sie bei AUswahl einer oder mehrerer oben genannter Mängel dennoch unten eine jeweils plausible Begründung ein, warum dieser Mangel vorhanden ist und welche Alternativen vorhanden sind, um dennoch an die Inhalte zu gelangen.', 'rrze-tos'),
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
			    'title'	=>  __('E-Mail-Adresse', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'desc'	=> __('Empfänger-Mailadresse zu Beschwerden oder Hilfeanfragen über mangelnde Zugänglichkeit. Bitte beachten Sie: Bleibt eine Anfrage über die Kontaktmöglichkeit innerhalb von sechs Wochen ganz oder teilweise unbeantwortet, prüft die zuständige Aufsichtsbehörde auf Antrag des Nutzers, ob im Rahmen der Überwachung gegenüber dem Betreiber des Webauftritts (also Ihnen) Maßnahmen erforderlich sind.', 'rrze-tos'),
			    'type'	=> 'inputEMailCallback',
			    'default'	=> $adminMail,
			    'required'     => 'required'
			),
			'accessibility_feedback_cc'=> array(
			    'title'	=>  __('E-Mail CC', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'desc'	=> __('Optionale zusätzliche Mailadresse.', 'rrze-tos'),
			    'type'	=> 'inputEMailCallback',
			    'default'	=> '',
			),
			'accessibility_feedback_subject'=> array(
			    'title'	=>  __('Subject', 'rrze-tos'),
			    'section'	=> 'rrze_tos_section_feedback',
			    'type'	=> 'inputTextCallback',
			    'default'	=>  __('Feedback-Formular Barrierefreiheit', 'rrze-tos'),
			    'required'     => 'required'
			),
			'accessibility_feedback_phone'=> array(
			    'title'	=>  __('Telefon', 'rrze-tos'),
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

    protected static function defaultRechtsraumData() {
        $rechtsraum = [
	0 =>  array(
	    'region' => __('Bundesebene Deutschland (Öffentlicher Dienst)','rrze-tos'),
	    'url_law'	=> 'https://www.gesetze-im-internet.de/bgg/BGG.pdf',
	    'url_vo'	=> 'https://www.gesetze-im-internet.de/bitv_2_0/BJNR184300011.html',
	    'controlling'   => 'Überwachungsstelle des Bundes für Barrierefreiheit von Informationstechnik',
	    'controlling_namezusatz'	=> 'Bundesministerium für Arbeit und Soziales',
	    'controlling_email'   => '',
	    'controlling_url'   => 'https://www.bfit-bund.de',
	    'controlling_plz'   => '10117',
	    'controlling_city'   => 'Berlin',
	    'controlling_street'   => 'Wilhelmstraße 49',
	),
	1 =>  array(
	    'region' => __('Baden-Württemberg','rrze-tos'),
	    'url_law'	=> 'http://www.landesrecht-bw.de/jportal/?quelle=jlink&query=BehGleichStG+BW&psml=bsbawueprod.psml&max=true',
	    'url_vo'	=> 'http://www.landesrecht-bw.de/jportal/?quelle=jlink&query=BehGleichStGDV+BW&psml=bsbawueprod.psml&max=true&aiz=true',
	    'controlling'   => 'Überwachungsstelle für mediale Barrierefreiheit des Landes Baden-Württemberg',
	    'controlling_email'   => 'ueberwachungsstelle@drv-bw.de',
	    'controlling_url'   => 'https://www.deutsche-rentenversicherung.de/BadenWuerttemberg/DE/Ueber-uns/Mediale-Barrierefreiheit/mediale-barrierefreiheit.html',
	    'controlling_address'	=> '',
	),
	2 =>  array(
	    'region' => __('Bayern','rrze-tos'),
	    'url_law'	=> 'http://gesetze-bayern.de/Content/Document/BayBGG/true',
	    'url_vo'	=> 'https://www.gesetze-bayern.de/Content/Document/BayBITV',
	    'controlling'   => 'Landesamt für Digitalisierung, Breitband und Vermessung',
	    'controlling_namezusatz'	=> 'IT-Dienstleistungszentrum des Freistaats Bayern Durchsetzungs- und Überwachungsstelle für barrierefreie Informationstechnik',
	    'controlling_email'   => 'bitv@bayern.de',
	    'controlling_url'   => 'https://www.ldbv.bayern.de/digitalisierung/bitv.html',
	    'controlling_phone'   => '+49 89 2129-1111',
	    'controlling_plz'   => '81541',
	    'controlling_city'   => 'München',
	    'controlling_street'   => 'St.-Martin-Straße 47',

	),
	3 =>  array(
	    'region' => __('Berlin','rrze-tos'),
	    'url_law'	=> 'http://gesetze.berlin.de/jportal/?quelle=jlink&query=BIKTG+BE+%C2%A7+3&psml=bsbeprod.psml&max=true',
	    'url_vo'	=> '',
	    'controlling'   => 'Landesbeauftragte für digitale Barrierefreiheit',
	    'controlling_email'   => 'Digitale-Barrierefreiheit@senInnDS.berlin.de',
	    'controlling_url'   => 'https://www.berlin.de/moderne-verwaltung/barrierefreie-it/',
    	    'controlling_plz'   => '10179',
	    'controlling_city'   => 'Berlin',
	    'controlling_street'   => 'Klosterstraße 47',
	),
	4 =>  array(
	    'region' => __('Brandenburg','rrze-tos'),
	    'url_law'	=> 'https://bravors.brandenburg.de/gesetze/bbgbgg',
	    'url_vo'	=> 'https://bravors.brandenburg.de/verordnungen/bbgbitv',
	    'controlling'   => 'Landesamt für Soziales und Versorgung, Überwachungsstelle Barrierefreies Internet',
	    'controlling_email'   => 'Durchsetzung.BIT@MSGIV.Brandenburg.de',
	    'controlling_url'   => 'https://lasv.brandenburg.de/',
	     'controlling_plz'   => '14467',
	    'controlling_city'   => 'Potsdam',
	    'controlling_street'   => 'Henning-von-Tresckow-Straße 2-13',
	),
	5 =>  array(
	    'region' => __('Bremen','rrze-tos'),
	    'url_law'	=> 'https://www.transparenz.bremen.de/sixcms/detail.php?gsid=bremen2014_tp.c.124514.de&asl=bremen203_tpgesetz.c.55340.de&template=20_gp_ifg_meta_detail_d#jlr-BGGBR2018pP12',
	    'url_vo'	=> 'http://www.gesetze-im-internet.de/bitv_2_0/',
	    'controlling'   => 'Zentralstelle für barrierefreie Informationstechnik',
	    'controlling_email'   => 'ulrike.peter@lbb.bremen.de',
	    'controlling_url'   => 'https://www.behindertenbeauftragter.bremen.de/der-beauftragte/zentralstelle-fuer-barrierefreie-informationstechnik-28011',
	     'controlling_plz'   => '28199',
	    'controlling_city'   => 'Bremen',
	    'controlling_street'   => 'Teerhof 59',
	),
	6 =>  array(
	    'region' => __('Hamburg','rrze-tos'),
	    'url_law'	=> 'http://www.landesrecht-hamburg.de/jportal/portal/page/bshaprod.psml?showdoccase=1&st=lr&doc.id=jlr-GleichstbMGHArahmen',
	    'url_vo'	=> '',
	    'controlling'   => 'Die Überwachungsstelle für Barrierefreiheit von Informationstechnik der Freien und Hansestadt Hamburg',
	    'controlling_email'   => 'ueberwachungsstelle.barrierefreiheit@sk.hamburg.de',
	    'controlling_url'   => 'https://www.hamburg.de/ueberwachungsstelle-barrierefreiheit/',
	    'controlling_address'	=> '',

	),
	7 =>  array(
	    'region' => __('Hessen','rrze-tos'),
	    'url_law'	=> 'https://www.rv.hessenrecht.hessen.de/bshe/document/jlr-BGGHEV8IVZ',
	    'url_vo'	=> '',
	    'controlling'   => 'Durchsetzungs- und Überwachungsstelle Barrierefreie Informationstechnik,  Hessisches Ministerium für Soziales und Integration',
	    'controlling_email'   => 'Durchsetzungsstelle-LBIT@rpgi.hessen.de',
	    'controlling_url'   => 'https://soziales.hessen.de/ueber-uns/beauftragte-fuer-barrierefreie-it/aufgaben-der-landesbeauftragten-fuer-barrierefreie-it',
	     'controlling_plz'   => '35390',
	    'controlling_city'   => 'Gießen',
	    'controlling_street'   => 'Landgraf-Philipp-Platz 1-7',
	),
	8 =>  array(
	    'region' => __('Mecklenburg-Vorpommern','rrze-tos'),
	    'url_law'	=> 'http://www.landesrecht-mv.de/jportal/portal/page/bsmvprod.psml;jsessionid=0061262BA90EF14DA9B6664FD15E61B7.jp26?showdoccase=1&st=lr&doc.id=jlr-BGGMVrahmen&doc.part=X&doc.origin=bs',
	    'url_vo'	=> 'https://www.regierung-mv.de/Landesregierung/sm/Soziales/Behinderungen/Das-Landesbehindertengleichstellungsgesetz-und-seine-Rechtsverordnungen',
	    'controlling'   => 'Überwachungsstelle Mecklenburg-Vorpommern',
	    'controlling_email'   => 'ueberwachungsstelle@sm.mv-regierung.de',
	    'controlling_url'   => 'https://www.regierung-mv.de/Landesregierung/sm/Soziales/Ueberwachungsstelle/',
	     'controlling_plz'   => '19055',
	    'controlling_city'   => 'Schwerin',
	    'controlling_street'   => 'Werderstraße 124',
	),
	9 =>  array(
	    'region' => __('Niedersachsen','rrze-tos'),
	    'url_law'	=> 'http://www.voris.niedersachsen.de/jportal/?quelle=jlink&query=BehGleichG+ND&psml=bsvorisprod.psml&max=true&aiz=true',
	    'url_vo'	=> '',
	    'controlling'   => 'Barrierefreie IT in Niedersachsen',
	    'controlling_email'   => 'schlichtungsstelle@ms.niedersachsen.de',
	    'controlling_url'   => 'https://www.ms.niedersachsen.de/startseite/service_kontakt/barrierefreie_it/barrierefreie-it-in-niedersachsen-183088.html',
	    'controlling_address'	=> '',
	),
	10 =>  array(
	    'region' => __('Nordrhein-Westfalen','rrze-tos'),
	    'url_law'	=> 'http://recht.nrw.de/lmi/owa/br_bes_text?anw_nr=2&gld_nr=2&ugl_nr=201&bes_id=5216&aufgehoben=N&menu=1&sg=0#det190773',
	    'url_vo'	=> 'https://recht.nrw.de/lmi/owa/br_vbl_detail_text?anw_nr=6&vd_id=17834&ver=8&val=17834&sg=0&menu=1&vd_back=N',
	    'controlling'   => 'Überwachungsstelle für barrierefreie Informationstechnik des Landes Nordrhein-Westfalen',
	    'controlling_email'   => 'ueberwachungsstelle-nrw@it.nrw.de',
	    'controlling_url'   => 'https://www.mags.nrw/ueberwachungsstelle-barrierefreie-informationstechnik',
	    'controlling_address'	=> '',
	),
	11 =>  array(
	    'region' => __('Rheinland-Pfalz','rrze-tos'),
	    'url_law'	=> 'http://landesrecht.rlp.de/jportal/portal/t/im6/page/bsrlpprod.psml;jsessionid=9ED11D4B99D0BC1B86F507A116B67B2F.jp25?pid=Dokumentanzeige&showdoccase=1&js_peid=Trefferliste&documentnumber=1&numberofresults=1&fromdoctodoc=yes&doc.id=jlr-BehGleichGRPrahmen&doc.part=X&doc.price=0.0#focuspoint',
	    'url_vo'	=> '',
	    'controlling'   => 'Überwachungsstelle für barrierefreie Informationstechnik',
	    'controlling_email'   => 'IT-Barrierefreiheit@lfst.fin-rlp.de',
	    'controlling_url'   => 'https://www.lfst-rlp.de/startseite/ueberwachungsstelle-fuer-barrierefreie-informationstechnik',
	     'controlling_plz'   => '56073',
	    'controlling_city'   => 'Koblenz',
	    'controlling_street'   => 'Ferdinand-Sauerbruch-Str. 17',
	),
	12 =>  array(
	    'region' => __('Saarland','rrze-tos'),
	    'url_law'	=> '',
	    'url_vo'	=> 'http://sl.juris.de/cgi-bin/landesrecht.py?d=http://sl.juris.de/sl/gesamt/SBGV_SL_2006.htm#SBGV_SL_2006_rahmen',
	    'controlling'   => 'Schlichtungsstelle, Ministerium für Soziales, Gesundheit, Frauen und Familie - Ref. B1',
	    'controlling_email'   => 'inklusion@soziales.saarland.de',
	    'controlling_url'   => '',
	     'controlling_plz'   => '66119',
	    'controlling_city'   => 'Saarbrücken',
	    'controlling_street'   => 'Franz-Josef-Röder-Straße 23',
	),
	13 =>  array(
	    'region' => __('Sachsen','rrze-tos'),
	    'url_law'	=> 'https://www.revosax.sachsen.de/vorschrift/18283-Saechsisches-Inklusionsgesetz#p9',
	    'url_vo'	=> 'https://www.revosax.sachsen.de/vorschrift/18133-Barrierefreie-Websites-Gesetz',
	    'controlling'   => 'Überwachungsstelle in Sachsen',
	    'controlling_email'   => 'bfit-sachsen@dzblesen.de',
	    'controlling_url'   => 'https://www.dzblesen.de/ueber-uns/fachthemen-kooperationen-projekte/ueberwachungsstelle-in-sachsen',
	    'controlling_address'	=> '',
	),
	14 =>  array(
	    'region' => __('Sachsen-Anhalt','rrze-tos'),
	    'url_law'	=> 'http://www.landesrecht.sachsen-anhalt.de/jportal/?quelle=jlink&query=BehGleichG+ST&psml=bssahprod.psml&max=true',
	    'url_vo'	=> '',
	    'controlling'   => 'Beauftragter der Sächsischen Staatsregierung für die Belange von Menschen mit Behinderungen',
	    'controlling_email'   => 'info.behindertenbeauftragter@sk.sachsen.de',
	    'controlling_url'   => '',
	     'controlling_plz'   => '01097',
	    'controlling_city'   => 'Dresden',
	    'controlling_street'   => 'Archivstraße 1',
	),
	15 =>  array(
	    'region' => __('Schleswig-Holstein','rrze-tos'),
	    'url_law'	=> 'http://www.gesetze-rechtsprechung.sh.juris.de/jportal/?quelle=jlink&query=BGG+SH&psml=bsshoprod.psml&max=true',
	    'url_vo'	=> '',
	    'controlling'   => 'Beschwerdestelle für barrierefreie Informationstechnik',
	    'controlling_email'   => 'bbit@landtag.ltsh.de',
	    'controlling_url'   => 'https://www.landtag.ltsh.de/beauftragte/beschwerdestelle-fuer-barrieren/',
	     'controlling_plz'   => '24105',
	    'controlling_city'   => 'Kiel',
	    'controlling_street'   => 'Karolinenweg 1',
	),
	16 =>  array(
	    'region' => __('Thüringen','rrze-tos'),
	    'url_law'	=> 'http://landesrecht.thueringen.de/jportal/portal/t/ps9/page/bsthueprod.psml;jsessionid=FBEDF07ACA45BF60CF8E5C576567D539.jp27?pid=Dokumentanzeige&showdoccase=1&js_peid=Trefferliste&documentnumber=1&numberofresults=1&fromdoctodoc=yes&doc.id=jlr-BfWebGTHrahmen&doc.part=X&doc.price=0.0#focuspoint',
	    'url_vo'	=> 'http://landesrecht.thueringen.de/jportal/?quelle=jlink&query=BITV+TH&psml=bsthueprod.psml&max=true&aiz=true',
	    'controlling'   => 'Zentrale Überwachungsstelle digitale Barrierefreiheit',
	    'controlling_email'   => 'ueberwachung-digitale-barrierefreiheit@tfm.thueringen.de',
	    'controlling_url'   => 'https://finanzen.thueringen.de/ministerium/zentrale-ueberwachungsstelle-digitale-barrierefreiheit',
	     'controlling_plz'   => '99096',
	    'controlling_city'   => 'Erfurt',
	    'controlling_street'   => ' Jürgen-Fuchs-Straße 1',
	),


        ];

        return $rechtsraum;
    }
    /*-----------------------------------------------------------------------------------*/
    /* Endpoints for generated pages
    /*-----------------------------------------------------------------------------------*/
    public static function getEndPoints() {
	$settings = self::defaultAdminSettings();
	$endpoints = array();
    $endpointsMap = array();
	$langCode = Locale::getLangCode();
	foreach ($settings as $field => $data) {
	    if (isset($data['endpoint'][$langCode])) {
		$endpoints[$field] = $data['endpoint'][$langCode];
	    } else {
		$endpoints[$field] = $data['endpoint']['de'];
	    }
        $endpointsMap[$field] = $data['endpoint'];
	}
	return apply_filters('rrze_tos_endpoints', $endpoints, $endpointsMap);
    }

    /*-----------------------------------------------------------------------------------*/
    /* gets options from get_option() table and merges them with defaults if needed
    /*-----------------------------------------------------------------------------------*/
    public static function getOptions() {
        $defaults = self::defaultOptions();
	 $options = (array) get_option(self::$optionName);
	$options = wp_parse_args($options, $defaults);
        return (object) $options;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Hole Daten zum jeweiligen Rechtsraum
    /*-----------------------------------------------------------------------------------*/
    public static function getRechtsraumData() {
        $rechtsraum = self::defaultRechtsraumData();
	return (object) $rechtsraum;

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
    /*-----------------------------------------------------------------------------------*/
    /* Get Tab Slugs
    /*-----------------------------------------------------------------------------------*/
    public static function getSettingsPageSlug() {
	$tablist = array();
	$defaults = self::defaultAdminSettings();
	foreach ($defaults as $tab => $data) {
	    $tablist[$tab] = $data['tabtitle'];
	 }
	return $tablist;
    }


}
