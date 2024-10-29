<?php
/**
 * Plugin ARCaptcha
 *
 * @package              arcaptcha-wp
 * @author               ARCaptcha
 * @license              GPL-2.0-or-later
 * @wordpress-plugin
 *
 * Plugin Name:          آرکپچا - محافظ فرم‌ها و صفحات
 * Plugin URI:           https://arcaptcha.ir/
 * Description:          آرکپچا یک سرویس محافظ بات است که از فرم‌ها، صفحات، اطلاعات مهم، کاربران و مشتریان وب‌سایت شما، در برابر حملات تصاحب حساب، سرقت اطلاعات، اسپم و دیگر حملات مبتنی بر بات، محافظت می‌کند
 * Version:              1.4
 * Requires at least:    4.4
 * Requires PHP:         5.6
 * Author:               arcaptcha
 * Author URI:           https://arcaptcha.ir
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          arcaptcha
 * Domain Path:          /languages/
 *
 * WC requires at least: 3.0
 * WC tested up to:      6.2.2
 */

// If this file is called directly, abort.
use ARCaptcha\CF7\CF7;

if (!defined('ABSPATH')) {
    // @codeCoverageIgnoreStart
    exit;
    // @codeCoverageIgnoreEnd
}

/*
 * Plugin version.
 */
define('ARCAPTCHA_VERSION', '1.2');

/*
 * Path to the plugin dir.
 */
define('ARCAPTCHA_PATH', __DIR__);

/*
 * Plugin dir url.
 */
define('ARCAPTCHA_URL', untrailingslashit(plugin_dir_url(__FILE__)));

/*
 * Main plugin file.
 */
define('ARCAPTCHA_FILE', __FILE__);

require_once ARCAPTCHA_PATH . '/vendor/autoload.php';

// Add admin page.
if (is_admin()) {
    require 'backend/nav.php';
}

require 'common/functions.php';
require 'common/request.php';

/**
 * Load plugin modules.
 *
 * @noinspection PhpIncludeInspection
 */
function arcap_load_modules()
{
    $modules = array(

        'Login Form'                => array(
            'arcaptcha_lf_status',
            '',
            'default/login-form.php'
        ),
        'Register Form'             => array(
            'arcaptcha_rf_status',
            '',
            'default/register-form.php'
        ),
        'Comment Form'              => array(
            'arcaptcha_cmf_status',
            '',
            'default/comment-form.php'
        ),
        'Lost Password Form'        => array(
            'arcaptcha_lpf_status',
            '',
            array('common/lost-password-form.php', 'default/lost-password.php')
        ),
        'WooCommerce Login'         => array(
            'arcaptcha_wc_login_status',
            'woocommerce/woocommerce.php',
            'wc/wc-login.php'
        ),
        'WooCommerce Register'      => array(
            'arcaptcha_wc_reg_status',
            'woocommerce/woocommerce.php',
            'wc/wc-register.php'
        ),
        'WooCommerce Lost Password' => array(
            'arcaptcha_wc_lost_pass_status',
            'woocommerce/woocommerce.php',
            array('common/lost-password-form.php', 'wc/wc-lost-password.php')
        ),
        'WooCommerce Checkout'      => array(
            'arcaptcha_wc_checkout_status',
            'woocommerce/woocommerce.php',
            'wc/wc-checkout.php'
        ),
        
        'WPForms Lite'              => array(
            'arcaptcha_wpforms_status',
            'wpforms-lite/wpforms.php',
            'wpforms/wpforms.php'
        ),
        'WPForms Pro'               => array(
            'arcaptcha_wpforms_pro_status',
            'wpforms/wpforms.php',
            'wpforms/wpforms.php'
        ),
        'Contact Form 7'            => array(
            'arcaptcha_cf7_status',
            'contact-form-7/wp-contact-form-7.php',
            CF7::class
        )
        
    );

    if (!function_exists('is_plugin_active')) {
        // @codeCoverageIgnoreStart
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        // @codeCoverageIgnoreEnd
    }

    foreach ($modules as $module) {
        $status = get_option($module[0]);

        if ('on' !== $status) {
            continue;
        }

        if (($module[1] && !is_plugin_active($module[1]))) {
            continue;
        }

        foreach ((array) $module[2] as $component) {
            if (false === strpos($component, '.php')) {
                new $component();
                continue;
            }

            require_once ARCAPTCHA_PATH . '/' . $component;
        }
    }
}

add_action('plugins_loaded', 'arcap_load_modules', -PHP_INT_MAX);

/**
 * Add the arcaptcha script to footer.
 */
function arcap_captcha_script()
{
    wp_enqueue_script(
        'arcaptcha-script',
        '//widget.arcaptcha.ir/1/api.js',
        [],
        ARCAPTCHA_VERSION,
        true
    );
}

add_action('wp_enqueue_scripts', 'arcap_captcha_script');
add_action('login_enqueue_scripts', 'arcap_captcha_script');

/**
 * Load plugin text domain.
 */
function arcaptcha_wp_load_textdomain()
{
    load_plugin_textdomain(
        'arcaptcha-plugin',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}

add_action('plugins_loaded', 'arcaptcha_wp_load_textdomain');
