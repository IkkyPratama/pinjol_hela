<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Cek apakah sudah login & role admin
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Jika admin klik setuju/verifikasi
if (isset($_GET['setuju'])) {
    $id = intval($_GET['setuju']);
    $query = "UPDATE users SET status = 'active' WHERE id = $id";
    mysqli_query($conn, $query);

    $_SESSION['success'] = "Nasabah berhasil diverifikasi!";
    header("Location: verifikasi_nasabah.php");
    exit();
}

// Jika admin klik tolak
if (isset($_GET['tolak'])) {
    $id = intval($_GET['tolak']);
    $query = "UPDATE users SET status = 'rejected' WHERE id = $id";
    mysqli_query($conn, $query);

    $_SESSION['success'] = "Pendaftaran nasabah ditolak!";
    header("Location: verifikasi_nasabah.php");
    exit();
}

// Ambil semua user pending
$pending = mysqli_query($conn, "SELECT * FROM users WHERE role = 'nasabah' AND status = 'pending'");
$pending_count = mysqli_num_rows($pending);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Nasabah</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid py-4">
        <div class="row">
            
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-md-9">

                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Verifikasi Nasabah Baru</h5>
                        <?php if ($pending_count > 0): ?>
                            <span class="badge bg-warning"><?php echo $pending_count; ?> menunggu</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($pending_count == 0): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada nasabah menunggu verifikasi</h5>
                                <p class="text-secondary">Semua nasabah telah diverifikasi</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>NAMA</th>
                                            <th>EMAIL</th>
                                            <th>NO HP</th>
                                            <th>TANGGAL DAFTAR</th>
                                            <th style="width: 150px;">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($pending)):
                                            $tanggal_daftar = isset($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : date('d/m/Y');
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                                            <td><?php echo $tanggal_daftar; ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="?setuju=<?php echo $row['id']; ?>" 
                                                       class="btn btn-success btn-sm"
                                                       onclick="return confirm('Setujui nasabah <?php echo addslashes($row['nama']); ?>?')">
                                                        <i class="fas fa-check me-1"></i> Setuju
                                                    </a>
                                                    <a href="?tolak=<?php echo $row['id']; ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Tolak nasabah <?php echo addslashes($row['nama']); ?>?')">
                                                        <i class="fas fa-times me-1"></i> Tolak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                        $no++;
                                        endwhile; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple confirmation with better UX
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href*="setuju"], a[href*="tolak"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const action = this.href.includes('setuju') ? 'menyetujui' : 'menolak';
                    const nama = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                    
                    if (!confirm(`Anda yakin ${action} nasabah ${nama}?`)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>