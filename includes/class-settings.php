<?php
/**
 * WordPress TOS Class
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	defined( 'ABSPATH' ) || exit;

	/**
	 * Class Settings
	 *
	 * @property string res
	 * @package RRZE\Tos
	 */
	class Settings {
		/**
		 * Main class.
		 *
		 * @var Main
		 */
		protected $main;

		/**
		 * Name of the option object.
		 *
		 * @var string
		 */
		protected $option_name;

		/**
		 * All option defined.
		 *
		 * @var array
		 */
		protected $options;

		/**
		 * "Screen ID" der Einstellungsseite.
		 *
		 * @var array
		 */
		protected $admin_settings_page;

		/**
		 * Settings constructor.
		 *
		 * @param Main $main
		 */
		public function __construct( Main $main ) {
			$this->main        = $main;
			$this->option_name = $this->main->options->get_option_name();
			$this->options     = $this->main->options->get_options();
			include_once ABSPATH . 'wp-admin/includes/plugin.php';

			$status_code = check_wmp();

			if ( 200 === $status_code ) {
				$this->res = get_json_wmp();
			} else {
				$this->res = '';
			}
		}

		/**
		 * @return array
		 */
		private static function options_page_tabs() {
			$tabs = [
				'accessibility'   => __( 'Accessibility', 'rrze-tos' ),
				'imprint'         => __( 'Imprint', 'rrze-tos' ),
				'data_protection' => __( 'Data Protection', 'rrze-tos' ),
			];

			return $tabs;
		}

		/**
		 * @param $tab
		 *
		 * @return int|null|string
		 */
		private static function current_tab( $tab ) {
			$tabs = self::options_page_tabs();
			if ( isset( $tab['tab'] ) ) {
				$current = $tab['tab'];
			} else {
				reset( $tabs );
				$current = key( $tabs );
			}

			return $current;
		}

		/**
		 * Füge eine Optionsseite in das Menü "Einstellungen" hinzu.
		 *
		 * @return void
		 */
		public function admin_settings_page() {
			$this->admin_settings_page = add_options_page( __( 'Accessibility', 'rrze-tos' ), __( 'Accessibility', 'rrze-tos' ), 'manage_options', $this->option_name,
				[
					$this,
					'settings_page',
				] );
			add_action( 'load-' . $this->admin_settings_page, array( $this, 'admin_help_menu' ) );
		}

		/**
		 * Die Ausgabe der Optionsseite.
		 *
		 * @return void
		 */
		public function settings_page() {
			$tabs    = self::options_page_tabs();
			$current = self::current_tab( $_GET );

			?>
			<div class="wrap">
				<h2><?php esc_html_e( 'Settings &rsaquo; TOS', 'rrze-tos' ); ?></h2>
				<h3 class="nav-tab-wrapper">
					<?php
					// Add tabs to settings page
					foreach ( $tabs as $tab => $name ) {
						$class = ( $tab == $current ) ? ' nav-tab-active' : '';
						echo "<a class='nav-tab$class' href='?page=$this->option_name&tab=$tab'>$name</a>";
					}
					?>
				</h3>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'rrze_tos_options' );
					do_settings_sections( 'rrze_tos_options' );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Legt die Einstellungen der Optionsseite fest.
		 *
		 * @return void
		 */
		public function admin_settings() {
			register_setting( 'rrze_tos_options', $this->option_name, array( $this, 'options_validate' ) );

			if ( isset( $_GET ) ) {
				$tab = self::current_tab( $_GET );
			}

			switch ( $tab ) {
				case 'accessibility' :
				default:
					// --------
					// Section General
					// --------
					add_settings_section( 'rrze_tos_section_general', __( 'General', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );

					add_settings_field(
						'rrze_tos_conformity', __( 'Are the conformity conditions of the WCAG 2.0 AA fulfilled?', 'rrze-tos' ),
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
									'1' => __( 'Yes', 'rrze-tos' ),
									'2' => __( 'No', 'rrze-tos' ),
								],
						]
					);
					add_settings_field( 'rrze_tos_no_reason', __( 'If not, with what reason', 'rrze-tos' ),
						[
							$this,
							'rrze_tos_textarea_callback',
						],
						'rrze_tos_options', 'rrze_tos_section_general',
						[
							'name'        => 'rrze_tos_no_reason',
							'description' => __( 'Please include all necessary details', 'rrze-tos' ),
						]
					);

					// --------
					// Section Responsible
					// --------
					add_settings_section( 'rrze_tos_section_responsible', __( 'Responsible', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );

					add_settings_field(
						'rrze_tos_responsible_firstname', __( 'First name', 'rrze-tos' ),
						[
							$this,
							'rrze_tos_textbox_callback',
						],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_firstname' ]
					);
					add_settings_field(
						'rrze_tos_responsible_lastname', __( 'Lastname', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_lastname' ]
					);
					add_settings_field(
						'rrze_tos_responsible_street', __( 'Street', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_street' ]
					);
					add_settings_field(
						'rrze_tos_responsible_city', __( 'City', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_city' ]
					);
					add_settings_field(
						'rrze_tos_responsible_phone', __( 'Phone', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_phone' ]
					);
					add_settings_field(
						'rrze_tos_responsible_email', __( 'E-Mail', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_responsible',
						[ 'name' => 'rrze_tos_responsible_email' ]
					);
					if ( is_plugin_active( 'fau-person/fau-person.php' ) ) {
						add_settings_field(
							'rrze_tos_responsible_ID', __( 'Person-ID', 'rrze-tos' ), [
							$this,
							'rrze_tos_textbox_callback'
						],
							'rrze_tos_options',
							'rrze_tos_section_responsible',
							[ 'name' => 'rrze_tos_responsible_ID' ]
						);
					}

					// --------
					// Section Webmaster
					// --------
					add_settings_section( 'rrze_tos_section_webmaster', __( 'Webmaster', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );

					add_settings_field(
						'rrze_tos_webmaster_firstname', __( 'First Name', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_firstname' ]
					);
					add_settings_field(
						'rrze_tos_webmaster_lastname', __( 'Last Name', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_lastname' ]
					);
					add_settings_field(
						'rrze_tos_webmaster_street', __( 'Street', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_street' ]
					);
					add_settings_field(
						'rrze_tos_webmaster_city', __( 'City', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_city' ]
					);
					add_settings_field(
						'rrze_tos_webmaster_phone', __( 'Phone', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_phone' ]
					);
					add_settings_field(
						'rrze_tos_webmaster_email', __( 'E-Mail', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_webmaster',
						[ 'name' => 'rrze_tos_webmaster_email' ]
					);
					if ( is_plugin_active( 'fau-person/fau-person.php' ) ) {
						add_settings_field(
							'rrze_tos_webmaster_ID', __( 'Person-ID', 'rrze-tos' ), [
							$this,
							'rrze_tos_textbox_callback'
						],
							'rrze_tos_options',
							'rrze_tos_section_webmaster',
							[ 'name' => 'rrze_tos_webmaster_ID' ]
						);
					}

					// --------
					// Section E-Mail Settings
					// --------
					add_settings_section( 'rrze_tos_section_email', __( 'E-Mail Settings', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );

					add_settings_field(
						'rrze_tos_receiver_email', __( 'Receiver E-Mail', 'rrze-tos' ), [
						$this,
						'rrze_tos_textbox_callback'
					],
						'rrze_tos_options',
						'rrze_tos_section_email',
						[ 'name' => 'rrze_tos_receiver_email' ]
					);
					add_settings_field(
						'rrze_tos_subject', __( 'Subject', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_email',
						[ 'name' => 'rrze_tos_subject' ]
					);
					add_settings_field(
						'rrze_tos_cc', __( 'CC', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_email',
						[ 'name' => 'rrze_tos_cc' ]
					);

					break;
				// --------
				// Tab imprint
				// --------
				case 'imprint':
					// --------
					// Section Editor
					// --------
					add_settings_section( 'rrze_tos_section_editor', __( 'Editor', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );
					add_settings_field(
						'rrze_tos_editor_name', __( 'Name', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_editor',
						[
							'name'        => 'rrze_tos_editor_name',
							'description' => __( 'Full name of the editor', 'rrze-tos' )
						]
					);
					add_settings_field(
						'rrze_tos_editor_street', __( 'Street', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_editor',
						[ 'name' => 'rrze_tos_editor_street', 'description' => __( 'Street Number', 'rrze-tos' ) ]
					);
					add_settings_field(
						'rrze_tos_editor_place', __( 'Place', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_editor',
						[
							'name'        => 'rrze_tos_editor_place',
							'description' => __( 'PLZ Place', 'rrze-tos' ),
						]
					);

					// --------
					// Section Content
					// --------
					add_settings_section( 'rrze_tos_section_content', __( 'Content Manager', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );
					add_settings_field(
						'rrze_tos_content_name', __( 'Name', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_content',
						[
							'name'        => 'rrze_tos_content_name',
							'description' => __( 'Full name of the content manager', 'rrze-tos' )
						]
					);
					add_settings_field(
						'rrze_tos_content_street', __( 'Street', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_content',
						[ 'name' => 'rrze_tos_content_street', 'description' => __( 'Street Number', 'rrze-tos' ) ]
					);
					add_settings_field(
						'rrze_tos_content_place', __( 'Place', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_content',
						[
							'name'        => 'rrze_tos_content_place',
							'description' => __( 'PLZ Place', 'rrze-tos' ),
						]
					);
					add_settings_field(
						'rrze_tos_content_tel', __( 'Phone', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_content',
						[
							'name'        => 'rrze_tos_content_tel',
							'description' => __( 'Direct dialing', 'rrze-tos' ),
						]
					);
					add_settings_field(
						'rrze_tos_content_fax', __( 'Fax', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_content',
						[
							'name'        => 'rrze_tos_content_fax',
							'description' => __( 'Fax number, if still available', 'rrze-tos' ),
						]
					);
					break;
				// --------
				// Tab data_protection
				// --------
				case 'data_protection':
					// --------
					// Section Content
					// --------
					add_settings_section( 'rrze_tos_section_newsletter', __( 'Newsletter', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );
					add_settings_field(
						'rrze_tos_show_newsletter', __( 'Name', 'rrze-tos' ), [ $this, 'rrze_tos_textbox_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_newsletter',
						[
							'name'        => 'rrze_tos_show_newsletter',
							'description' => __( 'Full name of the content manager', 'rrze-tos' )
						]
					);
					add_settings_field(
						'rrze_tos_show_newsletter', __( 'Do you want to show the newsletter section?', 'rrze-tos' ),
						[ $this, 'rrze_tos_radio_callback' ],
						'rrze_tos_options',
						'rrze_tos_section_newsletter',
						[
							'name'    => 'rrze_tos_show_newsletter',
							'options' =>
								[
									'1' => __( 'Yes', 'rrze-tos' ),
									'2' => __( 'No', 'rrze-tos' ),
								],
						]
					);
					break;
			}

		}

		/**
		 * Validiert die Eingabe der Optionsseite.
		 *
		 * @param array $input Each of the options to be validated.
		 *
		 * @return array
		 */
		public function options_validate( $input ) {
			$input['rrze_tos_text'] = ! empty( $input['rrze_tos_title'] ) ? $input['rrze_tos_title'] : '';
			$input['rrze_tos_text'] = ! empty( $input['rrze_tos_no_reason'] ) ? $input['rrze_tos_no_reason'] : '';

			// TODO: Check for all fields
			return $input;
		}

		/**
		 * General callback function for text input.
		 *
		 * @param array $args It contains name and description of the text field.
		 */
		public function rrze_tos_textbox_callback( $args ) {
			if ( array_key_exists( 'name', $args ) ) {
				$name = esc_attr( $args['name'] );
			}
			if ( array_key_exists( 'description', $args ) ) {
				$description = esc_attr( $args['description'] );
			}
			?>
			<?php if ( isset( $name ) ) { ?>
				<input size="50" name="<?php printf( '%s[' . $name . ']', $this->option_name ); ?>" type='text'
				       value="<?php if ( array_key_exists( $name, $this->options ) ) {
					       echo $this->options->$name;
				       } ?>">
				<br/>
				<?php if ( isset( $description ) ) { ?>
					<span class="description"><?php esc_html_e( $description ); ?></span>
					<?php
				}
			}
		}

		/**
		 * General callback function for text area input.
		 *
		 * @param array $args It contains name and description of the text area field.
		 */
		public function rrze_tos_textarea_callback( $args ) {
			if ( array_key_exists( 'name', $args ) ) {
				$name = esc_attr( $args['name'] );
			}
			if ( array_key_exists( 'description', $args ) ) {
				$description = esc_attr( $args['description'] );
			}
			?>
			<?php if ( isset( $name ) ) { ?>
				<textarea name="<?php printf( '%s[' . $name . ']', $this->option_name ); ?>" cols="50" rows="8">
					<?php
					if ( array_key_exists( $name, $this->options ) ) {
						if ( is_array( $this->options->$name ) && count( $this->options->$name ) > 0 && $this->options->$name[0] !== '' ) {
							echo implode( "\n", $this->options->$name );
						} else {
							echo $this->options->$name;
						}
					}
					?>
				</textarea><br/>
			<?php } ?>
			<?php if ( isset( $description ) ) { ?>
				<span class="description"><?php esc_html_e( $description ); ?></span>
				<?php
			}
		}

		/**
		 * General callback function for radio input.
		 *
		 * @param array $args All options only one can be selected.
		 */
		public function rrze_tos_radio_callback( $args ) {
			$radios = [];
			if ( array_key_exists( 'name', $args ) ) {
				$name = esc_attr( $args['name'] );
			}
			if ( array_key_exists( 'description', $args ) ) {
				$description = esc_attr( $args['description'] );
			}
			if ( array_key_exists( 'options', $args ) ) {
				$radios = $args['options'];
			}
			if ( isset( $name ) ) {
				foreach ( $radios as $_k => $_v ) {
					?>
					<label>
						<input name="<?php printf( '%s[' . $name . ']', $this->option_name ); ?>"
						       type='radio'
						       value='<?php print $_k; ?>'
							<?php
							if ( array_key_exists( $name, $this->options ) ) {
								checked( $this->options->$name, $_k );
							}
							?>
						>
						<?php print $_v; ?>
					</label><br/>
					<?php
				}
			}
			if ( isset( $description ) ) {
				?>
				<p class="description"><?php esc_html_e( $description ); ?></p>
				<?php
			}
		}

		/**
		 * General callback function for Select input.
		 *
		 * @param array $args All options than can be selected.
		 */
		public function cris_select_callback( $args ) {
			$limit = [];
			if ( array_key_exists( 'name', $args ) ) {
				$name = esc_attr( $args['name'] );
			}
			if ( array_key_exists( 'description', $args ) ) {
				$description = esc_attr( $args['description'] );
			}
			if ( array_key_exists( 'options', $args ) ) {
				$limit = $args['options'];
			} ?>
			<?php if ( isset( $name ) ) { ?>
				<select name="<?php printf( '%s[' . $name . ']', $this->option_name ); ?>">
					<?php foreach ( $limit as $_k => $_v ) { ?>
						<option value='<?php print $_k; ?>'
							<?php if ( array_key_exists( $name, $this->options ) ) {
								selected( $this->options->$name, $_k );
							} ?>>
							<?php print $_v; ?>
						</option>
					<?php } ?>
				</select>
			<?php } ?>
			<?php
			if ( isset( $description ) ) { ?>
				<p class="description"><?php esc_html_e( $description ); ?></p>
				<?php
			}
		}

		/**
		 * Erstellt die Kontexthilfe der Optionsseite.
		 *
		 * @return void
		 */
		public function admin_help_menu() {

			$content = array(
				'<p>' . __( 'Here comes the Context Help content.', 'rrze-tos' ) . '</p>',
			);


			$help_tab = array(
				'id'      => $this->admin_settings_page,
				'title'   => __( 'Overview', 'rrze-tos' ),
				'content' => implode( PHP_EOL, $content ),
			);

			$help_sidebar = sprintf( '<p><strong>%1$s:</strong></p><p><a href="http://blogs.fau.de/webworking">RRZE-Webworking</a></p><p><a href="https://github.com/RRZE-Webteam">%2$s</a></p>', __( 'For more information', 'rrze-tos' ), __( 'RRZE Webteam on Github', 'rrze-tos' ) );

			$screen = get_current_screen();

			if ( $screen->id != $this->admin_settings_page ) {
				return;
			}

			$screen->add_help_tab( $help_tab );

			$screen->set_help_sidebar( $help_sidebar );
		}
	}
}
