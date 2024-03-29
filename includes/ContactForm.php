<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class ContactForm {
    protected $error;
    protected $options;

    public function __construct() {
        $this->error = false;
        $this->options = Options::getOptions();
    }

    public function setForm() {
        $captcha = $this->generateCaptcha();
        $wp_nonce = wp_nonce_field('tos_contact_form','_wpnonce',true,false);

        $defaultData = [
            'captcha_num_1'  => mb_convert_case($captcha['num_1'], MB_CASE_TITLE, 'UTF-8'),
            'captcha_num_2'  => $captcha['num_2'],
            'captcha_result' => $captcha['result'],
            'wp_nonce'       => $wp_nonce
        ];

        if (isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'tos_contact_form')) {
            global $wp;

            $_wpnonce = $_POST['_wpnonce'];
            $transientName = $this->generateHash();

            if (isset($_POST['message_name'])) {
                $name = sanitize_text_field(wp_unslash($_POST['message_name']));
                $data['name'] = $name;
            }
            if (isset($_POST['message_email'])) {
                $email = sanitize_email(wp_unslash($_POST['message_email']));
                $data['email'] = $email;
            }
            if (isset($_POST['message_feedback'])) {
                $message = sanitize_textarea_field(wp_unslash($_POST['message_feedback']));
                $data['message'] = $message;
            }
            if (isset($_POST['message_human'])) {
                $result = sanitize_text_field(wp_unslash($_POST['message_human']));
            }
            if (isset($_POST['message_solution'])) {
                $solution = sanitize_text_field(wp_unslash($_POST['message_solution']));
            }

            $this->validateForm($name, $email, $message, $result, $solution);

            if ($this->hasError()) {
                $data = array_merge(
                    $data,
                    $defaultData,
                    $this->getError()
                );
                set_transient($transientName, $data, MINUTE_IN_SECONDS);
            } else {
                $response = $this->sendMail($name, $email, $message);
                if (! $response) {
                    $data = array_merge(
                        $data,
                        $defaultData,
                        [
                            'error_message_could_not_be_sent' => __('Die Nachricht konnte nicht gesendet werden.', 'rrze-tos')
                        ]
                    );
                } else {
                    $data = array_merge(
                        $defaultData,
                        [
                            'message_has_been_sent_successfully' => __('Danke für Ihre Nachricht. Sie wurde erfolgreich gesendet.', 'rrze-tos')
                        ]
                    );
                }
                set_transient($transientName, $data, MINUTE_IN_SECONDS);
            }
            $redirectUrl = home_url(
                add_query_arg(
                    [
                        '_wpnonce'   => $_wpnonce,
                        '_transient' => $transientName
                    ],
                    $wp->request
                )
            );
            wp_redirect($redirectUrl);
            exit;
        } elseif (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'tos_contact_form')) {
            global $wp;

            $transientName = isset($_GET['_transient']) ? sanitize_key($_GET['_transient']) : '';
            $data = get_transient($transientName);
            if ($data !== false) {
                delete_transient($transientName);
                $data = array_merge(
                    $data,
                    $defaultData
                );
            } else {
                $redirectUrl = home_url(
                    add_query_arg(
                        [],
                        $wp->request
                    )
                );
                wp_redirect($redirectUrl);
                exit;
            }
        } else {
            $data = $defaultData;
        }

        return Template::getContent('contact-form', $data);
    }

    protected function sendMail($name, $from, $message)  {
	$to = sanitize_email($this->options->accessibility_feedback_email);
        $subject = sanitize_text_field($this->options->accessibility_feedback_subject);
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            sprintf('Reply-To: %1$s <%2$s>', sanitize_text_field($name), sanitize_text_field($from))
        ];
        if ($this->options->accessibility_feedback_cc) {
            $headers[] = sprintf('CC: <%s>', sanitize_email($this->options->accessibility_feedback_cc));
        }
	
	$pretext = __('Die folgende Nachricht wurde im Feedback-Formular zur Barrierefreiheit eingegeben.', 'rrze-tos')." \n\n";
	$pretext .= __('Absender:', 'rrze-tos')." \n";
	$pretext .= __('   Eingegebener Name:', 'rrze-tos').'          '.sanitize_text_field($name)." \n";
	$pretext .= __('   Eingegebene E-Mail-Adresse:', 'rrze-tos').' '.sanitize_email($from)." \n";	
	
	
	if (isset( $_SERVER['HTTP_USER_AGENT'] )) {
	    $pretext .= __('   Verwendeter User-Agent: ', 'rrze-tos').'    '.sanitize_text_field($_SERVER['HTTP_USER_AGENT'])." \n";	    
	}

	$pretext .= __('   Absendezeit:', 'rrze-tos').'                '.date("d.m.Y - H:i")." \n";
	$pretext .= __('   Formular-Website:', 'rrze-tos').'           '.get_option('siteurl')." \n\n";
	$pretext .= __('Vom Absender eingegebene Nachricht:', 'rrze-tos')." \n\n";
	
	
	$message = $pretext .$message; 
	    
	
	$message .= "\n\n-- \n";
	$message .= __('Website aufrufen:', 'rrze-tos').' '.get_option('siteurl')." \n";
	$message .= __('Dashboard:', 'rrze-tos').'        '.get_option('siteurl')."/wp-admin/ \n";
	
        return wp_mail($to, $subject, $message, $headers);
    }

    protected function validateForm($name, $email, $message, $result, $solution)
    {
        if (empty($name)) {
            $this->error['error_name'] = __('Bitte geben Sie einen Namen an', 'rrze-tos');
        }
        if (empty($email)) {
            $this->error['error_email'] = __('Die Feld der E-Mail-Adresse muss ausgefüllt sein und eine korrekt geschriebene E-Mailadresse enthalten.', 'rrze-tos');
        } elseif (! is_email($email)) {
            $this->error['error_email'] = __('Die angegebene E-Mailadresse ist nicht korrekt.', 'rrze-tos');
        }
        if (empty($message)) {
            $this->error['error_message'] = __('Bitte geben Sie einen Text an.', 'rrze-tos');
        }
        if (empty($result)) {
            $this->error['error_captcha'] = __('Bitte geben Sie eine Zahl als Lösung ein.', 'rrze-tos');
        } elseif ($result !== $solution) {
            $this->error['error_captcha'] = __('Die eingegebene Zahl ist falsch.', 'rrze-tos');
        }
    }

    protected function hasError() {
        return $this->error !== false ? true : false;
    }

    protected function getError()  {
        return $this->error;
    }

    protected function messageResponse($type, $message) {
        global $form_error;
        if ('success' === $type) {
            echo '<div class="alert alert-success">' . esc_html($message) . '</div>';
        } else {
            if ($_POST && $form_error instanceof \WP_Error && is_wp_error($message)) {
                foreach ($form_error->get_error_messages() as $error) {
                    echo '<div class="alert alert-warning" role="alert">';
                    echo '<strong>'.__('Fehler','rrze-tos').'</strong>:';
                    echo esc_html($error) . '<br/>';
                    echo '</div>';
                }
            }
        }
    }

    protected function generateHash() {
        return sprintf(
            '%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000
        );
    }

    protected function generateCaptcha()  {
        $numbers = [
            __('Null', 'rrze-tos'),
            __('Eins', 'rrze-tos'),
            __('Zwei', 'rrze-tos'),
            __('Drei', 'rrze-tos'),
            __('Vier', 'rrze-tos'),
            __('Fünf', 'rrze-tos'),
            __('Sechs', 'rrze-tos'),
            __('Sieben', 'rrze-tos'),
            __('Acht', 'rrze-tos'),
            __('Neun', 'rrze-tos')
        ];

        $num_1 = wp_rand(2, 6);
        $num_2 = wp_rand(2, 6);
        $result = $num_1 * $num_2;

        return [
            'num_1' => $numbers[$num_1],
            'num_2' => $numbers[$num_2],
            'result' => $result
        ];
    }
}
