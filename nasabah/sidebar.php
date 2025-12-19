<?php

?>
<div class="card sidebar-card">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 text-primary">
            <i class="fas fa-bars me-2"></i>Menu Utama
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="pinjaman.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'pinjaman.php' ? 'active' : ''; ?>">
                <i class="fas fa-hand-holding-usd me-2"></i>Ajukan Pinjaman
            </a>
            <a href="bayar_tagihan.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'bayar_tagihan.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card me-2"></i>Bayar Tagihan
            </a>
            <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'riwayat_pembayaran.php' ? 'active' : ''; ?>">
                <i class="fas fa-history me-2"></i>Riwayat Pembayaran
            </a>
            <a href="riwayat.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice me-2"></i>Riwayat Pinjaman
            </a>
            <a href="profil.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-edit me-2"></i>Profil Saya
            </a>
        </div>
    </div>
</div>

<!-- Card Info Tagihan -->
<?php
// Cek apakah user memiliki pinjaman aktif
include '../config/database.php';
$user_id = $_SESSION['user_id'];
$query_pinjaman = "SELECT p.*, 
                  (p.pokok_pinjaman + p.bunga_total) as total_pinjaman,
                  (p.pokok_pinjaman + p.bunga_total) / p.tenor as angsuran_per_bulan,
                  p.tenor - p.bulan_berjalan as sisa_bulan
                  FROM pinjaman p 
                  WHERE p.user_id = '$user_id' 
                  AND p.status = 'disetujui'
                  AND p.bulan_berjalan <= p.tenor";
$result_pinjaman = mysqli_query($conn, $query_pinjaman);
$data_pinjaman_aktif = mysqli_fetch_assoc($result_pinjaman);
?>

<?php if ($data_pinjaman_aktif): ?>
<div class="card sidebar-card mt-3">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 text-success">
            <i class="fas fa-info-circle me-2"></i>Info Tagihan
        </h6>
    </div>
    <div class="card-body">
        <div class="mb-2">
            <small class="text-muted">Bulan Berjalan</small>
            <div class="fw-bold text-primary">Bulan ke-<?php echo $data_pinjaman_aktif['bulan_berjalan']; ?></div>
        </div>
        <div class="mb-2">
            <small class="text-muted">Angsuran/Bulan</small>
            <div class="fw-bold text-success">Rp <?php echo number_format($data_pinjaman_aktif['angsuran_per_bulan'], 0, ',', '.'); ?></div>
        </div>
        <div class="mb-2">
            <small class="text-muted">Sisa Tenor</small>
            <div class="fw-bold text-warning"><?php echo $data_pinjaman_aktif['sisa_bulan']; ?> Bulan</div>
        </div>
        <a href="bayar_tagihan.php" class="btn btn-success btn-sm w-100 mt-2">
            <i class="fas fa-credit-card me-1"></i>Bayar Sekarang
        </a>
    </div>
</div>
<script>
// Auto refresh info tagihan setiap 30 detik
setInterval(function() {
    fetch('get_tagihan_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.hasTagihan) {
                document.getElementById('bulanBerjalan').innerText = 'Bulan ke-' + data.bulan_berjalan;
                document.getElementById('angsuranBulanan').innerText = 'Rp ' + data.angsuran_per_bulan;
                document.getElementById('sisaBulan').innerText = data.sisa_bulan + ' Bulan';
            }
        });
}, 30000);
</script>
<?php endif; ?>


<style>
.sidebar-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.sidebar-card .card-header {
    border-radius: 10px 10px 0 0 !important;
    padding: 15px 20px;
}

.list-group-item {
    border: none;
    padding: 12px 20px;
    color: #495057;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.list-group-item.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.list-group-item i {
    width: 20px;
    text-align: center;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 0.85rem;
}
</style>