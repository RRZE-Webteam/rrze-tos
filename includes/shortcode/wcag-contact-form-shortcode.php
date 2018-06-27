<?php
/**
 * DOC
 *
 * @package WordPress
 */

namespace RRZE\Wcag;

add_shortcode( 'contact', 'RRZE\WCag\contact_shortcode' );

/**
 * Shortcode formular.
 *
 * @param array $atts Atributes.
 */
function contact_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'field-one'   => '',
			'field-two'   => '',
			'field-three' => '',
			'field-four'  => '',
			'field-five'  => '',
			'field-six'   => '',
			'timeout'     => 3,
		),
		$atts
	);

	$filter = array_filter( $atts );

	foreach ( $filter as $key => $value ) {
		$fields[] = explode( ',', $value );
	}
	return generate_form( $fields );
}

/**
 * Generate form.
 *
 * @param array $values Used to create form.
 */
function generate_form( $values )
{

	$salt          = get_salt();
	$captcha_array = get_captcha();
	$encrypted     = md5( $captcha_array['task_encrypted'] . $salt );
	$flag          = 0;

	$fields  = $values;
	$timeout = array_pop( $fields );

	if (isset( $_POST['submit'] ) ) {

		$values = assign_post_values($_POST);
		$current_time = time();
		$submitted    = $values['timeout'] + $timeout[0];

		$has_errors = check_errors( $values );
		$ans        = $values['captcha'];
		$checksum   = md5( $ans );
		$salted     = md5( $checksum . $salt );
		$clean      = array_filter( $has_errors );

		if ( isset( $clean ) && ! count( $clean ) ) {
			if ( $current_time < $submitted ) {
				esc_html_e('Your are a bot!', 'rrze-wcag');
			} elseif ( $salted === $values['answer'] ) {
				echo '<h2>' . esc_html__( 'Many Thanks! We will contact you immediately!', 'rrze-wcag' ) . '</h2>';
				send_mail( $values['feedback'], $values['rrze-email'], $values['rrze-name'] );
				$flag = 1;
			} else {
				$flag                  = 0;
				$has_errors['captcha'] = __( 'Wrong solution! Try it again.', 'rrze-wcag' );
			}
		}
	}

	if ( 0 === $flag ) {
		?>
		<form method="post" id="feedback_form">
			<?php
			for ( $i = 0; $i < count( $fields ); $i ++ ) {
				switch ( $fields[ $i ][1] ) {
					case 'text':
						if ( 'captcha' === $fields[ $i ][0] ) {
							?>
							<p>
							<?php if ( isset( $_POST['submit'] ) && isset( $has_errors ) && array_key_exists( $fields[ $i ][0], $has_errors ) ) { ?>
								<div class="error"><?php esc_html( $has_errors[ $fields[ $i ][0] ] ); ?></div>
							<?php } ?>
							<label for="check"><?php esc_html_e( 'Solve the following task:', 'rrze-wcag' ); ?></label>
							<br/>
							<?php esc_html( $captcha_array['task_string'] . ' ' ); ?><input type="text" name="captcha"  id="check">
							</p>
						<?php } else { ?>
							<p>
							<?php if ( isset($_POST['submit'] ) && isset( $has_errors ) && array_key_exists( $fields[ $i ][3], $has_errors ) ) { ?>
								<div class="error"><?php esc_html( $has_errors[ $fields[ $i ][3] ] ); ?></div>
							<?php } ?>
							<label
								for=<?php esc_html( $fields[ $i ][2] ); ?>><?php esc_html( 'email' === $fields[ $i ][0] ? 'E-Mail' : ucfirst($fields[ $i ][0] ) ); ?>
								:</label><br/>
							<input type="text" name=<?php esc_html( $fields[ $i ][3] ); ?> id=<?php esc_html( $fields[ $i ][2] ); ?>
							       placeholder=<?php esc_html( 'email' === $fields[ $i ][0] ? 'E-Mail' : ucfirst( $fields[ $i ][0] ) ); ?> value=<?php esc_html( isset( $_POST['submit'] ) ? $values[ $fields[ $i ][3] ] : '' ); ?> >
							</p><?php }
						break;
					case 'textarea':
						?>
						<p>
						<?php if ( isset( $_POST['submit'] ) && isset( $has_errors ) && array_key_exists( $fields[ $i ][0], $has_errors ) ) { ?>
						<div class="error"><?php esc_html( $has_errors[ $fields[ $i ][0] ] ); ?></div>
					<?php } ?>
						<label for=<?php esc_html( $fields[ $i ][2] ); ?>><?php esc_html( ucfirst( $fields[ $i ][0] ) ); ?>:</label>
						<textarea name=<?php esc_html( $fields[ $i ][0] ); ?>  id=<?php esc_html( $fields[ $i ][2] ); ?> cols="150"
						          rows="10"><?php esc_html( ( isset( $_POST['submit'] ) ) ? $values[ $fields[ $i ][0] ] : '' ); ?></textarea>
						</p>
						<?php break;
					case 'hidden':
						if ( 'answer' === $fields[ $i ][0] ) {
							?>
							<p>
								<input type="hidden" class="form-control"
								       name=<?php esc_html( $fields[ $i ][0] . '[]' ); ?> value=<?php esc_html( $encrypted ); ?>>
							</p>
							<?php
						} else {
							?>
							<p>
								<input type="hidden" class="form-control"
								       name=<?php esc_html( $fields[ $i ][0] ); ?> value=<?php esc_html( time() ); ?>>
							</p>
							<?php
							break;
						}
				}
			}
			?>
			<input type="submit" class="submit_wcag_form" name="submit" form="feedback_form" value="Senden">
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
		if ( 'answer' !== $key ) {
			$array[ $key ] = strip_tags( htmlspecialchars( $value) );
		} else {
			$array[ $key ] = strip_tags( htmlspecialchars( $value[0] ) );
		}
	}

	return $array;
}

