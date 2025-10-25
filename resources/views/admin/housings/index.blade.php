@extends('layouts.app')

@section('title', 'Housings Management')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <div x-data="housingForm()" x-init="loadProvinces()">

            {{-- Header dan tombol --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Perumahan</h2>
                <button @click="openAdd()"
                    class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
                </button>
            </div>

            {{-- Form Search --}}
            <div>
                <form method="GET" action="{{ route('housings.index') }}" class="relative mb-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perumahan..."
                        class="bg-white w-full pr-10 h-10 pl-3 py-2 placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-200 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md" />
                    <button type="submit"
                        class="absolute h-8 w-8 right-1 top-1 flex items-center justify-center rounded text-slate-600 hover:bg-slate-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>

                <!-- Table -->
                <div class="relative flex flex-col w-full overflow-hidden text-gray-700 bg-white shadow-md rounded-lg">
                    <table class="w-full text-left table-auto text-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold text-center w-10">No
                                </th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Nama</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Alamat</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold text-center">RT/RW
                                </th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Provinsi</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Kota/Kabupaten</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Kecamatan</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold">Desa</th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold whitespace-nowrap">
                                    Kode Pos
                                </th>
                                <th class="p-3 border-b border-slate-200 text-slate-700 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($housings as $index => $housing)
                                <tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors">
                                    <td class="p-3 text-center text-slate-500">{{ $housings->firstItem() + $index }}</td>
                                    <td class="p-3 font-medium text-slate-800">{{ $housing->housing_name }}</td>
                                    <td class="p-3 text-slate-600">{{ $housing->address }}</td>
                                    <td class="p-3 text-center text-slate-600">{{ $housing->rt }}/{{ $housing->rw }}</td>
                                    <td class="p-3 text-slate-600">{{ $housing->province->name ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $housing->district->name ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $housing->subdistrict->name ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $housing->village->name ?? '-' }}</td>
                                    <td class="p-3 text-slate-600 whitespace-nowrap">{{ $housing->postal_code }}</td>
                                    <td class="p-3 text-center space-x-2">
                                        <div class="flex items-center space-x-1">
                                            <a href="{{ route('admin.housings.residents', $housing->id) }}"
                                                class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-md transition">
                                                Detail
                                            </a>
                                            <!-- Tombol Edit -->
                                            <a href="#" @click.prevent="openEdit('{{ $housing->id }}')"
                                                class="px-2 py-1 text-blue-600 border border-blue-500 rounded-md text-xs font-medium hover:bg-blue-500 hover:text-white transition cursor-pointer">
                                                Edit
                                            </a>

                                            <!-- Tombol Delete -->
                                            <form action="{{ route('housings.destroy', $housing->id) }}" method="POST"
                                                class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="px-2 py-1 text-red-600 border border-red-500 rounded-md text-xs font-medium hover:bg-red-500 hover:text-white transition delete-btn">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-4 text-center text-slate-500">Belum ada data perumahan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Modal Tambah -->
                    <div x-show="isOpenAdd" x-cloak
                        class="fixed inset-0 flex items-center justify-center z-50 bg-opacity-50">
                        <div @click.away="isOpenAdd = false"
                            class="bg-white w-full max-w-3xl mx-auto rounded-lg shadow-lg p-6 relative">
                            <h3 class="text-lg font-semibold mb-4">Tambah Data Perumahan</h3>

                            @include('admin.housings.form')

                            <div class="flex justify-end mt-4 space-x-2">
                                <button @click="closeModal()"
                                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    Tutup
                                </button>
                                <button type="submit" form="housing-form"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit -->
                    <div x-show="isOpenEdit" x-cloak
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black/40">
                        <div @click.away="isOpenEdit = false"
                            class="bg-white w-full max-w-3xl mx-auto rounded-lg shadow-lg p-6 relative">
                            <h3 class="text-lg font-semibold mb-4">Edit Data Perumahan</h3>

                            {{-- panggil form dari file edit --}}
                            @include('admin.housings.edit')

                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if ($housings->hasPages())
                        <div
                            class="flex justify-between items-center px-4 py-3 bg-gray-50 border-t border-slate-200 text-xs text-slate-600">
                            <div>
                                Menampilkan <b>{{ $housings->firstItem() }}</b> - <b>{{ $housings->lastItem() }}</b>
                                dari <b>{{ $housings->total() }}</b> data
                            </div>
                            <div class="flex space-x-1 text-[12px]">
                                @if ($housings->onFirstPage())
                                    <span
                                        class="px-3 py-1 border border-slate-200 rounded bg-gray-100 text-slate-400 cursor-not-allowed">Prev</span>
                                @else
                                    <a href="{{ $housings->previousPageUrl() }}"
                                        class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">Prev</a>
                                @endif

                                @foreach ($housings->getUrlRange(1, $housings->lastPage()) as $page => $url)
                                    @if ($page == $housings->currentPage())
                                        <span
                                            class="px-3 py-1 border border-slate-200 bg-slate-800 text-white rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}"
                                            class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($housings->hasMorePages())
                                    <a href="{{ $housings->nextPageUrl() }}"
                                        class="px-3 py-1 border border-slate-200 rounded hover:bg-slate-100 transition">Next</a>
                                @else
                                    <span
                                        class="px-3 py-1 border border-slate-200 rounded bg-gray-100 text-slate-400 cursor-not-allowed">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert dan Alpine.js script tetap sama --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.querySelector('.delete-btn').addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus data ini?',
                        text: "Data yang dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('housingForm', () => ({
                // === STATE ===
                isOpenAdd: false,
                isOpenEdit: false,
                editData: {},
                provinces: [],
                districts: [],
                subdistricts: [],
                villages: [],
                selectedProvince: '',
                selectedDistrict: '',
                selectedSubdistrict: '',
                selectedVillage: '',

                baseUrl: "http://localhost:8000/api",
                token: "1|z0WyGXFMTM9Jx6LJSIVg8Mim1DAxt44NdEkhy4yVff4a11e2", // ganti sesuai token kamu

                // =============================
                // CRUD
                // =============================
                async updateHousing(id) {
                    try {
                        console.log("🚀 Mengirim update untuk ID:", id);
                        const form = document.getElementById('housing-form-edit');
                        const formData = new FormData(form);

                        const res = await fetch(`/admin/housings/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        if (!res.ok) {
                            const text = await res.text();
                            console.error("❌ Gagal update:", text);
                            alert("Gagal menyimpan perubahan data perumahan.");
                            return;
                        }

                        const result = await res.json();
                        console.log("✅ Update berhasil:", result);

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#f0f9ff',
                            color: '#1e3a8a',
                            iconColor: '#2563eb',
                        });

                        Toast.fire({
                            icon: 'info',
                            title: result.message || 'Data berhasil diperbarui!'
                        });
                        this.isOpenEdit = false;
                        setTimeout(() => window.location.reload(), 2000);

                    } catch (err) {
                        console.error("💥 Error updateHousing:", err);
                        alert("Terjadi kesalahan saat menyimpan perubahan.");
                    }
                },

                // =============================
                // MODALS
                // =============================
                openAdd() {
                    this.resetForm();
                    this.isOpenAdd = true;
                },
                async openEdit(id) {
                    this.resetForm();
                    this.isOpenEdit = true;

                    try {
                        await this.loadProvinces();
                        const res = await fetch(`/admin/housings/${id}`);
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const result = await res.json();

                        this.editData = result.data ?? result;

                        this.selectedProvince = this.editData.province_code ?? '';
                        await this.loadDistricts();

                        this.selectedDistrict = this.editData.district_code ?? '';
                        await this.loadSubdistricts();

                        this.selectedSubdistrict = this.editData.subdistrict_code ?? '';
                        await this.loadVillages();

                        this.selectedVillage = this.editData.village_code ?? '';

                    } catch (err) {
                        console.error("❌ Gagal memuat data:", err);
                        alert("Terjadi kesalahan saat memuat data perumahan.");
                        this.isOpenEdit = false;
                    }
                },
                closeModal() {
                    this.isOpenAdd = false;
                    this.isOpenEdit = false;
                },
                resetForm() {
                    this.editData = {};
                    this.selectedProvince = '';
                    this.selectedDistrict = '';
                    this.selectedSubdistrict = '';
                    this.selectedVillage = '';
                },

                // =============================
                // API CALLS
                // =============================
                async loadProvinces() {
                    try {
                        const url =
                            `${this.baseUrl}/master/list?entity=provinces&columns[]=code&columns[]=name&columns[]=id&per_page=40`;
                        const res = await fetch(url, {
                            headers: {
                                "Authorization": `Bearer ${this.token}`,
                                "Accept": "application/json"
                            }
                        });
                        const json = await res.json();
                        this.provinces = json.data || [];
                        console.log("🌍 Provinces loaded:", this.provinces.length);
                    } catch (err) {
                        console.error("❌ Gagal load provinces:", err);
                    }
                },

                async loadDistricts() {
                    if (!this.selectedProvince) return;
                    try {
                        const url = `${this.baseUrl}/master/list?entity=districts` +
                            `&columns[]=id&columns[]=code&columns[]=name&columns[]=province_code` +
                            `&filters[0][column]=province_code` +
                            `&filters[0][operator]=` +
                            `&filters[0][value]=${this.selectedProvince}` +
                            `&per_page=40`;

                        const res = await fetch(url, {
                            headers: {
                                "Authorization": `Bearer ${this.token}`,
                                "Accept": "application/json"
                            }
                        });

                        const json = await res.json();
                        this.districts = json.data || [];
                        console.log("🏙️ Districts loaded:", this.districts.length);
                    } catch (err) {
                        console.error("❌ Gagal load districts:", err);
                    }
                },

                async loadSubdistricts() {
                    if (!this.selectedDistrict) {
                        this.subdistricts = [];
                        this.villages = [];
                        return;
                    }

                    this.subdistricts = [];
                    this.villages = [];

                    try {
                        const districtCode = this.selectedDistrict; // jangan ubah jadi 6 digit!

                        const url = `${this.baseUrl}/master/list?entity=subdistricts` +
                            `&columns[]=id&columns[]=code&columns[]=name` +
                            `&columns[]=province_code&columns[]=district_code` +
                            `&filters[0][column]=district_code` +
                            `&filters[0][operator]=` +
                            `&filters[0][value]=${districtCode}` +
                            `&order_by=code` +
                            `&per_page=40`;

                        console.log("🌐 Fetching subdistricts:", url);

                        const res = await fetch(url, {
                            headers: {
                                "Authorization": `Bearer ${this.token}`,
                                "Accept": "application/json"
                            }
                        });

                        const json = await res.json();
                        this.subdistricts = json.data || [];
                        console.log(`✅ Loaded ${this.subdistricts.length} subdistricts`);
                    } catch (err) {
                        console.error("💥 Gagal load subdistricts:", err);
                    }
                },

                async loadVillages() {
                    if (!this.selectedSubdistrict) return;
                    try {
                        const url = `${this.baseUrl}/master/list?entity=villages` +
                            `&columns[]=id&columns[]=code&columns[]=name` +
                            `&columns[]=province_code&columns[]=district_code&columns[]=subdistrict_code` +
                            `&filters[0][column]=subdistrict_code` +
                            `&filters[0][operator]=` +
                            `&filters[0][value]=${this.selectedSubdistrict}` +
                            `&per_page=40`;
                        const res = await fetch(url, {
                            headers: {
                                "Authorization": `Bearer ${this.token}`,
                                "Accept": "application/json"
                            }
                        });

                        const json = await res.json();
                        this.villages = json.data || [];
                        console.log(`🏘️ Loaded ${this.villages.length} villages`);
                    } catch (err) {
                        console.error("❌ Gagal load villages:", err);
                    }
                },
            }));
        });
    </script>
@endsection
