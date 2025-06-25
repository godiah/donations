<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome!</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px;">
        <h2 style="color: #1d4ed8;">Welcome, {{ $name }}!</h2>

        <p>
            Congratulations on successfully setting up your account as a <strong>{{ $userType }}</strong> on our
            platform.
        </p>

        <p>
            You can now access your dashboard and start using the services available to you.
        </p>

        <p>We’re excited to have you on board!</p>

        <p style="margin-top: 30px;">Best regards,<br>The Team</p>
    </div>
</body>

</html>
