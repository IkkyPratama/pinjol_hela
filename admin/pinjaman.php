<?php
// admin/pinjaman.php

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

$page_title = "Pengajuan Pinjaman - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

// Proses persetujuan pinjaman
if (isset($_GET['setuju'])) {
    $id = mysqli_real_escape_string($conn, $_GET['setuju']);
    $query = "UPDATE pinjaman SET status = 'disetujui' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Pinjaman disetujui!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Proses penolakan pinjaman
if (isset($_GET['tolak'])) {
    $id = mysqli_real_escape_string($conn, $_GET['tolak']);
    $query = "UPDATE pinjaman SET status = 'ditolak' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Pinjaman ditolak!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data pengajuan pinjaman
$query = "SELECT p.*, u.nama, u.email, u.telepon, u.limit_pinjaman 
          FROM pinjaman p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);
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
                <h2 class="fw-bold text-primary">Pengajuan Pinjaman</h2>
            </div>

            <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Daftar Pengajuan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nasabah</th>
                                    <th>Pokok Pinjaman</th>
                                    <th>Bunga</th>
                                    <th>Tenor</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): 
                                    $bunga_total = hitungBunga($row['pokok_pinjaman'], $row['bunga'], $row['tenor']);
                                    $total_bayar = $row['pokok_pinjaman'] + $bunga_total;
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <strong><?php echo $row['nama']; ?></strong><br>
                                        <small class="text-muted"><?php echo $row['email']; ?></small>
                                    </td>
                                    <td><?php echo formatRupiah($row['pokok_pinjaman']); ?></td>
                                    <td><?php echo $row['bunga']; ?>%</td>
                                    <td><?php echo $row['tenor']; ?> bulan</td>
                                    <td><?php echo formatRupiah($total_bayar); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $row['status'] == 'disetujui' ? 'success' : 
                                                 ($row['status'] == 'ditolak' ? 'danger' : 'warning'); 
                                        ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'pending'): ?>
                                        <a href="?setuju=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Setujui pinjaman ini?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="?tolak=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Tolak pinjaman ini?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal"
                                                data-nasabah="<?php echo $row['nama']; ?>"
                                                data-email="<?php echo $row['email']; ?>"
                                                data-telepon="<?php echo $row['telepon']; ?>"
                                                data-pokok="<?php echo formatRupiah($row['pokok_pinjaman']); ?>"
                                                data-bunga="<?php echo $row['bunga']; ?>%"
                                                data-tenor="<?php echo $row['tenor']; ?> bulan"
                                                data-bungatotal="<?php echo formatRupiah($bunga_total); ?>"
                                                data-total="<?php echo formatRupiah($total_bayar); ?>"
                                                data-angsuran="<?php echo formatRupiah($total_bayar / $row['tenor']); ?>"
                                                data-status="<?php echo ucfirst($row['status']); ?>"
                                                data-limit="<?php echo formatRupiah($row['limit_pinjaman']); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
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
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan Pinjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Nasabah</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nama</strong></td><td id="detail_nasabah"></td></tr>
                            <tr><td><strong>Email</strong></td><td id="detail_email"></td></tr>
                            <tr><td><strong>Telepon</strong></td><td id="detail_telepon"></td></tr>
                            <tr><td><strong>Limit</strong></td><td id="detail_limit"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Pinjaman</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Pokok</strong></td><td id="detail_pokok"></td></tr>
                            <tr><td><strong>Bunga</strong></td><td id="detail_bunga"></td></tr>
                            <tr><td><strong>Tenor</strong></td><td id="detail_tenor"></td></tr>
                            <tr><td><strong>Total Bunga</strong></td><td id="detail_bungatotal"></td></tr>
                            <tr><td><strong>Total Bayar</strong></td><td id="detail_total"></td></tr>
                            <tr><td><strong>Angsuran/Bulan</strong></td><td id="detail_angsuran"></td></tr>
                            <tr><td><strong>Status</strong></td><td id="detail_status"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        
        document.getElementById('detail_nasabah').textContent = button.getAttribute('data-nasabah');
        document.getElementById('detail_email').textContent = button.getAttribute('data-email');
        document.getElementById('detail_telepon').textContent = button.getAttribute('data-telepon');
        document.getElementById('detail_limit').textContent = button.getAttribute('data-limit');
        document.getElementById('detail_pokok').textContent = button.getAttribute('data-pokok');
        document.getElementById('detail_bunga').textContent = button.getAttribute('data-bunga');
        document.getElementById('detail_tenor').textContent = button.getAttribute('data-tenor');
        document.getElementById('detail_bungatotal').textContent = button.getAttribute('data-bungatotal');
        document.getElementById('detail_total').textContent = button.getAttribute('data-total');
        document.getElementById('detail_angsuran').textContent = button.getAttribute('data-angsuran');
        document.getElementById('detail_status').textContent = button.getAttribute('data-status');
    });
});
</script>

<?php include '../includes/footer.php'; ?>