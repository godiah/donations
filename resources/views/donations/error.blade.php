<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Error</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background-color: #fef2f2;
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

        .error-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            background-color: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-mark {
            color: white;
            font-size: 32px;
        }

        h1 {
            color: #991b1b;
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
        <div class="error-icon">
            <span class="error-mark">✗</span>
        </div>

        <h1>Payment Error</h1>
        <p>There was an error processing your payment. Please try again or contact support if the problem persists.</p>
        <p>No charges were made to your account.</p>

        <a href="javascript:history.back()" class="btn">Try Again</a>
        <a href="/" class="btn btn-secondary">Return to Home</a>
    </div>
</body>

</html>

<!-- resources/views/donation/mpesa.blade.php (Placeholder) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background-color: #f8fafc;
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

        .mpesa-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            background-color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1 {
            color: #065f46;
            margin-bottom: 1rem;
        }

        .btn {
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="mpesa-icon">
            <span style="color: white; font-size: 24px;">M</span>
        </div>

        <h1>M-Pesa Payment</h1>
        <p>M-Pesa integration coming soon!</p>

        @if ($contribution)
            <div
                style="background-color: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0; text-align: left;">
                <strong>Contribution Details:</strong><br>
                Amount: {{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}<br>
                Email: {{ $contribution->email }}<br>
                Phone: {{ $contribution->phone }}
            </div>
        @endif

        <a href="/" class="btn">Return to Home</a>
    </div>
</body>

</html>
