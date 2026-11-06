<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Your links</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg p-4">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Stat cards --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <p class="text-sm text-gray-500">Total links</p>
                    <p class="mt-1 text-3xl font-bold">{{ $stats['links'] }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <p class="text-sm text-gray-500">Total clicks</p>
                    <p class="mt-1 text-3xl font-bold">{{ $stats['clicks'] }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <p class="text-sm text-gray-500">Active links</p>
                    <p class="mt-1 text-3xl font-bold">{{ $stats['active'] }}</p>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Original URL</th>
                            <th class="px-4 py-3">Short link</th>
                            <th class="px-4 py-3">Clicks</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($urls as $url)
                            <tr>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $url->original_url }}">
                                    {{ $url->original_url }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('short.redirect', $url) }}" target="_blank"
                                       class="text-indigo-600 hover:underline">
                                        /s/{{ $url->short_code }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('urls.analytics', $url) }}"
                                       class="inline-block bg-indigo-50 text-indigo-700 rounded-full px-3 py-1 font-medium hover:bg-indigo-100">
                                        {{ $url->clicks_count }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    @if (! $url->is_active)
                                        <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-1 text-xs">Inactive</span>
                                    @elseif ($url->isExpired())
                                        <span class="bg-red-100 text-red-700 rounded-full px-2 py-1 text-xs">Expired</span>
                                    @else
                                        <span class="bg-green-100 text-green-700 rounded-full px-2 py-1 text-xs">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <form method="POST" action="{{ route('urls.toggle', $url) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-xs border rounded-lg px-3 py-1 hover:bg-gray-50">
                                            {{ $url->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('urls.destroy', $url) }}"
                                          onsubmit="return confirm('Delete this link and all its click data?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs border border-red-200 text-red-600 rounded-lg px-3 py-1 hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No links yet. <a href="{{ route('home') }}" class="text-indigo-600 underline">Create one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $urls->links() }}
        </div>
    </div>
</x-app-layout>