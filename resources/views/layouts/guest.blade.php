<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TalentMatch') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-white antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <div class="mb-6 text-center">
                <a href="/" class="text-2xl font-bold text-white">TalentMatch</a>
                <p class="mt-1 text-sm text-text-secondary">AI Screening Assistant</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>