<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'POS System' }} - {{ config('app.name') }}</title>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 font-sans text-gray-950 antialiased dark:text-white">
    
    {{ $slot }}

    @filamentScripts
</body>
</html>
