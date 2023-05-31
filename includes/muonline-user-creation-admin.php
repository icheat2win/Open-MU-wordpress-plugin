<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function muonline_user_creation_settings_init() {
    register_setting('muonline_user_creation', 'muonline_user_creation_settings');

    add_settings_section(
        'muonline_user_creation_section_email',
        'Email Settings',
        'muonline_user_creation_section_email_callback',
        'muonline_user_creation'
    );

    add_settings_field(
        'from_name',
        'From Name',
        'muonline_user_creation_from_name_callback',
        'muonline_user_creation',
        'muonline_user_creation_section_email'
    );

    add_settings_field(
        'from_email',
        'From Email',
        'muonline_user_creation_from_email_callback',
        'muonline_user_creation',
        'muonline_user_creation_section_email'
    );

    add_settings_field(
        'email_subject',
        'Email Subject',
        'muonline_user_creation_email_subject_callback',
        'muonline_user_creation',
        'muonline_user_creation_section_email'
    );

    add_settings_field(
        'email_message',
        'Email Message',
        'muonline_user_creation_email_message_callback',
        'muonline_user_creation',
        'muonline_user_creation_section_email'
    );
	
}
add_action('admin_init', 'muonline_user_creation_settings_init');




// User Creation
function muonline_user_creation_section_email_callback() {
    echo '<p>Email settings for muonline User Creation plugin.</p>';
}

function muonline_user_creation_from_name_callback() {
    $options = get_option('muonline_user_creation_settings');
    ?>
    <input type="text" name="muonline_user_creation_settings[from_name]" value="<?php echo esc_attr($options['from_name']); ?>">
    <?php
}

function muonline_user_creation_from_email_callback() {
    $options = get_option('muonline_user_creation_settings');
    ?>
    <input type="text" name="muonline_user_creation_settings[from_email]" value="<?php echo esc_attr($options['from_email']); ?>">
    <?php
}

function muonline_user_creation_email_subject_callback() {
    $options = get_option('muonline_user_creation_settings');
    ?>
    <input type="text" name="muonline_user_creation_settings[email_subject]" value="<?php echo esc_attr($options['email_subject']); ?>">
    <?php
}

function muonline_user_creation_email_message_callback() {
    $options = get_option('muonline_user_creation_settings');
    ?>
    <textarea name="muonline_user_creation_settings[email_message]" rows="50" cols="200"><?php echo esc_textarea($options['email_message']); ?></textarea>
    <?php
}


function muonline_user_creation_mail_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<div id="send-test-email-container">
            <h2>Send Test Email</h2>
            <p>Enter an email address to send a test email:</p>
            <input type="email" id="test-email-address" placeholder="Email Address">
            <button id="send-test-email" class="button button-primary">Send Test Email</button>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('muonline_user_creation');
            do_settings_sections('muonline_user_creation');
            submit_button();
            ?>
        </form>
</div>
        
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#send-test-email').on('click', function() {
            var to_email = $('#test-email-address').val();
            if (!validateEmail(to_email)) {
                alert('Please enter a valid email address.');
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'muonline_send_test_email',
                    to_email: to_email
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data);
                    } else {
                        alert(response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('An error occurred: ' + textStatus + ' - ' + errorThrown);
                }
            });
        });

        function validateEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email.toLowerCase());
        }
    });
    </script>
    <?php
}
