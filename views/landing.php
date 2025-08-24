<?php
// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header('Location: index.php?action=dashboard');
    exit;
}

include __DIR__ . "/header.php";
?>

<style>
/* Landing Page Specific Styles */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.hero-content h1 {
    font-size: 3.5em;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: fadeInUp 1s ease-out;
}

.hero-content p {
    font-size: 1.3em;
    margin-bottom: 40px;
    opacity: 0.9;
    max-width: 600px;
    animation: fadeInUp 1s ease-out 0.2s both;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 1s ease-out 0.4s both;
}

.cta-btn {
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 180px;
    justify-content: center;
}

.cta-primary {
    background: linear-gradient(45deg, #27ae60, #2ecc71);
    color: white;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
}

.cta-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cta-secondary:hover {
    background: white;
    color: #667eea;
    transform: translateY(-3px);
}

.features-section {
    padding: 80px 20px;
    background: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    margin-top: 60px;
}

.feature-card {
    text-align: center;
    padding: 40px 30px;
    border-radius: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    background: white;
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #3498db, #2980b9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 30px;
    color: white;
}

.feature-card h3 {
    color: #2c3e50;
    font-size: 1.5em;
    margin-bottom: 15px;
}

.feature-card p {
    color: #7f8c8d;
    line-height: 1.6;
}

.section-title {
    text-align: center;
    color: #2c3e50;
    font-size: 2.5em;
    margin-bottom: 20px;
}

.section-subtitle {
    text-align: center;
    color: #7f8c8d;
    font-size: 1.1em;
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.stats-section {
    padding: 60px 20px;
    background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 800px;
    margin: 0 auto;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 3em;
    font-weight: 700;
    display: block;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.1em;
    opacity: 0.9;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5em;
    }
    
    .hero-content p {
        font-size: 1.1em;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1><i class="fas fa-handshake"></i> Portal Jemari 5.0</h1>
        <p>Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja - Platform terintegrasi untuk mengelola kemitraan strategis dalam pengembangan pasar kerja Indonesia</p>
        
        <div class="cta-buttons">
            <a href="auth.php?action=login_form" class="cta-btn cta-primary">
                <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
            </a>
            <a href="#features" class="cta-btn cta-secondary">
                <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="section-subtitle">Platform komprehensif untuk mengelola seluruh aspek kemitraan dalam pengembangan pasar kerja</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3>Tahapan Kerjasama</h3>
                <p>Kelola tahapan kerjasama dengan berbagai jenis mitra mulai dari kementerian, pemerintah daerah, hingga sektor swasta dengan sistem tracking yang komprehensif.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <h3>Manajemen Kontak</h3>
                <p>Database kontak mitra yang terorganisir dengan baik, memudahkan komunikasi dan koordinasi dengan berbagai pihak terkait.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>Dokumen Terintegrasi</h3>
                <p>Sistem manajemen dokumen yang memungkinkan penyimpanan, pengorganisasian, dan berbagi dokumen kerjasama dengan aman.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Penjadwalan</h3>
                <p>Kelola jadwal pertemuan, deadline, dan milestone penting dalam proses kerjasama dengan sistem kalender terintegrasi.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Monitoring & Evaluasi</h3>
                <p>Pantau progress kerjasama dan evaluasi hasil dengan dashboard analitik yang memberikan insight mendalam.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Keamanan Data</h3>
                <p>Sistem keamanan berlapis dengan enkripsi data, kontrol akses, dan audit trail untuk melindungi informasi sensitif.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <h2 class="section-title">Dampak & Pencapaian</h2>
        
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">500+</span>
                <span class="stat-label">Mitra Kerjasama</span>
            </div>
            
            <div class="stat-item">
                <span class="stat-number">1000+</span>
                <span class="stat-label">Program Terkelola</span>
            </div>
            
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">Daerah Terjangkau</span>
            </div>
            
            <div class="stat-item">
                <span class="stat-number">24/7</span>
                <span class="stat-label">Sistem Tersedia</span>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Tentang Portal Jemari 5.0</h2>
        <div style="max-width: 800px; margin: 0 auto; text-align: center; color: #7f8c8d; line-height: 1.8; font-size: 1.1em;">
            <p>Portal Jemari 5.0 PaskerID adalah evolusi terbaru dari sistem informasi kemitraan Pusat Pasar Kerja. Dirancang untuk mempermudah pengelolaan kemitraan strategis dengan berbagai stakeholder dalam upaya mengembangkan ekosistem pasar kerja yang lebih baik.</p>
            
            <p>Dengan teknologi modern dan antarmuka yang intuitif, platform ini memungkinkan tim untuk bekerja lebih efisien dalam mengelola hubungan kemitraan, mulai dari tahap inisiasi hingga evaluasi hasil kerjasama.</p>
            
            <div style="margin-top: 40px;">
                <a href="auth.php?action=login_form" class="cta-btn cta-primary">
                    <i class="fas fa-rocket"></i> Mulai Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading animation to CTA buttons
    document.querySelectorAll('.cta-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.href.includes('#')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
            }
        });
    });
});
</script>

<?php include __DIR__ . "/footer.php"; ?>