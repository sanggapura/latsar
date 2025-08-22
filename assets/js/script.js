// Tambahkan interaksi sederhana
document.addEventListener("DOMContentLoaded", function() {
    console.log("Partner Cooperation Website loaded ✅");

    // Tambahkan konfirmasi khusus saat klik hapus
    const deleteLinks = document.querySelectorAll("a[href*='action=delete']");
    deleteLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            const confirmDelete = confirm("Apakah Anda yakin ingin menghapus partner ini?");
            if (!confirmDelete) {
                e.preventDefault();
            }
        });
    });
});
