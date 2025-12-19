<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Query error: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Cek status verifikasi
        if ($user['status'] != 'active') {
            $error = "Akun Anda belum diverifikasi admin!";
        } 
        // Cek password
        else if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../nasabah/index.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PinjamYuk</title>
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
                            <i class="fas fa-hand-holding-usd fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold">PinjamYuk</h2>
                            <p class="text-muted">Silakan login ke akun Anda</p>
                        </div>

                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>

                            <!-- Tambahan tombol daftar -->
                            <div class="text-center">
                                Belum punya akun? <a href="register.php" >Daftar disini!</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
