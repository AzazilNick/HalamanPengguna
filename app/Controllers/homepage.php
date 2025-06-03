<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JobAbility</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-5 col-md-6 text-center mb-4 mb-md-0">
                    <img src="https://storage.googleapis.com/a1aa/image/1adf67a5-808d-4670-03b5-04021b36858e.jpg" 
                         alt="Illustration of diverse people including person in wheelchair" 
                         class="img-fluid" style="max-width: 400px;">
                </div>
                <div class="col-lg-4 col-md-6 text-center text-md-start">
                    <h1 class="fw-bold mb-4">Melangkahlah dengan caramu sendiri</h1>
                    <form>
                        <label for="search" class="form-label fw-semibold">Cari Peluangmu Di Sini</label>
                        <div class="input-group">
                            <input type="text" class="form-control search-input rounded-pill" 
                                   id="search" placeholder="Cari">
                            <button class="btn position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" 
                                    type="submit" style="z-index: 10;">
                                <i class="fas fa-search text-white"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan -->
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Fitur Unggulan</h2>
        </div>
        
        <div class="bg-primary-custom py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="feature-icon me-3">
                                        <i class="fas fa-briefcase text-dark"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Cari Pekerjaan</h5>
                                </div>
                                <p class="card-text small">
                                    Temukan peluang kerja yang dirancang untuk semua. Dengan sistem pencarian yang mudah diakses, 
                                    kamu dapat menjelajahi lowongan cari pekerjaan yang mengedepankan kelengkapan dan kesetaraan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="feature-icon me-3">
                                        <i class="fas fa-pencil-alt text-dark"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Pelatihan Daring</h5>
                                </div>
                                <p class="card-text small">
                                    Tingkatkan kemampuan tanpa batas ruang. Kami menyediakan akses ke pelatihan online GRATIS 
                                    maupun bersertifikat yang disesuaikan dengan berbagai kebutuhan disabilitas.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="feature-icon me-3">
                                        <i class="fas fa-coffee text-dark"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Komunitas Disabilitas</h5>
                                </div>
                                <p class="card-text small">
                                    Bergabung dalam ruang hangat yang penuh dukungan. Di sini, kamu bisa berbagi cerita, 
                                    pengetahuan, dan semangat dengan sesama pejuang disabilitas.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="feature-icon me-3">
                                        <i class="fas fa-laptop text-dark"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Informasi Hak & Regulasi</h5>
                                </div>
                                <p class="card-text small">
                                    Ketahui hak-hakmu sebagai pekerja dan warga negara. Panduan praktis terkait kebijakan, 
                                    regulasi, dan perlindungan hukum bagi penyandang disabilitas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lowongan Kerja Terbaru -->
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Lowongan Kerja Terbaru</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card job-card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary-custom">Digital Marketing</h5>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-briefcase me-2"></i>Keuangan dan Akuntansi</li>
                                <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Sumen Jawa Barat</li>
                                <li class="mb-1"><i class="fas fa-clock me-2"></i>Malang, Jawa Timur</li>
                            </ul>
                            <img src="https://storage.googleapis.com/a1aa/image/d9c4e4eb-1389-4099-1438-e4e50e6dd445.jpg" 
                                 alt="Telkom Indonesia Logo" class="mb-3" style="height: 20px;">
                            <button class="btn btn-outline-primary-custom btn-sm">Selengkapnya</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card job-card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary-custom">Digital Marketing</h5>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-briefcase me-2"></i>Keuangan dan Akuntansi</li>
                                <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Sumen Jawa Barat</li>
                                <li class="mb-1"><i class="fas fa-clock me-2"></i>Malang, Jawa Timur</li>
                            </ul>
                            <img src="https://storage.googleapis.com/a1aa/image/d9c4e4eb-1389-4099-1438-e4e50e6dd445.jpg" 
                                 alt="Telkom Indonesia Logo" class="mb-3" style="height: 20px;">
                            <button class="btn btn-outline-primary-custom btn-sm">Selengkapnya</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card job-card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary-custom">Digital Marketing</h5>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-briefcase me-2"></i>Keuangan dan Akuntansi</li>
                                <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Sumen Jawa Barat</li>
                                <li class="mb-1"><i class="fas fa-clock me-2"></i>Malang, Jawa Timur</li>
                            </ul>
                            <img src="https://storage.googleapis.com/a1aa/image/d9c4e4eb-1389-4099-1438-e4e50e6dd445.jpg" 
                                 alt="Telkom Indonesia Logo" class="mb-3" style="height: 20px;">
                            <button class="btn btn-outline-primary-custom btn-sm">Selengkapnya</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card job-card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary-custom">Digital Marketing</h5>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-briefcase me-2"></i>Keuangan dan Akuntansi</li>
                                <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Sumen Jawa Barat</li>
                                <li class="mb-1"><i class="fas fa-clock me-2"></i>Malang, Jawa Timur</li>
                            </ul>
                            <img src="https://storage.googleapis.com/a1aa/image/d9c4e4eb-1389-4099-1438-e4e50e6dd445.jpg" 
                                 alt="Telkom Indonesia Logo" class="mb-3" style="height: 20px;">
                            <button class="btn btn-outline-primary-custom btn-sm">Selengkapnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Webinar -->
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Webinar</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-primary-custom text-white position-relative">
                        <span class="badge webinar-badge position-absolute top-0 start-0 rounded-end">Top Rated</span>
                        <img src="https://storage.googleapis.com/a1aa/image/c91ed2e2-be52-4036-ac39-3b823257902f.jpg" 
                             alt="Webinar illustration" class="card-img-top">
                        <div class="card-body">
                            <p class="card-text small mb-3">
                                Webinar live, interaktif, dan inspiratif, dipandu praktisi karier dan pengembangan 
                                profesional dan perusahaan ternama!
                            </p>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-calendar-alt me-2"></i>30 Maret 2023</li>
                                <li class="mb-1"><i class="fas fa-users me-2"></i>6518</li>
                                <li class="mb-1"><i class="fas fa-star me-2"></i>4.5</li>
                            </ul>
                            <button class="btn btn-light btn-sm text-primary-custom">Selengkapnya</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-primary-custom text-white position-relative">
                        <span class="badge webinar-badge position-absolute top-0 start-0 rounded-end">Top Rated</span>
                        <img src="https://storage.googleapis.com/a1aa/image/c91ed2e2-be52-4036-ac39-3b823257902f.jpg" 
                             alt="Webinar illustration" class="card-img-top">
                        <div class="card-body">
                            <p class="card-text small mb-3">
                                Webinar live, interaktif, dan inspiratif, dipandu praktisi karier dan pengembangan 
                                profesional dan perusahaan ternama!
                            </p>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-calendar-alt me-2"></i>30 Maret 2023</li>
                                <li class="mb-1"><i class="fas fa-users me-2"></i>6518</li>
                                <li class="mb-1"><i class="fas fa-star me-2"></i>4.5</li>
                            </ul>
                            <button class="btn btn-light btn-sm text-primary-custom">Selengkapnya</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <button class="btn btn-outline-primary-custom btn-sm">Lainnya &gt;</button>
            </div>
            
            <p class="fst-italic text-center mt-4 fs-5">
                "Karier hebat dimulai dari kemauan untuk terus belajar" - JobAbility
            </p>
        </div>
    </section>

    <!-- Testimoni -->
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Testimoni</h2>
            <div id="testimonialCarousel" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <img src="https://storage.googleapis.com/a1aa/image/1056784a-e0bd-488a-82b1-87797d7bc237.jpg" 
                                             alt="Rina Amelia" class="rounded img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-9">
                                        <p class="mb-3 small fst-italic">
                                            "Saya awalnya ragu apakah perusahaan besar seperti Tokopedia mau menerima saya sebagai penyandang disabilitas. 
                                            Tapi melalui webinar dan info lowongan di platform ini, saya merasa percaya diri melamar. 
                                            Sekarang, saya bangga bisa bekerja dan berkembang di lingkungan yang inklusif!"
                                        </p>
                                        <div class="d-flex align-items-center">
                                            <img src="https://storage.googleapis.com/a1aa/image/7c2dd19b-8f7b-407a-83f9-4908a000b854.jpg" 
                                                 alt="Tokopedia logo" class="me-2" style="width: 20px; height: 20px;">
                                            <div>
                                                <p class="fw-semibold text-primary-custom mb-0">Rina Amelia</p>
                                                <p class="text-muted small mb-0">Customer Support Specialist – Tokopedia</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left text-dark"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right text-dark"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Komunitas -->
    <section class="py-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Komunitas</h2>
            <div class="row align-items-center">
                <div class="col-lg-3 text-center mb-4 mb-lg-0">
                    <p class="fst-italic text-muted">
                        "Kita mungkin berjalan dengan cara yang berbeda, tapi kita menuju tujuan yang sama" - JobAbility
                    </p>
                </div>
                <div class="col-lg-9">
                    <div class="card bg-primary-custom text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <img src="https://storage.googleapis.com/a1aa/image/3920f612-a94f-494d-0544-2d7c30204b31.jpg" 
                                         alt="Disability community icon" class="img-fluid" style="width: 110px;">
                                    <h5 class="mt-2">JobAbility Community</h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-3 small">
                                        JobAbility Community adalah ruang aman dan suportif bagi penyandang disabilitas 
                                        untuk saling berbagi, belajar, dan bertumbuh bersama.
                                    </p>
                                    <div class="bg-orange-custom text-primary-custom p-2 rounded small">
                                        "Di komunitas ini, saya tidak hanya mendapat kerja, tapi juga kesempatan untuk belajar, 
                                        support dan menguatkan satu sama lain." – Anggota Komunitas
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <button class="btn bg-orange-custom text-primary-custom btn-sm">Selengkapnya</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kesempatan -->
    <section class="py-5">
        <div class="container text-center">
            <h3 class="fw-bold fs-4">
                Kesempatan itu ada. Saatnya kamu<br>
                yang melangkah, di <span class="text-orange-custom">JobAbility</span>
            </h3>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
