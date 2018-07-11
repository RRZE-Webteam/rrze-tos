<?php
/**
 * WordPress TOS Class
 *
 * @package WordPress
 * @subpackage TOS
 * @since 3.4.0
 */

namespace RRZE\Tos {

	defined( 'ABSPATH' ) || exit;

	/**
	 * Class Settings
	 * @package RRZE\Tos
	 */
	class Settings {

		/*
		 * Main-Klasse
		 * object
		 */
		protected $main;

		/**
		 * @var
		 */
		protected $option_name;

		/**
		 * @var
		 */
		protected $options;

		/*
		 * "Screen ID" der Einstellungsseite
		 * string
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
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$status_code = check_wmp();

			if ( 200 === $status_code ) {
				$this->res = get_json_wmp();
			} else {
				$this->res = '';
			}
		}

		/*
		 * Füge eine Optionsseite in das Menü "Einstellungen" hinzu.
		 *
		 * @return void
		 */
		public function admin_settings_page() {
			$this->admin_settings_page = add_options_page( __( 'Accessibility', 'rrze-tos' ), __( 'Accessibility', 'rrze-tos' ), 'manage_options', 'rrze-tos', array(
				$this,
				'settings_page'
			) );
			add_action( 'load-' . $this->admin_settings_page, array( $this, 'admin_help_menu' ) );
		}

