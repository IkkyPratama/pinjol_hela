<?php
// admin/cetak_laporan.php

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

$page_title = "Cetak Laporan - PinjamYuk";
include '../config/database.php';

// Filter laporan
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m');
$bulan_tahun = explode('-', $filter_tanggal);
$bulan = $bulan_tahun[1];
$tahun = $bulan_tahun[0];

// Format Rupiah function
function formatRupiah($angka) {
    if (empty($angka)) return 'Rp 0';
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Ambil data laporan
$query = "SELECT p.*, u.nama 
          FROM pinjaman p 
          JOIN users u ON p.user_id = u.id
          WHERE MONTH(p.created_at) = '$bulan' 
          AND YEAR(p.created_at) = '$tahun'
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error dalam query: " . mysqli_error($conn));
}

// Hitung statistik
$query_total = "SELECT 
    COUNT(*) as total_pinjaman,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as pinjaman_disetujui,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as pinjaman_ditolak,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pinjaman_pending,
    COALESCE(SUM(pokok_pinjaman), 0) as total_pokok,
    COALESCE(SUM(bunga_total), 0) as total_bunga,
    COALESCE(SUM(pokok_pinjaman + bunga_total), 0) as total_nominal
    FROM pinjaman 
    WHERE MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun'";
    
$statistik_result = mysqli_query($conn, $query_total);

if (!$statistik_result) {
    die("Error dalam query statistik: " . mysqli_error($conn));
}

$statistik = mysqli_fetch_assoc($statistik_result);

// Set default values jika null
$statistik = array_map(function($value) {
    return $value === null ? 0 : $value;
}, $statistik);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pinjaman - PinjamYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; margin: 0; padding: 10px; }
            .container { max-width: 100% !important; padding: 0; }
            .print-area { box-shadow: none !important; padding: 0 !important; }
        }
        
        body { 
            font-family: Arial, sans-serif; 
            background: #f8f9fa;
            padding: 20px;
        }
        
        .print-area {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .period-info {
            font-size: 14px;
            color: #666;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .summary-box {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .summary-label {
            font-weight: 500;
        }
        
        .summary-value {
            font-weight: bold;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        
        .table th {
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
        }
        
        .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 40px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="print-area">
        <!-- Header -->
        <div class="header">
            <div class="company-name">PINJAMYUK</div>
            <div class="company-tagline">Pinjaman Modal</div>
            <div class="report-title">LAPORAN PINJAMAN BULANAN</div>
            <div class="period-info">
                Periode: <?php echo date('F Y', strtotime($filter_tanggal . '-01')); ?> | 
                Dicetak: <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>

        <!-- Ringkasan Cepat -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pinjaman</div>
                <div class="stat-value"><?php echo $statistik['total_pinjaman']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Disetujui</div>
                <div class="stat-value"><?php echo $statistik['pinjaman_disetujui']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ditolak</div>
                <div class="stat-value"><?php echo $statistik['pinjaman_ditolak']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value"><?php echo $statistik['pinjaman_pending']; ?></div>
            </div>
        </div>

        <!-- Ringkasan Keuangan -->
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">Total Pokok Pinjaman:</span>
                <span class="summary-value"><?php echo formatRupiah($statistik['total_pokok']); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Bunga:</span>
                <span class="summary-value"><?php echo formatRupiah($statistik['total_bunga']); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Nominal:</span>
                <span class="summary-value"><?php echo formatRupiah($statistik['total_nominal']); ?></span>
            </div>
        </div>

        <!-- Tabel Pinjaman -->
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Nasabah</th>
                    <th class="text-right">Pokok</th>
                    <th class="text-center">Bunga</th>
                    <th class="text-center">Tenor</th>
                    <th class="text-right">Angsuran/Bln</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php 
                    $no = 1;
                    $totalPokokManual = 0;
                    $totalBungaManual = 0;
                    while ($row = mysqli_fetch_assoc($result)): 
                        $angsuranBulanan = $row['tenor'] > 0 ? ($row['pokok_pinjaman'] + $row['bunga_total']) / $row['tenor'] : 0;
                        $totalPokokManual += $row['pokok_pinjaman'];
                        $totalBungaManual += $row['bunga_total'];
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="text-right"><?php echo formatRupiah($row['pokok_pinjaman']); ?></td>
                        <td class="text-center"><?php echo $row['bunga']; ?>%</td>
                        <td class="text-center"><?php echo $row['tenor']; ?> bln</td>
                        <td class="text-right"><?php echo formatRupiah($angsuranBulanan); ?></td>
                        <td class="text-center">
                            <span class="badge badge-<?php 
                                echo $row['status'] == 'disetujui' ? 'success' : 
                                     ($row['status'] == 'ditolak' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    
                    <!-- Total Row -->
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="3" class="text-right">TOTAL:</td>
                        <td class="text-right"><?php echo formatRupiah($totalPokokManual); ?></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><?php echo formatRupiah($totalBungaManual); ?></td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty-state">
                                <h5 class="text-muted">Tidak Ada Data</h5>
                                <p class="text-muted mb-0">Tidak ada data pinjaman untuk periode <?php echo date('F Y', strtotime($filter_tanggal . '-01')); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="signature">
                <div class="signature-box">
                    <div>Dicetak oleh:</div>
                    <div><strong><?php echo $_SESSION['nama'] ?? 'Admin Sistem'; ?></strong></div>
                    <div class="signature-line"></div>
                </div>
                <div class="signature-box">
                    <div>Mengetahui,</div>
                    <div><strong>Manager</strong></div>
                    <div class="signature-line"></div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="no-print btn-group">
            <a onclick="window.print()" class="btn btn-light btn-sm">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </a>
            <a href="laporan.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <script>
        // Auto print saat halaman load
        window.onload = function() {
            // Uncomment baris berikut untuk auto print
            // window.print();
        }
    </script>
</body>
</html>