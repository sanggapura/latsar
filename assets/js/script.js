// Tambahkan interaksi sederhana
document.addEventListener("DOMContentLoaded", function() {
    console.log("Partner Cooperation Website loaded ✅");

    // Tambahkan konfirmasi khusus saat klik hapus
    const deleteForms = document.querySelectorAll("form[action*='action=delete']");
    deleteForms.forEach(form => {
        form.addEventListener("submit", function(e) {
            if (!confirm("Apakah Anda yakin ingin menghapus partner ini?")) {
                e.preventDefault();
            }
        });
    });
});
