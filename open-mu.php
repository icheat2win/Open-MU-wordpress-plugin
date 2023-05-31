<?php
/**
* Plugin Name: OpenMU Wordpress Plugin
* Description: Creates OpenMU users using postgREST API and password reset function
* Version: 1.0.0
* Author: Agge
* Author URI: https://globalmu.org
*/
function muonline_get_api_url() {
    $options = get_option('muonline_admin_page_settings');
    return isset($options['api_url']) ? $options['api_url'] : '';
}
function muonline_get_api_token() {
    $options = get_option('muonline_admin_page_settings');
    return isset($options['api_token']) ? $options['api_token'] : '';
}
function muonline_get_recaptcha_google_site_key() {
    $options = get_option('muonline_admin_page_settings');
    return isset($options['recaptcha_google_site_key']) ? $options['recaptcha_google_site_key'] : '';
}
function muonline_get_recaptcha_google_secret_key() {
    $options = get_option('muonline_admin_page_settings');
    return isset($options['recaptcha_google_secret_key']) ? $options['recaptcha_google_secret_key'] : '';
}
define( 'MY_PLUGIN_PATH', plugin_dir_path( __DIR__ ) );
require_once plugin_dir_path(__FILE__) . 'includes/muonline-login.php';
require_once plugin_dir_path(__FILE__) . 'includes/muonline-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/muonline-password-reset-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/muonline-user-creation-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/muonline-characters.php';

function muonline_is_user_logged_in() {
    if (!session_id()) {
        session_start();
    }
    return isset($_SESSION['openmu_user']) ? $_SESSION['openmu_user'] : null;
}

function muonline_password_reset_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'muonline_password_reset_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'muonline_password_reset_enqueue_scripts');



function call_api($method, $endpoint, $data = null) {
    $base_url = muonline_get_api_url();
    $api_token = muonline_get_api_token();
    $recaptcha_google_site_key = muonline_get_recaptcha_google_site_key();
    
    if (empty($base_url) || empty($api_token)) {
        error_log('API URL or API Token is missing. Please check your plugin settings.');
        return null;
    }

    $url = $base_url . $endpoint;
    error_log('API Token: ' . $api_token);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Set the content type and authorization headers
    $headers = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $api_token
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
    } else {
        // Log the API response
        error_log('API Response: ' . $result);
    }

    curl_close($ch);

    return $result;
}

register_activation_hook(__FILE__, 'create_muonline_user_table');

function create_muonline_user_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'muonline_user';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id varchar(36) NOT NULL,
            password_reset_token varchar(255) DEFAULT NULL,
            password_reset_date DATETIME,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

register_deactivation_hook(__FILE__, 'delete_muonline_user_table');

function delete_muonline_user_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'muonline_user';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}