// Hitung pinjaman otomatis
function hitungPinjaman() {
    const pokok = parseFloat(document.getElementById('pokok').value) || 0;
    const bunga = parseFloat(document.getElementById('bunga').value) || 0;
    const tenor = parseInt(document.getElementById('tenor').value) || 0;
    const diskon = parseFloat(document.getElementById('diskon').value) || 0;

    // Hitung bunga total
    const bungaTotal = (pokok * bunga / 100) * tenor;
    
    // Hitung total sebelum diskon
    const totalSebelumDiskon = pokok + bungaTotal;
    
    // Hitung diskon
    const nilaiDiskon = totalSebelumDiskon * diskon / 100;
    
    // Hitung total setelah diskon
    const totalSetelahDiskon = totalSebelumDiskon - nilaiDiskon;
    
    // Hitung angsuran per bulan
    const angsuranPerBulan = totalSetelahDiskon / tenor;

    // Update hasil
    document.getElementById('bungaTotal').textContent = formatRupiah(bungaTotal);
    document.getElementById('totalSebelumDiskon').textContent = formatRupiah(totalSebelumDiskon);
    document.getElementById('nilaiDiskon').textContent = formatRupiah(nilaiDiskon);
    document.getElementById('totalSetelahDiskon').textContent = formatRupiah(totalSetelahDiskon);
    document.getElementById('angsuranPerBulan').textContent = formatRupiah(angsuranPerBulan);
}

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Validasi form
function validateForm() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

// Inisialisasi saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
    validateForm();
    
    // Auto calculate untuk input pinjaman
    const calcInputs = ['pokok', 'bunga', 'tenor', 'diskon'];
    calcInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', hitungPinjaman);
        }
    });
});

// Confirm delete
function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
    return confirm(message);
}