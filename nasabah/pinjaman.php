<?php
include '../includes/functions.php';
include '../config/database.php';

// ==== AUTH ==== //
if (!isLoggedIn()) redirect('../auth/login.php');
if (!isNasabah()) redirect('../admin/index.php');

$page_title = "Ajukan Pinjaman - PinjamYuk";
include '../includes/header.php';

// ==== USER SESSION DIKUNCI ==== //
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) die('Session tidak valid');

// ==== AMBIL DATA USER ==== //
$stmt = $conn->prepare("SELECT id, limit_pinjaman FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) die('User tidak ditemukan');

// ==== TOTAL PINJAMAN DISETUJUI ==== //
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(pokok_pinjaman),0) AS total 
    FROM pinjaman 
    WHERE user_id=? AND status='disetujui'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_pinjaman = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$limit_tersedia = max(0, $user['limit_pinjaman'] - $total_pinjaman);

// ==== PROSES AJUKAN PINJAMAN ==== //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajukan_pinjaman'])) {

    $pokok = (float) $_POST['pokok_pinjaman'];
    $bunga = (float) $_POST['bunga'];
    $tenor = (int) $_POST['tenor'];
    $diskon = (float) $_POST['diskon'];

    // VALIDASI DASAR
    if ($pokok <= 0 || $tenor <= 0) {
        $error = "Data pinjaman tidak valid";
    }
    elseif ($pokok > $limit_tersedia) {
        $error = "Pinjaman melebihi limit tersedia: " . formatRupiah($limit_tersedia);
    }
    else {
        // HITUNG BUNGA
        $bunga_total = hitungBunga($pokok, $bunga, $tenor);

        // INSERT (AMAN & KONSISTEN)
        $stmt = $conn->prepare("
            INSERT INTO pinjaman 
            (user_id, pokok_pinjaman, bunga, tenor, diskon, bunga_total, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param(
            "ididdd",
            $user_id,
            $pokok,
            $bunga,
            $tenor,
            $diskon,
            $bunga_total
        );

        if ($stmt->execute()) {
            $success = "Pengajuan pinjaman berhasil. Menunggu persetujuan admin.";
            echo "<script>
                setTimeout(()=>location.href='pinjaman.php',1500);
            </script>";
        } else {
            $error = "Gagal mengajukan pinjaman";
        }
        $stmt->close();
    }
}
?>


<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">Ajukan Pinjaman Baru</h2>
                <div class="limit-badge">
                    <i class="fas fa-wallet me-2"></i>
                    Limit Tersedia: <strong><?php echo formatRupiah($limit_tersedia); ?></strong>
                </div>
            </div>

            <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Form Pengajuan Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="formPinjaman">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jumlah Pinjaman</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" name="pokok_pinjaman" 
                                                   id="pokok_pinjaman" required 
                                                   min="1000000" 
                                                   max="<?php echo $limit_tersedia; ?>"
                                                   oninput="hitungPinjaman()"
                                                   placeholder="Masukkan jumlah pinjaman">
                                        </div>
                                        <div class="form-text">
                                            Minimal: Rp 1.000.000 | Maksimal: <?php echo formatRupiah($limit_tersedia); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Bunga per Tahun</label>
                                        <select class="form-control" name="bunga" id="bunga" onchange="hitungPinjaman()" required>
                                            <option value="25" selected>25% (Standard)</option>
                                        </select>
                                        <div class="form-text">Bunga kompetitif untuk Anda</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tenor Pinjaman</label>
                                        <select class="form-control" name="tenor" id="tenor" onchange="hitungPinjaman()" required>
                                            <option value="6">6 Bulan</option>
                                            <option value="12" selected>12 Bulan</option>
                                            <option value="18">18 Bulan</option>
                                            <option value="24">24 Bulan</option>
                                            <option value="36">36 Bulan</option>
                                        </select>
                                        <div class="form-text">Pilih periode pengembalian</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Diskon Khusus</label>
                                        <input type="number" class="form-control" name="diskon" 
                                               id="diskon" value="0" min="0" max="20" step="0.1"
                                               oninput="hitungPinjaman()">
                                        <div class="form-text">Diskon khusus untuk nasabah loyal (jika ada)</div>
                                    </div>
                                </div>

                                <!-- Simulasi Perhitungan -->
                                <div class="calculation-card mt-4">
                                    <h6 class="text-center mb-3 text-primary">
                                        <i class="fas fa-calculator me-2"></i>Simulasi Perhitungan
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-3">
                                            <div class="calc-box">
                                                <small class="text-muted">Total Bunga</small>
                                                <div id="total_bunga" class="fw-bold text-warning">Rp 0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="calc-box">
                                                <small class="text-muted">Sebelum Diskon</small>
                                                <div id="total_sebelum_diskon" class="fw-bold text-info">Rp 0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="calc-box">
                                                <small class="text-muted">Nilai Diskon</small>
                                                <div id="nilai_diskon" class="fw-bold text-success">Rp 0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="calc-box">
                                                <small class="text-muted">Total Bayar</small>
                                                <div id="total_setelah_diskon" class="fw-bold text-primary">Rp 0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <small class="text-muted">Estimasi Angsuran per bulan:</small>
                                        <div id="angsuran_per_bulan" class="h4 text-success">Rp 0</div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" name="ajukan_pinjaman" class="btn btn-primary btn-lg w-100 py-3">
                                        <i class="fas fa-paper-plane me-2"></i>Ajukan Pinjaman Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Informasi Penting -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-alert">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian!</h6>
                                <small>
                                    - Pastikan data yang diisi benar<br>
                                    - Pinjaman akan diverifikasi oleh admin<br>
                                    - Proses approval 1-2 hari kerja<br>
                                    - Bunga dihitung per tahun
                                </small>
                            </div>
                            
                            <h6 class="mt-3">Syarat & Ketentuan:</h6>
                            <ul class="small">
                                <li>Limit pinjaman sesuai profil</li>
                                <li>Tenor maksimal 24 bulan</li>
                                <li>Bunga competitive 1-3% per tahun</li>
                                <li>Diskon untuk nasabah loyal</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Status Limit -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Status Limit Anda</h5>
                        </div>
                        <div class="card-body">
                            <div class="limit-summary">
                                <div class="limit-item d-flex justify-content-between mb-2">
                                    <span>Limit Total</span>
                                    <strong><?php echo formatRupiah($user['limit_pinjaman']); ?></strong>
                                </div>
                                <div class="limit-item d-flex justify-content-between mb-2">
                                    <span>Terpakai</span>
                                    <strong class="text-warning"><?php echo formatRupiah($total_pinjaman); ?></strong>
                                </div>
                                <div class="limit-item d-flex justify-content-between mb-3">
                                    <span>Tersedia</span>
                                    <strong class="text-success"><?php echo formatRupiah($limit_tersedia); ?></strong>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <?php 
                                    $persentase = $user['limit_pinjaman'] > 0 ? ($total_pinjaman / $user['limit_pinjaman']) * 100 : 0;
                                    ?>
                                    <div class="progress-bar bg-success" style="width: <?php echo 100 - $persentase; ?>%"></div>
                                    <div class="progress-bar bg-warning" style="width: <?php echo $persentase; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<script>
function formatRupiah(angka) {
    if (isNaN(angka)) angka = 0;
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

function hitungPinjaman() {
    const pokok   = parseFloat(document.getElementById('pokok_pinjaman').value) || 0;
    const bunga   = parseFloat(document.getElementById('bunga').value) || 0;
    const tenor   = parseInt(document.getElementById('tenor').value) || 0;
    const diskon  = parseFloat(document.getElementById('diskon').value) || 0;

    // ✅ kalau belum isi form, reset tampilan
    if (pokok === 0 || tenor === 0) {
        document.getElementById('total_bunga').innerHTML = 'Rp 0';
        document.getElementById('total_sebelum_diskon').innerHTML = 'Rp 0';
        document.getElementById('nilai_diskon').innerHTML = 'Rp 0';
        document.getElementById('total_setelah_diskon').innerHTML = 'Rp 0';
        document.getElementById('angsuran_per_bulan').innerHTML = 'Rp 0';
        return;
    }

    // bunga per tahun → per bulan
    const bungaBulanan = (bunga / 100) / 12;

    const totalBunga = pokok * bungaBulanan * tenor;
    const totalSebelumDiskon = pokok + totalBunga;
    const nilaiDiskon = totalSebelumDiskon * (diskon / 100);
    const totalSetelahDiskon = totalSebelumDiskon - nilaiDiskon;
    const angsuranPerBulan = totalSetelahDiskon / tenor;

    document.getElementById('total_bunga').innerHTML =
        formatRupiah(totalBunga);

    document.getElementById('total_sebelum_diskon').innerHTML =
        formatRupiah(totalSebelumDiskon);

    document.getElementById('nilai_diskon').innerHTML =
        formatRupiah(nilaiDiskon);

    document.getElementById('total_setelah_diskon').innerHTML =
        formatRupiah(totalSetelahDiskon);

    document.getElementById('angsuran_per_bulan').innerHTML =
        formatRupiah(angsuranPerBulan);
}

// auto hitung saat halaman siap
document.addEventListener('DOMContentLoaded', hitungPinjaman);
</script>
