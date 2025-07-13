<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Payment...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .message {
            color: #666;
            margin-bottom: 1rem;
        }

        .security-note {
            font-size: 0.9rem;
            color: #888;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="spinner"></div>
        <div class="message">
            <h3>Redirecting to secure payment...</h3>
            <p>Please wait while we redirect you to our secure payment processor.</p>
        </div>

        <!-- Hidden form that will auto-submit to CyberSource -->
        <form id="cybersource-form" action="{{ $gateway_url }}" method="POST" style="display: none;">
            @foreach ($fields as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach
        </form>

        <div class="security-note">
            <p>🔒 Your payment information is processed securely by CyberSource.</p>
        </div>
    </div>

    <script>
        // Auto-submit form after a brief delay to show the loading message
        setTimeout(function() {
            document.getElementById('cybersource-form').submit();
        }, 2000);

        // Fallback: if form doesn't submit automatically, provide manual option
        setTimeout(function() {
            const container = document.querySelector('.container');
            container.innerHTML +=
                '<button onclick="document.getElementById(\'cybersource-form\').submit();" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">Continue to Payment</button>';
        }, 5000);
    </script>
</body>

</html>
