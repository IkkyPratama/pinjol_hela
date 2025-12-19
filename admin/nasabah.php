<?php
// admin/nasabah.php

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

$page_title = "Data Nasabah - PinjamYuk";
include '../includes/header.php';
include '../config/database.php';

// ... kode nasabah.php yang sudah ada ...
// Tambah nasabah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_nasabah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash('123456', PASSWORD_DEFAULT); // Password default
    
    $query = "INSERT INTO users (nama, email, telepon, alamat, password, role, limit_pinjaman) 
              VALUES ('$nama', '$email', '$telepon', '$alamat', '$password', 'nasabah', 10000000)";
    
    if (mysqli_query($conn, $query)) {
        $success = "Nasabah berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Update limit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_limit'])) {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $limit_pinjaman = mysqli_real_escape_string($conn, $_POST['limit_pinjaman']);
    
    $query = "UPDATE users SET limit_pinjaman = '$limit_pinjaman' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Limit pinjaman berhasil diupdate!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Hapus nasabah
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query = "DELETE FROM users WHERE id = '$id' AND role = 'nasabah'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Nasabah berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data nasabah
$query = "SELECT * FROM users WHERE role = 'nasabah' ORDER BY created_at DESC";
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

            <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Nasabah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Limit Pinjaman</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $row['nama']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['telepon']; ?></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo formatRupiah($row['limit_pinjaman']); ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editLimitModal"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-nama="<?php echo $row['nama']; ?>"
                                                data-limit="<?php echo $row['limit_pinjaman']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?hapus=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirmDelete('Yakin hapus nasabah <?php echo $row['nama']; ?>?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

<!-- Modal Tambah Nasabah -->
<div class="modal fade" id="tambahNasabahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Nasabah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" name="telepon" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>Password default: <strong>123456</strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_nasabah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Limit -->
<div class="modal fade" id="editLimitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Limit Pinjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Nasabah</label>
                        <input type="text" class="form-control" id="edit_nama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Limit Pinjaman</label>
                        <input type="number" class="form-control" name="limit_pinjaman" id="edit_limit" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_limit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk modal edit limit
document.addEventListener('DOMContentLoaded', function() {
    var editLimitModal = document.getElementById('editLimitModal');
    editLimitModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-id');
        var nama = button.getAttribute('data-nama');
        var limit = button.getAttribute('data-limit');
        
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_limit').value = limit;
    });
});
</script>

<?php include '../includes/footer.php'; ?>