<form id="housing-form-edit" method="POST" @submit.prevent="updateHousing(editData.id)" class="space-y-6">

    @csrf
    @method('PUT')

    <!-- 🔹 Nama & Alamat -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perumahan</label>
            <input type="text" x-model="editData.housing_name" name="housing_name"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <input type="text" x-model="editData.address" name="address"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required />
        </div>
    </div>

    <!-- 🔹 RT / RW -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
            <input type="text" x-model="editData.rt" name="rt"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
            <input type="text" x-model="editData.rw" name="rw"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500" />
        </div>
    </div>

    <!-- 🔹 Provinsi & Kabupaten -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <select x-model="selectedProvince" @change="loadDistricts()" name="province_code"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Provinsi</option>
                <template x-for="prov in provinces" :key="prov.id">
                    <option :value="prov.code" x-text="prov.name"></option>
                </template>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten / Kota</label>
            <select x-model="selectedDistrict" @change="loadSubdistricts()" name="district_code"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Kabupaten</option>
                <template x-for="dist in districts" :key="dist.id">
                    <option :value="dist.code" x-text="dist.name"></option>
                </template>
            </select>
        </div>
    </div>

    <!-- 🔹 Kecamatan & Desa -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
            <select x-model="selectedSubdistrict" @change="loadVillages()" name="subdistrict_code"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Kecamatan</option>
                <template x-for="subd in subdistricts" :key="subd.id">
                    <option :value="subd.code" x-text="subd.name"></option>
                </template>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
            <select x-model="selectedVillage" name="village_code"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                       focus:ring-2 focus:ring-blue-500">
                <option value="">Pilih Desa</option>
                <template x-for="village in villages" :key="village.id">
                    <option :value="village.code" x-text="village.name"></option>
                </template>
            </select>
        </div>
    </div>

    <!-- 🔹 Kode Pos -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
        <input type="text" x-model="editData.postal_code" name="postal_code"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-800
                   focus:ring-2 focus:ring-blue-500"
            required />
    </div>

    <!-- 🔹 Tombol -->
    <div class="flex justify-end mt-6 space-x-2">
        <button type="button" @click="closeModal()"
            class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-2 rounded-md text-sm font-medium transition">
            Tutup
        </button>
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium transition">
            Simpan Perubahan
        </button>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('housingForm', () => ({
            baseUrl: "http://localhost:8000/api",
            token: "6|j8qBYMu97KLiWVLJFKOKTkSpiaC89lFGQNK4fRrk1d57e3f7",

            // === STATE ===
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

            // === LOAD DATA ===
            async loadProvinces() {
                const res = await fetch(
                    `${this.baseUrl}/master/list?entity=provinces&columns[]=id&columns[]=code&columns[]=name&per_page=40`, {
                        headers: {
                            "Authorization": `Bearer ${this.token}`,
                            "Accept": "application/json"
                        }
                    });
                const json = await res.json();
                this.provinces = json.data || [];
            },
            async loadDistricts() {
                if (!this.selectedProvince) return;
                const res = await fetch(
                    `${this.baseUrl}/master/list?entity=districts&columns[]=id&columns[]=code&columns[]=name&filters[0][column]=province_code&filters[0][operator]==&filters[0][value]=${this.selectedProvince}`, {
                        headers: {
                            "Authorization": `Bearer ${this.token}`,
                            "Accept": "application/json"
                        }
                    });
                const json = await res.json();
                this.districts = json.data || [];
            },
            async loadSubdistricts() {
                if (!this.selectedDistrict) return;
                const res = await fetch(
                    `${this.baseUrl}/master/list?entity=subdistricts&columns[]=id&columns[]=code&columns[]=name&filters[0][column]=district_code&filters[0][operator]==&filters[0][value]=${this.selectedDistrict}`, {
                        headers: {
                            "Authorization": `Bearer ${this.token}`,
                            "Accept": "application/json"
                        }
                    });
                const json = await res.json();
                this.subdistricts = json.data || [];
            },
            async loadVillages() {
                if (!this.selectedSubdistrict) return;
                const res = await fetch(
                    `${this.baseUrl}/master/list?entity=villages&columns[]=id&columns[]=code&columns[]=name&filters[0][column]=subdistrict_code&filters[0][operator]==&filters[0][value]=${this.selectedSubdistrict}`, {
                        headers: {
                            "Authorization": `Bearer ${this.token}`,
                            "Accept": "application/json"
                        }
                    });
                const json = await res.json();
                this.villages = json.data || [];
            },

            // === OPEN EDIT MODAL ===
            async openEdit(id) {
                try {
                    const res = await fetch(`/admin/housings/${id}`);
                    const result = await res.json();

                    this.editData = result.data ?? result;
                    this.isOpenEdit = true;

                    await this.loadProvinces();
                    this.selectedProvince = this.editData.province_code ?? '';
                    await this.loadDistricts();
                    this.selectedDistrict = this.editData.district_code ?? '';
                    await this.loadSubdistricts();
                    this.selectedSubdistrict = this.editData.subdistrict_code ?? '';
                    await this.loadVillages();
                    this.selectedVillage = this.editData.village_code ?? '';

                } catch (err) {
                    console.error("❌ Gagal load data edit:", err);
                    alert("Gagal memuat data perumahan.");
                }
            },

            // === UPDATE / SIMPAN PERUBAHAN ===
            async updateHousing(id) {
                try {
                    console.log("🚀 SUBMITTING UPDATE...", id);

                    const form = document.getElementById('housing-form-edit');
                    const formData = new FormData(form);
                    formData.append('_method', 'PUT');

                    const res = await fetch(`/admin/housings/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!res.ok) throw new Error("HTTP error " + res.status);

                    const result = await res.json();
                    console.log("✅ Update result:", result);

                    // ✅ Tampilkan Toast pakai SweetAlert2 (pojok kanan atas)
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#f8fafc',
                        color: '#1e293b',
                        iconColor: '#16a34a',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: result.message || 'Data berhasil diperbarui!'
                    });

                    this.isOpenEdit = false;
                    setTimeout(() => window.location.reload(), 2000);

                } catch (err) {
                    console.error("💥 Error updateHousing:", err);

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#fef2f2',
                        color: '#7f1d1d',
                        iconColor: '#dc2626',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'Gagal memperbarui data!'
                    });
                }
            },
        }));
    });
</script>
