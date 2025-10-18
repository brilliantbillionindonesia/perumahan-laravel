@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-sm font-medium text-gray-500 mb-2">Jumlah Pengguna</h2>
            <p class="text-4xl font-bold text-gray-900">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-sm font-medium text-gray-500 mb-2">Jumlah Perumahan</h2>
            <p class="text-4xl font-bold text-gray-900">{{ $totalHousings }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-sm font-medium text-gray-500 mb-2">Tahun Aktif</h2>
            <p class="text-4xl font-bold text-gray-900">{{ now()->year }}</p>
        </div>
    </div>

    <!-- Grafik Pertumbuhan Pengguna -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pertumbuhan Pengguna per Tahun</h2>
        <canvas id="userGrowthChart" height="120"></canvas>
    </div>
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
@endsection