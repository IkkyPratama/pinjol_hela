<?php
// admin/index.php

// Include functions.php hanya sekali di awal
include '../includes/functions.php';

// Redirect jika belum login
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Redirect jika bukan admin
if (!isAdmin()) {
    redirect('../nasabah/index.php');
}

$page_title = "Admin Dashboard - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

// Fungsi dengan error handling untuk query
function executeQuery($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return false;
    }
    return $result;
}

// Hitung statistik dengan error handling
$total_nasabah = 0;
$total_pinjaman = 0;
$pinjaman_pending = 0;
$total_bunga = 0;
$total_disetujui = 0;
$total_ditolak = 0;

// Query statistik
$query1 = "SELECT COUNT(*) as total FROM users WHERE role = 'nasabah'";
$result1 = executeQuery($conn, $query1);
if ($result1) {
    $data = mysqli_fetch_assoc($result1);
    $total_nasabah = $data['total'] ?? 0;
}

$query2 = "SELECT SUM(pokok_pinjaman) as total FROM pinjaman WHERE status = 'disetujui'";
$result2 = executeQuery($conn, $query2);
if ($result2) {
    $data = mysqli_fetch_assoc($result2);
    $total_pinjaman = $data['total'] ?? 0;
}

$query3 = "SELECT COUNT(*) as total FROM pinjaman WHERE status = 'pending'";
$result3 = executeQuery($conn, $query3);
if ($result3) {
    $data = mysqli_fetch_assoc($result3);
    $pinjaman_pending = $data['total'] ?? 0;
}

$query4 = "SELECT SUM(bunga_total) as total FROM pinjaman WHERE status = 'disetujui'";
$result4 = executeQuery($conn, $query4);
if ($result4) {
    $data = mysqli_fetch_assoc($result4);
    $total_bunga = $data['total'] ?? 0;
}

$query5 = "SELECT COUNT(*) as total FROM pinjaman WHERE status = 'disetujui'";
$result5 = executeQuery($conn, $query5);
if ($result5) {
    $data = mysqli_fetch_assoc($result5);
    $total_disetujui = $data['total'] ?? 0;
}

$query6 = "SELECT COUNT(*) as total FROM pinjaman WHERE status = 'ditolak'";
$result6 = executeQuery($conn, $query6);
if ($result6) {
    $data = mysqli_fetch_assoc($result6);
    $total_ditolak = $data['total'] ?? 0;
}

// Data untuk chart status pinjaman
$status_data = [
    'disetujui' => $total_disetujui,
    'pending' => $pinjaman_pending,
    'ditolak' => $total_ditolak
];

// Query untuk tabel pengajuan terbaru
$query_recent = "SELECT p.*, u.nama, u.email FROM pinjaman p 
                 JOIN users u ON p.user_id = u.id 
                 ORDER BY p.created_at DESC LIMIT 6";
$result_recent = executeQuery($conn, $query_recent);

