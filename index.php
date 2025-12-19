<?php
include 'includes/functions.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/index.php');
    } else {
        redirect('nasabah/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinjamYuk - Solusi Pinjaman Modal Terpercaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="brand-logo me-2">
                    <i class="fas fa-hand-holding-usd fa-lg"></i>
                </div>
                <div>
                    <strong class="fw-bold">PinjamYuk</strong>
                    <small class="d-block text-warning" style="font-size: 0.7rem; line-height: 1;">Pinjaman Modal</small>
                </div>
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="auth/login.php" class="btn btn-outline-light">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-80">
            <div class="col-lg-6">
                <div class="hero-content">
                    <div class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">
                        <i class="fas fa-star me-1"></i>Trusted by 10,000+ Businesses
                    </div>
                    <h1 class="display-4 fw-bold mb-4">
                        Modal Usaha 
                        <span class="text-warning">Mudah</span> 
                        dengan <span class="text-warning">PinjamYuk</span>
                    </h1>
                    <p class="lead mb-4 fs-5">
                        Dapatkan akses cepat ke modal usaha hingga <strong>Rp 100 Juta</strong> 
                        dengan proses sederhana, bunga kompetitif, dan approval dalam 24 jam.
                    </p>
                    <div class="hero-stats row mb-4">
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold mb-1">10K+</h3>
                                <small>Nasabah Aktif</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold mb-1">Rp 50M+</h3>
                                <small>Dana Disalurkan</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="fw-bold mb-1">98%</h3>
                                <small>Kepuasan</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="auth/login.php" class="btn btn-light btn-lg" 
                        style="position: relative; z-index: 1000; cursor: pointer; text-decoration: none;">
                            <i class="fas fa-rocket me-2"></i>Mulai Sekarang
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg" 
                        style="position: relative; z-index: 1000; cursor: pointer; text-decoration: none;">
                            <i class="fas fa-info-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual text-center">
                    <div class="floating-card card-1">
                        <i class="fas fa-money-bill-wave text-success"></i>
                        <span>Pinjaman Cair</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-chart-line text-warning"></i>
                        <span>Bisnis Tumbuh</span>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-smile text-info"></i>
                        <span>Nasabah Happy</span>
                    </div>
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Business Growth" class="img-fluid rounded-3 hero-image shadow-lg">
                </div>
            </div>
        </div>
    </div>
    <style>
/* Custom Styles for New Index */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff" fill-opacity="0.05" points="0,1000 1000,0 1000,1000"/></svg>');
}

.hero-visual {
    position: relative;
}

.hero-image {
    position: relative;
    z-index: 2;
}

.floating-card {
    position: absolute;
    background: white;
    color: #2c3e50;
    padding: 10px 15px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    font-weight: 600;
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-1 {
    top: 10%;
    left: -10%;
    animation: float 3s ease-in-out infinite;
}

.card-2 {
    top: 50%;
    right: -5%;
    animation: float 3s ease-in-out infinite 1s;
}

.card-3 {
    bottom: 20%;
    left: -5%;
    animation: float 3s ease-in-out infinite 2s;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.navbar {
    backdrop-filter: blur(10px);
    background: rgba(44, 62, 80, 0.95) !important;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}
</style>
</section>
<!-- Features Section -->
<section id="features" class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">Mengapa Memilih Kami</span>
                <h2 class="fw-bold display-5 mb-3">Solusi Terbaik untuk<br>Pengembangan Usaha Anda</h2>
                <p class="lead text-muted">Didesain khusus untuk membantu UMKM tumbuh dan berkembang</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-bolt fa-2x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Proses Super Cepat</h5>
                        <p class="text-muted">Pengajuan online, approval dalam 24 jam, dana cair maksimal 2 hari kerja</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-percentage fa-2x text-success"></i>
                        </div>
                        <h5 class="fw-bold">Bunga Terjangkau</h5>
                        <p class="text-muted">Bunga kompetitif mulai 1.5% per bulan dengan diskon khusus tenor panjang</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">100% Aman</h5>
                        <p class="text-muted">Sistem terenkripsi, data terproteksi, dan proses yang transparan</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-headset fa-2x text-info"></i>
                        </div>
                        <h5 class="fw-bold">Support 24/7</h5>
                        <p class="text-muted">Tim customer service siap membantu kapan saja melalui berbagai channel</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Calculator Section -->
    <section id= "calculator" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold text-primary mb-4">Hitung Pinjaman Anda</h2>
                    <p class="lead mb-4">
                        Gunakan kalkulator kami untuk memperkirakan angsuran pinjaman yang sesuai dengan kebutuhan Anda.
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Bunga Kompetitif</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Tenor Fleksibel</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Proses Transparan</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Tanpa Biaya Tambahan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="calculator-card">
                        <form id="calculatorForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Pinjaman</label>
                                    <input type="number" class="form-control" id="calcAmount" value="10000000" min="1000000" max="100000000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bunga (%)</label>
                                    <select class="form-control" id="calcInterest">
                                        <option value="25" selected>25% (Standard)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tenor (Bulan)</label>
                                <select class="form-control" id="calcTenor">
                                    <option value="6">6 Bulan</option>
                                    <option value="12" selected>12 Bulan</option>
                                    <option value="18">18 Bulan</option>
                                    <option value="24">24 Bulan</option>
                                    <option value="36">36 Bulan</option>
                                </select>
                            </div>
                            <button type="button" onclick="calculateLoan()" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-calculator me-2"></i>Hitung Sekarang
                            </button>
                        </form>

                        <div id="calculationResult" class="calculator-result mt-4" style="display: none;">
                            <h5 class="text-center mb-3">Hasil Perhitungan</h5>
                            <div class="row text-center">
                                <div class="col-6 mb-2">
                                    <small>Total Bunga</small>
                                    <div id="resultInterest" class="fw-bold"></div>
                                </div>
                                <div class="col-6 mb-2">
                                    <small>Total Bayar</small>
                                    <div id="resultTotal" class="fw-bold"></div>
                                </div>
                                <div class="col-12">
                                    <small>Angsuran per Bulan</small>
                                    <div id="resultMonthly" class="h5"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
<section id="testimonials" class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">Testimoni</span>
                <h2 class="fw-bold display-5 mb-3">Apa Kata <span class="text-primary">Nasabah Kami</span></h2>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"Sebagai Pemilik Klub Bar, kami sering butuh modal cepat untuk restock minuman atau event dadakan. PinjamYuk bantu kami putar modal dengan cepat dan prosesnya gampang banget! Bunga pun terjangkau."</p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="assets/images/aa.png" 
                                     alt="User" class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-0">Zaenal Abidin</h6>
                                <small class="text-muted">Pemilik Club Bar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"Meskipun sedang tidak bekerja, saya dapat kesempatan untuk mulai usaha kecil-kecilan. Dana cepat cair kurang dari 24 jam! Sangat menolong untuk modal awal barang dagangan. Terima kasih, PinjamYuk!"</p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="assets/images/dede.jpeg" 
                                     alt="User" class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-0">Maman Resink</h6>
                                <small class="text-muted">Pengangguran</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-4">"Dengan jadwal yang padat sebagai Guru Sekolah, saya butuh solusi keuangan yang tidak ribet. Sistem PinjamYuk sangat mudah digunakan (user-friendly) dan tim support mereka responsif. Sangat direkomendasikan untuk siapa saja yang butuh dana cepat!"</p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="assets/images/neng.png" 
                                     alt="User" class="rounded-circle" width="50">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-0">Nengsih</h6>
                                <small class="text-muted">Guru Sekolah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Siap Mengajukan Pinjaman?</h2>
            <p class="lead mb-4">
                Daftar sekarang dan dapatkan akses mudah ke pinjaman modal untuk mengembangkan usaha Anda.
            </p>
            <a href="auth/register.php" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
<footer class="footer bg-dark text-light pt-5" id="footer">
        <div class="container">
            <div class="row g-4">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <div class="d-flex align-items-center">
                            <div class="brand-logo me-2">
                                <i class="fas fa-hand-holding-usd fa-lg text-warning"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">PinjamYuk</h5>
                                <small class="text-warning">Pinjaman Modal </small>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3 text-light opacity-75">
                        Solusi pinjaman modal mudah dan terpercaya untuk mengembangkan usaha Anda. 
                        Dapatkan akses cepat ke modal usaha dengan proses yang sederhana dan transparan.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link me-2" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link me-2" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link me-2" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title mb-3 text-warning">Quick Links</h6>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Beranda
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#features" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Fitur
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#calculator" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Kalkulator
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#testimonials" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Testimoni
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title mb-3 text-warning">Layanan</h6>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Pinjaman Modal Usaha
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Pinjaman Mikro
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Pinjaman Pendidikan
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="footer-link">
                                <i class="fas fa-chevron-right me-1 small"></i>Konsultasi Bisnis
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title mb-3 text-warning">Kontak Kami</h6>
                    <div class="contact-info">
                        <div class="contact-item mb-3">
                            <div class="contact-icon me-3">
                                <i class="fas fa-map-marker-alt text-warning"></i>
                            </div>
                            <div>
                                <small class="text-light opacity-75">
                                    Jl.Re.Martadinata No.23<br> Rangkasbitung, 42315
                                </small>
                            </div>
                        </div>
                        <div class="contact-item mb-3">
                            <div class="contact-icon me-3">
                                <i class="fas fa-phone text-warning"></i>
                            </div>
                            <div>
                                <small class="text-light opacity-75">(021) 1234-5678</small>
                            </div>
                        </div>
                        <div class="contact-item mb-3">
                            <div class="contact-icon me-3">
                                <i class="fas fa-envelope text-warning"></i>
                            </div>
                            <div>
                                <small class="text-light opacity-75">info@pinjamyuk.com</small>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon me-3">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                            <div>
                                <small class="text-light opacity-75">Senin - Jumat: 08:00 - 17:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Bottom Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        &copy; 2024 <strong class="text-warning">PinjamYuk</strong>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-bottom-links">
                        <a href="#" class="footer-link me-3">Privacy Policy</a>
                        <a href="#" class="footer-link me-3">Terms of Service</a>
                        <a href="#" class="footer-link">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateLoan() {
            const amount = parseFloat(document.getElementById('calcAmount').value) || 0;
            const interest = parseFloat(document.getElementById('calcInterest').value) || 0;
            const tenor = parseInt(document.getElementById('calcTenor').value) || 0;

            // Hitung bunga total
            const monthlyInterest = interest / 12 / 100;
            const totalInterest = amount * monthlyInterest * tenor;
            const totalPayment = amount + totalInterest;
            const monthlyPayment = totalPayment / tenor;

            // Format ke Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Tampilkan hasil
            document.getElementById('resultInterest').textContent = formatRupiah(totalInterest);
            document.getElementById('resultTotal').textContent = formatRupiah(totalPayment);
            document.getElementById('resultMonthly').textContent = formatRupiah(monthlyPayment);
            
            document.getElementById('calculationResult').style.display = 'block';
        }

        // Hitung otomatis saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            calculateLoan();
        });
         AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Loading Spinner
        window.addEventListener('load', function() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.display = 'none';
            }
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Theme Switcher
        const themeSwitcher = document.getElementById('themeSwitcher');
        const themeIcon = themeSwitcher?.querySelector('i');
        
        if (themeSwitcher) {
            themeSwitcher.addEventListener('click', function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-bs-theme', newTheme);
                themeIcon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                
                // Save theme preference
                localStorage.setItem('theme', newTheme);
            });

            // Load saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            if (themeIcon) {
                themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        // Back to Top Button
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTop.style.display = 'block';
                } else {
                    backToTop.style.display = 'none';
                }
            });

            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add active class to current page in navigation
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref && linkHref.includes(currentPage)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>