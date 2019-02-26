<?php
/**
 * WordPress TOS contact form shortcode
 *
 * @package    WordPress
 * @subpackage TOS
 * @since      3.4.0
 */

namespace RRZE\Tos {

	/**
	 * Create a front-end contact form shortcode to be used inside templates.
	 *
	 * @param array $atts list of comments.
	 *
	 * @return string
	 */
	function contact_form_shortcode( $atts ) {
		global $form_error;
		global $captcha;
		if ( ! $form_error instanceof \WP_Error ) {

		}
		global $name, $email, $message, $result, $solution;

		// Attributes!
		$atts = shortcode_atts(
			array(
				'captcha' => 'true',
			),
			$atts,
			'tos-contact-form'
		);

		$captcha = random_number();

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'tos_contact_form' ) ) {
			if ( isset( $_POST['message_name'] ) ) {
				$name = sanitize_text_field( wp_unslash( $_POST['message_name'] ) ); // input var okay!
			}
			if ( isset( $_POST['message_email'] ) ) {
				$email = sanitize_email( wp_unslash( $_POST['message_email'] ) ); // input var okay!
			}
			if ( isset( $_POST['message_feedback'] ) ) {
				$message = sanitize_textarea_field( wp_unslash( $_POST['message_feedback'] ) ); // input var okay!
			}
			if ( isset( $_POST['message_human'] ) ) {
				$result = sanitize_text_field( wp_unslash( $_POST['message_human'] ) ); // input var okay!
			}
			if ( isset( $_POST['message_solution'] ) ) {
				$solution = sanitize_text_field( wp_unslash( $_POST['message_solution'] ) ); // input var okay!
			}

			// validate the user form input.
			validate_form( $name, $email, $message, $result, $solution );

			// send the mail.
			send_mail( $name, $email, $message );
		}

		$output  = '<form method="post" class="wcag-contact-form">';
		$output .= wp_nonce_field( 'tos_contact_form' );
		$output .= '
<input type="hidden" value="'. $captcha[2] .'" name="message_solution">
  <div class="form-group">
    <label class="form-control-label" style="display: inherit;" for="message_name">'.esc_attr__( 'Name', 'rrze-tos' ).'</label>
    <input type="text" class="form-control form-control-success" name="message_name" value="' . $name . '" placeholder="'.esc_attr__( 'Enter name', 'rrze-tos' ).'">';
		if ( isset( $_POST['message_name'] ) && $form_error->get_error_message( 'empty_name' ) ) {
			$output .= '
<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>';// input var okay!
			$output .= $form_error->get_error_message( 'empty_name' );
			$output .= '</div>';
		}
		$output .= '
  </div>
    <div class="form-group">
    <label  class="form-control-label" style="display: inherit;" for="message_email">'.esc_attr__( 'Email', 'rrze-tos' ).'</label>
    <input type="text" class="form-control" name="message_email" value="' . $email . '" placeholder="' . esc_attr__( 'Enter email', 'rrze-tos' ) . '">';
		if ( isset( $_POST['message_email'] ) && $form_error->get_error_message( 'empty_email' ) ) {
			$output .= '
<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>';// input var okay!
			$output .= ! empty( $form_error->get_error_message( 'invalid_email' ) ) ? $form_error->get_error_message( 'invalid_email' ) : $form_error->get_error_message( 'empty_email' );
			$output .= '</div>';
		}
		$output .= '
  </div>
  <div class="form-group">
    <label style="display: inherit;"  for="message_name">'.esc_attr__( 'Feedback', 'rrze-tos' ).'</label>
    <textarea type="text" class="form-control" name="message_feedback" id="message_feedback" style="
    margin-bottom: 10px;">' . $message . '</textarea>';
		if ( isset( $_POST['message_feedback'] ) && $form_error->get_error_message( 'empty_message' ) ) {
			$output .= '
<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>';// input var okay!
			$output .= $form_error->get_error_message( 'empty_message' );
			$output .= '</div>';
		}
		$output .= '</div>';
		if ( 'true' === $atts['captcha'] ) {
			$output .= '<div class="form-group">
			<label for="message_human"  style="display: inherit;">' . esc_attr__( 'Verification', 'rrze-tos' ) . ': '.$captcha[0] . ' times '. $captcha[1].' </label>
			<input type="text" class="form-control form-control-success" name="message_human" id="message_human" placeholder="'.esc_attr__( 'Enter result', 'rrze-tos' ).'">';
			if ( isset( $_POST['message_human'] ) && ( $form_error->get_error_message( 'empty_result' ) || $form_error->get_error_message( 'invalid_result' ) ) ) {
				$output .= '
				<div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>';// input var okay!
				$output .= ! empty( $form_error->get_error_message( 'invalid_result' ) ) ? $form_error->get_error_message( 'invalid_result' ) : $form_error->get_error_message( 'empty_result' );
				$output .= '</div>';
			}
			$output .= '
			
			</div>';
		}
		$output .= '
  <input type="submit" class="btn">
