<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class Endpoint
{
    protected $options;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->options = Options::getOptions();

        add_action('init', [__CLASS__, 'addRewrite']);
        add_action('template_redirect', [$this, 'templateRedirect']);
    }

    /**
     * [addRewrite description]
     */
    public static function addRewrite()
    {
        foreach (self::getEndPoints() as $name) {
            add_rewrite_endpoint(sanitize_title($name), EP_ROOT);
        }
    }

    /**
     * [getEndPoints description]
     * @return array [description]
     */
    public static function getEndPoints()
    {
        return [
            'imprint'       => __('imprint', 'rrze-tos'),
            'privacy'       => __('privacy', 'rrze-tos'),
            'accessibility' => __('accessibility', 'rrze-tos'),
        ];
    }

    /**
     * [templateRedirect description]
     * @return void
     */
    public function templateRedirect()
    {
        global $wp_query;

        $template = '';
        $endpointName = '';
        $endPoints = self::getEndPoints();

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

        $styleFile = sprintf(
            '%1$sincludes/templates/themes/%2$s.php',
            plugin_dir_path(RRZE_PLUGIN_FILE),
            $stylesheetGroup
        );

        $title = mb_convert_case($endpointName, MB_CASE_TITLE, 'UTF-8');

        $rrzeTosWebsites = explode(PHP_EOL, $this->options->rrze_tos_websites);
        $this->options->rrze_tos_websites_more = count($rrzeTosWebsites) > 1 ? 1 : 0;
        $this->options->websites = implode(', ', $rrzeTosWebsites);
        $this->options->webmaster_more = do_shortcode($this->options->rrze_tos_webmaster_more);
        $this->options->privacy_new_section_text = do_shortcode($this->options->rrze_tos_privacy_new_section_text);

        $this->options->imprint_url = home_url($endPoints['imprint']);
        $this->options->privacy_url = home_url($endPoints['privacy']);
        $this->options->accessibility_url = home_url($endPoints['accessibility']);

        $accessibilityConformityOptions = Options::getAccessibilityConformity();
        $accessibilityConformity = $this->options->accessibility_conformity;
        if ($accessibilityConformity !== 1) {
            $this->options->accessibility_conformity = 0;
        }
        $this->options->accessibility_conformity_val = isset($accessibilityConformityOptions[$accessibilityConformity]) ? $accessibilityConformityOptions[$accessibilityConformity] : '';

        $this->options->accessibility_creation_date_val = date_i18n(get_option('date_format'), strtotime($this->options->accessibility_creation_date));
        $this->options->accessibility_last_review_date_val = date_i18n(get_option('date_format'), strtotime($this->options->accessibility_last_review_date));

        $accessibilityMethodologyOptions = Options::getAccessibilityMethodology();
        $accessibilityMethodology = $this->options->accessibility_methodology;
        $this->options->accessibility_methodology_val = isset($accessibilityMethodologyOptions[$accessibilityMethodology]) ? $accessibilityMethodologyOptions[$accessibilityMethodology] : '';

        $contactForm = new ContactForm();
        $this->options->contact_form = $contactForm->setForm();

        $content = Template::getContent($template, $this->options);

        if (is_readable($styleFile)) {
            include $styleFile;
        }

        exit;
    }
}
