<?php


function generate_security_code($length = 4) {
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}

function generate_guid() {
    if (function_exists('com_create_guid')) {
        return trim(com_create_guid(), '{}');
    } else {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(16384, 20479), mt_rand(32768, 49151),
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
$logged_in_user = muonline_is_user_logged_in();

function muonline_create_user_form() {
    global $logged_in_user;
    ob_start();
    if ($logged_in_user) {
    ?>
    <div class="warning-summary ">You are already logged in so you can't register a new account.</div>
    <?php
    } else {
?>
    <div id="registration-form-container">
        <form id="muonline_user_creation_form" method="post" action="#">
            <table class="center-table" id="module-register">
                <tbody>
                    <tr>
                        <td style="width:30%" class="title"><b>Login</b></td>
                        <td class="text-center"><input type="text" name="username" maxlength="10"
                                value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pb-20">Login is the name of your account, which you will use to log
                            into the game, forum and site.</td>
                    </tr>
    
                    <tr>
                        <td class="title"><b>E-mail</b></td>
                        <td class="text-center"><input type="text" name="email"
                                value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pb-20">Be sure to input your real E-mail.</td>
                    </tr>
                    <tr>
                        <td class="title"><b>Password</b></td>
                        <td class="text-center"><input type="password" name="password" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="checkbox" name="regz"
                                <?php echo isset($_POST['regz']) ? 'checked' : ''; ?>> I agree with <a
                                href="index.php?page=terms">terms of service & server rules</a>.</td>
                    </tr>
                    <tr style="text-align:center;">
                        <td colspan="2" class="pb-20">
                            <button id="register-button" type="submit" name="submit"
                                style="display:block; margin: 0 auto;">Register</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div id="login-form-container" style="display: none;">
        <p>User created successfully. You can now log in:</p>
        [login_form]
    </div>
	<script>
    jQuery('#muonline_user_creation_form').submit(function (e) {
        e.preventDefault();

        var username = jQuery('input[name="username"]').val();
        var email = jQuery('input[name="email"]').val();
        var password = jQuery('input[name="password"]').val();
        var checkbox = jQuery('input[name="regz"]').is(':checked');

        var errorMsg = '';
        if (!username) {
            errorMsg += 'Please enter a username.\n';
        }
        if (!email) {
            errorMsg += 'Please enter an email address.\n';
        } else if (!validateEmail(email)) {
            errorMsg += 'Please enter a valid email address.\n';
        }
        if (!password) {
            errorMsg += 'Please enter a password.\n';
        }
        if (!checkbox) {
            errorMsg += 'Please accept the terms of service & server rules.\n';
        }

        if (errorMsg) {
            alert(errorMsg);
            return;
        }

        jQuery.post(muonline_password_reset_vars.ajaxurl, {
            action: 'muonline_check_username',
            username: username
        }, function (response) {
            if (response.success) {
                // Username is available
                jQuery.post(muonline_password_reset_vars.ajaxurl, {
					action: 'muonline_create_user',
					username: username,
					email: email,
					password: password,
				}, function (response) {
					if (response.success) {
						// Hide the registration form container
						jQuery('#registration-form-container').hide();

						// Show the login form container
						jQuery('#login-form-container').show();
					} else {
						alert(response.data.message)
					}
				}).fail(function (jqXHR, textStatus, errorThrown) {
					console.error('AJAX request failed:', textStatus, errorThrown);
				});
            } else {
                alert('The username is already in use. Please choose a different one.');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed:', textStatus, errorThrown);
        });
    });
	function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email.toLowerCase());
    }
    </script>
    <?php
    }
    return ob_get_clean();

}

add_shortcode('muonline_create_user_form', 'muonline_create_user_form');

function muonline_check_username() {
    $username = $_POST['username'];
    $response = call_api('GET', '/account?LoginName=eq.' . urlencode($username));
    $result = json_decode($response);

    if (empty($result)) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}

add_action('wp_ajax_nopriv_muonline_check_username', 'muonline_check_username');
add_action('wp_ajax_muonline_check_username', 'muonline_check_username');

function muonline_create_user() {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Generate missing field values
    $id = generate_guid();
    $vault_id = null;
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $security_code = generate_security_code();
    $registration_date = date('Y-m-d H:i:s');
    $state = 0;
	$timezone = 2;
    $vault_password = "";
    $is_vault_extended = false;

    $response = call_api('POST', '/account', [
        'Id' => $id,
        'LoginName' => $username,
        'EMail' => $email,
        'PasswordHash' => $password_hash,
        'SecurityCode' => $security_code,
        'RegistrationDate' => $registration_date,
        'State' => $state,
		'TimeZone' => $timezone,
        'VaultPassword' => $vault_password,
        'IsVaultExtended' => $is_vault_extended
    ]);


        // Send email
        $options = get_option('muonline_user_creation_settings');
        $from_name = $options['from_name'];
        $from_email = $options['from_email'];
        $subject = $options['email_subject'];
        $message = $options['email_message'];
        $message = str_replace(
            array('{$username}', '{$security_code}'),
            array($username, $security_code),
            $message
        );

        $headers = array();
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "From: {$from_name} <{$from_email}>";

        wp_mail($email, $subject, $message, $headers);
        wp_send_json_success(array());
}

add_action('wp_ajax_nopriv_muonline_create_user', 'muonline_create_user');
add_action('wp_ajax_muonline_create_user', 'muonline_create_user');


