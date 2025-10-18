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

<script>
    function locationDropdowns() {
        const baseUrl = "http://localhost:8000/api";
        const token = "6|j8qBYMu97KLiWVLJFKOKTkSpiaC89lFGQNK4fRrk1d57e3f7"; // ganti sesuai environment

        return {
            provinces: [],
            districts: [],
            subdistricts: [],
            villages: [],

            selectedProvince: '',
            selectedDistrict: '',
            selectedSubdistrict: '',
            selectedVillage: '',

            // === Load Provinces ===
            async loadProvinces() {
                console.log("🔄 Memuat data provinsi...");
                try {
                    const url =
                        `${baseUrl}/master/list?entity=provinces&columns[]=code&columns[]=name&columns[]=id&per_page=40`;
                    const res = await fetch(url, {
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "Accept": "application/json"
                        }
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const json = await res.json();
                    console.log("✅ Data provinsi berhasil dimuat:", json);
                    this.provinces = json.data || [];
                } catch (err) {
                    console.error("❌ Gagal load provinces:", err);
                }
            },

            // === Load Districts (Kabupaten/Kota) ===
            async loadDistricts() {
                if (!this.selectedProvince) {
                    this.districts = [];
                    this.subdistricts = [];
                    this.villages = [];
                    return;
                }

                this.districts = [];
                this.subdistricts = [];
                this.villages = [];

                const url = `${baseUrl}/master/list?entity=districts` +
                    `&columns[]=id&columns[]=code&columns[]=name&columns[]=province_code` +
                    `&filters[0][column]=province_code&filters[0][operator]==&filters[0][value]=${this.selectedProvince}`;

                try {
                    const res = await fetch(url, {
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "Accept": "application/json"
                        }
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const json = await res.json();
                    console.log("✅ Districts:", json);
                    this.districts = json.data || [];
                } catch (err) {
                    console.error("❌ Gagal load districts:", err);
                }
            },

            // === Load Subdistricts (Kecamatan) ===
            async loadSubdistricts() {
                if (!this.selectedDistrict) {
                    this.subdistricts = [];
                    this.villages = [];
                    return;
                }

                this.subdistricts = [];
                this.villages = [];

                // Cari object district (jika ada)
                const found = this.districts.find(d => d.code == this.selectedDistrict || d.id == this
                    .selectedDistrict);
                let districtCode = found ? (found.code || this.selectedDistrict) : this.selectedDistrict;

                // Jika districtCode formatnya 4 digit, ubah jadi 6 digit (contoh: "1304" -> "130401")
                // Pilihan padding: append '01' atau padEnd sesuai kebutuhan dataset
                if (typeof districtCode === 'string' && districtCode.length === 4) {
                    // jika found punya province_code, gabungkan agar lebih akurat
                    if (found && found.province_code) {
                        // contoh: province_code "13", districtCode "1304" => kita gunakan last 2 digit dari districtCode (04)
                        // sehingga jadi province_code + last2 => "13" + "04" => "1304" (masih 4),
                        // Tapi tabel subdistricts punya district_code 6 digit (contoh 130401),
                        // Maka kita append '01' sebagai default suffix kecamatan pertama
                        districtCode = `${districtCode}01`;
                    } else {
                        districtCode = districtCode + '01';
                    }
                }

                console.log("📍 Fetching subdistricts with district_code:", districtCode);

                const url = `${baseUrl}/master/list?entity=subdistricts` +
                    `&columns[]=id&columns[]=code&columns[]=name&columns[]=province_code&columns[]=district_code` +
                    `&filters[0][column]=district_code&filters[0][operator]==&filters[0][value]=${districtCode}`;

                try {
                    const res = await fetch(url, {
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "Accept": "application/json"
                        }
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const json = await res.json();
                    console.log("✅ Subdistricts Response:", json);
                    this.subdistricts = json.data || [];
                } catch (err) {
                    console.error("💥 Gagal load subdistricts:", err);
                }
            },

            // === Load Villages (Desa) ===
            async loadVillages() {
                if (!this.selectedSubdistrict) {
                    this.villages = [];
                    return;
                }

                this.villages = [];

                console.log("📍 Fetching villages with subdistrict_code:", this.selectedSubdistrict);

                const url = `${baseUrl}/master/list?entity=villages` +
                    `&columns[]=id&columns[]=code&columns[]=name&columns[]=province_code&columns[]=district_code&columns[]=subdistrict_code` +
                    `&filters[0][column]=subdistrict_code&filters[0][operator]==&filters[0][value]=${this.selectedSubdistrict}`;

                try {
                    const res = await fetch(url, {
                        headers: {
                            "Authorization": `Bearer ${token}`,
                            "Accept": "application/json"
                        }
                    });
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    const json = await res.json();
                    console.log("✅ Villages Response:", json);
                    this.villages = json.data || [];
                } catch (err) {
                    console.error("❌ Gagal load villages:", err);
                }
            }
        };
    }
</script>
