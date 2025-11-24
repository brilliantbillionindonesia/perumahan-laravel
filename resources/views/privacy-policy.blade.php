<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serumpun Padi</title>

    <!-- EXTERNAL CSS -->
    <link rel="icon" type="images/x-icon" href="assets/images/logopadi.png" />
    <link rel="stylesheet" href="/assets/css/remixicon.css" />
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />

    <!-- TAILwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">

    <!-- NAVBAR -->
    <header class="w-full fixed top-0 left-0 z-50 bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
            <a href="/" class="logo-link flex items-center gap-2">
                <img src="/assets/images/logopadi.png" class="h-8" />
                <span class="text-lg font-bold text-[#E53935]">Serumpun Padi</span>
            </a>

            <ul class="hidden md:flex gap-10 text-[17px] font-semibold">
                <li><a href="/#home" class="nav-default">Home</a></li>
                <li><a href="/#about" class="nav-default">About</a></li>
                <li><a href="/#features" class="nav-default">Features</a></li>
                <li><a href="/#download" class="nav-default">Download</a></li>
            </ul>

            <button id="navToggle" class="md:hidden text-3xl">
                <i class="ri-menu-line"></i>
            </button>
        </div>

        <div id="mobileMenu" class="hidden flex-col bg-white border-t px-6 py-4 space-y-3 md:hidden shadow-sm">
            <a href="/#home" class="nav-default block">Home</a>
            <a href="/#about" class="nav-default block">About</a>
            <a href="/#features" class="nav-default block">Features</a>
            <a href="/#download" class="nav-default block">Download</a>
        </div>
    </header>


    <!-- MAIN CONTENT -->
    <main class="pt-24 pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-2">

            <div class="bg-white rounded-2xl shadow p-6 sm:p-10">

                <!-- JUDUL -->
                <div class="mb-12">
                    <h1 class="text-3xl font-bold text-center text-gray-900">
                        Kebijakan Privasi
                    </h1>
                </div>

                <!-- WRAPPER UNTUK MENYEJAJARKAN SEMUA KONTEN (SOLUSI MOBILE) -->
                <div class="space-y-10 px-2 sm:px-0">

                    <x-pp-title number="1">Tentang Serumpun Padi</x-pp-title>
                    <x-pp-text>
                        Serumpun Padi adalah platform digital manajemen perumahan berbasis komunitas
                        yang membantu warga dan pengurus dalam mengelola data, iuran, informasi,
                        serta komunikasi lingkungan secara transparan, cepat, dan terpusat.
                    </x-pp-text>

                    <x-pp-title number="2">Informasi yang Kami Kumpulkan</x-pp-title>
                    <x-pp-text>
                        Untuk menyediakan layanan terbaik, Serumpun Padi dapat mengumpulkan informasi berikut dari
                        pengguna:
                    </x-pp-text>

                    <x-pp-list :items="[
                        'Data pribadi seperti nama, alamat, nomor telepon, dan email.',
                        'Data rumah dan keluarga seperti blok, nomor rumah, dan anggota keluarga.',
                        'Aktivitas penggunaan aplikasi seperti pelaporan, pembayaran iuran, dan login.',
                    ]" />

                    <x-pp-title number="3">Bagaimana Data Digunakan</x-pp-title>
                    <x-pp-text>
                        Data digunakan untuk kebutuhan berikut:
                    </x-pp-text>

                    <x-pp-list :items="[
                        'Memfasilitasi komunikasi antara warga dan pengurus.',
                        'Menampilkan informasi iuran, laporan, dan aktivitas warga.',
                        'Meningkatkan keamanan dan personalisasi aplikasi.',
                        'Memberikan notifikasi penting terkait lingkungan dan sistem.',
                    ]" />

                    <x-pp-title number="4">Keamanan Data</x-pp-title>
                    <x-pp-text>
                        Kami menjaga data Anda dengan enkripsi dan kontrol akses terbatas.
                        Data tidak dibagikan ke pihak ketiga tanpa izin kecuali sesuai hukum.
                    </x-pp-text>

                    <x-pp-title number="5">Hak Pengguna</x-pp-title>
                    <x-pp-text>
                        Anda berhak untuk:
                    </x-pp-text>

                    <x-pp-list :items="[
                        'Mengakses dan memperbarui data pribadi.',
                        'Meminta penghapusan data tertentu.',
                        'Menolak penggunaan data untuk promosi.',
                    ]" />

                    <x-pp-title number="6">Penyimpanan Data</x-pp-title>
                    <x-pp-text>
                        Data Anda disimpan aman di server penyedia terpercaya
                        dan hanya disimpan selama diperlukan.
                    </x-pp-text>

                    <x-pp-title number="7">Perubahan Kebijakan</x-pp-title>
                    <x-pp-text>
                        Kebijakan dapat diperbarui sewaktu-waktu. Notifikasi akan diberikan dalam aplikasi
                        jika ada perubahan besar.
                    </x-pp-text>

                    <x-pp-title number="8">Kontak Kami</x-pp-title>
                    <x-pp-text>
                        📧 Email:
                        <a href="mailto:support@serumpunpadi.com" class="text-blue-600">support@serumpunpadi.com</a><br>
                        🌐 Website:
                        <a href="https://serumpunpadi.com" class="text-blue-600">serumpunpadi.com</a>
                    </x-pp-text>

                </div>
                <!-- END WRAPPER -->

            </div>
        </div>
    </main>
    <!-- FOOTER -->
    <footer class="pt-4 pb-4 bg-[#E53935]">
        <div class="container px-3">

            <!-- TOP CONTENT -->
            <div class="row gy-4">

                <!-- Brand + Desc -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="assets/images/logoputih.png" style="height: 30px;">
                        <h5 class="m-0 text-white fw-semibold">Serumpun Padi</h5>
                    </div>

                    <p class="text-light" style="font-size:0.9rem; line-height:1.6; max-width:300px;">
                        Aplikasi manajemen perumahan berbasis digital
                        yang membantu pengurus dan warga mengelola data,
                        iuran, serta informasi secara mudah, cepat, dan transparan.
                    </p>
                </div>

                <!-- Sitemap -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-semibold text-white mb-3">Sitemap</h6>
                    <ul class="list-unstyled" style="font-size:0.9rem; line-height:1.9;">
                        <li><a href="#" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="#" class="text-light text-decoration-none">About</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Features</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Download</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-semibold text-white mb-3">Legal</h6>
                    <ul class="list-unstyled" style="font-size:0.9rem; line-height:1.9;">
                        <li><a href="{{ route('privacy-policy') }}" class="text-light text-decoration-none">Privacy
                                Policy</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Terms Of Condition</a></li>
                    </ul>
                </div>

            </div>

            <hr class="border-light opacity-50 my-4">

            <!-- Bottom section -->
            <div class="row align-items-center">

                <div class="col-lg-6 text-lg-start text-center mb-3 mb-lg-0">
                    <p class="m-0 text-light fw-semibold" style="font-size:1rem;">
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        Serumpun Padi — All right reserved
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex justify-content-lg-end justify-content-center gap-3">
                        <a href="#"><i class="ri-facebook-fill text-white"></i></a>
                        <a href="#"><i class="ri-instagram-line text-white"></i></a>
                        <a href="#"><i class="ri-twitter-fill text-white"></i></a>
                        <a href="#"><i class="ri-youtube-fill text-white"></i></a>
                    </div>
                </div>

            </div>

        </div>
    </footer>


    <!-- JS: MOBILE NAV -->
    <script>
        const toggle = document.getElementById('navToggle');
        const menu = document.getElementById('mobileMenu');

        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>

    <!-- NAV CSS -->
    <style>
        .nav-default {
            color: #4A4A68;
            font-weight: 600;
            transition: .25s ease;
            text-decoration: none !important;
        }

        .nav-default:hover {
            color: #F28B82;
        }

        .logo-link {
            text-decoration: none !important;
        }
    </style>

</body>

</html>
