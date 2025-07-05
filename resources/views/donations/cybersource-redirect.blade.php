<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Redirecting to Payment...</title>
</head>

<body>
    <form id="cybersource-form" action="{{ $actionUrl }}" method="post">
        @foreach ($params as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>
        document.getElementById('cybersource-form').submit();
    </script>
</body>

</html>
