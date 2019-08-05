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
            __('Rechtliche Pflichtangaben', 'rrze-tos'),
            __('Rechtliche Pflichtangaben', 'rrze-tos'),
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
		if (preg_match('/^([a-z0-9]+)\-([_a-z0-9]+)/i',$_k, $key)) {	
		    $name	= $key[2];
		    $fieldset	= $key[1];
		    $fieldset_opt = $this->settings->$fieldset;
		    $oldval	= $this->options->$name;
		    
		    $type	= $fieldset_opt['settings']['fields'][$name]['type'];
		    $title	= $fieldset_opt['settings']['fields'][$name]['title'];

		    if ($name == 'imprint_websites') {
			$val = implode(PHP_EOL, array_map('sanitize_text_field', explode(PHP_EOL, wp_unslash($_v))));
			if ($oldval !== $val) {
			    $this->options->$name = $val;
			    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
			}
			 
		    } else {
			switch($type) {
			    case 'inputRadioCallback':
				$val = intval($_v);
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;
			    case 'inputSelectCallback':
				$val = wp_kses_post(wp_unslash($_v));
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
			    case 'inputURLCallback':
				$val = esc_url(wp_unslash($_v));
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;    	
			    case 'inputEMailCallback':
				$val = sanitize_email(wp_unslash($_v));
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;    		
			    case 'inputDateCallback':
				$val =  date('Y-m-d', strtotime($_v));
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;        
			    case 'inputTextareaCallback':
			    case 'inputWPEditor':
				$val =  wp_kses_post(wp_unslash($_v));
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;    
			    case 'inputCheckboxListCallback':
				$val =  (array) $_v;
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
				break;        	
			    default:
				 $val =  sanitize_text_field(wp_unslash($_v));
				if ($oldval !== $val) {
				    $this->options->$name = $val;
				    $message .= "<li>\"".$title."\" ".__("was updated", "rrze-tos")."</li>";
				}
			}
		    }
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
            <h1><?php echo __('Rechtliche Pflichtangaben bearbeiten', 'rrze-tos'); ?></h1>
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
	   <?php $this->addEndpointInfo($currentTab); ?>
            <form method="post" action="options.php" id="tos-admin-form">
                <?php settings_fields('rrze_tos_options'); ?>
                <?php do_settings_sections('rrze_tos_options'); ?>
                <p class="submit">
                    <?php submit_button(esc_html__('Save Changes', 'rrze-tos'), 'primary', 'rrze-tos-submit', false); ?>
                </p>
            </form>    
        </div>
        <?php
	
	echo "<pre>";
	var_dump($this->options); 
	echo "</pre>";
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
    public function addEndpointInfo($fieldset = 'imprint') {
	$endpoints = Options::getEndPoints();
	$url = home_url($endpoints[$fieldset]);

	echo "<p><em>";
	echo __('Die Informationen dieser Seite werden unter folgender Adresse abrufbar:','rrze-tos');
	echo ' <a href="'.$url.'">'.$url.'</a></em></p>';
	
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
		    $min = $max = $step = '';
		    $addbreak = false;
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
		     if (isset($fielddata['addbreak'])) {
			  $addbreak = $fielddata['addbreak'];
		    }
		     if (isset($fielddata['min'])) {
			  $min = $fielddata['min'];
		    }
		     if (isset($fielddata['max'])) {
			  $max = $fielddata['max'];
		    }
		     if (isset($fielddata['step'])) {
			  $step = $fielddata['step'];
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
				'min'		=> $min,
				'max'		=> $max,
				'addbreak'	=> $addbreak,
				'step'		 => $step
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
    /* Callback: Generisches Texteingabefeld
    /*-----------------------------------------------------------------------------------*/
    public function inputURLCallback($args) {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

       
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = $args['description'];
        } elseif (array_key_exists('desc', $args)) {
	    $description = $args['desc'];
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
            type="url" placeholder="https://" class="<?php echo isset($class) ? esc_attr($class) : 'regular-text'; ?>"
            value="<?php echo isset($oldval) ? $oldval : ''; ?>">

        <br>
        <?php if (isset($description)) :
            $description = is_array($description) ? implode('<br>', array_map('esc_attr', $description)) : esc_attr($description); ?>
            <p class="description"><?php echo make_clickable($description); ?></p>
        <?php endif;
    }
    /*-----------------------------------------------------------------------------------*/
    /* Callback: EMail
    /*-----------------------------------------------------------------------------------*/
    public function inputEMailCallback($args) {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

       
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = $args['description'];
        } elseif (array_key_exists('desc', $args)) {
	    $description = $args['desc'];
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
            type="email"  class="<?php echo isset($class) ? esc_attr($class) : 'regular-text'; ?>"
            value="<?php echo isset($oldval) ? $oldval : ''; ?>">

        <br>
        <?php if (isset($description)) :
            $description = is_array($description) ? implode('<br>', array_map('esc_attr', $description)) : esc_attr($description); ?>
            <p class="description"><?php echo make_clickable($description); ?></p>
        <?php endif;
    }
        
    /*-----------------------------------------------------------------------------------*/
    /* Callback: Generisches Eingabefeld für Datumsangaben
    /*-----------------------------------------------------------------------------------*/
    public function inputDateCallback($args) {
        if (! array_key_exists('name', $args)) {
            return;
        }
        $name = esc_attr($args['name']);

       
        if (array_key_exists('class', $args)) {
            $class = esc_attr($args['class']);
        }
        if (array_key_exists('description', $args)) {
            $description = $args['description'];
        } elseif (array_key_exists('desc', $args)) {
	    $description = $args['desc'];
	}
	
        if (array_key_exists('required', $args)) {
            $required = esc_attr($args['required']);
        }
	 if (array_key_exists('min', $args)) {
            $required = esc_attr($args['min']);
        }
	 if (array_key_exists('max', $args)) {
            $required = esc_attr($args['max']);
        }
	 if (array_key_exists('step', $args)) {
            $required = esc_attr($args['step']);
        }
        
	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
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
	
	$args = '';
	if (isset($min)) { $args .= ' min="'.esc_attr($min).'"'; }
	if (isset($max)) { $args .= ' max="'.esc_attr($max).'"'; }
	if (isset($step)) { $args .= ' step="'.esc_attr($step).'"'; }
	
	
	?>
        <input
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
            type="date" <?php if (isset($args)) { echo $args; } ?> 
            value="<?php echo isset($oldval) ? $oldval : ''; ?>">
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
	$oldval = sanitize_textarea_field($this->options->$name);
	if (isset($fieldset)) {
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	    $postname = $fieldset."-".$name;
	} else {
	    $postname = $name;
	}
	?>
        <textarea
            name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
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
	$addbreak = $args['addbreak'];
	$oldval = $this->options->$name;
	if (isset($fieldset)) {
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	    $postname = $fieldset."-".$name;
	} else {
	    $postname = $name;
	}
        $radios = [];
        if (array_key_exists('options', $args)) {
            $radios = $args['options'];
        }
        foreach ($radios as $_k => $_v) : ?>
            <label>
                <input name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
                    type="radio" value="<?php echo esc_attr($_k); ?>" 
                    <?php if (isset($oldval)): checked($oldval, $_k); endif; ?>
                >
                <?php echo esc_attr($_v); ?>	
            </label>
	   <?php if ($addbreak==true) { echo '<br>'; } 
        endforeach;
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
    }
    
      /*-----------------------------------------------------------------------------------*/
    /* Callback: Checkboxlist
    /*-----------------------------------------------------------------------------------*/  
     public function inputCheckboxListCallback($args) {
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
	
	
         $default = $args['default'];

	if (array_key_exists('fieldset', $args)) {
            $fieldset = esc_attr($args['fieldset']);
        }
	$addbreak = $args['addbreak'];
	$oldval = $this->options->$name;
	if (isset($fieldset)) {
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	    $postname = $fieldset."-".$name;
	} else {
	    $postname = $name;
	}
        $radios = [];
        if (array_key_exists('options', $args)) {
            $radios = $args['options'];
        }

        foreach ($radios as $_k => $_v) : ?>
            <label>
                <input name="<?php printf('%1$s[%2$s][]', esc_attr($this->optionName), esc_attr($postname)); ?>"
		    type="checkbox" value="<?php echo esc_attr($_k); ?>" 
                    <?php if (isset($oldval)): checked( in_array( $_k, $oldval ), 1); endif; ?>
                >
                <?php echo esc_attr($_v); ?>	
            </label>
	   <?php if ($addbreak==true) { echo '<br>'; } 
        endforeach;
        if (isset($description)) : ?>
            <p class="description"><?php echo esc_attr($description); ?></p>
        <?php endif;
     }
    
    /*-----------------------------------------------------------------------------------*/
    /* Callback: SelectListe
    /*-----------------------------------------------------------------------------------*/
    public function inputSelectCallback($args) {
        $limit = [];
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
	$oldval = $this->options->$name;
	if (isset($fieldset)) {
	    if ((!isset($oldval)) && (isset($default))) {
		$oldval = $default;
	    }
	     $postname = $fieldset."-".$name;
	} else {
	     $postname = $name;
	}
        if (array_key_exists('options', $args)) {
            $limit = $args['options'];
        } ?>
        <?php if (isset($name)) {
            ?>
            <select
                name="<?php printf('%1$s[%2$s]', esc_attr($this->optionName), esc_attr($postname)); ?>"
                title="<?php __('Please select one', 'rrze-tos'); ?>">
                <?php foreach ($limit as $_k => $_v) : ?>
                    <option value="<?php echo esc_attr($_k); ?>" <?php selected($oldval, $_k); ?>><?php echo esc_attr($_v); ?></option>
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
