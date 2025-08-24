<?php
/**
 * Portal Jemari 5.0 PaskerID - Main Configuration File
 * 
 * This file contains main application configuration settings
 * including database, security, and application settings.
 */

// Prevent direct access
if (!defined('APP_START')) {
    define('APP_START', true);
}

// =========================
// APPLICATION SETTINGS
// =========================
define('APP_NAME', 'Portal Jemari 5.0 PaskerID');
define('APP_VERSION', '5.0.0');
define('APP_AUTHOR', 'Pusat Pasar Kerja');
define('APP_DESCRIPTION', 'Sistem Informasi Substansi Jejaring Kemitraan Pusat Pasar Kerja');

// Base URL configuration
define('BASE_URL', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/assets/uploads');

// Directory paths
define('ROOT_PATH', __DIR__);
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

// =========================
// DATABASE CONFIGURATION
// =========================
define('DB_HOST', getenv('DB_HOST') ?: '194.233.86.160');
define('DB_NAME', getenv('DB_NAME') ?: 'latsar_db');
define('DB_USER', getenv('DB_USER') ?: 'pasker');
define('DB_PASS', getenv('DB_PASS') ?: 'Getjoblivebetter!');
define('DB_CHARSET', 'utf8mb4');

// =========================
// SECURITY SETTINGS
// =========================
define('SESSION_NAME', 'JEMARI_5_SESS');
define('SESSION_LIFETIME', 3600 * 2); // 2 hours
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 300); // 5 minutes

// Password hashing options
define('PASSWORD_OPTIONS', [
    'cost' => 12
]);

// =========================
// FILE UPLOAD SETTINGS
// =========================
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 
    'jpg', 'jpeg', 'png', 'gif',
    'txt', 'zip', 'rar'
]);

// Upload directories
define('UPLOAD_DIRS', [
    'documents' => UPLOADS_PATH . '/documents',
    'images' => UPLOADS_PATH . '/images',
    'temp' => UPLOADS_PATH . '/temp'
]);

// =========================
// EMAIL SETTINGS
// =========================
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'localhost');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@jemari5.pasker.id');
define('MAIL_FROM_NAME', 'Portal Jemari 5.0 PaskerID');

// =========================
// LOGGING SETTINGS
// =========================
define('LOG_LEVEL', getenv('LOG_LEVEL') ?: 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('LOG_MAX_FILES', 5);

// =========================
// PAGINATION & LIMITS
// =========================
define('ITEMS_PER_PAGE', 20);
define('MAX_SEARCH_RESULTS', 100);

// =========================
// TIMEZONE & LOCALE
// =========================
define('DEFAULT_TIMEZONE', 'Asia/Jakarta');
define('DEFAULT_LOCALE', 'id_ID');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// =========================
// ERROR REPORTING
// =========================
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('DEBUG_MODE', true);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    define('DEBUG_MODE', false);
}

// =========================
// SESSION CONFIGURATION
// =========================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.name', SESSION_NAME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    
    session_start();
}

// =========================
// AUTO-CREATE DIRECTORIES
// =========================
$required_dirs = [
    LOGS_PATH,
    UPLOADS_PATH,
    UPLOADS_PATH . '/documents',
    UPLOADS_PATH . '/images',
    UPLOADS_PATH . '/temp',
    ROOT_PATH . '/storage/cache',
    ROOT_PATH . '/storage/sessions'
];

foreach ($required_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// =========================
// USER ROLES & PERMISSIONS
// =========================
define('USER_ROLES', [
    'user' => 'User',
    'admin' => 'Administrator',
    'super_admin' => 'Super Administrator'
]);

define('PERMISSIONS', [
    'view_contacts' => 'View Contacts',
    'manage_contacts' => 'Manage Contacts',
    'view_stages' => 'View Stages',
    'manage_stages' => 'Manage Stages',
    'view_documents' => 'View Documents',
    'manage_documents' => 'Manage Documents',
    'view_schedules' => 'View Schedules',
    'manage_schedules' => 'Manage Schedules',
    'manage_users' => 'Manage Users',
    'system_settings' => 'System Settings'
]);

// =========================
// STAGE TYPES
// =========================
define('STAGE_TYPES', [
    'kementerian' => 'Kementerian/Lembaga',
    'daerah' => 'Pemerintah Daerah',
    'mitra' => 'Mitra Pembangunan',
    'swasta' => 'Swasta/Perusahaan'
]);

// =========================
// COOPERATION STAGES
// =========================
define('COOPERATION_STAGES', [
    'initial_contact' => 'Kontak Awal',
    'proposal_review' => 'Review Proposal',
    'negotiation' => 'Negosiasi',
    'agreement_draft' => 'Draft Kesepakatan',
    'legal_review' => 'Review Legal',
    'signing' => 'Penandatanganan',
    'implementation' => 'Implementasi',
    'monitoring' => 'Monitoring',
    'evaluation' => 'Evaluasi',
    'renewal' => 'Perpanjangan'
]);

// =========================
// STATUS DEFINITIONS
// =========================
define('DOCUMENT_STATUS', [
    'draft' => 'Draft',
    'review' => 'Under Review',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'archived' => 'Archived'
]);

define('COOPERATION_STATUS', [
    'active' => 'Aktif',
    'inactive' => 'Tidak Aktif',
    'pending' => 'Pending',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
]);

// =========================
// HELPER FUNCTIONS
// =========================

/**
 * Get configuration value
 */
function config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if application is in debug mode
 */
function is_debug() {
    return config('DEBUG_MODE', false);
}

/**
 * Get base URL
 */
function base_url($path = '') {
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Get assets URL
 */
function assets_url($path = '') {
    return ASSETS_URL . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Check if user has permission
 */
function has_permission($user, $permission) {
    if (!isset($user['role'])) {
        return false;
    }
    
    // Super admin has all permissions
    if ($user['role'] === 'super_admin') {
        return true;
    }
    
    // Admin has most permissions except system settings
    if ($user['role'] === 'admin') {
        return $permission !== 'system_settings';
    }
    
    // Regular users have limited permissions
    $user_permissions = [
        'view_contacts', 'view_stages', 'view_documents', 'view_schedules'
    ];
    
    return in_array($permission, $user_permissions);
}