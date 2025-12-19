<?php
// nasabah/profil.php

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
$page_title = "Profil Saya - PinjamYuk";
include '../config/database.php';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Ambil data profil
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Hitung statistik pinjaman
$q_total_pinjaman = mysqli_query(
    $conn,
    "SELECT SUM(pokok_pinjaman) AS total 
     FROM pinjaman 
     WHERE user_id = '$user_id' AND status = 'disetujui'"
);
$row_total_pinjaman = mysqli_fetch_assoc($q_total_pinjaman);
$total_pinjaman = (int) ($row_total_pinjaman['total'] ?? 0);

$q_total_pengajuan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM pinjaman 
     WHERE user_id = '$user_id'"
);
$row_total_pengajuan = mysqli_fetch_assoc($q_total_pengajuan);
$total_pengajuan = (int) ($row_total_pengajuan['total'] ?? 0);

$q_pinjaman_aktif = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM pinjaman 
     WHERE user_id = '$user_id' AND status = 'disetujui'"
);
$row_pinjaman_aktif = mysqli_fetch_assoc($q_pinjaman_aktif);
$pinjaman_aktif = (int) ($row_pinjaman_aktif['total'] ?? 0);

// Hitung limit
$limit_tersedia   = (int) $user['limit_pinjaman'] - $total_pinjaman;
$persentase_limit = $user['limit_pinjaman'] > 0 
    ? ($total_pinjaman / $user['limit_pinjaman']) * 100 
    : 0;

// Update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $query = "UPDATE users SET nama = '$nama', email = '$email', telepon = '$telepon', alamat = '$alamat' 
              WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        $success = "Profil berhasil diperbarui!";
        
        // Refresh data
        $query = "SELECT * FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Update password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    if (password_verify($password_lama, $user['password'])) {
        if ($password_baru == $konfirmasi_password) {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = '$password_hash' WHERE id = '$user_id'";
            
            if (mysqli_query($conn, $query)) {
                $success_password = "Password berhasil diubah!";
            } else {
                $error_password = "Error: " . mysqli_error($conn);
            }
        } else {
            $error_password = "Password baru dan konfirmasi tidak cocok!";
        }
    } else {
        $error_password = "Password lama salah!";
    }
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>

            <!-- Statistik Profil -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Saya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total Pengajuan</span>
                        <span class="badge bg-primary"><?php echo $total_pengajuan; ?></span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Pinjaman Aktif</span>
                        <span class="badge bg-success"><?php echo $pinjaman_aktif; ?></span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Limit Terpakai</span>
                        <span class="badge bg-warning"><?php echo number_format($persentase_limit, 1); ?>%</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Member Sejak</span>
                        <small class="text-muted"><?php echo date('M Y', strtotime($user['created_at'])); ?></small>
                    </div>
                </div>
            </div>

            <!-- Limit Info -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-wallet me-2"></i>Info Limit
                    </h6>
                </div>
                <div class="card-body">
                    <div class="limit-progress mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small>Limit Terpakai</small>
                            <small class="fw-bold"><?php echo number_format($persentase_limit, 1); ?>%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar <?php echo $persentase_limit > 80 ? 'bg-danger' : ($persentase_limit > 50 ? 'bg-warning' : 'bg-success'); ?>" 
                                 style="width: <?php echo $persentase_limit; ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="limit-details">
                        <div class="limit-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Total:</span>
                            <strong><?php echo formatRupiah($user['limit_pinjaman']); ?></strong>
                        </div>
                        <div class="limit-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Terpakai:</span>
                            <strong class="text-warning"><?php echo formatRupiah($total_pinjaman); ?></strong>
                        </div>
                        <div class="limit-item d-flex justify-content-between">
                            <span class="text-muted">Tersedia:</span>
                            <strong class="text-success"><?php echo formatRupiah($limit_tersedia); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">Profil Saya</h2>
                    <p class="text-muted mb-0">Kelola informasi profil dan akun Anda</p>
                </div>
                <div class="user-status">
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Aktif
                    </span>
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
                <!-- Profil Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Informasi Profil</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="nama" 
                                               value="<?php echo $user['nama']; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo $user['email']; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="telepon" 
                                           value="<?php echo $user['telepon']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" name="alamat" rows="4" required><?php echo $user['alamat']; ?></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="update_profil" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card mt-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success_password)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_password; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($error_password)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_password; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Password Lama</label>
                                    <input type="password" class="form-control" name="password_lama" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" name="password_baru" required>
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" name="konfirmasi_password" required>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Profile Summary -->
                <div class="col-lg-4">
                    <div class="card profile-summary">
                        <div class="card-body text-center">
<div class="mb-3">
                                <img src="../assets/images/acu.jpg" alt="Foto Profil" class="profile-picture" width="200">
                            </div>
                            <h4 class="mb-1"><?php echo $user['nama']; ?></h4>
                            <p class="text-muted mb-3">Nasabah</p>
                            
                            <div class="profile-info">
                                <div class="info-item d-flex align-items-center mb-3">
                                    <i class="fas fa-envelope text-primary me-3"></i>
                                    <div class="text-start">
                                        <small class="text-muted">Email</small>
                                        <div class="fw-bold"><?php echo $user['email']; ?></div>
                                    </div>
                                </div>
                                <div class="info-item d-flex align-items-center mb-3">
                                    <i class="fas fa-phone text-success me-3"></i>
                                    <div class="text-start">
                                        <small class="text-muted">Telepon</small>
                                        <div class="fw-bold"><?php echo $user['telepon']; ?></div>
                                    </div>
                                </div>
                                <div class="info-item d-flex align-items-start mb-3">
                                    <i class="fas fa-map-marker-alt text-danger me-3 mt-1"></i>
                                    <div class="text-start">
                                        <small class="text-muted">Alamat</small>
                                        <div class="fw-bold small"><?php echo $user['alamat']; ?></div>
                                    </div>
                                </div>
                                <div class="info-item d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-info me-3"></i>
                                    <div class="text-start">
                                        <small class="text-muted">Bergabung</small>
                                        <div class="fw-bold"><?php echo date('d F Y', strtotime($user['created_at'])); ?></div>
                                    </div>
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