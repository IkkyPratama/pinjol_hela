<?php
// includes/functions.php

// Pastikan file tidak diinclude lebih dari sekali
if (!defined('FUNCTIONS_INCLUDED')) {
    define('FUNCTIONS_INCLUDED', true);

    // Pastikan session start hanya dipanggil sekali
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Include config database dengan path yang benar
    if (!defined('DB_INCLUDED')) {
        include __DIR__ . '/../config/database.php';
        define('DB_INCLUDED', true);
    }

    // Deklarasi fungsi hanya jika belum ada
    if (!function_exists('hitungBunga')) {
        function hitungBunga($pokok, $bunga, $tenor) {
            // Bunga per tahun, konversi ke bulan
            return ($pokok * $bunga / 100) * ($tenor / 12);
        }
    }

    if (!function_exists('hitungDiskon')) {
        function hitungDiskon($total, $diskon) {
            return $total - ($total * $diskon / 100);
        }
    }

    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) {
            if ($angka == 0 || $angka == '') return 'Rp 0';
            return 'Rp ' . number_format($angka, 0, ',', '.');
        }
    }

    if (!function_exists('isLoggedIn')) {
        function isLoggedIn() {
            return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        }
    }

    if (!function_exists('isAdmin')) {
        function isAdmin() {
            return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
        }
    }

    if (!function_exists('isNasabah')) {
        function isNasabah() {
            return isset($_SESSION['role']) && $_SESSION['role'] == 'nasabah';
        }
    }

    if (!function_exists('redirect')) {
        function redirect($url) {
            if (!headers_sent()) {
                header("Location: $url");
                exit();
            } else {
                echo "<script>window.location.href='$url';</script>";
                exit();
            }
        }
    }

    if (!function_exists('cleanInput')) {
        function cleanInput($data) {
            global $conn;
            if (isset($conn)) {
                return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
            }
            return htmlspecialchars(strip_tags(trim($data)));
        }
    }

    if (!function_exists('debug')) {
        function debug($data) {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
    }
}
?>