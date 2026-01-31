// File JavaScript untuk konfirmasi delete dan fungsi tambahan

// Fungsi konfirmasi delete (sudah ada inline di HTML, ini backup)
function confirmDelete(postTitle) {
    return confirm(`Yakin ingin menghapus post "${postTitle}"? Tindakan ini tidak bisa dibatalkan!`);
}

// Fungsi untuk auto-hide alert setelah beberapa detik
document.addEventListener('DOMContentLoaded', function() {
    // Cari semua alert
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
    
    // Hide setelah 5 detik
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});

// Fungsi untuk validasi form di client-side (optional, sebagai tambahan)
function validatePostForm() {
    const title = document.querySelector('input[name="title"]').value;
    const content = document.querySelector('textarea[name="content"]').value;
    
    if (title.length < 5) {
        alert('Judul minimal 5 karakter!');
        return false;
    }
    
    if (content.length < 20) {
        alert('Konten minimal 20 karakter!');
        return false;
    }
    
    return true;
}