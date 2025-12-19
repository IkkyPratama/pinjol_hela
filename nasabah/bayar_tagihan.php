<?php
include '../includes/functions.php';
include '../config/database.php';

if (!isLoggedIn()) redirect('../auth/login.php');
if (isAdmin()) redirect('../admin/index.php');

$page_title = "Bayar Tagihan - PinjamYuk";
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

/* ================= AMBIL PINJAMAN AKTIF ================= */
$q = mysqli_query($conn, "
    SELECT *, (pokok_pinjaman + bunga_total) / tenor AS angsuran
    FROM pinjaman
    WHERE user_id='$user_id'
    AND status='disetujui'
    AND bulan_berjalan <= tenor
");
$pinjaman = mysqli_fetch_assoc($q);
$hasPinjaman = $pinjaman ? true : false;

/* ================= HITUNG DENDA ================= */
$denda = 0;
if ($hasPinjaman) {
    $jatuhTempo = date('Y-m-10');
    $hariIni = date('Y-m-d');

    if ($hariIni > $jatuhTempo) {
        $telat = (strtotime($hariIni) - strtotime($jatuhTempo)) / 86400;
        $denda = ceil($telat) * 5000;
    }

    $totalBayar = $pinjaman['angsuran'] + $denda;
}

/* ================= PROSES BAYAR ================= */
if (isset($_POST['bayar'])) {
    $metode = $_POST['metode_bayar'];

    if ($metode == '') {
        $_SESSION['error'] = 'Metode pembayaran wajib dipilih';
        header('Location: bayar_tagihan.php'); exit;
    }

    mysqli_query($conn, "INSERT INTO pembayaran
        (pinjaman_id, bulan_ke, jumlah_bayar, denda, total_bayar, metode_bayar, status, tanggal_bayar)
        VALUES (
        '{$pinjaman['id']}', '{$pinjaman['bulan_berjalan']}', '{$pinjaman['angsuran']}', '$denda', '$totalBayar', '$metode', 'menunggu_verifikasi', CURDATE())
    ");

    mysqli_query($conn, "UPDATE pinjaman SET bulan_berjalan = bulan_berjalan + 1 WHERE id='{$pinjaman['id']}'");

    $_SESSION['success'] = 'Pembayaran berhasil dikirim';
    header('Location: riwayat_pembayaran.php'); exit;
}
?>

<style>
.metode-card{
    border:2px solid #e5e7eb;
    border-radius:14px;
    padding:16px;
    cursor:pointer;
    transition:.2s;
}
.metode-card:hover{border-color:#2563eb;background:#f0f7ff}
.metode-card.active{border-color:#16a34a;background:#ecfdf5}
</style>

<div class="container-fluid">
<div class="row" style="min-height:calc(100vh - 80px)">

<div class="col-md-3 bg-light border-end">
<?php include 'sidebar.php'; ?>
</div>

<div class="col-md-9 d-flex align-items-center">
<div class="w-100 px-4">

<?php if($hasPinjaman): ?>

<!-- INFO TAGIHAN -->
<div class="row mb-3">
<div class="col-md-4"><div class="alert alert-primary text-center">Bulan<br><b><?= $pinjaman['bulan_berjalan']; ?>/<?= $pinjaman['tenor']; ?></b></div></div>
<div class="col-md-4"><div class="alert alert-success text-center">Angsuran<br><b>Rp <?= number_format($pinjaman['angsuran']); ?></b></div></div>
<div class="col-md-4"><div class="alert alert-warning text-center">Denda<br><b>Rp <?= number_format($denda); ?></b></div></div>
</div>

<form method="POST" id="paymentForm">
<input type="hidden" name="metode_bayar" id="metodeBayar">

<div class="card shadow-sm">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3">
<h5>Total Bayar</h5>
<h3 class="text-success">Rp <?= number_format($totalBayar); ?></h3>
</div>

<h6 class="mb-2">Metode Pembayaran</h6>
<div class="row g-3 mb-3">
<div class="col-md-6">
  <div class="metode-card" onclick="pilihMetode('Transfer Bank',this);showInstruksi('bank')">
    🏦 Transfer Bank<br><small>BCA, BRI, BNI, Mandiri</small>
  </div>
</div>
<div class="col-md-6">
  <div class="metode-card" onclick="pilihMetode('QRIS',this);showInstruksi('qris')">
    📱 QRIS<br><small>Scan QR</small>
  </div>
</div>
<div class="col-md-6">
  <div class="metode-card" onclick="pilihMetode('Alfamart',this);showInstruksi('alfa')">
    🏪 Alfamart<br><small>Bayar di kasir</small>
  </div>
</div>
<div class="col-md-6">
  <div class="metode-card" onclick="pilihMetode('Indomaret',this);showInstruksi('indo')">
    🏪 Indomaret<br><small>Bayar di kasir</small>
  </div>
</div>
<div class="col-md-12">
  <div class="metode-card" onclick="pilihMetode('Mitra',this);showInstruksi('mitra')">
    🤝 Mitra Pembayaran<br><small>Agen resmi</small>
  </div>
</div>
</div>

<!-- INSTRUKSI PEMBAYARAN -->
<div id="instruksi" class="alert alert-info d-none">
  <div id="bank" class="d-none">
    <b>Transfer ke salah satu rekening berikut:</b><br>
    BCA: <b id="bca">1234567890</b> <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('bca')">Salin</button><br>
    BRI: <b id="bri">9876543210</b> <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('bri')">Salin</button><br>
    Mandiri: <b id="mandiri">1122334455</b> <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyText('mandiri')">Salin</button>
  </div>
  <div id="qris" class="d-none">
    <b>QRIS:</b><br>Scan QR Code pembayaran di aplikasi e-wallet
  </div>
  <div id="alfa" class="d-none">
    <b>Alfamart:</b><br>Sebutkan kode bayar <b>PYK123456</b> ke kasir
  </div>
  <div id="indo" class="d-none">
    <b>Indomaret:</b><br>Sebutkan kode bayar <b>PYK654321</b> ke kasir
  </div>
  <div id="mitra" class="d-none">
    <b>Mitra Pembayaran:</b><br>Datangi agen resmi PinjamYuk terdekat
  </div>
</div>

<button type="submit" name="bayar" class="btn btn-success w-100">Konfirmasi Pembayaran</button>

</div></div>
</form>

<?php else: ?>
<div class="alert alert-info text-center">Tidak ada tagihan aktif</div>
<?php endif; ?>

</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function pilihMetode(m,el){
 document.getElementById('metodeBayar').value=m;
 document.querySelectorAll('.metode-card').forEach(c=>c.classList.remove('active'));
 el.classList.add('active');
}

document.getElementById('paymentForm')?.addEventListener('submit',function(e){
 if(document.getElementById('metodeBayar').value==''){
  e.preventDefault();
  Swal.fire('Oops','Pilih metode pembayaran dulu','warning');
 }
});

function showInstruksi(tipe){
    // tampilkan box instruksi
    const box = document.getElementById('instruksi');
    box.classList.remove('d-none');

    // sembunyikan semua isi
    ['bank','qris','alfa','indo','mitra'].forEach(id=>{
        document.getElementById(id)?.classList.add('d-none');
    });

    // tampilkan sesuai pilihan
    document.getElementById(tipe).classList.remove('d-none');
}

function copyText(id){
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text);

    Swal.fire({
        toast:true,
        position:'top-end',
        icon:'success',
        title:'Nomor disalin',
        showConfirmButton:false,
        timer:1500
    });
}
</script>

<?php include '../includes/footer.php'; ?>
