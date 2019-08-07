<?php

namespace RRZE\Tos;

defined('ABSPATH') || exit;

class ContactForm
{
    protected $error;

    protected $options;

    public function __construct()
    {
        $this->error = false;
        $this->options = Options::getOptions();
    }

    public function setForm()
    {
        $captcha = $this->generateCaptcha();
        $wp_nonce = wp_nonce_field('tos_contact_form');

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
                            'error_message_could_not_be_sent' => __('The message could not be sent.', 'rrze-tos')
                        ]
                    );
                } else {
                    $data = array_merge(
                        $defaultData,
                        [
                            'message_has_been_sent_successfully' => __('Thank you for contacting us.', 'rrze-tos')
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
            sprintf('From: %1$s <%2$s>', sanitize_text_field($name), sanitize_text_field($from))
        ];
        if ($this->options->accessibility_feedback_cc) {
            $headers[] = sprintf('CC: <%s>', sanitize_email($this->options->accessibility_feedback_cc));
        }
	if (isset($this->options->accessibility_feedback_mailpretext)) {
	    $message = $this->options->accessibility_feedback_mailpretext . $message;
	}
	if (isset($this->options->accessibility_feedback_mailposttext)) {
	    $message = $message . $this->options->accessibility_feedback_mailposttext;
	}
        return wp_mail($to, $subject, $message, $headers);
    }

    protected function validateForm($name, $email, $message, $result, $solution)
    {
        if (empty($name)) {
            $this->error['error_name'] = __('Name field should not be empty.', 'rrze-tos');
        }
        if (empty($email)) {
            $this->error['error_email'] = __('Email field should not be empty.', 'rrze-tos');
        } elseif (! is_email($email)) {
            $this->error['error_email'] = __('Email Address Invalid.', 'rrze-tos');
        }
        if (empty($message)) {
            $this->error['error_message'] = __('Message field should not be empty.', 'rrze-tos');
        }
        if (empty($result)) {
            $this->error['error_captcha'] = __('Result field should not be empty.', 'rrze-tos');
        } elseif ($result !== $solution) {
            $this->error['error_captcha'] = __('Human verification incorrect.', 'rrze-tos');
        }
    }

    protected function hasError()
    {
        return $this->error !== false ? true : false;
    }

    protected function getError()
    {
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
                    echo '<strong>'.__('Error','rrze-tos').'</strong>:';
                    echo esc_html($error) . '<br/>';
                    echo '</div>';
                }
            }
        }
    }

    protected function generateHash()
    {
        return sprintf(
            '%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000
        );
    }

    protected function generateCaptcha()
    {
        $numbers = [
            __('zero', 'rrze-tos'),
            __('one', 'rrze-tos'),
            __('two', 'rrze-tos'),
            __('three', 'rrze-tos'),
            __('four', 'rrze-tos'),
            __('five', 'rrze-tos'),
            __('six', 'rrze-tos'),
            __('seven', 'rrze-tos'),
            __('eight', 'rrze-tos'),
            __('nine', 'rrze-tos')
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
