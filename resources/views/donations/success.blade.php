<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 2rem;
            background-color: #f0fdf4;
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

        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            background-color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkmark {
            color: white;
            font-size: 32px;
        }

        h1 {
            color: #065f46;
            margin-bottom: 1rem;
        }

        .details {
            background-color: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: left;
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
        <div class="success-icon">
            <span class="checkmark">✓</span>
        </div>

        <h1>Payment Successful!</h1>
        <p>Thank you for your generous donation. Your payment has been processed successfully.</p>

        @if ($contribution)
            <div class="details">
                <strong>Payment Details:</strong><br>
                Amount: {{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}<br>
                Email: {{ $contribution->email }}<br>
                Donation Type: {{ ucfirst($contribution->donation_type) }}<br>
                @if ($contribution->cybersource_transaction_id)
                    Transaction ID: {{ $contribution->cybersource_transaction_id }}
                @endif
            </div>
        @endif

        <p><small>You will receive a confirmation email shortly.</small></p>
        <a href="/" class="btn">Return to Home</a>
    </div>
</body>

</html>
