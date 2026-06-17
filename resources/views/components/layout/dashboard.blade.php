@props([
    'title' => 'TalentMatch',
])

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — AI Screening Assistant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-bg font-sans text-white antialiased">
    <div class="flex h-full flex-col">
        {{-- Top Navbar --}}
        <x-navbar />

        <div class="flex flex-1 overflow-hidden">
            {{-- Left Sidebar --}}
            <x-sidebar />

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
