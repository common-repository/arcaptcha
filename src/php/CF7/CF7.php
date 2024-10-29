<?php
/**
 * CF7 form class file.
 *
 * @package arcaptcha-wp
 */

namespace ARCaptcha\CF7;

use WPCF7_Submission;
use WPCF7_Validation;

/**
 * Class CF7.
 */
class CF7
{

    /**
     * Content has cf7-arcaptcha shortcode flag.
     *
     * @var boolean
     */
    private $has_shortcode = false;

    /**
     * CF7 constructor.
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Init hooks.
     */
    public function init_hooks()
    {
        add_filter('wpcf7_form_elements', [$this, 'wpcf7_form_elements']);
        add_shortcode('cf7-arcaptcha', [$this, 'cf7_arcaptcha_shortcode']);
        add_filter('wpcf7_validate', [$this, 'verify_arcaptcha'], 20, 2);
        add_action('wp_print_footer_scripts', [$this, 'enqueue_scrips'], 9);
    }

    /**
     * Add CF7 form element.
     *
     * @param mixed $form CF7 form.
     *
     * @return string
     */
    public function wpcf7_form_elements($form)
    {

        /*
         * The quickest and easiest way to add the arcaptcha shortcode if it's not added in the CF7 form fields.
         */
        if (strpos($form, '[cf7-arcaptcha]') === false) {
            $form = str_replace('<input type="submit"', '[cf7-arcaptcha]<br><input type="submit"', $form);
        }
        $form = do_shortcode($form);

        return $form;
    }

    /**
     * CF7 ARCaptcha shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */
    public function cf7_arcaptcha_shortcode($atts)
    {
        $arcaptcha_api_key = get_option('arcaptcha_api_key');
        $arcaptcha_theme = get_option('arcaptcha_theme');
        $arcaptcha_size = get_option('arcaptcha_size');
        $this->has_shortcode = true;

        return (
            '<span class="wpcf7-form-control-wrap arcap_cf7-arcaptcha-invalid">' .
            '<span id="' . uniqid('arcap_cf7-', true) .
            '" class="wpcf7-form-control arcaptcha arcap_cf7-arcaptcha" data-site-key="' . esc_html($arcaptcha_api_key) .
            '" data-theme="' . esc_html($arcaptcha_theme) .
            '" data-size="' . esc_html($arcaptcha_size) . '">' .
            '</span>' .
            '</span>' .
            wp_nonce_field('arcaptcha_contact_form7', 'arcaptcha_contact_form7', true, false)
        );
    }

    /**
     * Verify CF7 recaptcha.
     *
     * @param WPCF7_Validation $result Result.
     *
     * @return mixed
     */
    public function verify_arcaptcha($result)
    {
        // As of CF7 5.1.3, NONCE validation always fails. Returning to false value shows the error, found in issue #12
        // if (!isset($_POST['arcaptcha_contact_form7_nonce']) || (isset($_POST['arcaptcha_contact_form7_nonce']) && !wp_verify_nonce($_POST['arcaptcha_contact_form7'], 'arcaptcha_contact_form7'))) {
        // return false;
        // }
        //
        // CF7 author's comments: "any good effect expected from a nonce is limited when it is used for a publicly-open contact form that anyone can submit,
        // and undesirable side effects have been seen in some cases.”
        //
        // Our comments: ARCaptcha passcodes are one-time use, so effectively serve as a nonce anyway.

        $submission = WPCF7_Submission::get_instance();
        if (null === $submission) {
            return $result;
        }

        $data = $submission->get_posted_data();
        $wpcf7_id = filter_var(
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            isset($_POST['_wpcf7']) ? wp_unslash($_POST['_wpcf7']) : 0,
            FILTER_VALIDATE_INT
        );

        if (empty($wpcf7_id)) {
            return $result;
        }

        $cf7_text = do_shortcode('[contact-form-7 id="' . $wpcf7_id . '"]');
        $arcaptcha_api_key = get_option('arcaptcha_api_key');
        if (empty($arcaptcha_api_key) || false === strpos($cf7_text, $arcaptcha_api_key)) {
            return $result;
        }

        if (empty($data['arcaptcha-token'])) {
            $result->invalidate(
                [
                    'type' => 'captcha',
                    'name' => 'arcap_cf7-arcaptcha-invalid'
                ],
                __('Please complete the captcha.', 'arcaptcha-for-forms-and-more')
            );
        } else {
            $captcha_result = arcaptcha_request_verify($data['arcaptcha-token']);
            if ('fail' === $captcha_result) {
                $result->invalidate(
                    [
                        'type' => 'captcha',
                        'name' => 'arcap_cf7-arcaptcha-invalid'
                    ],
                    __('The Captcha is invalid.', 'arcaptcha-for-forms-and-more')
                );
            }
        }

        return $result;
    }

    /**
     * Enqueue CF7 scripts.
     */
    public function enqueue_scrips()
    {
        if (!$this->has_shortcode) {
            return;
        }

        wp_enqueue_script(
            'cf7-arcaptcha',
            ARCAPTCHA_URL . '/assets/js/cf7.js',
            [],
            ARCAPTCHA_VERSION,
            true
        );
    }
}
