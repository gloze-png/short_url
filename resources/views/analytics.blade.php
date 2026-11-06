<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analytics: /s/{{ $shortUrl->short_code }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to dashboard</a>

            <div class="bg-white shadow-sm rounded-xl p-5">
                <p class="text-sm text-gray-500">Destination</p>
                <p class="break-all">{{ $shortUrl->original_url }}</p>
                <p class="mt-3 text-sm text-gray-500">Total clicks</p>
                <p class="text-3xl font-bold">{{ $totalClicks }}</p>
            </div>

            {{-- Chart --}}
            <div class="bg-white shadow-sm rounded-xl p-5">
                <h3 class="font-semibold mb-4">Clicks per day (last 14 days)</h3>
                <canvas id="clicksChart" height="100"></canvas>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Top referrers --}}
                <div class="bg-white shadow-sm rounded-xl p-5">
                    <h3 class="font-semibold mb-4">Top referrers</h3>
                    <ul class="space-y-2 text-sm">
                        @forelse ($referrers as $row)
                            <li class="flex justify-between gap-3">
                                <span class="truncate" title="{{ $row->source }}">{{ $row->source }}</span>
                                <span class="font-medium">{{ $row->total }}</span>
                            </li>
                        @empty
                            <li class="text-gray-500">No clicks yet.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Recent clicks --}}
                <div class="bg-white shadow-sm rounded-xl p-5 lg:col-span-2 overflow-x-auto">
                    <h3 class="font-semibold mb-4">Recent clicks</h3>
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-500">
                            <tr>
                                <th class="py-2 pr-4">Time (UTC)</th>
                                <th class="py-2 pr-4">Browser / device</th>
                                <th class="py-2">Referrer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($recentClicks as $click)
                                <tr>
                                    <td class="py-2 pr-4 whitespace-nowrap">{{ $click->clicked_at->format('M d, H:i') }}</td>
                                    <td class="py-2 pr-4" title="{{ $click->user_agent }}">
                                        {{ \Illuminate\Support\Str::limit($click->user_agent, 50) }}
                                    </td>
                                    <td class="py-2">{{ $click->referer ?? 'Direct' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-gray-500">No clicks recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('clicksChart'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Clicks',
                    data: @json($counts),
                    backgroundColor: '#6366f1',
                    borderRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    </script>
</x-app-layout>