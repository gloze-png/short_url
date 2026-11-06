<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link unavailable - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex items-center justify-center px-6">
    <div class="max-w-md text-center">
        <p class="text-6xl font-extrabold text-indigo-600">410</p>
        <h1 class="mt-4 text-2xl font-bold">This link is no longer available</h1>
        <p class="mt-3 text-gray-600">
            {{ $exception->getMessage() ?: 'This link has expired or been deactivated.' }}
        </p>
        <a href="{{ route('home') }}"
           class="inline-block mt-8 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
            Create your own short link
        </a>
    </div>
</body>
</html>