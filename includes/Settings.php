<?php

namespace RRZE\Tos;

use \WP_Error;
use \sync_helper;

defined('ABSPATH') || exit;

class Settings {
    protected $optionName;
    protected $options;
    protected $settings;
    protected $settingsScreenId;

    public function __construct()  {
	$this->optionName = Options::getOptionName();
	$this->options = Options::getOptions();
	$this->settings = Options::getAdminsettings();

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
    public function pluginActionLink($links) {
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

    /*-----------------------------------------------------------------------------------*/
    /* Get Tab Slugs
    /*-----------------------------------------------------------------------------------*/
    public function getSettingsPageSlug() {
	$tablist = array();
	
	foreach ($this->settings as $tab => $data) {
	    $tablist[$tab] = $data['tabtitle'];
	 }
	return $tablist;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Parse Query
    /*-----------------------------------------------------------------------------------*/
    protected function getQueryVar($var, $default = '') {
        return ! empty($_GET[$var]) ? esc_attr($_GET[$var]) : $default;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Define settings page
    /*-----------------------------------------------------------------------------------*/
    public function adminSettingsPage() {
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

     //   add_action(
     //       'load-' . $this->settingsScreenId,
     //       [
     //           $this,
     //           'adminHelpMenu'
    //       ]
    //    );
    }

    /*-----------------------------------------------------------------------------------*/
    /* Define Help Tab for settings page
    /*-----------------------------------------------------------------------------------*/
    public function adminHelpMenu() {
        new HelpMenu($this->settingsScreenId);
    }
    /*-----------------------------------------------------------------------------------*/
    /* Prüfung der Eingabewerte
    /*-----------------------------------------------------------------------------------*/
    public function optionsValidate($input) {
        if (isset($input) && is_array($input) && isset($_POST['_wpnonce'])
            && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'rrze_tos_options-options')
        ) {
	    

	   $message = '';
	
            foreach ($input as $_k => $_v) {
	//	$message .= "checking  $_k => $_v<br>";

		if (preg_match('/^([a-z0-9]+)\-([_a-z0-9]+)/i',$_k, $key)) {	
		    $name	= $key[2];
		    $fieldset	= $key[1];
		    $fieldset_opt = $this->settings->$fieldset;
		    $oldval	= $this->options->$name;
		    
		    $type	= $fieldset_opt['settings']['fields'][$name]['type'];
		    $title	= $fieldset_opt['settings']['fields'][$name]['title'];
	    // $message .= "name: $name, fieldset: $fieldset<br>";
	    // $message .= " &nbsp; old: $oldval newval: $_v<br>";
		    switch($type) {
			case 'inputRadioCallback':
			    $val = intval($_v);
			    if ($oldval !== $val) {
				$this->options->$name = $val;
				$message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
			    }
			    break;
			case 'inputTextCallback':
			    $val = sanitize_text_field(wp_unslash($_v));
			    if ($oldval !== $val) {
				$this->options->$name = $val;
				$message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
			    }
			    break;    
		    }
		    
		} elseif (preg_match('/email/i', $_k)) {
                    $this->options->$_k = sanitize_email(wp_unslash($_v));
                } elseif ('imprint_section_extra_text' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('privacy_section_extra_text' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('accessibility_non_accessible_content' == $_k) {
                    $this->options->$_k = wp_kses_post(wp_unslash($_v));
                } elseif ('imprint_websites' == $_k) {
                    $this->options->$_k = implode(PHP_EOL, array_map('sanitize_text_field', explode(PHP_EOL, wp_unslash($_v))));
                } elseif (in_array($_k, ['accessibility_creation_date', 'accessibility_last_review_date'])) {
                    $this->options->$_k = date('Y-m-d', strtotime($_v));
                } else {
                    $this->options->$_k = sanitize_text_field(wp_unslash($_v));
                }
            }
	    if (!empty($message)) {
		$message = '<ul>'.$message.'</ul>';
		add_settings_error(
		       'rrze-tos-updatenotice',
		       esc_attr( 'settings_updated' ),
		       $message,
		       'updated'
		   );
	    }
 
 
            if (isset($_POST['rrze-tos-wmp-search-responsible'])) {
                $this->getResponsibleWmpData();
            } elseif (isset($_POST['rrze-tos-wmp-search-webmaster'])) {
                $this->getWebmasterWmpData();
            }
        }
        return $this->options;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Create settings page
    /*-----------------------------------------------------------------------------------*/
    public function settingsPage() {
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
                </p>
            </form>
	    <pre>
	    <?php 
	    
	    var_dump($this->options);
	    ?>
	    </pre>
	    
        </div>
        <?php
    }

    /*-----------------------------------------------------------------------------------*/
    /* Create tabs within settings page 
    /*-----------------------------------------------------------------------------------*/
    public function adminSettings() {
        register_setting(
            'rrze_tos_options',
            $this->optionName,
            [$this, 'optionsValidate']
        );

        $slugs = self::getSettingsPageSlug();
        $default = array_keys($slugs)[0];
        switch ($this->getQueryVar('current-tab', $default)) {
            case 'accessibility':
		$this->addConfigSettings('accessibility');
                $this->addAccessibilityGeneralSection();
                $this->addFeedbackSection();
                break;
            case 'privacy':
	       $this->addConfigSettings('privacy');
                break;
            case 'imprint':
            default:
		$this->addConfigSettings('imprint');
        }
    }

    
    /*-----------------------------------------------------------------------------------*/
    /* Erstelle Settings und EIngabefelder der jeewiligen Tab
    /*-----------------------------------------------------------------------------------*/
    public function addConfigSettings($fieldset = 'imprint') {

	$fieldset_opt = $this->settings->$fieldset;
	foreach ($fieldset_opt["settings"] as $n => $v) {
	    if ($n == 'sections') {
		foreach ($v as $field => $fielddata) {
		    $id = $field;
		    $title = $fielddata['title'];
		    $page = $fielddata['page'];
		    if (!isset($page)) {
		       $page = 'rrze_tos_options';
		    }
		    if (isset($title)) {
			add_settings_section($id, $title, [$this, 'callback_SectionText' ], $page);  
		    }
			
		}
	    } 
	    if ($n == 'fields') {
		foreach ($v as $field => $fielddata) {
		    $fieldoptions = array();
		    $required = $rows = $desc = $default = '';
		    
		    if (isset($fielddata['id'])) {
			$id = $fielddata['id'];
		    } else {
			$id = $field;
		    }
		    
		    $title = $fielddata['title'];
		    $section = $fielddata['section'];
		    $type = $fielddata['type'];
		    
		    if (isset($fielddata['options'])) {
			$fieldoptions = $fielddata['options'];
		    }
		    if (isset($fielddata['required'])) {
			  $required = $fielddata['required'];
		    }
		    if (isset($fielddata['rows'])) {
			$rows = $fielddata['rows'];
		    }
		    if (isset($fielddata['desc'])) {
			$desc = $fielddata['desc'];
		    }
		    if (isset($fielddata['page'])) {
			$page = $fielddata['page'];
		    } else {
			$page = 'rrze_tos_options';
		    }
		     if (isset($fielddata['default'])) {
			$default = $fielddata['default'];
		    }
		    if (isset($fielddata['autocomplete'])) {
			$default = $fielddata['autocomplete'];
		    }
   		    
		    if (isset($this->options->$id)) {
			$default = $this->options->$id;
		    }
		    if ((isset($title)) && (isset($id))) {
			   add_settings_field(
			    $id, 
			    $title,  
			    [$this, $type ],
			    $page,
			    $section,
			    [
				'fieldset'	=> $fieldset,
				'name'		=> $id,
				'required'	=> $required,
				'rows'		=> $rows,
				'description'	=> $desc,
				'options'	=> $fieldoptions,
				'default'	=> $default,
				'autocomplete'    => $autocomplete
			    ]
			);
		    }
			
		}
	    } 
	 }
    }
    
    /*-----------------------------------------------------------------------------------*/
    /* Callback für Tabs: prints infos
    /*-----------------------------------------------------------------------------------*/    
    public function callback_SectionText( $section_passed ) {
       foreach ($this->settings as $tab => $name) {
	   foreach ($name['settings']['sections'] as $s => $setid) {
	       if ($s == $section_passed['id']) {
		   if (isset($setid['desc'])) {
		         echo "<p>".$setid['desc']."</p>"; 
		   }
		   if (isset($setid['notice'])) {
		        echo '<div class="notice-info"><p>'.$setid['notice']."</p></div>"; 
		   }
		   if (isset($setid['warning'])) {
		         echo '<div class="notice-warning"><p>'.$setid['warning']."</p></div>"; 
		   }
	       }
	   }
       }	
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

    /*-----------------------------------------------------------------------------------*/
    /* Callback: Generisches Texteingabefeld
    /*-----------------------------------------------------------------------------------*/
    public function inputTextCallback($args) {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

       
        if (array_key_exists('type', $args)) {
            $type = esc_attr($args['type']);
        }
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = $args['description'];
        } elseif (array_key_exists('desc', $args)) {
	    $description = $args['desc'];
	}
	
        if (array_key_exists('autocomplete', $args)) {
            $autocomplete = esc_attr($args['autocomplete']);
        }
        if (array_key_exists('required', $args)) {
            $required = esc_attr($args['required']);
        }
        if (array_key_exists('button', $args)) {
            $button = $args['button'];
        } 
	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
        }
	if (array_key_exists('default', $args)) {
            $default = esc_attr($args['default']);
        }
	if (isset($fieldset)) {
	    $oldval = $this->options->$name;
	    if ( (  !isset($oldval) || empty($oldval)    ) && (isset($default))) {
		$oldval = $default;
	    }
	    $postname = $fieldset."-".$name;
	} else {
	    $oldval = $this->options->$name;
	    $postname = $name;
	}
	?>
        <input
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
            type="<?php echo isset($type) ? $type : 'text'; ?>"
            class="<?php echo isset($class) ? esc_attr($class) : 'regular-text'; ?>"
            value="<?php echo isset($oldval) ? $oldval : ''; ?>"
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

    /*-----------------------------------------------------------------------------------*/
    /* Callback: Generisches Textarea-Eingabefeld
    /*-----------------------------------------------------------------------------------*/
    public function inputTextareaCallback($args) {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

  
        if (array_key_exists('rows', $args)) {
            $rows = absint($args['rows']);
        }
        if (array_key_exists('description', $args)) {
            $description = esc_attr($args['description']);
        } 
	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
        }
	if (isset($fieldset)) {
	    $oldval = sanitize_textarea_field($this->options->$name);
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	    $postname = $fieldset."-".$name;
	} else {
	    $oldval = sanitize_textarea_field($this->options->$name);
	    $postname = $name;
	}
	
	
	?>
        <textarea
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($name)); ?>"
            cols="50"
            rows="<?php echo isset($rows) && $rows > 0 ? $rows : 8; ?>"
        ><?php echo isset($oldval) ? $oldval : ''; ?></textarea>
        <br>
        <?php if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }

    /*-----------------------------------------------------------------------------------*/
    /* Callback: Radio-Inputfelder
    /*-----------------------------------------------------------------------------------*/
    public function inputRadioCallback($args) {
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
	if (array_key_exists('default', $args)) {
            $default = sanitize_key($args['default']);
        }
	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
        }
	if (isset($fieldset)) {
	    $oldval = $this->options->$name;
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	     $postname = $fieldset."-".$name;
	} else {
	    $oldval = $this->options->$name;
	     $postname = $name;
	}
        $radios = [];
        if (array_key_exists('options', $args)) {
            $radios = $args['options'];
        }
	
	

	
        foreach ($radios as $_k => $_v) : ?>
            <label>
                <input
                    name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
                    type="radio"
                    value="<?php echo esc_attr($_k); ?>"
                    <?php if (isset($oldval)): checked($oldval, $_k); endif; ?>
                >
                <?php echo esc_attr($_v); ?>
            </label>
           
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
    public function selectCallback($args) {
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

    /*-----------------------------------------------------------------------------------*/
    /* Callback: WPEditor
    /*-----------------------------------------------------------------------------------*/
    public function inputWPEditor($args) {
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
	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
        }
	if (isset($fieldset)) {
	    if ((!isset($content)) && (isset($default))) {
		 $content = wp_unslash($default);
	    }
	     $postname = $fieldset."-".$name;
	} else {
	     $postname = $name;
	}

        $settings = [
            'teeny'         => true,
            'wpautop'       => false,
            'editor_height' => isset($height) && $height > 150 ? $height : 250,
            'media_buttons' => false,
            'textarea_name' => sprintf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname))
        ];

        wp_editor($content, $name, $settings);
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }
    
    /*-----------------------------------------------------------------------------------*/
    /* Submit button
    /*-----------------------------------------------------------------------------------*/
    protected function submitButton($args) {
        $text = array_key_exists('text', $args) ? esc_html($args['text']) : '';
        $type = array_key_exists('type', $args) ? esc_attr($args['type']) : 'secondary';
        $name = array_key_exists('name', $args) ? esc_attr($args['name']) : '';

        submit_button($text, $type, $name, false);
    }

 
}
