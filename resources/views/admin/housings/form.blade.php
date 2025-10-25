<form id="housing-form" action="{{ route('housings.store') }}" method="POST" class="space-y-6" x-data="locationDropdowns()"
    x-init="loadProvinces()">

    @csrf

    <!-- 🔹 Nama & Alamat -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perumahan</label>
            <input type="text" name="housing_name" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <input type="text" name="address" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" />
        </div>
    </div>

    <!-- 🔹 RT / RW -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
            <input type="text" name="rt" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
            <input type="text" name="rw" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700" />
        </div>
    </div>

    <!-- 🔹 Provinsi & Kabupaten -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Provinsi -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <select x-model="selectedProvince" @change="loadDistricts()" name="province_code"
                class="w-full px-4 py-2 border rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700">
                <option value="">Pilih Provinsi</option>
                <template x-for="prov in provinces" :key="prov.id">
                    <option :value="prov.code" x-text="prov.name"></option>
                </template>
            </select>
        </div>

        <!-- Kabupaten / Kota -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten / Kota</label>
            <select x-model="selectedDistrict" @change="loadSubdistricts()" name="district_code"
                class="w-full px-4 py-2 border rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700">
                <option value="">Pilih Kabupaten</option>
                <template x-for="dist in districts" :key="dist.id">
                    <option :value="dist.code" x-text="dist.name"></option>
                </template>
            </select>
        </div>
    </div>

    <!-- 🔹 Kecamatan & Desa -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Kecamatan -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
            <select x-model="selectedSubdistrict" @change="loadVillages()" name="subdistrict_code"
                class="w-full px-4 py-2 border rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700">
                <option value="">Pilih Kecamatan</option>
                <template x-for="subd in subdistricts" :key="subd.id">
                    <option :value="subd.code" x-text="subd.name"></option>
                </template>
            </select>
        </div>

        <!-- Desa -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
            <select x-model="selectedVillage" name="village_code"
                class="w-full px-4 py-2 border rounded-md
                       focus:ring-2 focus:ring-blue-500 text-gray-700">
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
        <input type="text" name="postal_code" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700" />
    </div>
</form>
