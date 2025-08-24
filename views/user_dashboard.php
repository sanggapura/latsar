<?php
// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: auth.php?action=login_form');
    exit;
}

$user = $_SESSION['user'];
include __DIR__ . "/header.php";
?>

<style>
.dashboard-container {
    padding: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
}

.welcome-content h1 {
    font-size: 2.5em;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.welcome-content p {
    font-size: 1.2em;
    opacity: 0.9;
    margin-bottom: 0;
}

.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(45deg, #3498db, #2980b9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 28px;
    color: white;
}

.stat-number {
    font-size: 2.5em;
    font-weight: 700;
    color: #2c3e50;
    display: block;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 1.1em;
    color: #7f8c8d;
    font-weight: 500;
}

.main-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.menu-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 1px solid #f0f0f0;
    display: block;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    text-decoration: none;
    color: inherit;
}

.menu-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.menu-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #ff6b35, #f7931e);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-right: 20px;
}

.menu-title {
    font-size: 1.4em;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.menu-description {
    color: #7f8c8d;
    line-height: 1.6;
    margin-bottom: 15px;
}

.menu-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 8px 16px;
    background: #ecf0f1;
    color: #2c3e50;
    text-decoration: none;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background: #3498db;
    color: white;
    text-decoration: none;
}

.recent-activity {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 40px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.section-title {
    font-size: 1.5em;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.view-all-btn {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
}

.view-all-btn:hover {
    text-decoration: underline;
}

.activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #27ae60, #2ecc71);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    margin-right: 15px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-text {
    color: #2c3e50;
    margin: 0 0 5px 0;
    font-weight: 500;
}

.activity-time {
    color: #95a5a6;
    font-size: 13px;
    margin: 0;
}

.no-activity {
    text-align: center;
    color: #95a5a6;
    padding: 40px;
    font-style: italic;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 20px;
    }
    
    .welcome-content h1 {
        font-size: 2em;
    }
    
    .welcome-content p {
        font-size: 1em;
    }
    
    .quick-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .main-menu {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .menu-header {
        flex-direction: column;
        text-align: center;
    }
    
    .menu-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
}
</style>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1>🎉 Selamat Datang, <?= htmlspecialchars($user['username']) ?>!</h1>
            <p>Portal Jemari 5.0 - Kelola kemitraan Anda dengan efisien dan terstruktur</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <span class="stat-number">0</span>
            <span class="stat-label">Total Mitra</span>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <span class="stat-number">0</span>
            <span class="stat-label">Tahapan Aktif</span>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-folder"></i>
            </div>
            <span class="stat-number">0</span>
            <span class="stat-label">Dokumen</span>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <span class="stat-number">0</span>
            <span class="stat-label">Jadwal Mendatang</span>
        </div>
    </div>

    <!-- Main Menu -->
    <div class="main-menu">
        <a href="index.php?action=stages" class="menu-card">
            <div class="menu-header">
                <div class="menu-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="menu-title">Tahapan Kerjasama</h3>
            </div>
            <p class="menu-description">
                Kelola dan pantau tahapan kerjasama dengan berbagai jenis mitra mulai dari kementerian, pemerintah daerah, hingga sektor swasta.
            </p>
            <div class="menu-actions">
                <span class="action-btn"><i class="fas fa-eye"></i> Lihat Semua</span>
                <span class="action-btn"><i class="fas fa-plus"></i> Tambah Baru</span>
            </div>
        </a>

        <a href="index.php?action=contacts" class="menu-card">
            <div class="menu-header">
                <div class="menu-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <h3 class="menu-title">Kontak Mitra</h3>
            </div>
            <p class="menu-description">
                Database kontak lengkap dengan informasi perusahaan, PIC, dan detail komunikasi untuk memudahkan koordinasi.
            </p>
            <div class="menu-actions">
                <span class="action-btn"><i class="fas fa-list"></i> Daftar Kontak</span>
                <span class="action-btn"><i class="fas fa-user-plus"></i> Tambah Kontak</span>
            </div>
        </a>

        <a href="index.php?action=documents" class="menu-card">
            <div class="menu-header">
                <div class="menu-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="menu-title">Dokumen</h3>
            </div>
            <p class="menu-description">
                Sistem manajemen dokumen terintegrasi untuk menyimpan, mengorganisir, dan berbagi dokumen kerjasama.
            </p>
            <div class="menu-actions">
                <span class="action-btn"><i class="fas fa-file-alt"></i> Lihat Dokumen</span>
                <span class="action-btn"><i class="fas fa-upload"></i> Upload</span>
            </div>
        </a>

        <a href="index.php?action=schedule" class="menu-card">
            <div class="menu-header">
                <div class="menu-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="menu-title">Jadwal & Timeline</h3>
            </div>
            <p class="menu-description">
                Kelola jadwal pertemuan, deadline, dan milestone penting dalam proses kerjasama dengan kalender terintegrasi.
            </p>
            <div class="menu-actions">
                <span class="action-btn"><i class="fas fa-calendar"></i> Kalender</span>
                <span class="action-btn"><i class="fas fa-clock"></i> Jadwal Baru</span>
            </div>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <div class="section-header">
            <h2 class="section-title"><i class="fas fa-history"></i> Aktivitas Terbaru</h2>
            <a href="#" class="view-all-btn">Lihat Semua</a>
        </div>
        
        <div class="no-activity">
            <i class="fas fa-inbox" style="font-size: 48px; color: #bdc3c7; margin-bottom: 15px;"></i>
            <p>Belum ada aktivitas terbaru. Mulai dengan menambahkan kontak mitra atau membuat tahapan kerjasama baru.</p>
        </div>
        
        <!-- Example activity items (will be populated dynamically) -->
        <!--
        <ul class="activity-list">
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">Kontak mitra baru "PT Teknologi Indonesia" telah ditambahkan</p>
                    <p class="activity-time">2 jam yang lalu</p>
                </div>
            </li>
            
            <li class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="activity-content">
                    <p class="activity-text">Tahapan "Review Proposal" telah diperbarui</p>
                    <p class="activity-time">5 jam yang lalu</p>
                </div>
            </li>
        </ul>
        -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animation to cards
    const cards = document.querySelectorAll('.stat-card, .menu-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Update stats with animation (placeholder for real data)
    setTimeout(() => {
        animateCounter('.stat-number', [12, 8, 24, 3]); // Example numbers
    }, 1000);
});

function animateCounter(selector, targets) {
    const counters = document.querySelectorAll(selector);
    counters.forEach((counter, index) => {
        const target = targets[index] || 0;
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });
}
</script>

<?php include __DIR__ . "/footer.php"; ?>