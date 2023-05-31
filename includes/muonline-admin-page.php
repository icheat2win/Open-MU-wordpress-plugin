<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once( MY_PLUGIN_PATH . 'open-mu/includes/default-texts.php');
require_once( MY_PLUGIN_PATH . 'open-mu/includes/muonline-password-reset-admin.php');
require_once( MY_PLUGIN_PATH . 'open-mu/includes/muonline-user-creation-admin.php');
// Include the muonline-user-creation-admin.php file.
function muonline_admin_page_settings_init() {
    register_setting('muonline_admin_page', 'muonline_admin_page_settings');

    add_settings_field(
        'api_url',
        'API URL',
        'muonline_admin_page_api_url_callback',
        'muonline_admin_page_reset',
        'muonline_admin_page_api_url'
    );
    add_settings_field(
        'api_token',
        'API Token',
        'muonline_admin_page_api_token_callback',
        'muonline_admin_page_reset',
        'muonline_admin_page_api_token'
    );
	add_settings_field(
        'recaptcha_google_site_key',
        'reCAPTCHA Google Site key',
        'muonline_admin_page_recaptcha_google_site_key_callback',
        'muonline_admin_page_reset',
        'muonline_admin_page_recaptcha_google_site_key'
    );
	add_settings_field(
        'recaptcha_google_secret_key',
        'reCAPTCHA Google Secret key',
        'muonline_admin_page_recaptcha_google_secret_key_callback',
        'muonline_admin_page_reset',
        'muonline_admin_page_recaptcha_google_secret_key'
    );
	
}
add_action('admin_init', 'muonline_admin_page_settings_init');

function muonline_admin_manage() {
    if (!current_user_can('manage_options')) {
        return;
    }
	
function muonline_admin_page_api_token_callback() {
    $options = get_option('muonline_admin_page_settings');
    ?>
    <input type="text" name="muonline_admin_page_settings[api_token]" value="<?php echo esc_attr($options['api_token']); ?>">
    <?php
}
function muonline_admin_page_api_url_callback() {
    $options = get_option('muonline_admin_page_settings');
    ?>
    <input type="text" name="muonline_admin_page_settings[api_url]" value="<?php echo esc_attr($options['api_url']); ?>">
    <?php
}
function muonline_admin_page_recaptcha_google_site_key_callback() {
    $options = get_option('muonline_admin_page_settings');
    ?>
    <input type="text" name="muonline_admin_page_settings[recaptcha_google_site_key]" value="<?php echo esc_attr($options['recaptcha_google_site_key']); ?>">
    <?php
}
function muonline_admin_page_recaptcha_google_secret_key_callback() {
    $options = get_option('muonline_admin_page_settings');
    ?>
    <input type="text" name="muonline_admin_page_settings[recaptcha_google_secret_key]" value="<?php echo esc_attr($options['recaptcha_google_secret_key']); ?>">
    <?php
}

    function muonline_admin_page() {
        ?>
        <style>
        .container {
          padding-top: 80px;
          max-width: 80%;
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .welcome-panel {
          text-align: center;
          padding: 24px;
          background-color: #f7f7f7;
        }

        .shortcode-container {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
          align-items: center;
          background-color: #f7f7f7;
          padding: 12px;
          margin-top: 24px;
          border-radius: 4px;
        }
		.button-wrapper input[type="submit"] {
		  display: block;
		  margin-left: auto;
		  margin-right: auto;
		}


        .shortcode-container p {
          margin: 0;
        }
		

        @media (max-width: 768px) {
          .shortcode-container {
            flex-direction: column;
          }

          .shortcode-container button {
            margin-top: 12px;
          }
        }
        </style>

        <div class="container">
          <div class="welcome-panel">
            <h2 class="h4 mb-4"><strong>Welcome to the OpenMu WordPress Plugin!</strong></h2>
            <p class="lead mb-4">The OpenMu WordPress Plugin is designed to streamline the process of creating and managing registration pages for your MuOnline server. </p>
            <p class="lead mb-4">With just a few clicks, you can have a fully functional registration page up and running in no time.</p>
            </br>
			<hr>
    <form method="post" action="options.php">
        <?php
        settings_fields('muonline_admin_page');
        do_settings_sections('muonline_admin_page');
        ?>
        <label for="api_url"><strong>API URL:</strong></label><br>
        <?php
        muonline_admin_page_api_url_callback();
        ?>
        <br>
		<br>
        <label for="api_token"><strong>API Token:</strong></label><br>
        <?php
        muonline_admin_page_api_token_callback();
        ?>
		<br>
		<br>
        <label for="recaptcha_google"><strong>Google reCAPTCHA Site key:</strong></label><br>
        <?php
        muonline_admin_page_recaptcha_google_site_key_callback();
        ?>
		<br>
		<br>
        <label for="recaptcha_google"><strong>Google reCAPTCHA Secret key:</strong></label><br>
        <?php
        muonline_admin_page_recaptcha_google_secret_key_callback();
        ?>
		
        <div class="button-wrapper">
            <?php submit_button('Save DATA'); ?>
        </div>
    </form>
	<hr>
            <h3 class="h4 mb-4"><strong>Easily Create a Registration Page</strong></h3>
            <div class="shortcode-container d-flex justify-content-between align-items-center">
              <p>Get started by creating a new page and adding the shortcode <code id="shortcode">[muonline_user_registration_form]</code><button class="btn btn-primary btn-sm" onclick="copyToClipboard('#shortcode')"><i class="fas fa-copy"></i> Copy</button> to embed the registration form. Customize the form as needed to suit your server's requirements.</p>
            </div>
			<hr>
            </br>
            <h3 class="h4 mb-4"><strong>Customize Registration Emails</strong></h3>
            <p class="mt-4">To edit the content of the registration emails sent to new users, navigate to the <a href="<?php echo admin_url('admin.php?page=muonline-user-creation-mail'); ?>">MuOnline User Creation mail</a> page in your WordPress admin dashboard.</p> 
            <p class="mt-4">Personalize the email template to provide essential information and a warm welcome to your new players.</p>
			<hr>
			 <h3 class="h4 mb-4"><strong>Customize Password Reset Mail</strong></h3>
            <p class="mt-4">To edit the content of the registration emails sent to new users, navigate to the <a href="<?php echo admin_url('admin.php?page=muonline-user-password-reset-mail'); ?>">MuOnline Password Reset mail</a> page in your WordPress admin dashboard.</p> 
            <p class="mt-4">Personalize the email template to provide essential information and a warm welcome to your new players.</p>
          </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
          function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
            alert("Copied to clipboard!");
          }
        </script>
        <?php

    }

function muonline_admin_menu() {
    add_menu_page(
        'Mu-Online',
        'Mu-Online',
        'manage_options',
        'muonline',
        'muonline_admin_page'
    );

	add_submenu_page(
        'muonline',
        'User Creation Mail',
        'User Creation Mail',
        'manage_options',
        'muonline-user-creation-mail',
        'muonline_user_creation_mail_html'
		
    );
	    add_submenu_page(
        'muonline',
        'Password Reset Mail',
        'Password Reset Mail',
        'manage_options',
        'muonline-user-password-reset-mail',
        'muonline_user_password_reset_settings_page_html'
    );
}
add_action('admin_menu', 'muonline_admin_menu');

}
add_action('plugins_loaded', 'muonline_admin_manage');




