<?php
// includes/header.php
// JANGAN include functions.php di sini, karena sudah diinclude di file yang memanggil header

$page_title = isset($page_title) ? $page_title : 'PinjamYuk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="brand-logo me-2">
                    <i class="fas fa-hand-holding-usd fa-lg"></i>
                </div>
                <div>
                    <strong class="fw-bold">PinjamYuk</strong>
                    <small class="d-block text-warning" style="font-size: 0.7rem; line-height: 1;">Pinjaman Modal</small>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar-sm me-2">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo $_SESSION['nama']; ?></div>
                                <small class="opacity-75">
                                    <?php echo isAdmin() ? 'Administrator' : 'Nasabah'; ?>
                                </small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo isAdmin() ? '../admin/profil.php' : '../nasabah/profil.php'; ?>">
                                    <i class="fas fa-user-edit me-2"></i>Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="../auth/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<style>
.navbar {
    backdrop-filter: blur(10px);
    background: rgba(44, 62, 80, 0.95) !important;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}
</style>