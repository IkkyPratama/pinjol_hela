<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validasi password
    if ($password !== $confirm) {
        $error = "Password dan Konfirmasi tidak sama!";
    } else {

        // Cek email sudah dipakai atau belum
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar!";
        } else {

            // Password disimpan plain text karena login.php masih pakai plain text
            $sql = "INSERT INTO users (nama, email, password, role, status)
                    VALUES ('$nama', '$email', '$password', 'nasabah', 'pending')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Pendaftaran berhasil! Tunggu verifikasi admin.');
                window.location='login.php';</script>";
                exit();
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PinjamYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="login-card p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Daftar Akun</h2>
                        <p class="text-muted">Buat akun untuk mengajukan pinjaman</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check"></i></span>
                                <input type="password" name="confirm" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Daftar
                        </button>

                        <p class="text-center">
                            Sudah punya akun? <a href="login.php">Login di sini</a>
                        </p>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
