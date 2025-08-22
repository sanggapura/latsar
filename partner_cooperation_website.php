
<?php
session_start();

// proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: partner_cooperation_website.php");
    exit;
}

// percobaan
// code dari cloudy
// proses login
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === "admin" && $password === "12345") {
        $_SESSION['loggedin'] = true;
    } else {
        $error = "Username atau password salah!";
    }
}

$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Jemari 5.0 PaskerID</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            min-height: 100vh;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }

        /* Login Styles */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }

        .login-title {
            text-align: center;
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .login-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: bold;
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 15px rgba(52, 152, 219, 0.3);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4);
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* Main App Styles */
        header {
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .logout-btn {
            position: absolute;
            top: 1rem;
            right: 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: white;
            color: #2c3e50;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        nav {
            background: #34495e;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }

        .nav-btn {
            background: none;
            border: none;
            color: white;
            padding: 1rem 2rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            flex: 1;
            max-width: 250px;
        }

        .nav-btn:hover {
            background: rgba(52, 152, 219, 0.3);
            transform: translateY(-2px);
        }

        .nav-btn.active {
            background: #3498db;
            border-bottom-color: #e74c3c;
        }

        .content {
            padding: 3rem 2rem;
            min-height: 500px;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Search Box */
        .search-container {
            margin-bottom: 2rem;
            text-align: center;
        }

        .search-box {
            width: 100%;
            max-width: 500px;
            padding: 1rem;
            border: 2px solid #e0e6ed;
            border-radius: 25px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .search-box:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 15px rgba(52, 152, 219, 0.3);
        }

        /* Category Cards */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .category-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #3498db;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .category-card.government {
            border-left-color: #e74c3c;
        }

        .category-card.development {
            border-left-color: #2ecc71;
        }

        .category-card.local {
            border-left-color: #f39c12;
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .category-description {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .partner-card {
            background: linear-gradient(135deg, #ffffff, #f1f3f4);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #e74c3c;
            transition: all 0.3s ease;
        }

        .partner-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .partner-name {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .partner-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-item {
            padding: 0.5rem;
        }

        .info-label {
            font-weight: bold;
            color: #7f8c8d;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .file-card {
            background: linear-gradient(135deg, #fff5f5, #ffe6e6);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #e74c3c;
            transition: all 0.3s ease;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .file-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        .download-btn {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4);
            background: linear-gradient(45deg, #2ecc71, #27ae60);
        }

        .section-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: linear-gradient(45deg, #3498db, #e74c3c);
            margin: 1rem auto;
            border-radius: 2px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .back-btn {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .step-card {
            background: linear-gradient(135deg, #fff5f5, #ffe6e6);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #e74c3c;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .step-number {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-wrap: wrap;
            }
            
            .nav-btn {
                flex: 1 1 50%;
                min-width: 150px;
            }
            
            .grid-container, .category-grid {
                grid-template-columns: 1fr;
            }
            
            .partner-info {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .content {
                padding: 2rem 1rem;
            }

            .logout-btn {
                position: relative;
                top: auto;
                right: auto;
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
<?php if (!$isLoggedIn): ?>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Portal Jemari 5.0</h2>
            <p class="login-subtitle">PaskerID - Sistem Kerjasama Mitra</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="container">
        <header>
            <a href="?logout=1" class="logout-btn">Logout</a>
            <h1>Portal Jemari 5.0 PaskerID</h1>
            <div class="subtitle">Sistem Manajemen Kerjasama Strategis</div>
        </header>

        <nav>
            <div class="nav-container">
                <button class="nav-btn active" onclick="showTab('mitra')">🤝 Tahapan Kerjasama</button>
                <button class="nav-btn" onclick="showTab('kontak')">📞 Kontak Mitra</button>
                <button class="nav-btn" onclick="showTab('file')">📋 File Kerjasama</button>
                <button class="nav-btn" onclick="showTab('lainnya')">📁 File Lainnya</button>
            </div>
        </nav>

        <div class="content">
            <div id="mitra" class="tab-content active">
                <div id="categoryView">
                    <h2 class="section-title">Kategori Kerjasama</h2>
                    
                    <div class="category-grid">
                        <div class="category-card government" onclick="showPartners('government')">
                            <div class="category-title">🏛️ Instansi Pemerintah K/L</div>
                            <div class="category-description">Kerjasama dengan Kementerian dan Lembaga pemerintah pusat</div>
                        </div>

                        <div class="category-card development" onclick="showPartners('development')">
                            <div class="category-title">🏢 Mitra Pembangunan</div>
                            <div class="category-description">Kerjasama dengan perusahaan swasta dan organisasi pembangunan</div>
                        </div>

                        <div class="category-card local" onclick="showPartners('local')">
                            <div class="category-title">🏛️ Dinas/Pemda</div>
                            <div class="category-description">Kerjasama dengan Pemerintah Daerah dan Dinas terkait</div>
                        </div>
                    </div>
                </div>

                <div id="partnerView" class="hidden">
                    <button class="back-btn" onclick="showCategories()">← Kembali ke Kategori</button>
                    <h2 class="section-title" id="partnerViewTitle"></h2>
                    
                    <div id="partnerList" class="category-grid">
                        <!-- Partner list will be populated by JavaScript -->
                    </div>
                </div>

                <div id="stepsView" class="hidden">
                    <button class="back-btn" onclick="showPartnerList()">← Kembali ke Daftar</button>
                    <h2 class="section-title" id="stepsViewTitle"></h2>
                    
                    <div id="stepsContainer">
                        <!-- Steps will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <div id="kontak" class="tab-content">
                <h2 class="section-title">Direktori Kontak Mitra</h2>
                
                <div class="search-container">
                    <input type="text" class="search-box" placeholder="🔍 Cari nama mitra, perusahaan, atau bidang usaha..." id="contactSearch" oninput="searchContacts()">
                </div>
                
                <div class="grid-container" id="contactContainer">
                    <div class="partner-card" data-search="pt teknologi maju indonesia teknologi informasi software development">
                        <div class="partner-name">PT. Teknologi Maju Indonesia</div>
                        <div class="partner-info">
                            <div class="info-item">
                                <div class="info-label">Nomor Telepon</div>
                                <div class="info-value">+62 21 1234 5678</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">partnership@teknologimaju.co.id</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">Jl. Sudirman No. 123, Jakarta Pusat 10220</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Bidang Usaha</div>
                                <div class="info-value">Teknologi Informasi & Software Development</div>
                            </div>
                        </div>
                    </div>

                    <div class="partner-card" data-search="cv global logistics logistik supply chain management">
                        <div class="partner-name">CV. Global Logistics</div>
                        <div class="partner-info">
                            <div class="info-item">
                                <div class="info-label">Nomor Telepon</div>
                                <div class="info-value">+62 31 9876 5432</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">info@globallogistics.id</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">Jl. Industri Raya No. 45, Surabaya 60177</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Bidang Usaha</div>
                                <div class="info-value">Logistik & Supply Chain Management</div>
                            </div>
                        </div>
                    </div>

                    <div class="partner-card" data-search="kementerian dalam negeri kemendagri pemerintahan">
                        <div class="partner-name">Kementerian Dalam Negeri</div>
                        <div class="partner-info">
                            <div class="info-item">
                                <div class="info-label">Nomor Telepon</div>
                                <div class="info-value">+62 21 3843 1000</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">humas@kemendagri.go.id</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">Jl. Medan Merdeka Utara No. 7, Jakarta 10110</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Bidang</div>
                                <div class="info-value">Pemerintahan Daerah & Otonomi Daerah</div>
                            </div>
                        </div>
                    </div>

                    <div class="partner-card" data-search="dinas komunikasi informatika jabar jawa barat">
                        <div class="partner-name">Dinas Kominfo Jawa Barat</div>
                        <div class="partner-info">
                            <div class="info-item">
                                <div class="info-label">Nomor Telepon</div>
                                <div class="info-value">+62 22 2534 1600</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">diskominfo@jabarprov.go.id</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Alamat</div>
                                <div class="info-value">Jl. Tamansari No. 34, Bandung 40116</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Bidang</div>
                                <div class="info-value">Komunikasi & Informatika Daerah</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="file" class="tab-content">
                <h2 class="section-title">Dokumen Kerjasama</h2>
                
                <div class="search-container">
                    <input type="text" class="search-box" placeholder="🔍 Cari dokumen kerjasama..." id="fileSearch" oninput="searchFiles()">
                </div>
                
                <div class="grid-container" id="fileContainer">
                    <div class="file-card" data-search="kontrak kerjasama teknologi sistem informasi digital">
                        <div class="file-icon">📄</div>
                        <h3>Kontrak Kerjasama Teknologi</h3>
                        <p>Perjanjian kerjasama pengembangan sistem informasi dan implementasi teknologi digital. Berlaku periode 2024-2027 dengan nilai kontrak Rp 2.5 Miliar.</p>
                        <button class="download-btn" onclick="downloadFile('kontrak-teknologi.pdf')">Download PDF</button>
                    </div>

                    <div class="file-card" data-search="mou logistik distribusi supply chain">
                        <div class="file-icon">📋</div>
                        <h3>MOU Logistik & Distribusi</h3>
                        <p>Memorandum of Understanding untuk kerjasama bidang logistik dan manajemen rantai pasok. Mencakup wilayah Jawa, Sumatra, dan Kalimantan.</p>
                        <button class="download-btn" onclick="downloadFile('mou-logistik.pdf')">Download PDF</button>
                    </div>

                    <div class="file-card" data-search="perjanjian pemasaran digital marketing brand">
                        <div class="file-icon">💼</div>
                        <h3>Perjanjian Pemasaran Digital</h3>
                        <p>Kontrak eksklusif untuk layanan digital marketing dan brand management. Termasuk strategi SEO, SEM, dan social media marketing selama 24 bulan.</p>
                        <button class="download-btn" onclick="downloadFile('kontrak-marketing.pdf')">Download PDF</button>
                    </div>

                    <div class="file-card" data-search="kerjasama fintech payment gateway pembayaran">
                        <div class="file-icon">💰</div>
                        <h3>Kerjasama Fintech Payment</h3>
                        <p>Perjanjian integrasi sistem pembayaran digital dan gateway payment. Revenue sharing model dengan komisi bersaing untuk semua transaksi.</p>
                        <button class="download-btn" onclick="downloadFile('kontrak-fintech.pdf')">Download PDF</button>
                    </div>

                    <div class="file-card" data-search="addendum kontrak 2024 perubahan">
                        <div class="file-icon">🤝</div>
                        <h3>Addendum Kontrak 2024</h3>
                        <p>Perubahan dan penyesuaian kontrak eksisting tahun 2024. Meliputi penyesuaian harga, scope kerja, dan perpanjangan periode kerjasama.</p>
                        <button class="download-btn" onclick="downloadFile('addendum-2024.pdf')">Download PDF</button>
                    </div>

                    <div class="file-card" data-search="laporan kinerja mitra q1 2024 evaluasi">
                        <div class="file-icon">📊</div>
                        <h3>Laporan Kinerja Mitra Q1 2024</h3>
                        <p>Laporan evaluasi kinerja semua mitra pada kuartal pertama 2024. Berisi analisis pencapaian target, KPI, dan rekomendasi perbaikan.</p>
                        <button class="download-btn" onclick="downloadFile('laporan-q1-2024.pdf')">Download PDF</button>
                    </div>
                </div>
            </div>

            <div id="lainnya" class="tab-content">
                <h2 class="section-title">File Lainnya</h2>
                <p>Belum ada file lainnya.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Data