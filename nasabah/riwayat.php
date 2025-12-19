<?php
// nasabah/riwayat.php

// Include functions.php pertama
include '../includes/functions.php';

// Redirect jika belum login
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Redirect jika bukan nasabah
if (!isNasabah()) {
    redirect('../admin/index.php');
}

$page_title = "Riwayat Pinjaman - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

$user_id = $_SESSION['user_id'];

// Filter berdasarkan status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query dengan filter
if ($status_filter == 'all') {
    $query = "SELECT * FROM pinjaman WHERE user_id = '$user_id' ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM pinjaman WHERE user_id = '$user_id' AND status = '$status_filter' ORDER BY created_at DESC";
}

$result = mysqli_query($conn, $query);

// Hitung statistik
$total_pinjaman = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjaman WHERE user_id = '$user_id'");
$total_pinjaman = mysqli_fetch_assoc($total_pinjaman)['total'];

$disetujui = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjaman WHERE user_id = '$user_id' AND status = 'disetujui'");
$disetujui = mysqli_fetch_assoc($disetujui)['total'];

$pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjaman WHERE user_id = '$user_id' AND status = 'pending'");
$pending = mysqli_fetch_assoc($pending)['total'];

$ditolak = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjaman WHERE user_id = '$user_id' AND status = 'ditolak'");
$ditolak = mysqli_fetch_assoc($ditolak)['total'];
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>

            <!-- Statistik Ringkas -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Pinjaman
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total</span>
                        <span class="badge bg-primary"><?php echo $total_pinjaman; ?></span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Disetujui</span>
                        <span class="badge bg-success"><?php echo $disetujui; ?></span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Pending</span>
                        <span class="badge bg-warning"><?php echo $pending; ?></span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Ditolak</span>
                        <span class="badge bg-danger"><?php echo $ditolak; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">Riwayat Pinjaman</h2>
                    <p class="text-muted mb-0">Lihat semua riwayat pengajuan pinjaman Anda</p>
                </div>
                <a href="pinjaman.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajukan Baru
                </a>
            </div>

            <!-- Filter Badge -->
            <?php if ($status_filter != 'all'): ?>
            <div class="alert alert-info alert-dismissible fade show mb-4">
                <i class="fas fa-filter me-2"></i>
                Menampilkan pinjaman dengan status: <strong class="text-capitalize"><?php echo $status_filter; ?></strong>
                <a href="riwayat.php" class="btn btn-sm btn-outline-info ms-2">Tampilkan Semua</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Riwayat Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-history me-2"></i>Daftar Pinjaman
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark">
                                Total: <?php echo mysqli_num_rows($result); ?> data
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Jumlah Pinjaman</th>
                                    <th>Bunga</th>
                                    <th>Tenor</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): 
                                    $total_bayar = $row['pokok_pinjaman'] + $row['bunga_total'];
                                    $tanggal = date('d M Y', strtotime($row['created_at']));
                                    $waktu = date('H:i', strtotime($row['created_at']));
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <div>
                                            <strong><?php echo $tanggal; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $waktu; ?> WIB</small>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary"><?php echo formatRupiah($row['pokok_pinjaman']); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?php echo $row['bunga']; ?>%
                                        </span>
                                    </td>
                                    <td><?php echo $row['tenor']; ?> bulan</td>
                                    <td class="fw-bold text-success"><?php echo formatRupiah($total_bayar); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $row['status']; ?>">
                                            <i class="fas fa-<?php 
                                                echo $row['status'] == 'disetujui' ? 'check-circle' : 
                                                     ($row['status'] == 'pending' ? 'clock' : 'times-circle'); 
                                            ?> me-1"></i>
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal"
                                                    data-tanggal="<?php echo $tanggal . ' ' . $waktu . ' WIB'; ?>"
                                                    data-pokok="<?php echo formatRupiah($row['pokok_pinjaman']); ?>"
                                                    data-bunga="<?php echo $row['bunga']; ?>%"
                                                    data-tenor="<?php echo $row['tenor']; ?> bulan"
                                                    data-bungatotal="<?php echo formatRupiah($row['bunga_total']); ?>"
                                                    data-diskon="<?php echo $row['diskon']; ?>%"
                                                    data-total="<?php echo formatRupiah($total_bayar); ?>"
                                                    data-angsuran="<?php echo formatRupiah($total_bayar / $row['tenor']); ?>"
                                                    data-status="<?php echo ucfirst($row['status']); ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($row['status'] == 'pending'): ?>
                                            <a href="../admin/pinjaman.php?action=detail&id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-info"
                                               data-bs-toggle="tooltip" title="Edit Pengajuan">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada riwayat pinjaman</h5>
                        <p class="text-muted mb-4">Mulai ajukan pinjaman pertama Anda</p>
                        <a href="pinjaman.php" class="btn btn-primary">
                            <i class="fas fa-hand-holding-usd me-2"></i>Ajukan Pinjaman Pertama
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Box -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card info-card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-3"></i>
                            <h5><?php echo $disetujui; ?></h5>
                            <p class="mb-0">Pinjaman Disetujui</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-3"></i>
                            <h5><?php echo $pending; ?></h5>
                            <p class="mb-0">Menunggu Approval</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card info-card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle fa-2x mb-3"></i>
                            <h5><?php echo $ditolak; ?></h5>
                            <p class="mb-0">Total Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Detail Pinjaman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pinjaman</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Tanggal Pengajuan</strong></td><td id="detail_tanggal"></td></tr>
                            <tr><td><strong>Pokok Pinjaman</strong></td><td id="detail_pokok"></td></tr>
                            <tr><td><strong>Bunga</strong></td><td id="detail_bunga"></td></tr>
                            <tr><td><strong>Tenor</strong></td><td id="detail_tenor"></td></tr>
                            <tr><td><strong>Status</strong></td><td id="detail_status"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Rincian Biaya</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Total Bunga</strong></td><td id="detail_bungatotal"></td></tr>
                            <tr><td><strong>Diskon</strong></td><td id="detail_diskon"></td></tr>
                            <tr><td><strong>Total Bayar</strong></td><td id="detail_total"></td></tr>
                            <tr><td><strong>Angsuran/Bulan</strong></td><td id="detail_angsuran"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="pinjaman.php" class="btn btn-primary">Ajukan Pinjaman Baru</a>
            </div>
        </div>
    </div>
</div>

<script>
// Script untuk modal detail
document.addEventListener('DOMContentLoaded', function() {
    var detailModal = document.getElementById('detailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        
        document.getElementById('detail_tanggal').textContent = button.getAttribute('data-tanggal');
        document.getElementById('detail_pokok').textContent = button.getAttribute('data-pokok');
        document.getElementById('detail_bunga').textContent = button.getAttribute('data-bunga');
        document.getElementById('detail_tenor').textContent = button.getAttribute('data-tenor');
        document.getElementById('detail_bungatotal').textContent = button.getAttribute('data-bungatotal');
        document.getElementById('detail_diskon').textContent = button.getAttribute('data-diskon');
        document.getElementById('detail_total').textContent = button.getAttribute('data-total');
        document.getElementById('detail_angsuran').textContent = button.getAttribute('data-angsuran');
        document.getElementById('detail_status').textContent = button.getAttribute('data-status');
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php include '../includes/footer.php'; ?>