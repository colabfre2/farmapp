// Ambil semua elemen yang punya class 'input-rupiah'
let rupiahInputs = document.querySelectorAll('.input-rupiah');

// Lakukan perulangan (loop) untuk memberikan event listener ke masing-masing input
rupiahInputs.forEach(function(input) {
    input.addEventListener('keyup', function(e) {
        this.value = formatRupiah(this.value, 'Rp. ');
    });
});

// Fungsi formatRupiah tetap sama seperti sebelumnya
function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split     = number_string.split(','),
        sisa      = split[0].length % 3,
        rupiah    = split[0].substr(0, sisa),
        ribuan    = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}


function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. SCRIPT UNTUK NOTIFIKASI SUKSES / ERROR
    // ==========================================
    const flashData = document.getElementById('flash-data');
    if (flashData) {
        const successMsg = flashData.getAttribute('data-success');
        const errorMsg = flashData.getAttribute('data-error');

        if (successMsg && successMsg !== '') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMsg,
                showConfirmButton: false,
                timer: 2000
            });
        }

        if (errorMsg && errorMsg !== '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMsg,
            });
        }
    }

    // ==========================================
    // 2. SCRIPT UNTUK KONFIRMASI HAPUS DATA
    // ==========================================
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah form langsung tersubmit
            
            let form = this.closest('.form-delete');
            
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit form jika user menekan "Ya, Hapus!"
                }
            });
        });
    });

});