/**
 *
 * @param $array
 *
 * @return mixed
 */
function check_errors( $array )
{
	foreach ( $array as $key1 => $value1 ) {
		if ($value1 === '' ) {
			if (preg_match('/email/', $key1) ) {
				$hasErrors[ $key1 ] = __('Please enter e-mail', 'rrze-wcag');
			} elseif (preg_match('/name/', $key1) ) {
				$hasErrors[ $key1 ] = __('Please enter name', 'rrze-wcag');
			} else {
				$hasErrors[ $key1 ] = __('Please enter ', 'rrze-wcag') . ucfirst($key1);
			}
		} elseif (preg_match('/email/', $key1) && ! filter_var($value1, FILTER_VALIDATE_EMAIL) ) {
			$hasErrors[ $key1 ] = __('Wrong e-mail format.', 'rrze-wcag');
		} elseif ($key1 == 'captcha' && ! preg_match('/^[0-9]{1,2}$/', $_POST['captcha']) ) {
			$hasErrors[ $key1 ] = __('You can enter a maximum of two digits.', 'rrze-wcag');
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
function send_mail( $feedback, $from, $name )
{

	$values = get_option('rrze_wcag');

	if (! $values ) {

		$status_code = check_wmp();

		if (200 === $status_code ) {
			$res = get_json_wmp();
		}

	}

	/*$to = (!empty($values['rrze_wcag_field_18']) ? $values['rrze_wcag_field_18'] : $res['metadata']['webmaster']['email']);
	$subject = $values['rrze_wcag_field_19'];
	$message = $feedback;
	$headers[] = "From: <$from>";
	$cc = (!empty($values['rrze_wcag_field_20']) ? $values['rrze_wcag_field_20'] : '');
	if(!empty($cc)) {
	$headers[] = "CC: <$cc>";
	}

	wp_mail( $to, $subject, $message, $headers );*/

	$to        = ( ! empty($values['rrze_wcag_field_18']) ? $values['rrze_wcag_field_18'] : $res['metadata']['webmaster']['email'] );
	$subject   = $values['rrze_wcag_field_19'];
	$message   = $feedback;
	$headers[] = "From: $name <$from>";
	$cc        = ( ! empty($values['rrze_wcag_field_20']) ? $values['rrze_wcag_field_20'] : '' );
	$cc_addr   = explode(",", $cc);
	if (! empty($cc) ) {
		foreach ( $cc_addr as $cc => $value ) {
			$headers[] = "CC: <$value>";
		}
	}

	wp_mail($to, $subject, $message, $headers);

}