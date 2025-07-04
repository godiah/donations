<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Support Our Cause')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-50">
    @include('partials.flash_messages')
    <div class="min-h-screen flex flex-col items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-3xl bg-white shadow-lg rounded-xl overflow-hidden">
            @yield('content')
        </div>
    </div>
</body>

</html>
