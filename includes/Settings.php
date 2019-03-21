<?php

namespace RRZE\Tos;

use \WP_Error;

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
                } elseif ('rrze_tos_protection_new_section_text' !== $_k && preg_match('/[\r\n\t ]+/', $_v)) {
                    $this->options->$_k = sanitize_textarea_field(wp_unslash($_v));
                } elseif ('rrze_tos_protection_new_section_text' === $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } else {
                    $this->options->$_k = sanitize_text_field(wp_unslash($_v));
                }
            }

            if (isset($_POST['rrze-tos-api-request'])) {
                $this->updateFromApi();
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
                printf('<a class="nav-tab %1$s" href="?page=rrze-tos&current-tab=%2$s">%3$s</a>',
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
                    <?php if ($currentTab == 'imprint') :
                        submit_button(esc_html__('Update data by using the WMP API', 'rrze-tos'), 'secondary', 'rrze-tos-api-request', false);
                    endif; ?>
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
            case 'imprint':
                $this->addWmpSection();
                $this->addResponsibleSection();
                $this->addWebmasterSection();
                break;
            case 'privacy':
                $this->addPrivacySection();
                $this->addExtraSection();
                break;
            case 'accessibility':
            default:
                $this->addGeneralSection();
                $this->addEmailSection();
        }
    }

    /**
     * [addWmpSection description]
     */
    protected function addWmpSection()
    {
        add_settings_section(
            'rrze_tos_section_wmp',
            __('WMP API', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_url',
            __('Server name', 'rrze-tos'),
            [
                $this,
                'inputTextCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_wmp',
            [
                'name'        => 'rrze_tos_url',
                'required'    => 'required',
                'description' => __('Enter a valid server name to request data using the WMP API.', 'rrze-tos')
            ]
        );
    }

    /**
     * [addResponsibleSection description]
     */
    protected function addResponsibleSection()
    {
        add_settings_section(
            'rrze_tos_section_responsible',
            __('Responsible', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_responsible_name',
            __('Name', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'         => 'rrze_tos_responsible_name',
                'autocomplete' => 'given-name'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_email',
            __('Email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'         => 'rrze_tos_responsible_email',
                'autocomplete' => 'email'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_street',
            __('Street', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'         => 'rrze_tos_responsible_street',
                'autocomplete' => 'address-line1'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_postalcode',
            __('Postcode', 'rrze-tos'),
            [
                $this,
                'inputTextCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'     => 'rrze_tos_responsible_postalcode'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_city',
            __('City', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'         => 'rrze_tos_responsible_city',
                'autocomplete' => 'address-level2'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_phone',
            __('Phone', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name'         => 'rrze_tos_responsible_phone',
                'autocomplete' => 'tel'
            ]
        );

        add_settings_field(
            'rrze_tos_responsible_org',
            __('Organization', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_responsible',
            [
                'name' => 'rrze_tos_responsible_org'
            ]
        );

        if (is_plugin_active('fau-person/fau-person.php')) {
            add_settings_field(
                'rrze_tos_responsible_id',
                __('Person-ID', 'rrze-tos'),
                [
                    $this,
                    'inputTextCallback'
                ],
                'rrze_tos_options',
                'rrze_tos_section_responsible',
                [
                    'name' => 'rrze_tos_responsible_id'
                ]
            );
        }
    }

    /**
     * [addWebmasterSection description]
     */
    protected function addWebmasterSection()
    {
        add_settings_section(
            'rrze_tos_section_webmaster',
            __('Webmaster', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_webmaster_name',
            __('Name', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_name'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_street',
            __('Street', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_street'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_city',
            __('City', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_city'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_phone',
            __('Phone', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_phone'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_fax',
            __('Fax', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_fax'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_email',
            __('Email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_email'
            ]
        );

        add_settings_field(
            'rrze_tos_webmaster_org',
            __('Organization', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_webmaster',
            [
                'name' => 'rrze_tos_webmaster_org'
            ]
        );

        if (is_plugin_active('fau-person/fau-person.php')) {
            add_settings_field(
                'rrze_tos_webmaster_ID',
                __('Person-ID', 'rrze-tos'),
                [
                    $this,
                    'inputTextCallback'
                ],
                'rrze_tos_options',
                'rrze_tos_section_webmaster',
                [
                    'name' => 'rrze_tos_webmaster_ID'
                ]
            );
        }
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
            'rrze_tos_protection_newsletter',
            __('Show the newsletter section?', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_privacy',
            [
                'name'    => 'rrze_tos_protection_newsletter',
                'options' =>
                    [
                        '1' => __('Yes', 'rrze-tos'),
                        '0' => __('No', 'rrze-tos')
                    ]
            ]
        );
    }

    /**
     * [addExtraSection description]
     */
    protected function addExtraSection()
    {
        add_settings_section(
            'rrze_tos_section_extra',
            __('New section', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_protection_new_section',
            __('Add a new section?', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_extra',
            [
                'name'    => 'rrze_tos_protection_new_section',
                'options' =>
                    [
                        '1' => __('Yes', 'rrze-tos'),
                        '0' => __('No', 'rrze-tos')
                    ]
            ]
        );

        add_settings_field(
            'rrze_tos_protection_new_section_text',
            __('Content of the new section', 'rrze-tos'),
            [
                $this,
                'wpEditor'
            ],
            'rrze_tos_options',
            'rrze_tos_section_extra',
            [
                'name' => 'rrze_tos_protection_new_section_text'
            ]
        );
    }

    /**
     * [addGeneralSection description]
     */
    protected function addGeneralSection()
    {
        add_settings_section(
            'rrze_tos_section_general',
            __('General', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_conformity',
            __('Are the conformity conditions of the WCAG 2.0 AA fulfilled?', 'rrze-tos'),
            [
                $this,
                'inputRadioCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_general',
            [
                'name'    => 'rrze_tos_conformity',
                'options' =>
                    [
                        '1' => __('Yes', 'rrze-tos'),
                        '0' => __('No', 'rrze-tos')
                    ]
            ]
        );

        add_settings_field(
            'rrze_tos_no_reason',
            __('If not, with what reason', 'rrze-tos'),
            [
                $this,
                'textareaCallback',
            ],
            'rrze_tos_options',
            'rrze_tos_section_general',
            [
                'name'        => 'rrze_tos_no_reason',
                'description' => __('Please include all necessary details', 'rrze-tos'),
            ]
        );
    }

    /**
     * [addEmailSection description]
     */
    protected function addEmailSection()
    {
        add_settings_section(
            'rrze_tos_section_email',
            __('Email', 'rrze-tos'),
            '__return_false',
            'rrze_tos_options'
        );

        add_settings_field(
            'rrze_tos_receiver_email',
            __('Receiver email', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_email',
            [
                'name'         => 'rrze_tos_receiver_email',
                'autocomplete' => 'email',
                'required'     => 'required'
            ]
        );

        add_settings_field(
            'rrze_tos_subject',
            __('Subject', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_email',
            [
                'name'     => 'rrze_tos_subject',
                'required' => 'required'
            ]
        );

        add_settings_field(
            'rrze_tos_cc_email',
            __('CC', 'rrze-tos'),
            [
                $this,
                'inputTextCallback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_email',
            [
                'name'         => 'rrze_tos_cc_email',
                'autocomplete' => 'email'
            ]
        );
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
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        }
        if (array_key_exists('autocomplete', $args)) {
            $autocomplete = esc_attr($args['autocomplete']);
        }
        if (array_key_exists('required', $args)) {
            $required = esc_attr($args['required']);
        } ?>
        <input
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
            type="text"
            class="<?php echo isset($class) ? esc_attr($class) : 'regular-text'; ?>"
            value="<?php echo isset($value) ? $value : ''; ?>"
            <?php echo isset($required) ? $required : ''; ?>
            <?php if (isset($autocomplete)) : ?>
                autocomplete="<?php echo esc_attr($autocomplete); ?>"
            <?php endif; ?>
        >
        <br>
        <?php if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
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
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        } ?>
        <textarea
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
            cols="50"
            rows="8"
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
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        }

        $settings = [
            'editor_height' => 300,
            'media_buttons' => false,
            'textarea_name' => sprintf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name))
        ];

        wp_editor($content, $name, $settings);
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /**
     * [updateFromApi description]
     * @return mixed [description]
     */
    protected function updateFromApi()
    {
        $statusCode = WMP::checkApiResponse($this->options->rrze_tos_url);
        if ($statusCode !== 200) {
            return new WP_Error($statusCode, __('Can not connect to the server.', 'rrze-tos'));
        }

        $wmpOptions = WMP::getJsonApiResponse($this->options->rrze_tos_url);
        if (! is_array($wmpOptions)) {
            return new WP_Error('empty-response', __('Response is empty.', 'rrze-tos'));
        }

        $responsibleKey = 'verantwortlich';
        if (! array_key_exists($responsibleKey, $wmpOptions)) {
            return new WP_Error('key-not-available', __('Key not available.', 'rrze-tos'));
        }
        foreach ($wmpOptions[$responsibleKey] as $_k => $_v) {
            if (! is_null($_v)) {
                $optionKey = "rrze_tos_responsible_$_k";
                $this->options->$optionKey = $_v;
            }
        }

        $webmasterKey = 'webmaster';
        if (! array_key_exists($webmasterKey, $wmpOptions)) {
            return new WP_Error('key-not-available', __('Key not available.', 'rrze-tos'));
        }
        foreach ($wmpOptions[$webmasterKey] as $_k => $_v) {
            if (! is_null($_v)) {
                $optionKey = "rrze_tos_webmaster_$_k";
                $this->options->$optionKey = $_v;
            }
        }

        return update_option($this->optionName, $this->options, true);
    }
}
