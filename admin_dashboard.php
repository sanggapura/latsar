<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: loginadm.php');
    exit;
}

$admin = $_SESSION['admin'];

// Get database connection
$db = (new Database())->getConnection();

// Get some basic statistics
$stats = [
    'total_users' => 0,
    'total_contacts' => 0,
    'total_documents' => 0,
    'total_stages' => 0,
    'active_partnerships' => 0
];

try {
    // Count users
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Count contacts
    $stmt = $db->query("SELECT COUNT(*) FROM kontak_mitra");
    $stats['total_contacts'] = $stmt->fetchColumn();
    
    // Count documents (if table exists)
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM documents");
        $stats['total_documents'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $stats['total_documents'] = 0;
    }
    
    // Count stages (if table exists)
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM cooperation_stages");
        $stats['total_stages'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $stats['total_stages'] = 0;
    }
    
} catch (PDOException $e) {
    log_message("Error fetching admin dashboard stats: " . $e->getMessage(), 'ERROR', 'admin.log');
}

// Get flash message
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portal Jemari 5.0 PaskerID</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f9fc;
            color: #333;
        }

        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .admin-title h1 {
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .admin-title p {
            opacity: 0.9;
            font-size: 14px;
        }

        .admin-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .logout-btn {
            background: rgba(231, 76, 60, 0.8);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(231, 76, 60, 1);
            text-decoration: none;
            color: white;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
        }

        .welcome-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: white;
        }

        .welcome-text h2 {
            color: #2c3e50;
            font-size: 1.8em;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #7f8c8d;
            font-size: 16px;
            line-height: 1.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: white;
        }

        .stat-card:nth-child(1) .stat-icon { background: linear-gradient(45deg, #3498db, #2980b9); }
        .stat-card:nth-child(2) .stat-icon { background: linear-gradient(45deg, #27ae60, #2ecc71); }
        .stat-card:nth-child(3) .stat-icon { background: linear-gradient(45deg, #f39c12, #e67e22); }
        .stat-card:nth-child(4) .stat-icon { background: linear-gradient(45deg, #9b59b6, #8e44ad); }
        .stat-card:nth-child(5) .stat-icon { background: linear-gradient(45deg, #e74c3c, #c0392b); }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            color: #2c3e50;
            display: block;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #7f8c8d;
            font-weight: 500;
            font-size: 14px;
        }

        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .menu-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-right: 20px;
        }

        .menu-card:nth-child(1) .menu-icon { background: linear-gradient(45deg, #3498db, #2980b9); }
        .menu-card:nth-child(2) .menu-icon { background: linear-gradient(45deg, #27ae60, #2ecc71); }
        .menu-card:nth-child(3) .menu-icon { background: linear-gradient(45deg, #f39c12, #e67e22); }
        .menu-card:nth-child(4) .menu-icon { background: linear-gradient(45deg, #9b59b6, #8e44ad); }
        .menu-card:nth-child(5) .menu-icon { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .menu-card:nth-child(6) .menu-icon { background: linear-gradient(45deg, #34495e, #2c3e50); }

        .menu-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .menu-description {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .menu-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-tag {
            background: #f8f9fa;
            color: #6c757d;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .recent-activity {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e3e6f0;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
        }

        .section-title {
            font-size: 1.4em;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .flash-message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .admin-menu {
                grid-template-columns: 1fr;
            }

            .welcome-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="admin-title">
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <p>Portal Jemari 5.0 PaskerID - Panel Administrasi</p>
            </div>
            <div class="admin-actions">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?= htmlspecialchars($admin['full_name'] ?? $admin['username']) ?></div>
                        <div style="font-size: 12px; opacity: 0.8;"><?= htmlspecialchars($admin['role'] ?? 'Administrator') ?></div>
                    </div>
                </div>
                <a href="loginadm.php?action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <?php if ($flash): ?>
            <div class="flash-message flash-<?= htmlspecialchars($flash['type']) ?>">
                <i class="fas fa-<?= $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle' ?>"></i>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <div class="welcome-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="welcome-text">
                    <h2>Selamat Datang, <?= htmlspecialchars($admin['full_name'] ?? $admin['username']) ?>!</h2>
                    <p>Kelola sistem Portal Jemari 5.0 dengan mudah melalui panel administrasi yang komprehensif. Pantau statistik, kelola pengguna, dan konfigurasi sistem dari satu tempat.</p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <span class="stat-number"><?= number_format($stats['total_users']) ?></span>
                <span class="stat-label">Total Pengguna</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <span class="stat-number"><?= number_format($stats['total_contacts']) ?></span>
                <span class="stat-label">Kontak Mitra</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-folder"></i>
                </div>
                <span class="stat-number"><?= number_format($stats['total_documents']) ?></span>
                <span class="stat-label">Dokumen</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <span class="stat-number"><?= number_format($stats['total_stages']) ?></span>
                <span class="stat-label">Tahapan Kerjasama</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <span class="stat-number"><?= number_format($stats['active_partnerships']) ?></span>
                <span class="stat-label">Kemitraan Aktif</span>
            </div>
        </div>

        <!-- Admin Menu -->
        <div class="admin-menu">
            <a href="admin_users.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3 class="menu-title">Kelola Pengguna</h3>
                </div>
                <p class="menu-description">
                    Tambah, edit, dan kelola akun pengguna sistem. Atur peran dan hak akses pengguna.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Tambah User</span>
                    <span class="action-tag">Edit Profile</span>
                    <span class="action-tag">Reset Password</span>
                </div>
            </a>

            <a href="admin_contacts.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <h3 class="menu-title">Master Data Kontak</h3>
                </div>
                <p class="menu-description">
                    Kelola data kontak mitra, perusahaan, dan stakeholder dalam sistem kemitraan.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Import Data</span>
                    <span class="action-tag">Export Excel</span>
                    <span class="action-tag">Bulk Edit</span>
                </div>
            </a>

            <a href="admin_stages.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3 class="menu-title">Tahapan & Workflow</h3>
                </div>
                <p class="menu-description">
                    Konfigurasi tahapan kerjasama, workflow approval, dan monitoring progress.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Template</span>
                    <span class="action-tag">Workflow</span>
                    <span class="action-tag">Timeline</span>
                </div>
            </a>

            <a href="admin_documents.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="menu-title">Manajemen Dokumen</h3>
                </div>
                <p class="menu-description">
                    Organisasi dokumen, pengaturan kategori, dan kontrol akses dokumen sistem.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Kategorisasi</span>
                    <span class="action-tag">Archive</span>
                    <span class="action-tag">Permissions</span>
                </div>
            </a>

            <a href="admin_reports.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="menu-title">Laporan & Analitik</h3>
                </div>
                <p class="menu-description">
                    Dashboard analitik, laporan kinerja, dan insight data kemitraan.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Dashboard</span>
                    <span class="action-tag">Export PDF</span>
                    <span class="action-tag">Scheduled</span>
                </div>
            </a>

            <a href="admin_settings.php" class="menu-card">
                <div class="menu-header">
                    <div class="menu-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="menu-title">Pengaturan Sistem</h3>
                </div>
                <p class="menu-description">
                    Konfigurasi sistem, pengaturan email, backup data, dan maintenance.
                </p>
                <div class="menu-actions">
                    <span class="action-tag">Config</span>
                    <span class="action-tag">Backup</span>
                    <span class="action-tag">Logs</span>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-history"></i> Aktivitas Sistem Terbaru</h2>
                <a href="admin_logs.php" style="color: #3498db; text-decoration: none; font-size: 14px;">Lihat Semua Log</a>
            </div>
            
            <div style="text-align: center; color: #95a5a6; padding: 40px;">
                <i class="fas fa-clock" style="font-size: 48px; margin-bottom: 15px;"></i>
                <p>Log aktivitas sistem akan ditampilkan di sini setelah ada aktivitas pengguna.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats on load
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach((stat, index) => {
                const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
                stat.textContent = '0';
                
                setTimeout(() => {
                    animateCounter(stat, finalValue);
                }, index * 200);
            });

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
        });

        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 20);
        }
    </script>
</body>
</html>