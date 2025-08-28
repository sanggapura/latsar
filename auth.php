<?php
// File: auth.php (Main File)
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
        }

        /* Animated background elements */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            animation: float 15s infinite ease-in-out;
        }

        .shape-1 {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            border-radius: 50%;
            top: 60%;
            right: 15%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            border-radius: 30%;
            top: 15%;
            right: 25%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #43e97b, #38f9d7);
            border-radius: 50%;
            top: 75%;
            left: 20%;
            animation-delay: -7s;
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) translateX(0px) rotate(0deg);
                opacity: 0.1;
            }
            25% { 
                transform: translateY(-20px) translateX(10px) rotate(90deg);
                opacity: 0.15;
            }
            50% { 
                transform: translateY(-40px) translateX(0px) rotate(180deg);
                opacity: 0.1;
            }
            75% { 
                transform: translateY(-20px) translateX(-10px) rotate(270deg);
                opacity: 0.08;
            }
        }

        /* Particle system */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            animation: particleFloat 8s infinite linear;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-20px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Main content area - adjusted for header */
        main {
            position: relative;
            z-index: 10;
            min-height: calc(100vh - 80px); /* Adjust based on header height */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            margin-top: 20px; /* Space for header */
        }

        .welcome-container {
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        /* Welcome content styling */
        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 30px;
            display: inline-block;
            animation: iconPulse 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
        }

        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleSlideUp 1.2s ease-out;
            position: relative;
        }

        .welcome-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
            animation: lineExpand 1.5s ease-out 0.5s both;
        }

        @keyframes titleSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes lineExpand {
            from {
                width: 0;
            }
            to {
                width: 100px;
            }
        }

        .welcome-message {
            font-size: 1.4rem;
            color: #5a6c7d;
            line-height: 1.8;
            margin-top: 40px;
            opacity: 0;
            animation: messageSlideUp 1.2s ease-out 0.8s both;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @keyframes messageSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-top: 20px;
            opacity: 0;
            animation: subtitleFade 1s ease-out 1.5s both;
            font-style: italic;
        }

        @keyframes subtitleFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Decorative elements */
        .decoration-dots {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #667eea;
            border-radius: 50%;
            opacity: 0.3;
            animation: dotFloat 4s ease-in-out infinite;
        }

        .dots-1 {
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .dots-2 {
            top: 30%;
            right: 10%;
            animation-delay: -1s;
        }

        .dots-3 {
            bottom: 25%;
            left: 20%;
            animation-delay: -2s;
        }

        .dots-4 {
            bottom: 35%;
            right: 18%;
            animation-delay: -3s;
        }

        @keyframes dotFloat {
            0%, 100% {
                transform: translateY(0px);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-15px);
                opacity: 0.7;
            }
        }

        /* Subtle glow effect */
        .glow-effect {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 5;
            animation: glowPulse 6s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.5;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.8;
            }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2.5rem;
            }
            
            .welcome-message {
                font-size: 1.2rem;
                padding: 0 10px;
            }
            
            .welcome-subtitle {
                font-size: 1rem;
                padding: 0 10px;
            }
            
            .welcome-icon {
                font-size: 3rem;
            }
            
            main {
                padding: 40px 20px;
                min-height: calc(100vh - 60px);
            }
            
            .glow-effect {
                width: 300px;
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .welcome-message {
                font-size: 1.1rem;
            }
            
            .welcome-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/views/header.php'; ?>

    <!-- Background animations -->
    <div class="bg-animation">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
    </div>

    <!-- Particle system -->
    <div class="particles" id="particles"></div>

    <!-- Decorative dots -->
    <div class="decoration-dots dots-1"></div>
    <div class="decoration-dots dots-2"></div>
    <div class="decoration-dots dots-3"></div>
    <div class="decoration-dots dots-4"></div>

    <!-- Glow effect -->
    <div class="glow-effect"></div>

    <main>
        <div class="welcome-container">
            <div class="welcome-icon">✨</div>
            <h1 class="welcome-title">Selamat Datang!</h1>
            <p class="welcome-message">
                Di Portal Jejaring Kerjasama PaskerID<br>
                
            </p>
            <p class="welcome-subtitle">
                
            </p>
        </div>
    </main>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random horizontal position
                particle.style.left = Math.random() * 100 + '%';
                
                // Random animation delay
                particle.style.animationDelay = Math.random() * 8 + 's';
                
                // Random size variation
                const size = Math.random() * 2 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                
                // Random color variation
                const colors = [
                    'rgba(102, 126, 234, 0.6)',
                    'rgba(118, 75, 162, 0.6)',
                    'rgba(240, 147, 251, 0.6)',
                    'rgba(79, 172, 254, 0.6)'
                ];
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize when page loads
        window.addEventListener('load', function() {
            createParticles();
        });

        // Add smooth entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.8s ease-in-out';
            
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Add subtle mouse movement parallax
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.floating-shape');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            shapes.forEach((shape, index) => {
                const speed = (index + 1) * 0.5;
                const x = (mouseX - 0.5) * speed;
                const y = (mouseY - 0.5) * speed;
                
                shape.style.transform += ` translate(${x}px, ${y}px)`;
            });
        });
    </script>
</body>
</html>

<?php
// ==========================================
// File: views/header.php (Sample Header)
// ==========================================
?>
<!--
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Header Styles */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo:hover {
            text-decoration: none;
            color: white;
            opacity: 0.8;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
        }

        .nav-menu li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-menu li a:hover {
            background: rgba(255, 255, 255, 0.2);
            text-decoration: none;
            color: white;
        }

        .nav-menu li a.active {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(102, 126, 234, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                gap: 0;
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-menu li {
                margin: 0.5rem 0;
            }

            .nav-menu li a {
                display: block;
                padding: 1rem;
                text-align: center;
            }

            .menu-toggle {
                display: block;
            }
        }

        /* Add body padding to account for fixed header */
        body {
            padding-top: 80px;
        }
    </style>
</head>
<body>
-->

<header>
    <div class="header-container">
        
    </div>
</header>

<script>
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.toggle('active');
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const navMenu = document.getElementById('navMenu');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
        navMenu.classList.remove('active');
    }
});
</script>

<?php
// ==========================================
// File: assets/style.css (Additional Styles)
// ==========================================
?>
<!--
/* You can add additional styles here */
/* This file should be created separately as assets/style.css */

/* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
}

/* Utility Classes */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.text-center {
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 0.8rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    text-decoration: none;
    color: white;
}

/* Form Styles */
.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Card Styles */
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin: 1rem 0;
}

.card-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.card-title {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
}
-->