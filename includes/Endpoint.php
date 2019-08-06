<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Endpoint {
    protected $options;

    public function __construct() {
        $this->options = Options::getOptions();

        add_action('init', [__CLASS__, 'addRewrite']);
        add_action('template_redirect', [$this, 'templateRedirect']);
    }

    /**
     * Define Endpoints
     */
    public static function addRewrite() {
        foreach (Options::getEndPoints() as $name) {
            add_rewrite_endpoint(sanitize_title($name), EP_ROOT);
        }
    }

   
    /**
     * Umleitung der definierten Endpoints auf die genierten Seiten.
     * Hinweis: Hierdurch werden etwaige Seiten aus WordPress, die denselben Slug haben, ignoriert
     * @return void
     */
    public function templateRedirect() {
        global $wp_query;

        $template = '';
        $endpointName = '';
        $endPoints = Options::getEndPoints();

        foreach ($endPoints as $k => $name) {
            if (isset($wp_query->query_vars[$name])) {
                $template = $k;
                $endpointName = $name;
                break;
            }
        }

        if (! $endpointName) {
            return;
        }

        $wp_query->is_home = false;
        $stylesheetGroup = Theme::getCurrentStylesheetGroup();

        wp_enqueue_style('rrze-tos-' . $stylesheetGroup);

        $styleFile = sprintf(
            '%1$stemplates/themes/%2$s.php',
            plugin_dir_path(RRZE_PLUGIN_FILE),
            $stylesheetGroup
        );
	
	/* 
	 * Dynamic variables for all endpoints
	 */
        $title = mb_convert_case($endpointName, MB_CASE_TITLE, 'UTF-8');
        $imprintWebsites = explode(PHP_EOL, $this->options->imprint_websites);
        $this->options->imprint_websites_extra = count($imprintWebsites) > 1 ? 1 : 0;
        $this->options->websites = implode(', ', $imprintWebsites);
        $this->options->webmaster_more = do_shortcode($this->options->imprint_section_extra_text);
        $this->options->privacy_new_section_text = do_shortcode($this->options->privacy_section_extra_text);

        $this->options->imprint_url = home_url($endPoints['imprint']);
        $this->options->privacy_url = home_url($endPoints['privacy']);
        $this->options->accessibility_url = home_url($endPoints['accessibility']);

	/* 
	 * Dynamic variables for endpoint: Privacy
	 */
	if ($this->options->display_template_youtube ||
	    $this->options->display_template_slideshare ||
	    $this->options->display_template_vimeo) {
	 $this->options->privacy_section_external = 1;
	}
	
	
	/* 
	 * Dynamic variables for endpoint: Accessibility
	 */
	$lawdata = Options::getRechtsraumData();
	$index =  $this->options->accessibility_region;
	if (isset($lawdata->$index['url_law']))  {
	    $this->options->accessibility_url_law = $lawdata->$index['url_law']; 
	}

	if (isset($lawdata->$index['controlling']))  {
	     $this->options->accessibility_controlling_name = $lawdata->$index['controlling']; 
	}

	if (isset($lawdata->$index['controlling_url']))  {
	    $this->options->accessibility_controlling_url = $lawdata->$index['controlling_url']; 
	}
	if (isset($lawdata->$index['controlling_email']))  {
	    $this->options->accessibility_controlling_email = $lawdata->$index['controlling_email']; 
	}
	if (isset($lawdata->$index['controlling_phone']))  {
	    $this->options->accessibility_controlling_phone = $lawdata->$index['controlling_phone']; 
	}
	if (isset($lawdata->$index['controlling_fax']))  {
	    $this->options->accessibility_controlling_fax = $lawdata->$index['controlling_fax']; 
	}
	if (isset($lawdata->$index['controlling_street']))  {
	   $this->options->accessibility_controlling_street = $lawdata->$index['controlling_street']; 
	}
	if (isset($lawdata->$index['controlling_city']))  {
	   $this->options->accessibility_controlling_city = $lawdata->$index['controlling_city']; 
	}
	if (isset($lawdata->$index['controlling_plz']))  {
	   $this->options->accessibility_controlling_postalcode = $lawdata->$index['controlling_plz']; 
	}
	
	
	
	$settings = Options::getAdminsettings();
	
	$style = 'warning';
	switch ($this->options->accessibility_conformity_val) {
	    case '-1':
		$style = 'alert';
		break;
	    case '0':	 
		$style = 'alert';
		break;
	    case '1':
		$style = 'info';
		break;
	    case '2':
		$style = 'success';
		break;   		 
	} 
	$this->options->accessibility_conformity_alertshortcodestyle = $style;
	$this->options->accessibility_conformity_text = $settings->accessibility['settings']['fields']['accessibility_conformity_val']['options'][$this->options->accessibility_conformity_val];
	$this->options->accessibility_methodik_text = $settings->accessibility['settings']['fields']['accessibility_methodology']['options'][$this->options->accessibility_methodology];

        $this->options->accessibility_creation_date_val = date_i18n(get_option('date_format'), strtotime($this->options->accessibility_creation_date));
        $this->options->accessibility_last_review_date_val = date_i18n(get_option('date_format'), strtotime($this->options->accessibility_last_review_date));

	if ($this->options->accessibility_non_accessible_content) {
	    $this->options->accessibility_non_accessible_info = 1;
	}
	$this->options->accessibility_non_accessible_content_reasons = wpautop($this->options->accessibility_non_accessible_content_reasons);
	$this->options->accessibility_non_accessible_content_alternatives = wpautop($this->options->accessibility_non_accessible_content_alternatives);
	$this->options->accessibility_non_accessible_content = wpautop($this->options->accessibility_non_accessible_content);
	
	if ($this->options->accessibility_non_accessible_content_helper==0) {
	    $textoptions = $settings->accessibility['settings']['fields']['accessibility_non_accessible_content_faillist']['options'];
	    $oldval = $this->options->accessibility_non_accessible_content_faillist;
	    $output = '';
	    if (isset($oldval)) {
		foreach ($textoptions as $_k => $_v) {
		    if (in_array( $_k, $oldval )) {
			$output .= '<li>';
			$output .= $textoptions[$_k];
			$output .= '</li>';
		    }
		}
	    }
	    if (!empty($output)) {
		$output = '<ul>'.$output.'</ul>';
		$this->options->accessibility_non_accessible_content_list = $output;
		$this->options->accessibility_non_accessible_info = 1;
	    }

	}
	
        $contactForm = new ContactForm();
        $this->options->contact_form = $contactForm->setForm();

	
	// Load additional Templates
	 foreach ($this->options as $n => $v) {
		    if (preg_match('/^display_template_([a-z0-9]+)/i',$n, $key)) {
			 
			if ($v) {
			    $subtemplate = $template.'-'.$key[1];
			    $subcontent = Template::getContent($subtemplate, $this->options);
			    $optname = $template.'_template_'.$key[1];
			    $this->options->$optname = $subcontent;
			}
		    }
	}
	    

	
	$string = Template::getContent($template, $this->options);
	$content = preg_replace( '/(^|[^\n\r])[\r\n](?![\n\r])/', '$1 ', $string );
	$content  = do_shortcode($content);
	
        if (is_readable($styleFile)) {
            include $styleFile;
        }

        exit;
    }
}
