<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serumpun Padi</title>
    <link rel="icon" type="images/x-icon" href="assets/images/logopadi.png" />
    <!-- remix icon  -->
    <link rel="stylesheet" href="assets/css/remixicon.css" />
    <!-- Swiper-slider Css -->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css" />
    <!-- bootstrap  -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/style.min.css" />
</head>

<body data-bs-spy="scroll" data-bs-target="#navbarCollapse"
    style="--bs-primary: #F28B82; --bs-primary-rgb: 35, 169, 167;">
    <div class="modal fade" id="exampleModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <video id="VisaChipCardVideo" class="video-box" controls>
                            <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4" />
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- header section start with navbar -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky sticky-light info-nav" id="navbar">
            <div class="container">
                <a class="navbar-brand logo-2 d-flex align-items-center" href="#">
                    <img src="assets/images/logopadi.png" alt=""
                        style="height: 35px; width: auto; margin-right: 8px;">
                    <span class="navbar-caption fs-4 fw-bold" style="color: #E53935;">
                        Serumpun Padi
                    </span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fw-bold text-dark fs-4"><i class="ri-menu-5-line"></i></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav navbar-link ms-auto" id="navbar-navlist">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#download">Download</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- header part end  -->
    <!-- hero section start  -->
    <section class="hero-part bg-img-5 bg-home-5 pb-5" id="home">
        <div class="container">
            <!-- row start  -->
            <div class="row position-relative align-items-center justify-content-between">
                <div class="col-xl-6 col-lg-12 col-md-12">
                    <div class="hero-text">
                        <h1 class="display-1 text-dark">
                            <!-- GAMBAR UNTUK MOBILE -->
                            <img src="assets/images/screen3.png" alt="serumpun padi app" width="340"
                                class="img-fluid d-block d-lg-none mx-auto hero-mobile-img mb-3" />
                            <style>
                                @media (max-width: 768px) {
                                    .hero-mobile-img {
                                        margin-top: -145px !important;
                                        /* geser ke atas */
                                    }
                                }
                            </style>
                            <span class="fw-semibold" style="color: #E53935;"><b> Capek Urusan Perumahan Yang Ribet ?
                                </b></span>
                            {{-- <span class="fw-semibold" style="color: #E53935;"> Capek update data warga masih manual ? --}}
                            </span>
                        </h1>
                        <p class="fw-medium mt-3 lh-base">
                            Capek input data warga <b>satu-satu</b> ?<br>
                            Capek data warga tidak <b>rapih</b> ?<br>
                            Capek <b>mengingatkan</b> penagihan tiap bulan ?<br>
                            Sering <b>lupa untuk verifikasi</b> pembayaran iuran warga ?<br>
                            <br>
                            Jika semua itu terdengar familiar, berarti saatnya Anda punya sistem yang membantu — bukan
                            membebani. <b>Serumpun Padi</b> jawabannya.
                        </p>
                    </div>
                    <div class="main-btn mt-4">
                        <a href="#" class="btn p-0 m-0 ms-2"><img src="assets/images/play-store.png"
                                alt="" /></a>
                    </div>
                    <div class="hero-icon mt-5">
                        <h5 class="my-4">Followed By :</h5>
                        <ul class="d-flex">
                            <li>
                                <a href="#" class="text-primary me-2"><i
                                        class="ri-facebook-fill p-2 border rounded-circle"></i></a>
                            </li>
                            <li>
                                <a href="#" class="text-primary mx-2"><i
                                        class="ri-instagram-fill p-2 border rounded-circle"></i></a>
                            </li>
                            <li>
                                <a href="#" class="text-primary mx-2"><i
                                        class="ri-twitter-fill p-2 border rounded-circle"></i></a>
                            </li>
                            <li>
                                <a href="#" class="text-primary mx-2"><i
                                        class="ri-github-fill p-2 border rounded-circle"></i></a>
                            </li>
                            <li>
                                <a href="#" class="text-primary mx-2"><i
                                        class="ri-google-fill p-2 border rounded-circle"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- <div class="col-lg-6 text-center">
                    <img src="images/banner-img.png" alt=""
                        class="img-fluid mb-5 w-75" />
                </div> -->
            </div>
            <!-- row end  -->
            <img src="assets/images/screen3.png" width="946" height="749"
                class="img-fluid hend-img position-absolute start-50" />
        </div>
    </section>
    <!-- hero section end  -->
    <section class="section counter-part bg-light pt-5 pb-5">
        <div class="container">
            <!-- row start  -->
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-3">
                    <div class="counter-no text-center d-flex p-4">
                        <div class="icon mb-5">
                            <i class="ri-team-line fs-1 p-3 bg-white rounded-3 text-primary shadow-sm"></i>
                        </div>
                        <div class="d-block ms-4 text-start">
                            <div class="number">
                                <h2 class="text-secondary fw-bold">1000+</h2>
                            </div>
                            <div class="content">
                                <p>Jumlah Warga</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="counter-no text-center d-flex p-4 shadow-none">
                        <div class="icon mb-5">
                            <i class="ri-parent-line fs-1 p-3 bg-white rounded-3 text-primary shadow-sm"></i>
                        </div>
                        <div class="d-block ms-4 text-start">
                            <div class="number">
                                <h2 class="text-secondary fw-bold">60+</h2>
                            </div>
                            <div class="content">
                                <p>Kepala Keluarga</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="counter-no text-center d-flex p-4">
                        <div class="icon mb-5">
                            <i class="ri-home-8-line fs-1 p-3 bg-white rounded-3 text-primary shadow-sm"></i>
                        </div>
                        <div class="d-block ms-4 text-start">
                            <div class="number">
                                <h2 class="text-secondary fw-bold">4</h2>
                            </div>
                            <div class="content">
                                <p>Perumahan</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="counter-no text-center d-flex p-4">
                        <div class="icon mb-5">
                            <i class="ri-hourglass-fill fs-1 p-3 bg-white rounded-3 text-primary shadow-sm"></i>
                        </div>
                        <div class="d-block ms-4 text-start">
                            <div class="number">
                                <h2 class="text-secondary fw-bold">500K</h2>
                            </div>
                            <div class="content">
                                <p>Keuangan Tercatat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row end  -->
        </div>
    </section>
    <section class="section about-part-5 hero-part pt-3 pb-3" id="about">
        <div class="container">
            <div class="about-header text-center mb-3 mb-md-5 pb-2 pb-md-5 pt-2 pt-md-5">
                <div class="title-sm">
                    <h2 class="text-primary">Latar Belakang</h2>
                </div>

                <div class="main-title mt-3">
                    <p class="mt-2 lh-base">
                        Banyak perumahan menghadapi masalah serupa: pencatatan manual yang tidak terpusat,
                        transparansi keuangan yang terbatas, serta respons keamanan dan aduan yang lambat.
                        Karena itu, Serumpun Padi hadir untuk membantu menciptakan lingkungan yang lebih
                        tertib, aman, dan transparan dengan sistem terintegrasi real-time.
                    </p>
                </div>
            </div>

            <!-- FULL WIDTH BACKGROUND -->
            <div class="w-100 bg-light py-4">
                <div class="container px-3 px-md-5">

                    <div class="row g-3">

                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="content shadow text-start bg-white p-4 rounded-4">
                                <div class="icon">
                                    <i class="ri-slideshow-line fs-1 text-primary"></i>
                                </div>
                                <div class="mt-4">
                                    <h6>Pengelolaan Manual dan Tidak Terpusat</h6>
                                    <p class="lh-base">
                                        Banyak kompleks perumahan masih mengelola data warga,
                                        keuangan, aduan, dan komunikasi warga secara manual.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="content shadow text-start bg-white p-4 rounded-4">
                                <div class="icon">
                                    <i class="ri-clipboard-line fs-1 text-primary"></i>
                                </div>
                                <div class="mt-4">
                                    <h6>Minim Transparansi dan Akuntabilitas</h6>
                                    <p class="lh-base">
                                        Warga sering tidak mengetahui kondisi keuangan lingkungan,
                                        seperti saldo kas & penggunaan dana.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="content shadow text-start bg-white p-4 rounded-4">
                                <div class="icon">
                                    <i class="ri-shield-user-line fs-1 text-primary"></i>
                                </div>
                                <div class="mt-4">
                                    <h6>Kurangnya Sistem Cepat dan Keamanan</h6>
                                    <p class="lh-base">
                                        Penanganan aduan dan situasi darurat sering terlambat
                                        karena tidak adanya sistem real-time.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>
    <section class="section services-part-5 bg-light" id="">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 text-center">
                    <img src="assets/images/screen1.png" alt="" class="img-fluid w-75">
                </div>
                <div class="col-lg-5">
                    <div class="services-header">
                        <div class="title-sm">
                            <h3 class="text-primary mb-4">Tujuan</h3>
                        </div>
                        {{-- <h2 class="display-6 text-dark lh-55">Serumpun Padi bertujuan menciptakan lingkungan perumahan
                            yang modern.
                            <br>
                        </h2> --}}
                        <p class="mt-3 lh-base">Serumpun Padi bertujuan meningkatkan akuntabilitas
                            pengelolaan keuangan, menertibkan administrasi serta pelayanan warga melalui pencatatan yang
                            rapi dan terpusat, serta memperkuat keamanan lingkungan lewat fitur aduan dan panic button
                            yang responsif.
                        </p>
                    </div>
                    <div class="services-contant mt-5">
                        <div class="step">
                            <span class="text-primary ms-3 fs-6 bg-info-subtle p-2 px-3 rounded-1">
                                Pendataan Warga :
                            </span>
                            <div class="row mt-3 align-items-center justify-content-center">
                                {{-- <div class="col-lg-3">
                                    <h5 class="">
                                        STEP 01<i class="ri-arrow-right-line ms-3"></i>
                                    </h5>
                                </div> --}}
                                <div class="col-lg-10 border-bottom">
                                    <p>
                                        Kini pengurus tidak perlu lagi memasukkan data manual, cukup scan dokumen Kartu
                                        keluarga -> validasi data -> lihat data terkini.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="step mt-3">
                            <span class="text-primary ms-3 fs-6 bg-info-subtle p-2 px-3 rounded-1">
                                Keuangan dan Iuran :
                            </span>
                            <div class="row mt-3 align-items-center justify-content-center">
                                <div class="col-lg-10 border-bottom">
                                    <p>Pengelolaan kas, iuran, serta pemasukan–pengeluaran menjadi lebih mudah dan.
                                        Iuran akan dibuat otomatis setiap bulan, rekap laporan keuangan otomatis,
                                        menampilkan saldo terkini, riwayat transaksi, serta laporan keuangan yang siap
                                        diperiksa kapan saja.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section guide-app">
        <div class="container">
            <div class="guide-header text-center">
                <div class="title-sm">
                    <h6 class="text-primary">Bagaimana Cara Memakainya?</h6>
                </div>
                <div class="main-title mt-4">
                    <h2 class="display-6 lh-55 text-dark">Aplikasi Serumpun Padi</h2>
                    <p>Mulai dari unduh, masuk, hingga langsung merasakan manfaatnya.</p>
                </div>
            </div>

            <div class="row align-items-stretch justify-content-between g-4 mt-5 pt-5">

                <!-- CARD 1 -->
                <div class="col-lg-4 d-flex">
                    <div class="guide-content text-center p-4 bg-light rounded-3 shadow-sm d-flex flex-column h-100">
                        <div class="mb-3">
                            <i class="ri-number-1 bg-info rounded-circle text-light p-3"></i>
                        </div>
                        <h5 class="mb-3">Download Aplikasi</h5>
                        <p class="flex-grow-1">
                            Mulai perjalanan Anda dengan mengunduh aplikasi Serumpun Padi.
                            Hanya butuh beberapa detik untuk siap mengelola lingkungan dengan cara yang lebih rapi dan
                            modern.
                        </p>
                        <a href="#"
                            class="text-primary fw-bold text-decoration-underline link-offset-1 mt-auto">
                            Download Sekarang
                        </a>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="col-lg-4 d-flex">
                    <div class="guide-content text-center p-4 bg-light rounded-3 shadow-sm d-flex flex-column h-100">
                        <div class="mb-3">
                            <i class="ri-number-2 bg-info rounded-circle text-light p-3"></i>
                        </div>
                        <h5 class="mb-3">Login Aplikasi</h5>
                        <p class="flex-grow-1">
                            Daftar sekarang. Tanpa proses rumit — begitu masuk, Anda langsung dapat melihat fitur yang
                            tersedia dan menyesuaikannya dengan kebutuhan lingkungan Anda.
                        </p>
                        <a href="#registrasi"
                            class="text-primary fw-bold text-decoration-underline link-offset-1 mt-auto">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="col-lg-4 d-flex">
                    <div class="guide-content text-center p-4 bg-light rounded-3 shadow-sm d-flex flex-column h-100">
                        <div class="mb-3">
                            <i class="ri-number-3 bg-info rounded-circle text-light p-3"></i>
                        </div>
                        <h5 class="mb-3">Nikmati Dengan Fitur</h5>
                        <p class="flex-grow-1">
                            Setelah login, Anda langsung dapat mengelola data warga, kas, dan iuran hingga menangani
                            aduan
                            secara real-time. Semua dilakukan dalam satu aplikasi — cepat, praktis, dan transparan.
                        </p>
                        <a href="#features"
                            class="text-primary fw-bold text-decoration-underline link-offset-1 mt-auto">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="section app-screenshort position-relative overflow-hidden" id="features">
        <div class="container">
            <div class="price-header text-center mb-5 pb-5">
                <div class="title-sm">
                    <h6 class="text-primary">Aplikasi Kami</h6>
                </div>
                <div class="main-title mt-4">
                    <h2 class="text-dark display-6 lh-55">
                        Fitur Serumpun Padi
                    </h2>
                    <p class="mt-3 lh-base">
                    </p>
                </div>
            </div>
            <div class="screenshort mt-5">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-12">
                        <div class="swiper mySwiper overflow-hidden">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide"><img src="assets/images/swiper/2.png" alt=""
                                        class="img-fluid w-75 shadow-lg"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/3.png" alt=""
                                        class="img-fluid w-75 shadow-lg"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/4.png" alt=""
                                        class="img-fluid w-75 shadow-lg"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/5.png" alt=""
                                        class="img-fluid w-75 shadow-lg"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/6.png" alt=""
                                        class="img-fluid w-75"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/7.png" alt=""
                                        class="img-fluid w-75"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/8.png" alt=""
                                        class="img-fluid w-75"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/9.png" alt=""
                                        class="img-fluid w-75"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/10.png" alt=""
                                        class="img-fluid w-75"></div>
                                <div class="swiper-slide"><img src="assets/images/swiper/11.png" alt=""
                                        class="img-fluid w-75"></div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                {{-- <img src="assets/images/mobile.png" alt="" class="img-fluid mobile-phone"> --}}
            </div>
        </div>
    </section>
    <section class="section faq py-5">
        <div class="container">

            <!-- Header -->
            <div class="text-center mb-4">
                <h6 class="text-primary">Pertanyaan & Jawaban</h6>
                <h2 class="display-6 text-dark mt-3">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="mt-2">
                    Persentase pengguna aplikasi yang baik akan membeli
                    barang dan jasa Anda.
                </p>
            </div>

            <!-- FAQ Content -->
            <div class="row g-4">

                <!-- Card 1 -->
                <div class="col-lg-6 col-md-12">
                    <div class="bg-light p-4 shadow-sm rounded h-100">
                        <h5 class="d-flex align-items-center">
                            <i class="ri-number-1 me-3 p-2 bg-info text-light rounded-circle"></i>
                            Apa Itu Serumpun Padi?
                        </h5>
                        <p class="ms-5 mt-3">
                            Serumpun Padi adalah aplikasi manajemen perumahan berbasis digital
                            yang membantu warga, pengurus RT/RW, dan developer mengelola data,
                            iuran, keuangan, dan informasi secara lebih mudah dan transparan.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-6 col-md-12">
                    <div class="bg-light p-4 shadow-sm rounded h-100">
                        <h5 class="d-flex align-items-center">
                            <i class="ri-number-2 me-3 p-2 bg-info text-light rounded-circle"></i>
                            Apa Manfaat Menggunakan Serumpun Padi?
                        </h5>
                        <p class="ms-5 mt-3">
                            Dengan Serumpun Padi, proses administrasi lingkungan menjadi mudah & transparan.
                            Warga bisa membayar iuran tanpa tatap muka, memantau histori pembayaran,
                            serta menyampaikan aduan langsung melalui aplikasi.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-lg-6 col-md-12">
                    <div class="bg-light p-4 shadow-sm rounded h-100">
                        <h5 class="d-flex align-items-center">
                            <i class="ri-number-3 me-3 p-2 bg-info text-light rounded-circle"></i>
                            Apakah Bermanfaat Untuk Lingkungan Warga?
                        </h5>
                        <p class="ms-5 mt-3">
                            Serumpun Padi meningkatkan efisiensi kerja pengurus melalui pencatatan otomatis,
                            notifikasi real-time, laporan otomatis, pengelolaan warga,
                            hingga manajemen keluhan.
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-lg-6 col-md-12">
                    <div class="bg-light p-4 shadow-sm rounded h-100">
                        <h5 class="d-flex align-items-center">
                            <i class="ri-number-4 me-3 p-2 bg-info text-light rounded-circle"></i>
                            Apakah Data Yang Tersimpan Aman?
                        </h5>
                        <p class="ms-5 mt-3">
                            Data dikelola secara terproteksi & terenkripsi.
                            Tidak dibagikan ke pihak lain, transaksi dan data aman & rahasia.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <section class="section cta-5" id="registrasi">
        <div class="container">
            <div class="cta-content bg-info rounded-4 text-center p-5">
                <div class="row align-items-center position-relative justify-content-center">
                    <div class="col-lg-6">
                        <div class="cta-header  text-center">
                            <div class="main-title mt-4">
                                <h2 class="text-light display-6">
                                    Kami siap membantu lingkungan Anda berkembang lebih baik.
                                </h2>
                                <p class="mb-4 text-white-50">Segera daftarkan email Anda untuk mencoba aplikasi
                                    sekarang juga.</p>
                                <div class="form-button cta-app mt-4">
                                    <form id="registerForm"
                                        class="subscribe-form d-flex align-items-center justify-content-center flex-wrap">

                                        <!-- Nama -->
                                        <input type="text" id="name"
                                            class="form-control border rounded-2 me-2 mb-2 flex-grow-1"
                                            placeholder="Masukkan nama lengkap" required />

                                        <!-- Email -->
                                        <input type="email" id="email"
                                            class="form-control border rounded-2 me-2 mb-2 flex-grow-1"
                                            placeholder="Masukkan email aktif" required />

                                        <!-- Tombol -->
                                        <button type="submit"
                                            class="btn btn-light rounded-2 mb-2 d-flex align-items-center justify-content-center px-3">
                                            <i class="ri-mail-add-line fs-5 fw-normal text-primary"></i>
                                        </button>
                                    </form>

                                    <div id="registerMessage" class="text-center mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <!-- brand section start  -->
    <section class="section download-part bg-light" id="download">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-5">
                    {{-- <div class="logo mb-4">
                        <a class="navbar-brand logo-2 d-flex align-items-center" href="#">
                            <img src="assets/images/logopadi.png" alt=""
                                style="height: 35px; width: auto; margin-right: 8px;">
                            <span class="navbar-caption fs-4 fw-bold" style="color: #E53935;">

                                Serumpun Padi
                            </span>
                        </a>
                    </div> --}}
                    <h2 class="fw-semibold">Mulai kelola perumahanmu sekarang, download dan rasakan kemudahannya.</h2>
                    {{-- <p>Download Aplikasi ini di Playstore<br> and app store</p>
                    <span class="text-dark fw-semibold me-1">Support
                        :</span>
                    <a href="#" class="text-muted">
                        brilliantbillionindonesia@gmail.com</a> --}}
                </div>
                <div class="col-lg-4">
                    {{-- <h5 class="text-primary">Download Aplikasi nya :</h5> --}}
                    <div class="main-btn mt-4 mb-4">
                        {{-- <a href="#" class="btn p-0 m-0"><img src="assets/images/app-store.png"
                                alt=""></a> --}}
                        <a href="#" class="btn p-0 m-0 ms-2"><img src="assets/images/play-store.png"
                                width="300" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- brand section end  -->
    <!-- START FOOTER -->
    <footer class="bg-info pt-4 pb-4">
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

                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-semibold text-white mb-3">Legal</h6>
                    <ul class="list-unstyled" style="font-size:0.9rem; line-height:1.9;">
                        <a href="{{ route('privacy-policy') }}" class="text-light text-decoration-none">Privacy Policy</a>
                        <li><a href="#" class="text-light text-decoration-none">Terms Of Condition</a></li>
                    </ul>
                </div>

            </div>

            <!-- Divider -->
            <hr class="border-light opacity-50 my-4">

            <!-- BOTTOM -->
            <div class="row align-items-center">

                <!-- Copyright -->
                <div class="col-lg-6 text-lg-start text-center mb-3 mb-lg-0">
                    <p class="m-0 text-light fw-semibold" style="font-size:1rem;">
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        Serumpun Padi — All right reserved
                    </p>
                </div>

                <!-- Social -->
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
    <!-- END FOOTER -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon-1.1.0.js"></script>
    <script src="assets/js/app.js"></script>
    <!-- Tambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById('registerForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();

                if (!name || !email) {
                    Swal.fire("Oops!", "Nama dan Email wajib diisi.", "warning");
                    return;
                }

                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch("{{ url('') }}/api/user/register-demo", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        },
                        body: JSON.stringify({
                            name,
                            email,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pendaftaran Berhasil 🎉',
                            html: `<p>Halo <b>${name}</b>! Silakan cek email <b>${email}</b> untuk informasi login.</p>`,
                            confirmButtonColor: '#198754',
                        });
                        form.reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mendaftar 😢',
                            text: data.message || 'Terjadi kesalahan saat pendaftaran.',
                            confirmButtonColor: '#dc3545',
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal ⚠️',
                        text: 'Tidak dapat terhubung ke server.',
                        confirmButtonColor: '#dc3545',
                    });
                    console.error("Error:", error);
                }
            });
        });
        </script>
</body>

</html>
