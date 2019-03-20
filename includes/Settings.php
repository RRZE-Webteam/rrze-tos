<?php

namespace RRZE\Tos;

use RRZE\Tos\Main;

defined('ABSPATH') || exit;

class Settings
{
    /**
     * [protected description]
     * @var string
     */
    protected $option_name;

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
     * [protected description]
     * @var array
     */
    protected $settings_page_slugs;

    /**
     * Settings-Klasse wird instanziiert.
     */
    public function __construct()
    {
        $this->option_name = Options::get_option_name();
        $this->options = Options::get_options();

        add_action(
            'admin_menu',
            [$this, 'admin_settings_page']
        );
        add_action(
            'admin_init',
            [$this, 'admin_settings']
        );

        add_action(
            'wp_ajax_tos_update_fields',
            [$this, 'tos_update_ajax_handler']
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

    protected function get_query_var($var, $default = '')
    {
        return ! empty($_GET['current-tab']) ? esc_attr($_GET['current-tab']) : $default;
    }

    /**
     * [admin_settings_page description]
     * @return [type] [description]
     */
    public function admin_settings_page()
    {
        $this->settingsScreenId = add_options_page(
            __('ToS', 'rrze-tos'),
            __('ToS', 'rrze-tos'),
            'manage_options',
            'rrze-tos',
            [
                $this,
                'settings_page'
            ]
        );

        add_action(
            'load-' . $this->settingsScreenId,
            [
                $this,
                'admin_help_menu'
            ]
        );
    }

    public function admin_help_menu()
    {
        new HelpMenu($this->settingsScreenId);
    }

    public function options_validate($input)
    {
        if (isset($input)) {
            foreach ($input as $key => $value) {
                if (
                    isset($_POST[$this->option_name][$key], $_POST['_wpnonce'])
                    && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'rrze_tos_options-options')
                ) {
                    if (preg_match('/email/i', $key)) {
                        $this->options->$key = sanitize_email(wp_unslash($_POST[ $this->option_name ][$key]));
                    } elseif ('rrze_tos_protection_new_section_text' !== $key && preg_match('/[\r\n\t ]+/', $value)) {
                        $this->options->$key = sanitize_textarea_field(wp_unslash($_POST[$this->option_name][$key]));
                    } elseif ('rrze_tos_protection_new_section_text' === $key) {
                        $this->options->$key = wp_kses_post(wp_unslash($_POST[ $this->option_name ][$key]));
                    } else {
                        $this->options->$key = sanitize_text_field(wp_unslash($_POST[ $this->option_name ][$key]));
                    }
                }
            }
        }
        return $this->options;
    }

    /**
     * [settings_page description]
     * @return void
     */
    public function settings_page()
    {
        ?>
        <div class="wrap">
            <h2>
                <?php echo __('Settings &rsaquo; ToS', 'rrze-tos'); ?>
            </h2>
            <h3 class="nav-tab-wrapper">
                <?php
                $slugs = self::getSettingsPageSlug();
                $default = array_keys($slugs)[0];
                foreach ($slugs as $tab => $name) {
                    $class = ($tab == $this->get_query_var('current-tab', $default)) ? 'nav-tab-active' : '';
                    printf(
                        '<a class="nav-tab %1$s" href="?page=rrze-tos&current-tab=%2$s">%3$s</a>',
                        esc_attr($class),
                        esc_attr($tab),
                        esc_attr($name)
                    );
                } ?>
            </h3>
            <form method="post" action="options.php" id="tos-admin-form">
                <?php settings_fields('rrze_tos_options'); ?>
                <?php do_settings_sections('rrze_tos_options'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * [admin_settings description]
     * @return void
     */
    public function admin_settings()
    {
        register_setting(
            'rrze_tos_options',
            $this->option_name,
            [$this, 'options_validate']
        );

        $slugs = self::getSettingsPageSlug();
        $default = array_keys($slugs)[0];
        switch ($this->get_query_var('current-tab', $default)) {
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
                'name' => 'rrze_tos_url',
                'required'     => 'required',
                'description' => __('Please enter a valid server name to request the imprint by using the WMP API.', 'rrze-tos')
            ]
        );

        add_settings_field(
            'rrze_tos_update_fields',
            __('', 'rrze-tos'),
            [
                $this,
                'rrze_tos_update_callback'
            ],
            'rrze_tos_options',
            'rrze_tos_section_wmp'
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
                'name'     => 'rrze_tos_webmaster_name'
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
                'name'     => 'rrze_tos_webmaster_email'
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
                'rrze_tos_radio_callback'
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
                'rrze_tos_radio_callback'
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
                'rrze_tos_radio_callback',
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
     * @return void
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
            name="<?php printf('%1$s[%2$s]', esc_attr($this->option_name), esc_attr($name)); ?>"
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
            name="<?php printf('%1$s[%2$s]', esc_attr($this->option_name), esc_attr($name)); ?>"
            cols="50"
            rows="8"
        ><?php echo isset($value) ? $value : ''; ?></textarea>
        <br>
        <?php if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    public function rrze_tos_radio_callback($args)
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
                    name="<?php printf('%1$s[%2$s]', esc_attr($this->option_name), esc_attr($name)); ?>"
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

    public function rrze_tos_select_callback($args)
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
                name="<?php printf('%1$s[%2$s]', esc_attr($this->option_name), esc_attr($name)); ?>"
                title="<?php __('Please select one', 'rrze-tos'); ?>">
                <?php foreach ($limit as $_k => $_v) {
                ?>
                    <option value='<?php echo esc_attr($_k); ?>'
                        <?php
                        if (array_key_exists($name, $this->options)) {
                            selected($this->options->$name, $_k);
                        } ?>
                    >
                        <?php echo esc_attr($_v); ?>
                    </option>
                <?php
            } ?>
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
     * @return void
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
            'textarea_name' => sprintf('%1$s[%2$s]', esc_attr($this->option_name), esc_attr($name)),
        ];

        wp_editor($content, $name, $settings);
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    public function rrze_tos_update_callback()
    {
        ?>
        <button class=" button button-primary " name="update" id="update">
            <span class=""><?php esc_html_e('Update imprint data by using the WMP API', 'rrze-tos'); ?></span>
        </button>
        <?php
    }

    public function tos_update_ajax_handler()
    {
        $status_code = WMP::checkApiResponse($this->options->rrze_tos_url);
        if (200 == $status_code) {
            $wmp_option = WMP::getJsonApiResponse($this->options->rrze_tos_url);
            foreach ($wmp_option['verantwortlich'] as $wmp_key => $wmp_value) {
                if (! is_null($wmp_value)) {
                    $options_key1                 = "rrze_tos_responsible_$wmp_key";
                    $this->options->$options_key1 = $wmp_value;
                }
            }

            foreach ($wmp_option['webmaster'] as $wmp_key => $wmp_value) {
                if (! is_null($wmp_value)) {
                    $options_key                 = "rrze_tos_webmaster_$wmp_key";
                    $this->options->$options_key = $wmp_value;
                }
            }

            update_option('rrze_tos', $this->options, true);
            $wmp_option['success'] = __('All fields were updated!', 'rrze-tos');
            echo wp_json_encode($wmp_option);
        } else {
            echo esc_html(header('HTTP/1.0 404 Not Found'));
            esc_html_e('Can not connect to the server', 'rrze-tos');
        }

        wp_die();
    }
}