		/*
		 * Die Ausgabe der Optionsseite.
		 *
		 * @return void
		 */
		public function settings_page() {
			?>
			<div class="wrap">
				<h2><?php echo __( 'Settings &rsaquo; Accessible', 'rrze-tos' ); ?></h2>
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

		/*
		 * Legt die Einstellungen der Optionsseite fest.
		 *
		 * @return void
		 */
		public function admin_settings() {
			register_setting( 'rrze_tos_options', $this->option_name, array( $this, 'options_validate' ) );
			add_settings_section( 'rrze_tos_section_1', __( 'General', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );
			add_settings_field( 'rrze_tos_field_title', __( 'Title', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_title'
			), 'rrze_tos_options', 'rrze_tos_section_1' );
			add_settings_field( 'rrze_tos_field_conformity', __( 'Are the conformity conditions of the WCAG 2.0 AA fulfilled?', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_conformity'
			), 'rrze_tos_options', 'rrze_tos_section_1' );
			add_settings_field( 'rrze_tos_field_no_reason', __( 'If not, with what reason', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_no_reason'
			), 'rrze_tos_options', 'rrze_tos_section_1' );
			add_settings_section( 'rrze_tos_section_2', __( 'Responsible', 'rrze-tos' ), '__return_false', 'rrze_tos_options' );
			add_settings_field( 'rrze_tos_field_responsible_firstname', __( 'Firstname', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_firstname'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			add_settings_field( 'rrze_tos_field_responsible_lastname', __( 'Lastname', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_lastname'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			add_settings_field( 'rrze_tos_field_responsible_street', __( 'Street', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_street'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			add_settings_field( 'rrze_tos_field_responsible_city', __( 'City', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_city'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			add_settings_field( 'rrze_tos_field_responsible_phone', __( 'Phone', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_phone'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			add_settings_field( 'rrze_tos_field_responsible_email', __( 'E-Mail', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_responsible_email'
			), 'rrze_tos_options', 'rrze_tos_section_2' );
			if ( is_plugin_active( 'fau-person/fau-person.php' ) ) {
				add_settings_field( 'rrze_tos_field_responsible_ID', __( 'Person-ID', 'rrze-tos' ), array(
					$this,
					'rrze_tos_field_responsible_ID'
				), 'rrze_tos_options', 'rrze_tos_section_2' );
			}
			add_settings_section( 'rrze_tos_section_3', 'Webmaster', '__return_false', 'rrze_tos_options' );
			add_settings_field( 'rrze_tos_field_webmaster_firstname', __( 'Firstname', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_firstname'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			add_settings_field( 'rrze_tos_field_webmaster_lastname', __( 'Lastname', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_lastname'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			add_settings_field( 'rrze_tos_field_webmaster_street', __( 'Street', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_street'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			add_settings_field( 'rrze_tos_field_webmaster_city', __( 'City', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_city'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			add_settings_field( 'rrze_tos_field_webmaster_phone', __( 'Phone', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_phone'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			add_settings_field( 'rrze_tos_field_webmaster_email', __( 'E-Mail', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_webmaster_email'
			), 'rrze_tos_options', 'rrze_tos_section_3' );
			if ( is_plugin_active( 'fau-person/fau-person.php' ) ) {
				add_settings_field( 'rrze_tos_field_webmaster_ID', __( 'Person-ID', 'rrze-tos' ), array(
					$this,
					'rrze_tos_field_webmaster_ID'
				), 'rrze_tos_options', 'rrze_tos_section_3' );
			}
			add_settings_section( 'rrze_tos_section_4', 'E-Mail Settings', '__return_false', 'rrze_tos_options' );
			add_settings_field( 'rrze_tos_field_receiver_email', __( 'Receiver E-Mail', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_receiver_email'
			), 'rrze_tos_options', 'rrze_tos_section_4' );
			add_settings_field( 'rrze_tos_field_subject', __( 'Subject', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_subject'
			), 'rrze_tos_options', 'rrze_tos_section_4' );
			add_settings_field( 'rrze_tos_field_cc', __( 'CC', 'rrze-tos' ), array(
				$this,
				'rrze_tos_field_cc'
			), 'rrze_tos_options', 'rrze_tos_section_4' );
		}

		/*
		 * Validiert die Eingabe der Optionsseite.
		 *
		 * @param array $input
		 *
		 * @return array
		 */
		public function options_validate( $input ) {
			$input['rrze_tos_text'] = ! empty( $input['rrze_tos_field_title'] ) ? $input['rrze_tos_field_title'] : '';
			$input['rrze_tos_text'] = ! empty( $input['rrze_tos_field_no_reason'] ) ? $input['rrze_tos_field_no_reason'] : '';

			return $input;
		}

		/*
		 * Erstes Feld der Optionsseite
		 *
		 * @return void
		 */
		public function rrze_tos_field_title() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_title]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_title; ?>" readonly>
			<?php
		}

		public function rrze_tos_field_conformity() {
			?>
			<input type="radio" name="<?php printf( '%s[rrze_tos_field_conformity]', $this->option_name ) ?>"
			       value="1" <?php checked( 1, $this->options->rrze_tos_field_conformity, true ); ?>><?php _e( 'Yes', 'rrze-tos' ) ?>
			<input type="radio" name="<?php printf( '%s[rrze_tos_field_conformity]', $this->option_name ) ?>"
			       value="2" <?php checked( 2, $this->options->rrze_tos_field_conformity, true ); ?>><?php _e( 'No', 'rrze-tos' ) ?>
			<?php
		}

		public function rrze_tos_field_no_reason() {
			?>
			<textarea rows="8" cols="50" size="50"
			          name="<?php printf( '%s[rrze_tos_field_no_reason]', $this->option_name ); ?>"><?php echo $this->options->rrze_tos_field_no_reason; ?></textarea>
			<?php
		}

		public function rrze_tos_field_responsible_firstname() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_firstname]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_firstname; ?>" <?php echo isset( $this->res['metadata']['verantwortlich']['vorname'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_responsible_lastname() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_lastname]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_lastname; ?>" <?php echo isset( $this->res['metadata']['verantwortlich']['nachname'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_responsible_street() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_street]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_street; ?>">
			<?php
		}

		public function rrze_tos_field_responsible_city() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_city]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_city; ?>">
			<?php
		}

		public function rrze_tos_field_responsible_phone() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_phone]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_phone; ?>">
			<?php
		}

		public function rrze_tos_field_responsible_email() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_email]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_email; ?>" <?php echo isset( $this->res['metadata']['verantwortlich']['email'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_responsible_ID() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_responsible_ID]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_responsible_ID; ?>">
			<?php
		}

		public function rrze_tos_field_webmaster_firstname() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_firstname]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_firstname; ?>" <?php echo isset( $this->res['metadata']['webmaster']['vorname'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_webmaster_lastname() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_lastname]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_lastname; ?>" <?php echo isset( $this->res['metadata']['webmaster']['nachname'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_webmaster_street() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_street]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_street; ?>">
			<?php
		}

		public function rrze_tos_field_webmaster_city() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_city]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_city; ?>">
			<?php
		}

		public function rrze_tos_field_webmaster_phone() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_phone]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_phone; ?>">
			<?php
		}

		public function rrze_tos_field_webmaster_email() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_email]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_email; ?>" <?php echo isset( $this->res['metadata']['webmaster']['email'] ) ? 'readonly' : '' ?>>
			<?php
		}

		public function rrze_tos_field_webmaster_ID() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_webmaster_ID]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_webmaster_ID; ?>">
			<?php
		}

		public function rrze_tos_field_receiver_email() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_receiver_email]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_receiver_email; ?>">
			<?php
		}

		public function rrze_tos_field_subject() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_subject]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_subject; ?>">
			<?php
		}

		public function rrze_tos_field_cc() {
			?>
			<input size="50" type='text' name="<?php printf( '%s[rrze_tos_field_cc]', $this->option_name ); ?>"
			       value="<?php echo $this->options->rrze_tos_field_cc; ?>">
			<?php
		}

		/*
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