<?php
// reset_password.php - File untuk reset password (HARAP DIHAPUS SETELAH DIGUNAKAN)

echo "<h3>Reset Password PinjamYuk</h3>";

// Include config database
include 'config/database.php';

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

echo "Status koneksi database: <strong>Berhasil</strong><br><br>";

// Password yang akan di-set
$password_plain = '123456';
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Password yang akan di-set: <strong>{$password_plain}</strong><br>";
echo "Password hash: <strong>{$hashed_password}</strong><br><br>";

// Reset password untuk admin
$query_admin = "UPDATE users SET password = '$hashed_password' WHERE email = 'admin@pinjamyuk.com'";
if (mysqli_query($conn, $query_admin)) {
    echo "✅ Password admin (admin@pinjamyuk.com) berhasil direset!<br>";
} else {
    echo "❌ Error reset password admin: " . mysqli_error($conn) . "<br>";
}

// Reset password untuk nasabah  
$query_nasabah = "UPDATE users SET password = '$hashed_password' WHERE email = 'nasabah@pinjamyuk.com'";
if (mysqli_query($conn, $query_nasabah)) {
    echo "✅ Password nasabah (nasabah@pinjamyuk.com) berhasil direset!<br>";
} else {
    echo "❌ Error reset password nasabah: " . mysqli_error($conn) . "<br>";
}

// Cek data user setelah reset
echo "<br><strong>Data User setelah reset:</strong><br>";
$query_check = "SELECT id, nama, email, role FROM users";
$result = mysqli_query($conn, $query_check);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['id']} - {$row['nama']} ({$row['email']}) - Role: {$row['role']}<br>";
    }
} else {
    echo "Tidak ada data user ditemukan!<br>";
}

echo "<br><hr>";
echo "<h4>📋 Informasi Login:</h4>";
echo "<strong>Admin:</strong><br>";
echo "Email: <strong>admin@gmail.com</strong><br>";
echo "Password: <strong>12345</strong><br><br>";

echo "<strong>Nasabah:</strong><br>";
echo "Email: <strong>nasabah@gmail.com</strong><br>";
echo "Password: <strong>123456</strong><br><br>";

echo "<div style='background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0;'>";
echo "<strong>⚠️ PERINGATAN KEAMANAN:</strong><br>";
echo "File ini sangat berbahaya jika tetap ada di server production.<br>";
echo "<strong>HAPUS FILE INI SETELAH BERHASIL LOGIN!</strong>";
echo "</div>";

echo "<br><a href='auth/login.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>🔐 Coba Login Sekarang</a>";

// Tutup koneksi
mysqli_close($conn);
?>