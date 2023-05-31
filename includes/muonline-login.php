<?php
class OpenMU_Login_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'openmu_login_widget',
            'OpenMU Login Widget',
            array('description' => 'An OpenMU login widget for your site')
        );
    }
	public function form($instance) {
    $links = !empty($instance['links']) ? $instance['links'] : '';
    ?>
    <p>
        <label for="<?php echo $this->get_field_id('links'); ?>">Links (one per line)Link Name|URL:</label>
        <textarea class="widefat" id="<?php echo $this->get_field_id('links'); ?>" name="<?php echo $this->get_field_name('links'); ?>" rows="5"><?php echo esc_textarea($links); ?></textarea>
    </p>
    <?php
}

public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['links'] = (!empty($new_instance['links'])) ? strip_tags($new_instance['links']) : '';

    return $instance;
}
    public function widget($args, $instance) {
        // Check if the user is logged in
        $this->logged_in_user = muonline_is_user_logged_in();

        echo $args['before_widget'];

        if ($this->logged_in_user) {
            // Display the logged-in user content
            ?>
            <div class="login-block">
                <span class="right-sidebar-title sidebar-title right-sidebar-title-top">Login</span>

                <div class="account-details border-bottom">
                    <div>Account: <span><?php echo htmlspecialchars($this->logged_in_user); ?></span></div>
                    <!-- Add other account details as necessary -->
                </div>

                <div class="account-menu">
                    <!-- Add links to user-specific pages -->
                    <?php
					$links = !empty($instance['links']) ? $instance['links'] : '';
					if (!empty($links)) {
						$links_array = explode("\n", $links);
						foreach ($links_array as $link) {
							list($name, $url) = explode('|', $link);
							echo '<br><a href="' . esc_url(trim($url)) . '">' . esc_html(trim($name)) . '</a>';
						}
					}
					?>
                    <!-- Add other links as necessary -->

                    <br>
                    <br>
<button type="button" class="button-go" id="logout-button">Logout</button>
<script>
document.getElementById('logout-button').addEventListener('click', function() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send('action=openmu_logout');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            location.reload();
        }
    };
});
</script>


                </div>
            </div>
            <?php
        } else {
            // Display the login form
            ?>
            <div class="login-block">
                <span class="right-sidebar-title sidebar-title right-sidebar-title-top">Login</span>
                <form id="login-form" method="POST">
                    <table class="login_form" align="center" width="133">
                        <tbody>
                        <tr>
                            <td align="center">
                                <input id="input_login" maxlength="10" type="text" name="username" placeholder="Username">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="login_form" align="center" width="133" style="margin-top:8px">
                        <tbody>
                        <tr>
                            <td align="center">
                                <input id="input_pass" maxlength="20" type="password" name="password" placeholder="Password">
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <button id="button-login">Login</button>

                    <div id="login-links" style="font-size:12px">
                        <a href="/password-reset">Password recovery</a>
                    </div>

                    <p class="error-message" style="display:none;"></p>
                </form>
            </div>
            <script>
                document.getElementById('login-form').addEventListener('submit', function(event) {
                    event.preventDefault();

                    var username = document.getElementById('input_login').value;
                    var password = document.getElementById('input_pass').value;
                    var errorMessage = document.querySelector('.error-message');

                    var xhr = new XMLHttpRequest();
					xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    xhr.send('action=openmu_login&username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));

                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 400) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                location.reload();
                            } else {
                                errorMessage.textContent = response.data.message;
                                errorMessage.style.display = 'block';
                            }
                        } else {
                            errorMessage.textContent = 'An error occurred. Please try again.';
                            errorMessage.style.display = 'block';
                        }
                    };
                });
            </script>
            <?php
        }

        echo $args['after_widget'];
    }
}

// Register the widget
function register_openmu_login_widget() {
    register_widget('OpenMU_Login_Widget');
}
add_action('widgets_init', 'register_openmu_login_widget');

function openmu_login() {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $response = call_api('GET', '/account?LoginName=eq.' . urlencode($username));

        if ($response) {
            $response_data = json_decode($response, true);
            $user = $response_data[0];
            $password_hash = $user['PasswordHash'];

            if (password_verify($password, $password_hash)) {
                wp_set_auth_cookie($user['Id']);
                $_SESSION['openmu_user'] = $username;
                wp_send_json_success();
            } else {
                wp_send_json_error(array('message' => 'Invalid username or password.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Invalid username or password.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Username and password fields are required.'));
    }
}
add_action('wp_ajax_openmu_login', 'openmu_login');
add_action('wp_ajax_nopriv_openmu_login', 'openmu_login');

function openmu_logout() {
    if (isset($_SESSION['openmu_user'])) {
        unset($_SESSION['openmu_user']);
    }
    wp_logout();
    wp_send_json_success();
}
add_action('wp_ajax_openmu_logout', 'openmu_logout');
add_action('wp_ajax_nopriv_openmu_logout', 'openmu_logout');

