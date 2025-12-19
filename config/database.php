<?php
// config/database.php

// Konfigurasi database - SESUAIKAN DENGAN XAMPP ANDA
$host = 'localhost';
$username = 'root';      
$password = '';          // Biasanya kosong di XAMPP
$database = 'pinjol';

// Buat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

?>