// Query untuk nasabah terbaru
$query_nasabah_baru = "SELECT * FROM users WHERE role = 'nasabah' ORDER BY created_at DESC LIMIT 5";
$result_nasabah_baru = executeQuery($conn, $query_nasabah_baru);
?>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>

            <!-- Quick Stats -->
            <div class="card mt-4 quick-stats-card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-chart-line me-2"></i>Statistik Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="quick-stat-item">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="stat-label">Total Nasabah</span>
                            <span class="stat-value text-primary"><?php echo $total_nasabah; ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo min($total_nasabah * 20, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="quick-stat-item">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="stat-label">Total Pinjaman</span>
                            <span class="stat-value text-success"><?php echo formatRupiah($total_pinjaman); ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: <?php echo min(($total_pinjaman / 100000000) * 100, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="quick-stat-item">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="stat-label">Pending Approval</span>
                            <span class="stat-value text-warning"><?php echo $pinjaman_pending; ?></span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: <?php echo min($pinjaman_pending * 20, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="quick-stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stat-label">Total Bunga</span>
                            <span class="stat-value text-info"><?php echo formatRupiah($total_bunga); ?></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: <?php echo min(($total_bunga / 5000000) * 100, 100); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card customers-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo $total_nasabah; ?></h4>
                                    <span class="text-muted">Total Nasabah</span>
                                </div>
                                <div class="stat-icon-circle bg-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Aktif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card revenue-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo formatRupiah($total_pinjaman); ?></h4>
                                    <span class="text-muted">Total Pinjaman</span>
                                </div>
                                <div class="stat-icon-circle bg-success">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-success">
                                    <i class="fas fa-trend-up me-1"></i>
                                    Disetujui
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card pending-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo $pinjaman_pending; ?></h4>
                                    <span class="text-muted">Menunggu Approval</span>
                                </div>
                                <div class="stat-icon-circle bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Perlu Tindakan
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card profit-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0"><?php echo formatRupiah($total_bunga); ?></h4>
                                    <span class="text-muted">Total Bunga</span>
                                </div>
                                <div class="stat-icon-circle bg-info">
                                    <i class="fas fa-percentage"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-success">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Pendapatan
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Tables Row -->
            <div class="row">
                <!-- Pengajuan Terbaru -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary">
                                    <i class="fas fa-history me-2"></i>Pengajuan Terbaru
                                </h5>
                                <a href="pinjaman.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-list me-1"></i>Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($result_recent && mysqli_num_rows($result_recent) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nasabah</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_recent)): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <i class="fas fa-user-circle text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo $row['nama']; ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo $row['email']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold"><?php echo formatRupiah($row['pokok_pinjaman']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="pinjaman.php?action=detail&id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-outline-primary"
                                                       data-bs-toggle="tooltip" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($row['status'] == 'pending'): ?>
                                                    <a href="pinjaman.php?setuju=<?php echo $row['id']; ?>" 
                                                       class="btn btn-outline-success"
                                                       data-bs-toggle="tooltip" title="Setujui">
                                                        <i class="fas fa-check"></i>
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
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada pengajuan</h5>
                                <p class="text-muted">Tidak ada data pengajuan pinjaman terbaru</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution & Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <!-- Status Distribution -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Status Pinjaman
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="status-distribution">
                                <?php 
                                $total_status = array_sum($status_data);
                                foreach ($status_data as $status => $count): 
                                    if ($total_status > 0) {
                                        $percentage = ($count / $total_status) * 100;
                                    } else {
                                        $percentage = 0;
                                    }
                                ?>
                                <div class="status-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="status-label text-capitalize"><?php echo $status; ?></span>
                                        <span class="status-count"><?php echo $count; ?> (<?php echo round($percentage, 1); ?>%)</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-<?php 
                                            echo $status == 'disetujui' ? 'success' : 
                                                 ($status == 'pending' ? 'warning' : 'danger'); 
                                        ?>" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Nasabah -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary">
                                    <i class="fas fa-users me-2"></i>Nasabah Terbaru
                                </h5>
                                <a href="nasabah.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($result_nasabah_baru && mysqli_num_rows($result_nasabah_baru) > 0): ?>
                            <div class="row">
                                <?php while ($nasabah = mysqli_fetch_assoc($result_nasabah_baru)): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="nasabah-card">
                                        <div class="d-flex align-items-center">
                                            <div class="nasabah-avatar me-3">
                                                <i class="fas fa-user-circle text-primary"></i>
                                            </div>
                                            <div class="nasabah-info">
                                                <h6 class="mb-1"><?php echo $nasabah['nama']; ?></h6>
                                                <small class="text-muted"><?php echo $nasabah['email']; ?></small>
                                                <br>
                                                <small class="text-success">
                                                    Limit: <?php echo formatRupiah($nasabah['limit_pinjaman']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Belum ada nasabah terdaftar</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?php include '../includes/footer.php'; ?>