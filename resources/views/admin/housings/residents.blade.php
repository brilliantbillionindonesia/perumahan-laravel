@extends('layouts.app')

@section('title', 'Data Warga - ' . $housing->housing_name)

@section('content')
    <div class="min-h-screen bg-gray-100 py-4 sm:py-6 flex flex-col justify-start">

        <div class="max-w-6xl mx-auto bg-white shadow-md rounded-lg p-6 mt-2">

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    Data Warga - {{ $housing->housing_name }}
                </h2>

                <a href="{{ route('housings.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm font-medium">
                    Kembali
                </a>
            </div>

            <!-- Card Informasi Perumahan -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-gray-50 border border-gray-200 p-4 rounded-lg mb-6">
                <div>
                    <p class="text-xs text-gray-500">Nama Perumahan</p>
                    <p class="font-semibold text-gray-800">{{ $housing->housing_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Alamat</p>
                    <p class="text-gray-800">{{ $housing->address }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">RT / RW</p>
                    <p class="text-gray-800">{{ $housing->rt }}/{{ $housing->rw }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Provinsi</p>
                    <p class="text-gray-800">{{ $housing->province->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Kabupaten / Kota</p>
                    <p class="text-gray-800">{{ $housing->district->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Kecamatan / Desa</p>
                    <p class="text-gray-800">
                        {{ $housing->subdistrict->name ?? '-' }},
                        {{ $housing->village->name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Kode Pos</p>
                    <p class="text-gray-800">{{ $housing->postal_code }}</p>
                </div>
            </div>

            <!-- Pencarian Data Warga -->
            <div x-data="citizenSearch" class="space-y-4">

                <!-- Input Pencarian -->
                <div class="flex items-center space-x-2">
                    <input type="text" autocomplete="off" placeholder="Ketik nama warga lalu tekan Enter..."
                        x-model="query" @keydown.enter.prevent="searchCitizen"
                        class="border rounded-lg px-3 py-2 w-full md:w-1/2 text-sm
                           focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none" />

                    <button @click="searchCitizen"
                        class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md
                           shadow hover:bg-blue-700 transition">
                        Cari
                    </button>
                </div>

                <!-- Hasil Pencarian -->
                <div id="residentContainer" class="space-y-3">
                    <div class="p-4 text-center text-gray-500 bg-slate-50 border border-slate-200 rounded-lg">
                        Silakan ketik nama warga untuk mencari data.
                    </div>
                </div>
            </div>

            <!-- Info Jumlah Warga dan Tombol Import -->
            <div class="flex justify-between items-center my-5">
                <span class="text-gray-500 text-sm">
                    Total Warga: {{ $residents->count() }}
                </span>

                <button type="button"
                    class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium
                       px-4 py-2 rounded-md transition cursor-pointer">
                    + Import Data Warga
                </button>
            </div>

            <!-- Tabel Data Warga -->
            <div class="relative bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full text-sm text-slate-700 border-collapse">
                        <thead class="bg-gray-100 text-slate-700 whitespace-nowrap">
                            <tr>
                                <th class="p-3 text-center border-b font-semibold">No</th>
                                <th class="p-3 text-left border-b font-semibold">Nama Lengkap</th>
                                <th class="p-3 text-left border-b font-semibold">NIK</th>
                                <th class="p-3 text-center border-b font-semibold">Tempat Lahir</th>
                                <th class="p-3 text-center border-b font-semibold">Tanggal Lahir</th>
                                <th class="p-3 text-center border-b font-semibold">Jenis Kelamin</th>
                                <th class="p-3 text-center border-b font-semibold">Agama</th>
                                <th class="p-3 text-center border-b font-semibold">Status Perkawinan</th>
                                <th class="p-3 text-center border-b font-semibold">Pekerjaan</th>
                                <th class="p-3 text-center border-b font-semibold">Pendidikan</th>
                            </tr>
                        </thead>

                        <tbody id="residentTable">
                            @forelse ($residents as $index => $resident)
                                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors">
                                    <td class="p-2 text-center text-slate-500 whitespace-nowrap">
                                        {{ $loop->iteration + ($residents->currentPage() - 1) * $residents->perPage() }}
                                    </td>
                                    <td class="p-2 text-slate-800 whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ $resident->fullname ?? '-' }}
                                    </td>
                                    <td class="p-2 text-slate-600 whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ $resident->citizen_card_number ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->birth_place ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($resident->birth_date)->format('d-m-Y') ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->gender ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->religion ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->marital_status ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->work_type ?? '-' }}
                                    </td>
                                    <td class="p-2 text-center text-slate-600 whitespace-nowrap">
                                        {{ $resident->education_type ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-slate-500 py-4">
                                        Belum ada data warga di perumahan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($residents->hasPages())
                <div
                    class="flex justify-between items-center px-4 py-3 bg-gray-50 border-t border-slate-200 text-xs text-slate-600">
                    <div>
                        Menampilkan <b>{{ $residents->firstItem() }}</b> - <b>{{ $residents->lastItem() }}</b>
                        dari <b>{{ $residents->total() }}</b> data
                    </div>

                    <div class="flex space-x-1 text-[12px]">
                        @if ($residents->onFirstPage())
                            <span
                                class="px-3 py-1 border border-slate-200 rounded bg-gray-100 text-slate-400 cursor-not-allowed">
                                Prev
                            </span>
                        @else
                            <a href="{{ $residents->previousPageUrl() }}"
                                class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">
                                Prev
                            </a>
                        @endif

                        @foreach ($residents->getUrlRange(1, $residents->lastPage()) as $page => $url)
                            @if ($page == $residents->currentPage())
                                <span class="px-3 py-1 border border-slate-200 bg-slate-800 text-white rounded">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($residents->hasMorePages())
                            <a href="{{ $residents->nextPageUrl() }}"
                                class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">
                                Next
                            </a>
                        @else
                            <span
                                class="px-3 py-1 border border-slate-200 rounded bg-gray-100 text-slate-400 cursor-not-allowed">
                                Next
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('citizenSearch', () => ({
                query: '',
                token: "1|z0WyGXFMTM9Jx6LJSIVg8Mim1DAxt44NdEkhy4yVff4a11e2",
                baseUrl: "http://localhost:8000/api",
                housingId: "{{ $housing->id ?? '' }}",
                isLoading: false,

                async searchCitizen() {
                    const name = this.query.trim();
                    const container = document.getElementById('residentContainer');

                    if (!name) {
                        container.innerHTML = `
                        <div class='p-4 text-center text-gray-500 bg-slate-50 border border-slate-200 rounded-lg'>
                            Silakan ketik nama warga untuk mencari data.
                        </div>`;
                        return;
                    }

                    this.isLoading = true;
                    container.innerHTML = `
                    <div class='p-4 text-center text-gray-500 bg-slate-50 border border-slate-200 rounded-lg animate-pulse'>
                        🔍 Mencari data warga <b>"${name}"</b>...
                    </div>`;

                    try {
                        const url =
                            `${this.baseUrl}/citizens/list?housing_id=${this.housingId}&search=${encodeURIComponent(this.query)}&per_page=30`;

                        const res = await fetch(url, {
                            method: "GET",
                            headers: {
                                "Accept": "application/json",
                                "Authorization": `Bearer ${this.token}`,
                                "X-Housing-Id": this.housingId,
                                "X-Role-Code": "housing_admin"
                            }
                        });

                        const json = await res.json();
                        console.log("📩 Response:", json);

                        this.isLoading = false;

                        if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                            container.innerHTML = json.data.map((c, i) => `
                            <div class="p-4 border border-slate-200 rounded-lg bg-white shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-slate-800">
                                        ${i + 1}. ${c.fullname ?? '-'}
                                    </h3>
                                    <span class="text-xs text-slate-500">${c.education_type ?? '-'}</span>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 text-sm mt-2 text-slate-600">
                                    <p><b>NIK:</b> ${c.citizen_card_number ?? '-'}</p>
                                    <p><b>Tempat Lahir:</b> ${c.birth_place ?? '-'}</p>
                                    <p><b>Tanggal Lahir:</b> ${c.birth_date ?? '-'}</p>
                                    <p><b>Jenis Kelamin:</b> ${c.gender ?? '-'}</p>
                                    <p><b>Agama:</b> ${c.religion ?? '-'}</p>
                                    <p><b>Status Perkawinan:</b> ${c.marital_status ?? '-'}</p>
                                    <p><b>Pekerjaan:</b> ${c.work_type ?? '-'}</p>
                                </div>
                            </div>
                        `).join('');
                        } else {
                            container.innerHTML = `
                            <div class='p-4 text-center text-amber-700 bg-amber-50 border border-amber-200 rounded-lg'>
                                ⚠️ Nama "<b>${name}</b>" tidak ditemukan.
                            </div>`;
                        }

                    } catch (err) {
                        console.error('❌ Gagal fetch:', err);
                        this.isLoading = false;
                        container.innerHTML = `
                        <div class='p-4 text-center text-red-700 bg-red-50 border border-red-200 rounded-lg'>
                            Terjadi kesalahan saat mengambil data. Coba lagi nanti.
                        </div>`;
                    }
                }
            }));
        });
    </script>
@endsection
