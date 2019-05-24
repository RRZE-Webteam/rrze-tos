<?php

namespace RRZE\Tos;

use \WP_Error;
use \sync_helper;

defined('ABSPATH') || exit;

class Settings
{
    /**
     * [protected description]
     * @var string
     */
    protected $optionName;

    /**
     * [protected description]
     * @var object
     */
    protected $options;

    /**
     * [protected description]
     * @var string
     */
    protected $settingsScreenId;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->optionName = Options::getOptionName();
        $this->options = Options::getOptions();

        add_action(
            'admin_menu',
            [$this, 'adminSettingsPage']
        );
        add_action(
            'admin_init',
            [$this, 'adminSettings']
        );

        add_filter(
            'plugin_action_links_' . plugin_basename(RRZE_PLUGIN_FILE),
            [$this, 'pluginActionLink']
        );
    }

    /**
     * [pluginActionLink description]
     * @param  array $links [description]
     * @return array        [description]
     */
    public function pluginActionLink($links)
    {
        if (! current_user_can('manage_options')) {
            return $links;
        }
        return array_merge(
            $links,
            [
                sprintf(
                    '<a href="%1$s">%2$s</a>',
                    admin_url('options-general.php?page=rrze-tos'),
                    __('Settings', 'rrze-tos')
                )
            ]
        );
    }

    /**
     * [getSettingsPageSlug description]
     * @return array [description]
     */
    protected static function getSettingsPageSlug()
    {
        return [
            'imprint'       => __('Imprint', 'rrze-tos'),
            'privacy'       => __('Privacy', 'rrze-tos'),
            'accessibility' => __('Accessibility', 'rrze-tos')
        ];
    }

    /**
     * [getQueryVar description]
     * @param  string $var     [description]
     * @param  string $default [description]
     * @return [type]          [description]
     */
    protected function getQueryVar($var, $default = '')
    {
        return ! empty($_GET[$var]) ? esc_attr($_GET[$var]) : $default;
    }

    /**
     * [adminSettingsPage description]
     * @return [type] [description]
     */
    public function adminSettingsPage()
    {
        $this->settingsScreenId = add_options_page(
            __('ToS', 'rrze-tos'),
            __('ToS', 'rrze-tos'),
            'manage_options',
            'rrze-tos',
            [
                $this,
                'settingsPage'
            ]
        );

        add_action(
            'load-' . $this->settingsScreenId,
            [
                $this,
                'adminHelpMenu'
            ]
        );
    }

    /**
     * [adminHelpMenu description]
     */
    public function adminHelpMenu()
    {
        new HelpMenu($this->settingsScreenId);
    }

    /**
     * [optionsValidate description]
     * @param  array $input [description]
     * @return object       [description]
     */
    public function optionsValidate($input)
    {
        if (isset($input) && is_array($input) && isset($_POST['_wpnonce'])
            && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'rrze_tos_options-options')
        ) {
            foreach ($input as $_k => $_v) {
                if (preg_match('/email/i', $_k)) {
                    $this->options->$_k = sanitize_email(wp_unslash($_v));
                } elseif ('imprint_section_extra_text' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('privacy_section_extra_text' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('accessibility_non_accessible_content' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('imprint_websites' == $_k) {
                    $this->options->$_k = implode(PHP_EOL, array_map('sanitize_text_field', explode(PHP_EOL, wp_unslash($_v))));
                } else {
                    $this->options->$_k = sanitize_text_field(wp_unslash($_v));
                }
            }

            if (isset($_POST['rrze-tos-wmp-search-responsible'])) {
                $this->getResponsibleWmpData();
            } elseif (isset($_POST['rrze-tos-wmp-search-webmaster'])) {
                $this->getWebmasterWmpData();
            }
        }
        return $this->options;
    }

    /**
     * [settingsPage description]
     */
    public function settingsPage()
    {
        $slugs = self::getSettingsPageSlug();
        $default = array_keys($slugs)[0];
        $currentTab = $this->getQueryVar('current-tab', $default); ?>
        <div class="wrap">
            <h1><?php echo __('Settings &rsaquo; ToS', 'rrze-tos'); ?></h1>
            <h2 class="nav-tab-wrapper wp-clearfix">
            <?php foreach ($slugs as $tab => $name) :
                $class = $tab == $currentTab ? 'nav-tab-active' : '';
        printf(
            '<a class="nav-tab %1$s" href="?page=rrze-tos&current-tab=%2$s">%3$s</a>',
            esc_attr($class),
            esc_attr($tab),
            esc_attr($name)
                );
        endforeach; ?>
            </h2>
            <form method="post" action="options.php" id="tos-admin-form">
                <?php settings_fields('rrze_tos_options'); ?>
                <?php do_settings_sections('rrze_tos_options'); ?>
                <p class="submit">
                    <?php submit_button(esc_html__('Save Changes', 'rrze-tos'), 'primary', 'rrze-tos-submit', false); ?>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * [adminSettings description]
     */
    public function adminSettings()
    {
        register_setting(
            'rrze_tos_options',
            $this->optionName,
            [$this, 'optionsValidate']
        );

        $slugs = self::getSettingsPageSlug();
        $default = array_keys($slugs)[0];
        switch ($this->getQueryVar('current-tab', $default)) {
            case 'accessibility':
                $this->addAccessibilityGeneralSection();
                $this->addFeedbackSection();
                break;
            case 'privacy':
                $this->addPrivacySection();
                $this->addPrivacyExtraSection();
                break;
            case 'imprint':
            default:
                $this->addImprintWebsitesSection();
                $this->addImprintResponsibleSection();
                $this->addImprintWebmasterSection();
                $this->addImprintExtraSection();
        }
    }

    /**
     * [addImprintWebsitesSection description]
     */
    protected function addImprintWebsitesSection()
    {
        add_settings_section(
            'rrze_tos_section_imprint_websites',
            '',
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'imprint_websites',
            __('Websites', 'rrze-tos'),
            [
                $this,
                'textareaCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_websites',
            [
                'name'        => 'imprint_websites',
                'required'    => 'required',
                'rows'        => 4,
                'description' => __('One or more websites referred to in the imprint.', 'rrze-tos')
            ]
        );
    }

    /**
     * [addImprintResponsibleSection description]
     */
    protected function addImprintResponsibleSection()
    {
        add_settings_section(
            'rrze_tos_section_imprint_responsible',
            __('Responsible', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'imprint_responsible_name',
            __('Name', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'         => 'imprint_responsible_name',
                'autocomplete' => 'given-name'
            ]
        );

        add_settings_field(
            'imprint_responsible_email',
            __('Email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'         => 'imprint_responsible_email',
                'autocomplete' => 'email'
            ]
        );

        add_settings_field(
            'imprint_responsible_street',
            __('Street', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'         => 'imprint_responsible_street',
                'autocomplete' => 'address-line1'
            ]
        );

        add_settings_field(
            'imprint_responsible_postalcode',
            __('Postcode', 'rrze-tos'),
            [
                $this,
                'inputTextCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'     => 'imprint_responsible_postalcode'
            ]
        );

        add_settings_field(
            'imprint_responsible_city',
            __('City', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'         => 'imprint_responsible_city',
                'autocomplete' => 'address-level2'
            ]
        );

        add_settings_field(
            'imprint_responsible_phone',
            __('Phone', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'         => 'imprint_responsible_phone',
                'autocomplete' => 'tel'
            ]
        );

        add_settings_field(
            'imprint_tos_responsible_org',
            __('Organization', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name' => 'imprint_tos_responsible_org'
            ]
        );

        add_settings_field(
            'imprint_wmp_search_responsible',
            __('WMP search', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_responsible',
            [
                'name'        => 'imprint_wmp_search_responsible',
                'description' => [
                    __('Search term (website) to get the data of the responsible using WMP API.', 'rrze-tos'),
                    'https://www.wmp.rrze.fau.de/suche/impressum/' . $this->options->imprint_wmp_search_responsible
                ],
                'button'      => [
                    'text' => __('Retrieve data', 'rrze-tos'),
                    'type' => 'secondary',
                    'name' => 'rrze-tos-wmp-search-responsible'
                ]
            ]
        );
    }

    /**
     * [addImprintWebmasterSection description]
     */
    protected function addImprintWebmasterSection()
    {
        add_settings_section(
            'rrze_tos_section_imprint_webmaster',
            __('Webmaster', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'imprint_webmaster_name',
            __('Name', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_name'
            ]
        );

        add_settings_field(
            'imprint_webmaster_street',
            __('Street', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_street'
            ]
        );

        add_settings_field(
            'imprint_webmaster_city',
            __('City', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_city'
            ]
        );

        add_settings_field(
            'imprint_webmaster_phone',
            __('Phone', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_phone'
            ]
        );

        add_settings_field(
            'imprint_webmaster_fax',
            __('Fax', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_fax'
            ]
        );

        add_settings_field(
            'imprint_webmaster_email',
            __('Email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_email'
            ]
        );

        add_settings_field(
            'imprint_webmaster_org',
            __('Organization', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name' => 'imprint_webmaster_org'
            ]
        );

        add_settings_field(
            'imprint_wmp_search_webmaster',
            __('WMP search', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_webmaster',
            [
                'name'        => 'imprint_wmp_search_webmaster',
                'description' => [
                    __('Search term (website) to get the data of the webmaster using WMP API.', 'rrze-tos'),
                    'https://www.wmp.rrze.fau.de/suche/impressum/' . $this->options->imprint_wmp_search_webmaster
                 ],
                'button'      => [
                    'text' => __('Retrieve data', 'rrze-tos'),
                    'type' => 'secondary',
                    'name' => 'rrze-tos-wmp-search-webmaster'
                ]
            ]
        );
    }

    /**
     * [addImprintExtraSection description]
     */
    protected function addImprintExtraSection()
    {
        add_settings_section(
            'rrze_tos_section_imprint_extra',
            __('New section', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'imprint_section_extra_text',
            __('Add a new section?', 'rrze-tos'),
            [
                $this,
                'wpEditor'
            ],
            'rrze_tos_options',
            'rrze_tos_section_imprint_extra',
            [
                'name'   => 'imprint_section_extra_text',
                'height' => 200,
            ]
        );
    }

    /**
     * [addPrivacySection description]
     */
    protected function addPrivacySection()
    {
        add_settings_section(
            'rrze_tos_section_privacy',
            __('Newsletter', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'privacy_newsletter',
            __('Show the newsletter section?', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_privacy',
            [
                'name'    => 'privacy_newsletter',
                'options' =>
                    [
                        '1' => __('Yes', 'rrze-tos'),
                        '0' => __('No', 'rrze-tos')
                    ]
            ]
        );
    }

    /**
     * [addPrivacyExtraSection description]
     */
    protected function addPrivacyExtraSection()
    {
        add_settings_section(
            'rrze_tos_section_privacy_extra',
            __('New section', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'privacy_section_extra',
            __('Add a new section?', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_privacy_extra',
            [
                'name'    => 'privacy_section_extra',
                'options' =>
                    [
                        '1' => __('Yes', 'rrze-tos'),
                        '0' => __('No', 'rrze-tos')
                    ]
            ]
        );

        add_settings_field(
            'privacy_section_extra_text',
            __('Content of the new section', 'rrze-tos'),
            [
                $this,
                'wpEditor'
            ],
            'rrze_tos_options',
            'rrze_tos_section_privacy_extra',
            [
                'name' => 'privacy_section_extra_text'
            ]
        );
    }

    /**
     * [addAccessibilityGeneralSection description]
     */
    protected function addAccessibilityGeneralSection()
    {
        add_settings_section(
            'rrze_tos_section_accessibility_general',
            __('General', 'rrze-tos'),
            [
                $this,
                'accessibilityGeneralSectionContent'
            ],
            'rrze_tos_options'
        );

        add_settings_field(
            'accessibility_conformity',
            __('This website', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_accessibility_general',
            [
                'name'    => 'accessibility_conformity',
                'options' => Options::getAccessibilityConformity()
            ]
        );

        add_settings_field(
            'accessibility_non_accessible_content',
            __('Non-accessible content', 'rrze-tos'),
            [
                $this,
                'wpEditor',
            ],
            'rrze_tos_options',
            'rrze_tos_section_accessibility_general',
            [
                'name'        => 'accessibility_non_accessible_content',
                'height'      => 200,
                'description' => __('Which accessible alternatives are available?', 'rrze-tos'),
            ]
        );

        add_settings_field(
            'accessibility_creation_date',
            __('Creation date', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_accessibility_general',
            [
                'name' => 'accessibility_creation_date',
                'type' => 'date'
            ]
        );

        add_settings_field(
            'accessibility_methodology',
            __('Methodology', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_accessibility_general',
            [
                'name'    => 'accessibility_methodology',
                'options' => Options::getAccessibilityMethodology()
            ]
        );

        add_settings_field(
            'accessibility_last_review_date',
            __('Last review date', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_accessibility_general',
            [
                'name' => 'accessibility_last_review_date',
                'type' => 'date'
            ]
        );
    }

    /**
     * [addFeedbackSection description]
     */
    protected function addFeedbackSection()
    {
        add_settings_section(
            'rrze_tos_section_feedback',
            __('Feedback', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'feedback_receiver_email',
            __('Receiver email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_feedback',
            [
                'name'         => 'feedback_receiver_email',
                'autocomplete' => 'email',
                'required'     => 'required'
            ]
        );

        add_settings_field(
            'feedback_subject',
            __('Subject', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_feedback',
            [
                'name'     => 'feedback_subject',
                'required' => 'required'
            ]
        );

        add_settings_field(
            'feedback_cc_email',
            __('CC', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_feedback',
            [
                'name'         => 'feedback_cc_email',
                'autocomplete' => 'email'
            ]
        );
    }

    /**
     * [accessibilityGeneralSectionContent description]
     * @return void
     */
    public function accessibilityGeneralSectionContent()
    {
        _e('Public institutions are required by Directive (EU) 2016/2102 of the European Parliament and of the Council to make their websites and/or mobile applications accessible. For public authorities, the directive was implemented in Art. 13 BayBGG and BayBITV.', 'rrze-tos');
    }

    /**
     * [inputTextCallback description]
     * @param  array $args [description]
     */
    public function inputTextCallback($args)
    {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

        if (array_key_exists($name, $this->options)) {
            $value = esc_attr($this->options->$name);
        }
        if (array_key_exists('type', $args)) {
            $type = esc_attr($args['type']);
        }
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = $args['description'];
        }
        if (array_key_exists('autocomplete', $args)) {
            $autocomplete = esc_attr($args['autocomplete']);
        }
        if (array_key_exists('required', $args)) {
            $required = esc_attr($args['required']);
        }
        if (array_key_exists('button', $args)) {
            $button = $args['button'];
        } ?>
        <input
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
            type="<?php echo isset($type) ? $type : 'text'; ?>"
            class="<?php echo isset($class) ? esc_attr($class) : 'regular-text'; ?>"
            value="<?php echo isset($value) ? $value : ''; ?>"
            <?php echo isset($required) ? $required : ''; ?>
            <?php if (isset($autocomplete)) : ?>
                autocomplete="<?php echo esc_attr($autocomplete); ?>"
            <?php endif; ?>
        >
        <?php if (isset($button) && is_array($button)) :
            $this->submitButton($button);
        endif; ?>
        <br>
        <?php if (isset($description)) :
            $description = is_array($description) ? implode('<br>', array_map('esc_attr', $description)) : esc_attr($description); ?>
            <p class="description"><?php echo make_clickable($description); ?></p>
        <?php endif;
    }

    /**
     * [textareaCallback description]
     * @param array $args [description]
     */
    public function textareaCallback($args)
    {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

        if (array_key_exists($name, $this->options)) {
            $value = sanitize_textarea_field($this->options->$name);
        }
        if (array_key_exists('rows', $args)) {
            $rows = absint($args['rows']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        } ?>
        <textarea
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
            cols="50"
            rows="<?php echo isset($rows) && $rows > 0 ? $rows : 8; ?>"
        ><?php echo isset($value) ? $value : ''; ?></textarea>
        <br>
        <?php if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /**
     * [inputRadioCallback description]
     * @param array $args [description]
     */
    public function inputRadioCallback($args)
    {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

        if (array_key_exists('name', $args)) {
            $name = esc_attr($args['name']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        }

        $radios = [];
        if (array_key_exists('options', $args)) {
            $radios = $args['options'];
        }
        foreach ($radios as $_k => $_v) : ?>
            <label>
                <input
                    name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
                    type="radio"
                    value="<?php echo esc_attr($_k); ?>"
                    <?php if (array_key_exists($name, $this->options)) :
                        checked($this->options->$name, $_k);
        endif; ?>
                >
                <?php echo esc_attr($_v); ?>
            </label>
            <br>
        <?php endforeach;
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /**
     * [selectCallback description]
     * @param  array $args [description]
     * @return void
     */
    public function selectCallback($args)
    {
        $limit = [];
        if (array_key_exists('name', $args)) {
            $name = esc_attr($args['name']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        }
        if (array_key_exists('options', $args)) {
            $limit = $args['options'];
        } ?>
        <?php if (isset($name)) {
            ?>
            <select
                name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
                title="<?php __('Please select one', 'rrze-tos'); ?>">
                <?php foreach ($limit as $_k => $_v) : ?>
                    <option value="<?php echo esc_attr($_k); ?>"
                        <?php
                        if (array_key_exists($name, $this->options)) {
                            selected($this->options->$name, $_k);
                        } ?>
                    ><?php echo esc_attr($_v); ?></option>
                <?php endforeach; ?>
            </select>
        <?php
        } ?>
        <?php if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /**
     * [wpEditor description]
     * @param  array $args [description]
     */
    public function wpEditor($args)
    {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

        $content = '';
        if (array_key_exists($name, $this->options)) {
            $content = wp_unslash($this->options->$name);
        }
        if (array_key_exists('wpautop', $args)) {
            $wpautop = esc_attr($args['wpautop']);
        }
        if (array_key_exists('height', $args)) {
            $height = esc_attr($args['height']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        }

        $settings = [
            'teeny'         => true,
            'wpautop'       => false,
            'editor_height' => isset($height) && $height > 150 ? $height : 250,
            'media_buttons' => false,
            'textarea_name' => sprintf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name))
        ];

        wp_editor($content, $name, $settings);
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /**
     * [submitButton description]
     * @param  array $args [description]
     * @return void
     */
    protected function submitButton($args)
    {
        $text = array_key_exists('text', $args) ? esc_html($args['text']) : '';
        $type = array_key_exists('type', $args) ? esc_attr($args['type']) : 'secondary';
        $name = array_key_exists('name', $args) ? esc_attr($args['name']) : '';

        submit_button($text, $type, $name, false);
    }

    /**
     * [getResponsibleWmpData description]
     * @return boolean|object [description]
     */
    protected function getResponsibleWmpData()
    {
        $this->updateFromWmpData(
            [
                'search' => $this->options->imprint_wmp_search_responsible,
                'key' => 'verantwortlich',
                'prefix' => 'imprint_responsible_'
            ]
        );
    }

    /**
     * [getWebmasterWmpData description]
     * @return boolean|object [description]
     */
    protected function getWebmasterWmpData()
    {
        $this->updateFromWmpData(
            [
                'search' => $this->options->imprint_wmp_search_webmaster,
                'key' => 'webmaster',
                'prefix' => 'imprint_webmaster_'
            ]
        );
    }

    /**
     * [updateFromWmpData description]
     * @param  array $args [description]
     * @return boolean|object [description]
     */
    protected function updateFromWmpData($args)
    {
        $search = array_key_exists('search', $args) ? esc_attr($args['search']) : '';
        $key = array_key_exists('key', $args) ? esc_attr($args['key']) : '';
        $prefix = array_key_exists('prefix', $args) ? esc_attr($args['prefix']) : '';

        $data = WMP::getJsonData($search);
        if (is_wp_error($data)) {
            return $data;
        }

        if (! array_key_exists($key, $data)) {
            return new WP_Error('wmp-key-is-not-available', __('WMP key is not available.', 'rrze-tos'));
        }

        foreach ($data[$key] as $_k => $_v) {
            if (! is_null($_v)) {
                $optionKey = sprintf('%1$s%2$s', $prefix, $_k);
                $this->options->$optionKey = $_v;
            }
        }
        return update_option($this->optionName, $this->options, true);
    }
}
