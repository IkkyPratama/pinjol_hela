<?php
// admin/sidebar.php
?>
<div class="card sidebar-card">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 text-primary">
            <i class="fas fa-bars me-2"></i>Menu Navigasi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">

            <a href="index.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>

            <a href="nasabah.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'nasabah.php' ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i>Data Nasabah
            </a>

            <a href="verifikasi_nasabah.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'verifikasi_nasabah.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-check me-2"></i>Verifikasi Nasabah
            </a>

            <a href="pinjaman.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'pinjaman.php' ? 'active' : ''; ?>">
                <i class="fas fa-hand-holding-usd me-2"></i>Pengajuan Pinjaman
            </a>

            <a href="laporan.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-2"></i>Laporan
            </a>

            <a href="profil.php" 
               class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-edit me-2"></i>Profil
            </a>

        </div>
    </div>
</div>
