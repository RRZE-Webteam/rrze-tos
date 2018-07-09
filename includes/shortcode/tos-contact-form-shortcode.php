<?php
/**
 * WordPress TOS contact form
 *
 * @package WordPress
 * @subpackage TOS
 * @since 3.4.0
 */

namespace RRZE\Tos {

	add_shortcode( 'contact', 'RRZE\Tos\contact_shortcode' );

	/**
	 *
	 * @param $atts
	 */
	function contact_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'field-name'   => '',
				'field-email'   => '',
				'field-feedback' => '',
				'field-captcha'  => '',
				'field-answer'  => '',
				'field-timeout'   => '',
				'timeout'     => 3,
			), $atts );

		$filter = array_filter( $atts );

		foreach ( $filter as $key => $value ) {
			$fields[] = explode( ",", $value );
		}

		/*echo '<pre>';
		print_r($fields);
		echo '</pre>';*/

		return generate_form( $fields );
	}

	/**
	 *
	 * @param $values
	 */
	function generate_form( $values ) {

		$salt          = get_salt();
		$captcha_array = getCaptcha();
		$encrypted     = md5( $captcha_array['task_encrypted'] . $salt );
		$flag          = 0;

		$fields  = $values;
		$timeout = array_pop( $fields );

		if ( isset( $_POST['submit'] ) ) {

			$values = assign_post_values( $_POST );

			/*echo '<pre>';
			print_r($values);
			echo '</pre>';*/

			$current_time = time();
			$submitted    = $values['timeout'] + $timeout[0];

			$hasErrors = check_errors( $values );
			$ans       = $values['captcha'];
			$checksum  = md5( $ans );
			$salted    = md5( $checksum . $salt );
			$clean     = array_filter( $hasErrors );

			if ( isset( $clean ) && ! count( $clean ) ) {
				if ( $current_time < $submitted ) {
					_e( 'Your are a bot!', 'rrze-tos' );
				} elseif ( $salted === $values['answer'] ) {
					echo '<h2>' . __( 'Many Thanks! We will contact you immediately!', 'rrze-tos' ) . '</h2>';
					send_mail( $values['feedback'], $values['rrze-email'], $values['rrze-name'] );
					$flag = 1;
				} else {
					$flag                 = 0;
					$hasErrors['captcha'] = __( 'Wrong solution! Try it again.', 'rrze-tos' );
				}

			}
		}

		if ( $flag == 0 ) {
			?>
			<form method="post" id="feedback_form">
				<?php
				for ( $i = 0; $i < sizeof( $fields ); $i ++ ) {
					switch ( $fields[ $i ][1] ) {
						case 'text':
							if ( $fields[ $i ][0] == 'captcha' ) { ?>
								<p>
								<?php if ( isset( $_POST['submit'] ) && isset( $hasErrors ) && array_key_exists( $fields[ $i ][0], $hasErrors ) ) { ?>
									<div class="error"><?php echo $hasErrors[ $fields[ $i ][0] ] ?></div>
								<?php } ?>
								<label for="check"><?php _e( 'Solve the following task:', 'rrze-tos' ) ?></label><br/>
								<?php echo $captcha_array['task_string'] . ' ' ?><input type="text" name="captcha"
								                                                        id="check">
								</p>
							<?php } else { ?>
								<p>
								<?php if ( isset( $_POST['submit'] ) && isset( $hasErrors ) && array_key_exists( $fields[ $i ][3], $hasErrors ) ) { ?>
									<div class="error"><?php echo $hasErrors[ $fields[ $i ][3] ] ?></div>
								<?php } ?>
								<label
									for=<?php echo $fields[ $i ][2] ?>><?php echo( $fields[ $i ][0] == 'email' ? 'E-Mail' : ucfirst( $fields[ $i ][0] ) ) ?>
									:</label><br/>
								<input type="text" name=<?php echo $fields[ $i ][3] ?> id=<?php echo $fields[ $i ][2] ?>
								       placeholder=<?php echo( $fields[ $i ][0] == 'email' ? 'E-Mail' : ucfirst( $fields[ $i ][0] ) ) ?> value=<?php echo ( isset( $_POST['submit'] ) ) ? $values[ $fields[ $i ][3] ] : '' ?> >
								</p><?php }
							break;
						case 'textarea': ?>
							<p>
							<?php if ( isset( $_POST['submit'] ) && isset( $hasErrors ) && array_key_exists( $fields[ $i ][0], $hasErrors ) ) { ?>
								<div class="error"><?php echo $hasErrors[ $fields[ $i ][0] ] ?></div>
							<?php } ?>
							<label for=<?php echo $fields[ $i ][2] ?>><?php echo ucfirst( $fields[ $i ][0] ) ?>:</label>
							<textarea name=<?php echo $fields[ $i ][0] ?>  id=<?php echo $fields[ $i ][2] ?> cols="150"
							          rows="10"><?php echo ( isset( $_POST['submit'] ) ) ? $values[ $fields[ $i ][0] ] : '' ?></textarea>
							</p><?php break;
						case 'hidden':
							if ( $fields[ $i ][0] == 'answer' ) { ?>
								<p>
								<input type="hidden" class="form-control"
								       name=<?php echo $fields[ $i ][0] . '[]' ?> value=<?php echo $encrypted ?>>
								</p><?php
							} else { ?>
								<p>
								<input type="hidden" class="form-control"
								       name=<?php echo $fields[ $i ][0] ?> value=<?php echo time() ?>>
								</p><?php break;
							}
					}
				} ?>
				<input type="submit" class="submit_tos_form" name="submit" form="feedback_form" value="Senden">
			</form>
			<?php
		}
	}

	/**
	 *
	 * @param $post
	 *
	 * @return mixed
	 */
	function assign_post_values( $post ) {

		foreach ( $post as $key => $value ) {
			if ( $key != 'answer' ) {
				$a[ $key ] = strip_tags( htmlspecialchars( $value ) );
			} else {
				$a[ $key ] = strip_tags( htmlspecialchars( $value[0] ) );
			}
		}

		return $a;
	}

	/**
	 *
	 * @param $values
	 *
	 * @return mixed
	 */
	function check_errors( $values ) {
		foreach ( $values as $key1 => $value1 ) {
			if ( $value1 === '' ) {
				if ( preg_match( '/email/', $key1 ) ) {
					$hasErrors[ $key1 ] = __( 'Please enter e-mail', 'rrze-tos' );
				} elseif ( preg_match( '/name/', $key1 ) ) {
					$hasErrors[ $key1 ] = __( 'Please enter name', 'rrze-tos' );
				} else {
					$hasErrors[ $key1 ] = __( 'Please enter ', 'rrze-tos' ) . ucfirst( $key1 );
				}
			} elseif ( preg_match( '/email/', $key1 ) && ! filter_var( $value1, FILTER_VALIDATE_EMAIL ) ) {
				$hasErrors[ $key1 ] = __( 'Wrong e-mail format.', 'rrze-tos' );
			} elseif ( $key1 == 'captcha' && ! preg_match( '/^[0-9]{1,2}$/', $_POST['captcha'] ) ) {
				$hasErrors[ $key1 ] = __( 'You can enter a maximum of two digits.', 'rrze-tos' );
			} else {
				$hasErrors['error'] = '';
			}
		}

		return $hasErrors;
	}

	/**
	 *
	 * @param $feedback
	 * @param $from
	 * @param $name
	 */
	function send_mail( $feedback, $from, $name ) {

		$values = get_option( 'rrze_tos' );

		if ( ! $values ) {

			$status_code = check_wmp();

			if ( 200 === $status_code ) {
				$res = get_json_wmp();
			}

		}

		/*$to = (!empty($values['rrze_tos_field_receiver_email']) ? $values['rrze_tos_field_receiver_email'] : $res['metadata']['webmaster']['email']);
		$subject = $values['rrze_tos_field_subject'];
		$message = $feedback;
		$headers[] = "From: <$from>";
		$cc = (!empty($values['rrze_tos_field_cc']) ? $values['rrze_tos_field_cc'] : '');
		if(!empty($cc)) {
			$headers[] = "CC: <$cc>";
		}

		wp_mail( $to, $subject, $message, $headers );*/

		$to        = ( ! empty( $values['rrze_tos_field_receiver_email'] ) ? $values['rrze_tos_field_receiver_email'] : $res['metadata']['webmaster']['email'] );
		$subject   = $values['rrze_tos_field_subject'];
		$message   = $feedback;
		$headers[] = "From: $name <$from>";
		$cc        = ( ! empty( $values['rrze_tos_field_cc'] ) ? $values['rrze_tos_field_cc'] : '' );
		$cc_addr   = explode( ",", $cc );
		if ( ! empty( $cc ) ) {
			foreach ( $cc_addr as $cc => $value ) {
				$headers[] = "CC: <$value>";
			}
		}

		wp_mail( $to, $subject, $message, $headers );

	}
}