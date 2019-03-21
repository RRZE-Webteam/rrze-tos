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

        $stylesheetGroup = Theme::getCurrentStylesheetGroup();

        $styleFile = sprintf(
            '%1$sincludes/templates/themes/%2$s.php',
            plugin_dir_path(RRZE_PLUGIN_FILE),
            $stylesheetGroup
        );

        $title = mb_convert_case($endpointName, MB_CASE_TITLE, 'UTF-8');

        $this->options->imprint_url = home_url($endPoints['imprint']);
        $this->options->privacy_url = home_url($endPoints['privacy']);
        $this->options->accessibility_url = home_url($endPoints['accessibility']);

        $contactForm = new ContactForm();
        $this->options->contact_form = $contactForm->setForm();

        $content = Template::getContent($template, $this->options);

        if (is_readable($styleFile)) {
            include $styleFile;
        }

        exit;
    }
}
