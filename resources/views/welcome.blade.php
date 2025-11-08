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
    <section class="hero-part bg-img-5 bg-home-5" id="home">
        <div class="container">
            <!-- row start  -->
            <div class="row position-relative align-items-center justify-content-between">
                <div class="col-xl-6 col-lg-12 col-md-12">
                    <div class="hero-text">
                        <h1 class="display-1 text-dark">
                            <span class="fw-semibold" style="color: #E53935;"> Capek urusan perumahan yang ribet ? </span>
                            {{-- <span class="fw-semibold" style="color: #E53935;"> Capek update data warga masih manual ? --}}
                            </span>
                        </h1>
                        <p class="fw-medium mt-3 lh-base">
                            Pengelolaan perumahan sering terasa melelahkan: pencatatan manual, penagihan berulang, laporan yang tercecer, dan komunikasi yang tidak terarah.
                            {{-- Dengan Serumpun Padi, data warga bisa di-update jauh lebih cepat berkat <b>AI</b> yang mampu membaca Kartu
                            Keluarga (KK) — cukup foto, data langsung terisi, pengurus cukup validasi.<br>
                            Pengurus tidak lagi perlu mengetik satu per satu — menghemat
                            banyak waktu. --}}
                            <br>
                            Serumpun Padi menyederhanakan semuanya dalam satu aplikasi — dari pengelolaan data warga,
                            kas, iuran, hingga laporan keuangan.
                            Jalan lebih cepat, data lebih rapi.
                        </p>
                    </div>
                    <div class="main-btn mt-4">
                        {{-- <a href="#" class="btn p-0 m-0"><img src="assets/images/app-store.png"
                                alt="" /></a> --}}
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
            <img src="assets/images/screen.png" alt=""
                class="img-fluid hend-img position-absolute start-50" />
        </div>
    </section>
    <!-- hero section end  -->
    <section class="section counter-part bg-light pb-5">
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
    <section class="section about-part-5 hero-part" id="about">
        <div class="container">
            <div class="about-header text-center mb-5 pb-5">
                <div class="title-sm">
                    <h2 class="text-primary">Latar Belakang</h2>
                </div>
                <div class="main-title mt-4">
                    <p class="mt-3 lh-base">
                        Banyak perumahan menghadapi masalah serupa: pencatatan manual yang tidak terpusat, transparansi
                        keuangan yang terbatas, serta respons keamanan dan aduan yang lambat.

                        Karena itu, Serumpun Padi hadir untuk membantu menciptakan lingkungan yang lebih tertib, aman,
                        dan transparan dengan sistem terintegrasi real-time.
                    </p>
                </div>
            </div>
            <div class="bg-light p-5 rounded-4">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="content shadow text-start bg-white p-4 rounded-4">
                            <div class="icon">
                                <i class="ri-slideshow-line fs-1 text-primary"></i>
                            </div>
                            <div class="mt-4">
                                <h6>Pengelolaan Manual dan Tidak Terpusat</h6>
                                <p class="lh-base">Banyak kompleks perumahan masih mengelola data warga, keuangan,
                                    aduan, dan komunikasi warga secara manual.
                                    <br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="content shadow text-start bg-white p-4 rounded-4">
                            <div class="icon">
                                <i class="ri-clipboard-line fs-1 text-primary"></i>
                            </div>
                            <div class="mt-4">
                                <h6>Minim Transparansi dan Akuntabilitas
                                </h6>
                                <p class="lh-base">Warga sering tidak mengetahui kondisi keuangan lingkungan secara
                                    jelas, seperti saldo kas, penggunaan dana,dan iuran.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="content shadow text-start bg-white p-4 rounded-4">
                            <div class="icon">
                                <i class="ri-shield-user-line fs-1 text-primary"></i>
                            </div>
                            <div class="mt-4">
                                <h6>Kurangnya Sistem Cepat dan Keamanan</h6>
                                <p class="lh-base">Penanganan aduan dan situasi darurat sering terlambat karena tidak
                                    adanya sistem terintegrasi secara real-time.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row end  -->
            </div>
        </div>
    </section>
    <section class="section services-part-5 bg-light" id="">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 text-center">
                    <img src="assets/images/layar.png" alt="" class="img-fluid w-75">
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
                    <h2 class="display-6 lh-55 text-dark">
                        Aplikasi Serumpun Padi
                    </h2>
                    <p>
                        Mulai dari unduh, masuk, hingga langsung merasakan manfaatnya.
                    </p>
                </div>
            </div>
            <div class="row align-items-center justify-content-between g-4 mt-5 pt-5">
                <div class="col-lg-4">
                    <div class="guide-content text-center p-4 bg-light position-relative rounded-3 shadow-sm">
                        <h5 class="mt-4 mb-3">Download Aplikasi</h5>
                        <p>Mulai perjalanan Anda dengan mengunduh aplikasi Serumpun Padi.
