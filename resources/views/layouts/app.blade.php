<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'TalentMatch') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-white antialiased">
    <x-navbar />
    <div class="flex min-h-[calc(100vh-4rem)]">
        <x-sidebar />
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>