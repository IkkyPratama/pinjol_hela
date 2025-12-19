<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

// ==== AUTH ==== //
if (!isLoggedIn()) redirect('../auth/login.php');
if (!isNasabah()) redirect('../admin/index.php');

$page_title = "Dashboard Nasabah - PinjamYuk";
include '../includes/header.php';

// ==== USER SESSION (DIKUNCI) ==== //
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) {
    die('Session user tidak valid');
}

// ==== AMBIL DATA USER ==== //
$stmt = $conn->prepare("SELECT id, nama, limit_pinjaman FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die('User tidak ditemukan');
}

// ==== HELPER STAT ==== //
function fetchSingle($conn, $sql, $user_id) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (float) ($row['total'] ?? 0);
}

// ==== STATISTIK ==== //
$total_pinjaman = fetchSingle(
    $conn,
    "SELECT SUM(pokok_pinjaman) AS total FROM pinjaman 
     WHERE user_id=? AND status='disetujui'",
    $user_id
);

$total_bunga = fetchSingle(
    $conn,
    "SELECT SUM(bunga_total) AS total FROM pinjaman 
     WHERE user_id=? AND status='disetujui'",
    $user_id
);

$pinjaman_pending = fetchSingle(
    $conn,
    "SELECT COUNT(*) AS total FROM pinjaman 
     WHERE user_id=? AND status='pending'",
    $user_id
);

// ==== LIMIT ==== //
$limit_total    = (float) $user['limit_pinjaman'];
$limit_terpakai = $total_pinjaman;
$limit_tersedia = max(0, $limit_total - $limit_terpakai);
$persentase     = $limit_total > 0 ? ($limit_terpakai / $limit_total) * 100 : 0;

// ==== PINJAMAN TERBARU ==== //
$stmt = $conn->prepare("
    SELECT created_at, pokok_pinjaman, bunga, tenor, status
    FROM pinjaman
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 4
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_pinjaman = $stmt->get_result();
?>

<div class="container-fluid py-4">
  <div class="row">

    <!-- SIDEBAR -->
    <div class="col-md-3">
      <?php include 'sidebar.php'; ?>

      <!-- INFO LIMIT -->
      <div class="card mt-4">
        <div class="card-header bg-white">
          <h6 class="mb-0 text-primary"><i class="fas fa-wallet me-2"></i>Info Limit</h6>
        </div>
        <div class="card-body">
          <small>Limit Terpakai</small>
          <div class="progress mb-2" style="height:10px">
            <div class="progress-bar <?= $persentase > 80 ? 'bg-danger' : ($persentase > 50 ? 'bg-warning' : 'bg-success'); ?>" style="width:<?= $persentase ?>%"></div>
          </div>
          <small class="fw-bold"><?= number_format($persentase,1) ?>%</small>
          <hr>
          <div class="d-flex justify-content-between"><span>Total</span><strong><?= formatRupiah($limit_total) ?></strong></div>
          <div class="d-flex justify-content-between"><span>Terpakai</span><strong class="text-warning"><?= formatRupiah($limit_terpakai) ?></strong></div>
          <div class="d-flex justify-content-between"><span>Tersedia</span><strong class="text-success"><?= formatRupiah($limit_tersedia) ?></strong></div>
        </div>
      </div>

      <!-- KALKULATOR -->
      <div class="card mt-4">
        <div class="card-header bg-white">
          <h6 class="mb-0 text-primary"><i class="fas fa-calculator me-2"></i>Kalkulator</h6>
        </div>
        <div class="card-body">
          <input type="number" id="calc_amount" class="form-control form-control-sm mb-2" placeholder="Jumlah Pinjaman">
          <select id="calc_bunga" class="form-control form-control-sm mb-2">
            <option value="25" selected>25%</option>
          </select>
          <select id="calc_tenor" class="form-control form-control-sm mb-2">
            <option value="6">6 Bulan</option>
            <option value="12" selected>12 Bulan</option>
            <option value="24">24 Bulan</option>
          </select>
          <button class="btn btn-primary btn-sm w-100" onclick="hitungKalkulator()">Hitung</button>
          <div id="calc_result" class="mt-3 d-none">
            <hr>
            <div class="d-flex justify-content-between"><small>Bunga</small><strong id="result_bunga"></strong></div>
            <div class="d-flex justify-content-between"><small>Total</small><strong id="result_total"></strong></div>
            <div class="d-flex justify-content-between"><small>/Bulan</small><strong id="result_angsuran"></strong></div>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN -->
    <div class="col-md-9">

      <!-- STAT CARDS -->
      <div class="row mb-4">
        <?php
          $stats = [
            ['Limit Tersedia', formatRupiah($limit_tersedia), 'success', 'wallet'],
            ['Total Pinjaman', formatRupiah($total_pinjaman), 'primary', 'hand-holding-usd'],
            ['Pending', $pinjaman_pending, 'warning', 'clock'],
            ['Total Bunga', formatRupiah($total_bunga), 'info', 'percentage'],
          ];
          foreach ($stats as $s):
        ?>
        <div class="col-md-3 mb-3">
          <div class="card">
            <div class="card-body d-flex justify-content-between">
              <div><h5><?= $s[1] ?></h5><small><?= $s[0] ?></small></div>
              <i class="fas fa-<?= $s[3] ?> text-<?= $s[2] ?> fa-2x"></i>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- PINJAMAN TERBARU -->
      <div class="card">
        <div class="card-header bg-white d-flex justify-content-between">
          <h5 class="mb-0">Pinjaman Terbaru</h5>
          <a href="riwayat.php" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
          <?php if ($result_pinjaman && $result_pinjaman->num_rows > 0): ?>
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th><th>Pokok</th><th>Bunga</th><th>Tenor</th><th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while($p = $result_pinjaman->fetch_assoc()): ?>
              <tr>
                <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                <td><?= formatRupiah($p['pokok_pinjaman']) ?></td>
                <td><?= $p['bunga'] ?>%</td>
                <td><?= $p['tenor'] ?> bln</td>
                <td>
                  <span class="badge bg-<?= $p['status']=='disetujui'?'success':($p['status']=='pending'?'warning':'secondary') ?>">
                    <?= ucfirst($p['status']) ?>
                  </span>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
          <?php else: ?>
            <div class="text-center py-5 text-muted">Belum ada pinjaman</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function hitungKalkulator(){
  const a=+calc_amount.value,b=+calc_bunga.value,t=+calc_tenor.value;
  if(!a) return alert('Masukkan jumlah');
  const bunga=a*(b/100/12)*t;
  const total=a+bunga;
  const bulan=total/t;
  const rp=n=>'Rp '+Math.round(n).toLocaleString('id-ID');
  result_bunga.innerText=rp(bunga);
  result_total.innerText=rp(total);
  result_angsuran.innerText=rp(bulan);
  calc_result.classList.remove('d-none');
}
</script>

<?php include '../includes/footer.php'; ?>