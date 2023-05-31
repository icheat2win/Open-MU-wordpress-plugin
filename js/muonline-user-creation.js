jQuery(document).ready(function ($) {
    function checkUsername(username) {
        return $.post({
            url: muonlineUserCreation.ajax_url,
            data: {
                action: 'muonline_check_username',
                username: username
            },
        });
    }
    
    async function validateForm() {
    var username = $('input[name="username"]').val();
    var email = $('input[name="email"]').val();
    var password = $('input[name="password"]').val();
    var regz = $('input[name="regz"]').is(':checked');

    if (username.length < 4 || username.length > 10) {
        alert("Username must be between 4 and 10 characters long.");
        return false;
    }
    
    try {
        var response = await checkUsername(username);
        if (response.length > 0) {
            alert("The username is already taken.");
            return false;
        }
    } catch (error) {
    alert('An error occurred while checking the username: ' + error.statusText);
    return false;
	}

    
    if (!validateEmail(email)) {
        alert("Please enter a valid email address.");
        return false;
    }
	
	if (password.length === 0) {
    alert("Please enter a password.");
    return false;
}

	
	if (!regz) {
        alert("You must agree with the terms of service & server rules.");
        return false;
    }

    return true;
}

    $('#muonline_user_creation_form').on('submit', async function (event) {
        event.preventDefault();

        var username = $('input[name="username"]').val();
        var email = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var timeZone = 0; // Add an input field for the time zone if necessary

        if (!await validateForm()) {
            return;
        }

        $.ajax({
            url: muonlineUserCreation.ajax_url,
            type: 'POST',
            data: {
                action: 'muonline_create_user',
                username: username,
                email: email,
                password: password,
                time_zone: timeZone,
            },
            success: function (response) {
                if (response.success) {
                    
                    $('#registration-form-container').hide();
                    $('#login-form-container').show();
                } else {
                    alert(response.data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus + ' - ' + errorThrown);
            },
        });
    });

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email.toLowerCase());
    }
	


});
