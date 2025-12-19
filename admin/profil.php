<?php
// admin/profil.php

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

$page_title = "Profil Admin - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

// ... kode profil.php yang sudah ada ...
$user_id = $_SESSION['user_id'];

// Ambil data profil
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

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
        $success = "Profil berhasil diupdate!";
        
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
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="fw-bold text-primary mb-4">Profil Admin</h2>

            <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profil</h5>
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
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" name="telepon" 
                                           value="<?php echo $user['telepon']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea class="form-control" name="alamat" rows="3" required><?php echo $user['alamat']; ?></textarea>
                                </div>
                                <button type="submit" name="update_profil" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profil
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card mt-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success_password)): ?>
                            <div class="alert alert-success"><?php echo $success_password; ?></div>
                            <?php endif; ?>
                            
                            <?php if (isset($error_password)): ?>
                            <div class="alert alert-danger"><?php echo $error_password; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Password Lama</label>
                                    <input type="password" class="form-control" name="password_lama" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" name="password_baru" required>
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

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <!-- Foto Profil -->
                            <div class="mb-3">
                                <img src="../assets/images/aww.jpg" alt="Foto Profil" class="profile-picture" width="200">
                            </div>
                            
                            <h4><?php echo $user['nama']; ?></h4>
                            <p class="text-muted">Administrator</p>
                            
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
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    Bergabung sejak: <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>