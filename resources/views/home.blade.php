<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Shorten your links</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- Top navigation --}}
    <nav class="flex items-center justify-between max-w-5xl mx-auto px-6 py-4">
        <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">
            {{ config('app.name') }}
        </a>
        <div class="space-x-4 text-sm">
            @auth
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Log in</a>
                <a href="{{ route('register') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Sign up</a>
            @endauth
        </div>
    </nav>

    {{-- Hero --}}
    <main class="max-w-2xl mx-auto px-6 py-16 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight">Shorten links. Track clicks.</h1>
        <p class="mt-3 text-gray-600">Paste a long URL and get a short one in a second.</p>

        {{-- Success result card --}}
        @if (session('short_url'))
            <div class="mt-8 bg-green-50 border border-green-200 rounded-xl p-5 text-left">
                <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <input id="short-url" type="text" readonly value="{{ session('short_url') }}"
                           class="flex-1 rounded-lg border-gray-300 bg-white text-sm">
                    <button type="button" id="copy-btn"
                            class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Copy
                    </button>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('urls.store') }}"
              class="mt-8 bg-white shadow-sm rounded-xl p-6 text-left space-y-4">
            @csrf

            <div>
                <label for="original_url" class="block text-sm font-medium">Long URL</label>
                <input id="original_url" name="original_url" type="url"
                       value="{{ old('original_url') }}" placeholder="https://example.com/very/long/link"
                       class="mt-1 w-full rounded-lg border-gray-300">
                @error('original_url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="custom_alias" class="block text-sm font-medium">
                        Custom alias <span class="text-gray-400">(optional)</span>
                    </label>
                    <input id="custom_alias" name="custom_alias" type="text"
                           value="{{ old('custom_alias') }}" placeholder="my-link"
                           class="mt-1 w-full rounded-lg border-gray-300">
                    @error('custom_alias')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium">
                        Expires on <span class="text-gray-400">(optional)</span>
                    </label>
                    <input id="expires_at" name="expires_at" type="datetime-local"
                           value="{{ old('expires_at') }}"
                           class="mt-1 w-full rounded-lg border-gray-300">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-700">
                Shorten URL
            </button>
        </form>
    </main>

    {{-- Copy to clipboard --}}
    <script>
        const copyBtn = document.getElementById('copy-btn');
        if (copyBtn) {
            copyBtn.addEventListener('click', async () => {
                const input = document.getElementById('short-url');
                try {
                    await navigator.clipboard.writeText(input.value);
                    copyBtn.textContent = 'Copied!';
                } catch (e) {
                    input.select();
                    document.execCommand('copy');
                    copyBtn.textContent = 'Copied!';
                }
                setTimeout(() => copyBtn.textContent = 'Copy', 2000);
            });
        }
    </script>
</body>
</html>