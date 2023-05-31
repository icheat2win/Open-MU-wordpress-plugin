<?php

function muonline_password_reset_form() {
	$recaptcha_google_site_key = muonline_get_recaptcha_google_site_key();
    ?>
    <div class="muonline-password-reset-form">
        
        <p>Please enter your email address, and we'll send you a password reset link.</p>

        <form id="password-reset-form">
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required>
            <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_google_site_key; ?>"></div>
                <div class="submit-container"><button type="submit">Submit</button></div>
        </form>
    </div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    

jQuery('#password-reset-form').submit(function (e) {
    e.preventDefault();

    var email = jQuery('#email').val();
    var recaptcha_response = jQuery('.g-recaptcha-response').val();

    // Check if the reCAPTCHA response is empty
    if (recaptcha_response === '') {
        alert('Please confirm you are not a robot.');
        return;
    }

    jQuery.post(muonline_password_reset_vars.ajaxurl, {
        action: 'muonline_password_reset_request',
        email: email,
        recaptcha_response: recaptcha_response,
    }, function (response) {
        console.log(response);
        if (response.success) {
            alert('Password reset link has been sent to your email.');
        } else {
            alert(response.data.message)
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error('AJAX request failed:', textStatus, errorThrown);
    });
});


    </script>
    <?php
}

add_shortcode('muonline_password_reset_form', 'muonline_password_reset_form');

function muonline_password_reset_request() {
    $email = $_POST['email'];
    $recaptcha_response = $_POST['recaptcha_response'];
    $recaptcha_google_secret_key = muonline_get_recaptcha_google_secret_key();
	
	    // Add the log statement here to check the received reCAPTCHA response
    error_log('Recaptcha response: ' . $recaptcha_response);

    // Verify reCAPTCHA response
    $recaptcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_verify_data = [
        'secret' => $recaptcha_google_secret_key,
        'response' => $recaptcha_response,
    ];

    $ch = curl_init($recaptcha_verify_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($recaptcha_verify_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $recaptcha_verify_result = curl_exec($ch);
    curl_close($ch);

    $recaptcha_verify_response = json_decode($recaptcha_verify_result);
	
	// Add the log statement here to check the reCAPTCHA verification response
    error_log('Recaptcha verification response: ' . json_encode($recaptcha_verify_response));


    if (!$recaptcha_verify_response->success) {
        wp_send_json_error(['message' => 'Invalid reCAPTCHA response.']);
        return;
    }

    $response = call_api('GET', '/account?EMail=eq.' . urlencode($email));
    $user_data = json_decode($response);

    if (empty($user_data)) {
        wp_send_json_error(['message' => 'User not found.']);
    } else {
        $user_data = $user_data[0];
    }

    $user_id = $user_data->Id;

    $password_reset_token = wp_generate_password(64, false);
    $password_reset_date = date('Y-m-d H:i:s', time() + 10 * 60);

    global $wpdb;
    $table_name = $wpdb->prefix . 'muonline_user';

    $wpdb->replace(
        $table_name,
        [
            'user_id' => $user_id,
            'password_reset_token' => $password_reset_token,
            'password_reset_date' => $password_reset_date,
        ],
        ['%s', '%s', '%s']
    );

    $password_reset_link = home_url('/password-reset-link?token=' . $password_reset_token);

    // Get email settings
    $options = get_option('muonline_user_password_reset_settings');
    $from_name = $options['from_name'];
    $from_email = $options['from_email'];
    $email_subject = $options['email_subject'];

    // Get the user's email and username
    $email_data = call_api('GET', '/account?Id=eq.' . $user_id . '&select=EMail,LoginName');
    $email_data = json_decode($email_data);
    $email = $email_data[0]->EMail;
    $username = $email_data[0]->LoginName;

    // Replace the placeholders in the email message
    $email_message = str_replace('{$username}', $username, $options['email_message']);
    $email_message = str_replace('{$reset_link}', $password_reset_link, $email_message);

    // Set email headers
    $headers = [];
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    // Send an email with the password reset link
    wp_mail($email, $email_subject, $email_message, $headers);

    wp_send_json_success(['message' => 'Password reset link has been sent to your email.']);
}

add_action('wp_ajax_nopriv_muonline_password_reset_request', 'muonline_password_reset_request');
add_action('wp_ajax_muonline_password_reset_request', 'muonline_password_reset_request');

function muonline_password_reset() {
    $token = $_GET['token'];

    // Check if the token is valid and has not expired
    global $wpdb;
    $table_name = $wpdb->prefix . 'muonline_user';

    $user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE password_reset_token = %s", $token));

    if (!$user_data || strtotime($user_data->password_reset_date) < time()) {
        echo 'Invalid or expired token';
        return;
    }

    // Reset the user's password in the API
    $new_password = wp_generate_password(12, true, true);
    // Hash the new password using bcrypt
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $response = call_api('PUT', '/account?Id=eq.' . $user_data->user_id, [
        'PasswordHash' => $hashed_password
    ]);

    if ($response) {
        // Remove the password reset token and date from the database
        $wpdb->update(
            $table_name,
            [
                'password_reset_token' => null,
                'password_reset_date' => null,
            ],
            ['user_id' => $user_data->user_id],
            ['%s', '%s'],
            ['%s']
        );
		// Get email settings
		$options = get_option('muonline_user_password_reset_settings');
		$from_name = $options['from_name'];
		$from_email = $options['from_email'];
		$email_subject = $options['email_subject'];

        // Notify the user of the new password by email
        $email_data = call_api('GET', '/account?Id=eq.' . $user_data->user_id . '&select=EMail,LoginName');
		$email_data = json_decode($email_data);
		$email = $email_data[0]->EMail;
		$username = $email_data[0]->LoginName;
		
		// Replace the placeholders in the email message
		$email_message_new_password = str_replace('{$username}', $username, $options['email_message_new_password']);
		$email_message_new_password = str_replace('{$new_password}', $new_password, $email_message_new_password);
		// Set email headers
		$headers = [];
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		// Send an email with the password
        $sent = wp_mail($email, $email_subject, $email_message_new_password, $headers);
		if (!$sent) {
			error_log('Email sending failed: ' . $email . ', ' . $email_subject);
			}
        echo 'Password reset successful. Please check your email for the new password.';
    } else {
        error_log('API Response: ' . $response); // Add this line for debugging
        echo 'An error occurred while resetting your password. Please try again later.';
    }
}

add_shortcode('muonline_password_reset', 'muonline_password_reset');