Hanya butuh beberapa detik untuk siap mengelola lingkungan dengan cara yang lebih rapi dan modern.</p>
                        <a href="#"
                            class="text-primary fw-bold  text-decoration-underline link-offset-1">Download
                            Sekarang</a>
                        <div class="step-icon">
                            <i class="ri-number-1 bg-info rounded-circle text-light p-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="guide-content text-center p-4 bg-light position-relative rounded-3 shadow-sm">
                        <h5 class="mt-4 mb-3">Login Aplikasi</h5>
                        <p>Daftar sekarang.
Tanpa proses rumit — begitu masuk, Anda langsung dapat melihat fitur yang tersedia dan menyesuaikannya dengan kebutuhan lingkungan Anda.</p>
                        <a href="#" class="text-primary fw-bold  text-decoration-underline link-offset-1">Daftar
                            Sekarang</a>
                        <div class="step-icon">
                            <i class="ri-number-2 bg-info rounded-circle text-light p-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="guide-content text-center p-4 bg-light position-relative rounded-3 shadow-sm">
                        <h5 class="mt-4 mb-3">Nikmati Dengan Fitur</h5>
                        <p>Setelah login, Anda langsung dapat mengelola data warga, kas, dan iuran hingga menangani aduan secara real-time.
Semua dilakukan dalam satu aplikasi — cepat, praktis, dan transparan.
                        </p>
                        <a href="#features"
                            class="text-primary fw-bold  text-decoration-underline link-offset-1">Pelajari Lebih
                            Lanjut</a>
                        <div class="step-icon">
                            <i class="ri-number-3 bg-info rounded-circle text-light p-3"></i>
                        </div>
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
    <section class="section faq">
        <div class="container">
            <div class="contact-header text-center mb-5">
                <div class="title-sm">
                    <h6 class="text-primary">Pertanyaan &
                        Jawaban</h6>
                </div>
                <div class="main-title my-4">
                    <h2 class="display-6 text-dark">
                        Pertanyaan yang Sering Diajukan
                    </h2>
                    <p class="mt-3 lh-base">
                        Persentase pengguna aplikasi yang baik akan membeli
                        barang dan jasa Anda.
                    </p>
                </div>
            </div>
            <div class="row align-items-center justify-content-center mt-5">
                <div class="col-lg-5">
                    <div class="bg-light p-4  shadow-sm">
                        <h5 class="lh-base">
                            <i class="ri-number-1 me-3 p-2 bg-info text-light rounded-circle align-middle"></i>
                            Apa itu Serumpun Padi?
                        </h5>
                        <p class="ms-5">Serumpun Padi adalah aplikasi manajemen perumahan berbasis digital yang
                            membantu warga, pengurus RT/RW, dan developer perumahan mengelola data, iuran, keuangan, dan
                            informasi secara lebih mudah dan transparan.</p>
                    </div>
                    <div class="bg-light p-4 my-4 shadow-sm">
                        <h5>
                            <i class="ri-number-2 me-3 p-2 bg-info text-light rounded-circle align-middle"></i>
                            Apa manfaat menggunakan Serumpun Padi?
                        </h5>
                        <p class="ms-5">Dengan Serumpun Padi, proses administrasi lingkungan menjadi lebih mudah dan
                            transparan.
                            Warga bisa membayar iuran tanpa tatap muka, memantau histori pembayaran, serta menyampaikan
                            aduan secara langsung melalui aplikasi.
                        </p>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="bg-light p-4 shadow-sm">
                        <h5 class="lh-base">
                            <i class="ri-number-3 me-3 p-2 bg-info text-light rounded-circle align-middle"></i>
                            Apakah bermanfaat untuk di lingkungan Warga?
                        </h5>
                        <p class="ms-5">Serumpun Padi meningkatkan efisiensi kerja pengurus melalui pencatatan
                            otomatis dan notifikasi real-time.
                            Pengurus dapat melihat data pemasukan, membuat laporan otomatis, mengelola informasi warga,
                            hingga menangani keluhan lebih cepat.</p>
                    </div>
                    <div class="bg-light p-4 my-4 shadow-sm">
                        <h5>
                            <i class="ri-number-4 me-3 p-2 bg-info text-light rounded-circle align-middle"></i>
                            Apakah data yang tersimpan aman?
                        </h5>
                        <p class="ms-5">Ya. Seluruh data dikelola melalui sistem backend terproteksi dan tidak
                            dibagikan ke pihak lain tanpa izin.
                            Setiap transaksi dan data warga tersimpan dalam sistem yang terenkripsi sehingga keamanan
                            dan kerahasiaan pengguna tetap terjaga.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section cta-5">
        <div class="container">
            <div class="cta-content bg-info rounded-4 text-center p-5">
                <div class="row align-items-center position-relative justify-content-center">
                    <div class="col-lg-6">
                        <div class="cta-header  text-center">
                            <div class="title-sm">
                                <h6 class="text-light">Contact Us</h6>
                            </div>
                            <div class="main-title mt-4">
                                <h2 class="text-light display-6 lh-55">
                                    We would love to hear from you
                                </h2>
                                <p class="mb-4 text-white-50">We are all excited
                                    to work with you and see ypu growing</p>
                                <div class="form-button cta-app mt-4">
                                    <form class="d-flex align-items-center justify-content-center">
                                        <input type="email" class="form-control border rounded-2 w-75"
                                            placeholder="Your email" required />
                                        <button type="submit" class="btn btn-light ms-2 rounded-2">
                                            <i class="ri-mail-add-line fs-5 fw-normal text-primary"></i>
                                        </button>
                                    </form>
                                </div>
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
    <footer class="section footer-5 pb-4 bg-info">
        <div class="footer-main">
            <div class="container">
                <div class="footer-item">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-sm-6">
                            <h6 class="fw-semibold m-0 text-white pb-3">Hubungi Kami</h6>
                            <p class="text-light lh-lg mb-2">Indonesia ji.Griya
                                permata Hijau Purwomartani Kalasan
                                543881</p>
                        </div>
                        {{-- <div class="col-lg-3 col-sm-6">
                            <h6 class="fw-semibold m-0 text-white pb-3">Stay Up
                                to date</h6>
                            <p class="text-light lh-lg">Subscribe to every
                                weekly read our Newsletter</p>
                            <div class="form-button">
                                <form action="" class="d-flex align-items-center ">
                                    <input type="email" class="form-control" placeholder="Enter email">
                                    <a href="#" class="me-1"><i class="ri-send-plane-2-line"></i></a>
                                </form>
                            </div>
                        </div> --}}
                    </div>
                    <!-- end row -->
                </div>
                <div class="footer-inner-in mt-4">
                    <div class="row align-items-center text-center">
                        <div class="col-lg-4">
                            <div class="logo">
                                <a class="fs-4 text-light fw-bold" href="#"><i
                                        class="fw-bold text-light align-middle"></i>
                                    Serumpun Padi
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex justify-content-center">
                                <p class="m-0 text-light pb-lg-0">
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> Serumpun Padi All right reserved
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="icon  d-flex justify-content-lg-end justify-content-center">
                                <a href="#"> <i class="ri-facebook-fill"></i></a>
                                <a href="#"><i class="ri-instagram-line"></i></a>
                                <a href="#"> <i class="ri-twitter-fill"></i></a>
                                <a href="#"><i class="ri-youtube-fill"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end container -->
        </div>
    </footer>
    <!-- END FOOTER -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon-1.1.0.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>
