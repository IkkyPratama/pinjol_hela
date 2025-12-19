<?php
include '../includes/functions.php';
include '../config/database.php';

if (!isLoggedIn()) redirect('../auth/login.php');
if (isAdmin()) redirect('../admin/index.php');

$page_title = "Riwayat Pembayaran - PinjamYuk";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Ambil semua riwayat pembayaran user
$query = "SELECT pb.*, p.pokok_pinjaman, p.bunga_total, p.tenor, p.bulan_berjalan
          FROM pembayaran pb
          JOIN pinjaman p ON pb.pinjaman_id = p.id
          WHERE p.user_id = '$user_id'
          ORDER BY pb.tanggal_bayar DESC";
$riwayat = mysqli_query($conn, $query);

// Hitung statistik
$total_pembayaran = 0;
$total_bulan = 0;
$pembayaran_terakhir = '';

if (mysqli_num_rows($riwayat) > 0) {
    mysqli_data_seek($riwayat, 0);
    while ($data = mysqli_fetch_assoc($riwayat)) {
        $total_pembayaran += $data['jumlah_bayar'];
        $total_bulan++;
    }
    mysqli_data_seek($riwayat, 0);
    $data_terakhir = mysqli_fetch_assoc($riwayat);
    $pembayaran_terakhir = date('d M Y', strtotime($data_terakhir['tanggal_bayar']));
}
?>

<style>
.history-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    margin-bottom: 25px;
}

.history-card:hover {
    transform: translateY(-5px);
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    border: none;
}

.stat-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card.warning {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.timeline-item {
    border-left: 3px solid #007bff;
    padding: 20px;
    margin-left: 20px;
    position: relative;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 25px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #007bff;
}

.payment-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.table-custom th {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 600;
}

.table-custom td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.history-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.filter-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}

.btn-filter {
    border-radius: 20px;
    padding: 8px 20px;
    font-weight: 500;
}

.amount-display {
    font-size: 1.5rem;
    font-weight: 700;
    color: #28a745;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 15px;
}

.status-completed {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-pending {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="text-primary mb-2"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h2>
                            <p class="text-muted mb-0">Lihat semua riwayat pembayaran pinjaman Anda</p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-success fs-6 p-2">
                                <i class="fas fa-receipt me-1"></i>TERCATAT DENGAN RAPI
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="opacity-75">TOTAL PEMBAYARAN</small>
                                <h3 class="mb-0">Rp <?php echo number_format($total_pembayaran, 0, ',', '.'); ?></h3>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="opacity-75">TOTAL BULAN</small>
                                <h3 class="mb-0"><?php echo $total_bulan; ?> Bulan</h3>
                            </div>
                            <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="opacity-75">TERAKHIR BAYAR</small>
                                <h4 class="mb-0"><?php echo $pembayaran_terakhir ?: '-'; ?></h4>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0 text-primary"><i class="fas fa-filter me-2"></i>Filter Riwayat</h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-filter active">Semua</button>
                            <button type="button" class="btn btn-outline-primary btn-filter">Bulan Ini</button>
                            <button type="button" class="btn btn-outline-primary btn-filter">3 Bulan</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (mysqli_num_rows($riwayat) > 0): ?>
                <!-- Tampilan Tabel -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-table me-2"></i>Daftar Pembayaran
            </h5>
            <span class="badge bg-light text-dark">
                <?php echo $total_bulan; ?> transaksi
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Periode</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                mysqli_data_seek($riwayat, 0);
                while ($data = mysqli_fetch_assoc($riwayat)):
                    $tanggal = date('d M Y', strtotime($data['tanggal_bayar']));
                    $waktu   = date('H:i', strtotime($data['created_at']));
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>

                        <td>
                            <strong><?php echo $tanggal; ?></strong><br>
                            <small class="text-muted"><?php echo $waktu; ?> WIB</small>
                        </td>

                        <td>
                            <strong class="text-primary">Bulan ke-<?php echo $data['bulan_ke']; ?></strong><br>
                            <small class="text-muted">
                                Angsuran <?php echo $data['bulan_ke']; ?> dari <?php echo $data['tenor']; ?>
                            </small>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-<?php 
                                    echo $data['metode_bayar'] == 'transfer' ? 'university' : 
                                         ($data['metode_bayar'] == 'virtual_account' ? 'credit-card' : 
                                         ($data['metode_bayar'] == 'qris' ? 'qrcode' : 'money-bill')); 
                                ?> me-1"></i>
                                <?php echo strtoupper($data['metode_bayar']); ?>
                            </span>
                        </td>

                        <td class="fw-bold text-success">
                            Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <span class="status-badge status-disetujui">
                                <i class="fas fa-check-circle me-1"></i>Lunas
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="showDetail(<?php echo $data['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


                <!-- Tampilan Timeline (Alternatif) -->
                <div class="card history-card">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="mb-0 text-primary"><i class="fas fa-stream me-2"></i>Timeline Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        mysqli_data_seek($riwayat, 0);
                        $timeline_no = 1;
                        while ($data = mysqli_fetch_assoc($riwayat)): 
                        ?>
                        <div class="timeline-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="fw-bold text-primary mb-1">Bulan <?php echo $data['bulan_ke']; ?></div>
                                        <small class="text-muted"><?php echo date('M Y', strtotime($data['tanggal_bayar'])); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="history-icon bg-success text-white me-3">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></div>
                                            <small class="text-muted"><?php echo ucfirst($data['metode_bayar']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d F Y', strtotime($data['tanggal_bayar'])); ?>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('H:i', strtotime($data['created_at'])); ?> WIB
                                    </small>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span class="payment-badge status-completed">
                                        <i class="fas fa-check me-1"></i>Berhasil
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php 
                        $timeline_no++;
                        endwhile; 
                        ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="card history-card">
                    <div class="empty-state">
                        <i class="fas fa-receipt fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Belum Ada Riwayat</h3>
                        <p class="text-muted mb-4">Anda belum melakukan pembayaran apapun.</p>
                        <a href="bayar_tagihan.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Bayar Tagihan Pertama
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.querySelectorAll('.btn-filter').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        this.classList.remove('btn-outline-primary');
        this.classList.add('btn-primary');
        
        // Here you can add filter logic
        const filterType = this.textContent.trim();
        console.log('Filter by:', filterType);
        // Add your filter implementation here
    });
});

// Show payment detail
function showDetail(paymentId) {
    Swal.fire({
        title: 'Detail Pembayaran',
        html: `
            <div class="text-start">
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>ID Transaksi:</strong><br>
                        <span class="text-primary">TRX-${paymentId.toString().padStart(6, '0')}</span>
                    </div>
                    <div class="col-6">
                        <strong>Status:</strong><br>
                        <span class="badge bg-success">LUNAS</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Tanggal Bayar:</strong><br>
                        ${new Date().toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })}
                    </div>
                </div>
                <div class="alert alert-info">
                    <strong>Informasi:</strong><br>
                    Pembayaran telah berhasil diproses dan tercatat dalam sistem.
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Tutup'
    });
}

// Add animation to cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.history-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include '../includes/footer.php'; ?>