@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach ($stats as $stat)
                <div
                    class="bg-white shadow-sm hover:shadow-md rounded-xl p-4 flex items-center justify-between transition-all duration-300">
                    <div>
                        <h4 class="text-sm text-gray-500">{{ $stat['title'] }}</h4>
                        <h2 class="text-2xl font-semibold text-gray-800 mt-1">{{ $stat['value'] }}</h2>
                        <p class="text-xs mt-1 text-gray-500">{{ $stat['change'] }}</p>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg {{ $stat['color'] }}">
                        <span class="text-lg">{{ $stat['icon'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer Volume -->
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 self-start">Circle Analytics</h3>
                <div class="w-full max-w-[500px] h-[320px] flex justify-center">
                    <canvas id="customerChart"></canvas>
                </div>
            </div>

            <!-- Sales Volume -->
            <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 self-start">Chart Analytics</h3>
                <div class="w-full max-w-[500px] h-[320px] flex justify-center">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', renderCharts);

                function renderCharts() {
                    const customerCtx = document.getElementById('customerChart')?.getContext('2d');
                    const salesCtx = document.getElementById('salesChart')?.getContext('2d');

                    if (!customerCtx || !salesCtx) return console.warn('Canvas Chart tidak ditemukan');

                    // === Customer Volume ===
                    new Chart(customerCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($customerVolume['labels']),
                            datasets: [{
                                data: @json($customerVolume['data']),
                                backgroundColor: @json($customerVolume['colors']),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });

                    // === Sales Volume ===
                    new Chart(salesCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($salesVolume['labels']),
                            datasets: [{
                                    label: 'Perumahan',
                                    data: @json($salesVolume['data']['Perumahan']),
                                    backgroundColor: @json($salesVolume['colors'][0]),
                                    borderRadius: 6
                                },
                                {
                                    label: 'Pengguna',
                                    data: @json($salesVolume['data']['Pengguna']),
                                    backgroundColor: @json($salesVolume['colors'][1]),
                                    borderRadius: 6
                                },
                                {
                                    label: 'Warga',
                                    data: @json($salesVolume['data']['Warga']),
                                    backgroundColor: @json($salesVolume['colors'][2]),
                                    borderRadius: 6
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#4B5563'
                                    },
                                    grid: {
                                        color: '#E5E7EB'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#4B5563'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
        @endpush
    </div>
<<<<<<< HEAD
=======
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($userGrowth, 'year')) !!},
            datasets: [{
                label: 'Jumlah Pengguna',
                data: {!! json_encode(array_column($userGrowth, 'count')) !!},
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { color: '#4B5563' } },
                x: { ticks: { color: '#4B5563' } }
            },
            plugins: {
                legend: { labels: { color: '#111827' } }
            }
        }
    });
</script>
>>>>>>> 3e10734edaa76f00959619efda7aee555dc256f1
@endsection