</form>';
		return $output;
	}

	/**
	 * Validate form fields before send email.
	 *
	 * @param string $name    Name of the person who send the email.
	 * @param string $email   Email of the person to be contacted.
	 * @param string $message Feedback message.
	 *
	 * @param        $result
	 *
	 * @param        $solution
	 *
	 * @return \WP_Error
	 */
	function validate_form( $name, $email, $message, $result, $solution ) {
		$empty_name    = __( 'Name field should not be empty.', 'rrze-tos' );
		$empty_email   = __( 'Email field should not be empty.', 'rrze-tos' );
		$empty_message = __( 'Feedback field should not be empty.', 'rrze-tos' );
		$empty_result  = __( 'Result field should not be empty.', 'rrze-tos' );
		$invalid_email = __( 'Email Address Invalid.', 'rrze-tos' );
		$not_human     = __( "Human verification incorrect.", 'rrze-tos' );
		// Make the WP_Error object global.
		global $form_error;
		global $captcha;

		// instantiate the class.
		$form_error = new \WP_Error();

		// Check empty fields.
		if ( empty( $name ) ) {
			$form_error->add( 'empty_name', $empty_name );
		}
		if ( empty( $email ) ) {
			$form_error->add( 'empty_email', $empty_email );
		}
		if ( empty( $message ) ) {
			$form_error->add( 'empty_message', $empty_message );
		}
		if ( empty( $result ) ) {
			$form_error->add( 'empty_result', $empty_result );
		}
		if ( $result !== $solution ) {
			$form_error->add( 'invalid_result', $not_human );
		}

		// Check valid email.
		if ( ! is_email( $email ) ) {
			$form_error->add( 'invalid_email', $invalid_email );
		}
		return $form_error;
	}

	/**
	 * Send feedback email.
	 *
	 * @param string $name    Name of the person who send the email.
	 * @param string $email   Email of the person to be contacted.
	 * @param string $message Feedback message.
	 */
	function send_mail( $name, $email, $message ) {
		$values = (array) get_option( 'rrze_tos' );
		global $form_error;

		// Ensure WP_Error object ($form_error) contain no error.
		if ( $form_error instanceof \WP_Error && 1 > count( $form_error->get_error_messages() ) ) {

			// sanitize user form input.
			$name      = sanitize_text_field( $name );
			$email     = sanitize_email( $email );
			$subject   = sanitize_text_field( $values['rrze_tos_subject'] );
			$message   = esc_textarea( $message );
			$email_to  = $values['rrze_tos_receiver_email'];
			$headers[] = "From: $name <$email>";
			if ( ! empty( $values['rrze_tos_cc'] ) ) {
				$email_cc  = sanitize_email( $values['rrze_tos_cc'] );
				$headers[] = "CC: <$email_cc>";
			}

			// If email has been process for sending, display a success message.
			if ( wp_mail( $email_to, $subject, $message, $headers ) ) {
				global $name, $email, $message;
				$name    = '';
				$email   = '';
				$message = '';
			}
		}
	}

	/**
	 * Create a message to the user.
	 *
	 * @param string        $type    It defines which message to show success/error.
	 * @param string|Object $message An specific string message or an Object with array of massages.
	 */
	function my_contact_form_generate_response( $type, $message ) {
		global $form_error;
		if ( 'success' === $type ) {
			echo '<div class="alert alert-success">' . esc_html( $message ) . '</div>';
		} else {
			if ( $_POST && $form_error instanceof \WP_Error && is_wp_error( $message ) ) { // input var okay!
				foreach ( $form_error->get_error_messages() as $error ) {
					echo '<div class="alert alert-warning" role="alert">';
					echo '<strong>ERROR</strong>:';
					echo esc_html( $error ) . '<br/>';
					echo '</div>';
				}
			}
		}
	}

	// Passing error to template.
	if ( isset( $_POST['message_name'] ) ) {
		$name     = isset( $_POST['message_name'] ) ? sanitize_text_field( wp_unslash( $_POST['message_name'] ) ) : ''; // input var okay!
		$email    = isset( $_POST['message_email'] ) ? sanitize_email( wp_unslash( $_POST['message_email'] ) ) : ''; // input var okay!
		$message  = isset( $_POST['message_feedback'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message_feedback'] ) ) : ''; // input var okay!
		$result   = isset( $_POST['message_human'] ) ? sanitize_text_field( wp_unslash( $_POST['message_human'] ) ) : ''; // input var okay!
		$solution = isset( $_POST['message_solution'] ) ? sanitize_text_field( wp_unslash( $_POST['message_solution'] ) ) : ''; // input var okay!

		$GLOBALS['tos_erros'] = validate_form( $name, $email, $message, $result, $solution);// input var okay!
	}

	/**
	 * @return array
	 */
	function random_number() {
		$numbers = [
			__( 'zero', 'rrze-tos' ),
			__( 'one', 'rrze-tos' ),
			__( 'two', 'rrze-tos' ),
			__( 'three', 'rrze-tos' ),
			__( 'four', 'rrze-tos' ),
			__( 'five', 'rrze-tos' ),
			__( 'six', 'rrze-tos' ),
			__( 'seven', 'rrze-tos' ),
			__( 'eight', 'rrze-tos' ),
			__( 'nine', 'rrze-tos' ),
		];

		$num_1      = mt_rand( 1, 9 );
		$num_2      = mt_rand( 1, 9 );
		$text_num_1 = $numbers[ $num_1 ];
		$result     = $num_1 * $num_2;

		return [ $text_num_1, $num_2, $result ];
	}

	add_shortcode( 'tos-contact-form', 'RRZE\Tos\contact_form_shortcode' );
}
