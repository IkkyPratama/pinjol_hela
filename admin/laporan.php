<?php
// admin/laporan.php

// Include functions.php pertama
include '../includes/functions.php';

// Redirect jika belum login
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Redirect jika bukan admin
if (!isAdmin()) {
    redirect('../nasabah/index.php');
}

$page_title = "Laporan - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

// Filter laporan
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m');
$bulan_tahun = explode('-', $filter_tanggal);
$bulan = $bulan_tahun[1];
$tahun = $bulan_tahun[0];

// Statistik bulanan
$query_total = "SELECT 
    COUNT(*) as total_pinjaman,
    SUM(pokok_pinjaman) as total_pokok,
    SUM(bunga_total) as total_bunga,
    AVG(bunga) as rata_bunga
    FROM pinjaman 
    WHERE MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun' 
    AND status = 'disetujui'";

$statistik = mysqli_query($conn, $query_total);
$statistik = mysqli_fetch_assoc($statistik);

// Data pinjaman per status
$query_status = "SELECT status, COUNT(*) as jumlah 
                 FROM pinjaman 
                 WHERE MONTH(created_at) = '$bulan' 
                 AND YEAR(created_at) = '$tahun'
                 GROUP BY status";
$data_status = mysqli_query($conn, $query_status);

// Detail pinjaman
$query_detail = "SELECT p.*, u.nama 
                 FROM pinjaman p 
                 JOIN users u ON p.user_id = u.id
                 WHERE MONTH(p.created_at) = '$bulan' 
                 AND YEAR(p.created_at) = '$tahun'
                 ORDER BY p.created_at DESC";
$detail_pinjaman = mysqli_query($conn, $query_detail);
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
                <h2 class="fw-bold text-primary">Laporan Pinjaman</h2>
                <form method="GET" class="d-flex">
                    <input type="month" class="form-control me-2" name="tanggal" value="<?php echo $filter_tanggal; ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                            <h5>Total Pinjaman</h5>
                            <h3><?php echo $statistik['total_pinjaman'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                            <h5>Total Pokok</h5>
                            <h3><?php echo formatRupiah($statistik['total_pokok'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fa-2x mb-2"></i>
                            <h5>Total Bunga</h5>
                            <h3><?php echo formatRupiah($statistik['total_bunga'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <h5>Rata Bunga</h5>
                            <h3><?php echo number_format($statistik['rata_bunga'] ?? 0, 2); ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Status Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_all = 0;
                                    $data_array = [];
                                    while ($row = mysqli_fetch_assoc($data_status)) {
                                        $data_array[] = $row;
                                        $total_all += $row['jumlah'];
                                    }
                                    
                                    foreach ($data_array as $row): 
                                        $persentase = $total_all > 0 ? ($row['jumlah'] / $total_all) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $row['status'] == 'disetujui' ? 'success' : 
                                                     ($row['status'] == 'ditolak' ? 'danger' : 'warning'); 
                                            ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-<?php 
                                                    echo $row['status'] == 'disetujui' ? 'success' : 
                                                         ($row['status'] == 'ditolak' ? 'danger' : 'warning'); 
                                                ?>" style="width: <?php echo $persentase; ?>%">
                                                    <?php echo number_format($persentase, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Detail Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nasabah</th>
                                            <th>Pokok</th>
                                            <th>Bunga</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($detail_pinjaman)): ?>
                                        <tr>
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo formatRupiah($row['pokok_pinjaman']); ?></td>
                                            <td><?php echo $row['bunga']; ?>%</td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $row['status'] == 'disetujui' ? 'success' : 
                                                         ($row['status'] == 'ditolak' ? 'danger' : 'warning'); 
                                                ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Cetak -->
            <div class="text-center mt-4">
                <a href="cetak_laporan.php?tanggal=<?php echo $filter_tanggal; ?>" 
                   class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>