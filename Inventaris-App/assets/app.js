// Tutup alert saat tombol .btn-close diklik
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-close')) {
        var alertBox = e.target.closest('.alert');
        if (alertBox) {
            alertBox.style.transition = 'opacity .2s';
            alertBox.style.opacity = '0';
            setTimeout(function () { alertBox.remove(); }, 200);
        }
    }
});