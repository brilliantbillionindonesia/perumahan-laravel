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
