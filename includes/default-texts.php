<?php

define('USER_CREATION_FROM_NAME', 'GlobalMU Support');
define('USER_CREATION_FROM_EMAIL', 'support@globalmu.org');
define('USER_CREATION_EMAIL_SUBJECT', 'Welcome to MuOnline Server');
define('USER_CREATION_EMAIL_MESSAGE', '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - GlobalMU</title>
    <link href="https://fonts.googleapis.com/css?family=Metamorphous:400,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Metamorphous", sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            max-width: 800px;
        }
        .title {
			font-family: "Metamorphous", sans-serif;
            background-color: #a22139;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 32px;
        }
        .content {
            padding: 20px;
            background-color: #f7f7f7;
        }
        .credentials {
            background-color: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1);
        }
        .credentials p {
            margin: 0;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            background-color: #f7f7f7;
        }
        a {
            color: #a22139;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="title">Welcome to GlobalMU</div>
        <div class="content">
            <p>Dear {$username},</p>
            <p>Thank you for registering at GlobalMU. We are excited to have you as a part of our community.</p>
            <p><img src="https://globalmu.org/wp-content/themes/mu-website/style/images/layout/gzholjthbdsn2iag3crx.jpg" alt="Mu-Online Gameplay Screenshot"></p>
            <p>To get started, log in to your account using the following credentials:</p>
            <div class="credentials">
                <p><strong>Username:</strong> {$username}</p>
                <p><strong>Password:</strong> Your chosen password during registration</p>
                <p><strong>Security code:</strong> {$security_code}</p>
            </div>
            <p>Please keep this email for your records, and let us know if you have any questions or need assistance.</p>
            <p>We look forward to seeing you in the game!</p>
            <p>Best regards,</p>
            <p>The GlobalMU Team</p>
        </div>
        <div class="footer">
            © 2023 GlobalMU. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
        </div>
    </div>
</body>
</html>');
define('PASSWORD_RESET_FROM_NAME', 'GlobalMU Support');
define('PASSWORD_RESET_FROM_EMAIL', 'support@globalmu.org');
define('PASSWORD_RESET_EMAIL_SUBJECT', 'Password Recovery - GlobalMU');
define('PASSWORD_RESET_EMAIL_MESSAGE', '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - GlobalMU</title>
    <link href="https://fonts.googleapis.com/css?family=Metamorphous:400,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Metamorphous", sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            max-width: 800px;
        }
        .title {
			font-family: "Metamorphous", sans-serif;
            background-color: #a22139;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 32px;
        }
        .content {
            padding: 20px;
            background-color: #f7f7f7;
        }
        .credentials {
            background-color: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1);
        }
        .credentials p {
            margin: 0;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            background-color: #f7f7f7;
        }
        a {
            color: #a22139;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="title">Password Recovery - GlobalMU</div>
        <div class="content">
            <p>Dear {$username},</p>
            <p>We received a request to reset your password.</p>
            <p>If you did not request a password reset, please ignore this email.</p>
            <div class="credentials">
                <p><strong>Please click the link below to confirm your identity and set a new password:</strong></p>
                <p><strong><a href="{$reset_link}">{$reset_link}</a></strong></p>
            </div>
            <p>Best regards,</p>
            <p>The GlobalMU Team</p>
        </div>
        <div class="footer">
            © 2023 GlobalMU. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
        </div>
    </div>
</body>
</html>
');



define('PASSWORD_RESET_EMAIL_MESSAGE_NEW_PASSWORD', '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - GlobalMU</title>
    <link href="https://fonts.googleapis.com/css?family=Metamorphous:400,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Metamorphous", sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
            background-color: #fafafa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            max-width: 800px;
        }
        .title {
			font-family: "Metamorphous", sans-serif;
            background-color: #a22139;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 32px;
        }
        .content {
            padding: 20px;
            background-color: #f7f7f7;
        }
        .credentials {
            background-color: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1);
        }
        .credentials p {
            margin: 0;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
            background-color: #f7f7f7;
        }
        a {
            color: #a22139;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="title">Password Recovery - GlobalMU</div>
        <div class="content">
            <p>Dear {$username},</p>
            <p>We received a request to reset your password.</p>
            <div class="credentials">
                <p><strong>Here is your new password {$new_password}</strong></p>
            </div>
            <p>Best regards,</p>
            <p>The GlobalMU Team</p>
        </div>
        <div class="footer">
            © 2023 GlobalMU. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
        </div>
    </div>
</body>
</html>
');