<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Checker Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 30px auto;
            padding: 20px 30px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn {
            display: inline-block;
            background-color: #1d4ed8;
            color: #ffffff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }

        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Complete Your Account Setup</h2>

        <p>Hello {{ $invitation->name }},</p>

        <p>
            You have been invited by <strong>{{ $makerName }}</strong> to act as a payout checker for application
            <strong>#{{ $applicationNumber }}</strong>.
        </p>

        <p>
            To complete your account setup and begin your review, please click the button below:
        </p>

        <p>
            <a href="{{ $invitationUrl }}" class="btn">Complete Account Setup</a>
        </p>

        <p>
            This invitation will expire on
            <strong>{{ \Carbon\Carbon::parse($expiresAt)->format('F j, Y \a\t g:i A') }}</strong>.
        </p>

        <p>
            If you weren’t expecting this invitation, you can safely ignore this email.
        </p>

        <p>Thank you,<br>The Team</p>

        <div class="footer">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </div>
    </div>
</body>

</html>
