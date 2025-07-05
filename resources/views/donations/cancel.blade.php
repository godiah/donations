<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background-color: #fef3c7;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .warning-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            background-color: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .warning-mark {
            color: white;
            font-size: 32px;
        }

        h1 {
            color: #92400e;
            margin-bottom: 1rem;
        }

        .btn {
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }

        .btn-secondary {
            background-color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="warning-icon">
            <span class="warning-mark">!</span>
        </div>

        <h1>Payment Cancelled</h1>
        <p>Your payment was cancelled. No charges were made to your account.</p>
        <p>If this was an error, you can try making the donation again.</p>

        <a href="javascript:history.back()" class="btn">Try Again</a>
        <a href="/" class="btn btn-secondary">Return to Home</a>
    </div>
</body>

</html>
