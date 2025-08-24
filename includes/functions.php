<?php
/**
 * Portal Jemari 5.0 PaskerID - Common Functions
 * 
 * This file contains commonly used helper functions
 * throughout the application.
 */

// Prevent direct access
if (!defined('APP_START')) {
    exit('Direct access denied.');
}

/**
 * Sanitize input data
 * 
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

/**
 * Validate email address
 * 
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token
 * @return bool
 */
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 * 
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Get current user data
 * 
 * @return array|null
 */
function get_current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if current user is admin
 * 
 * @return bool
 */
function is_admin() {
    $user = get_current_user();
    return $user && in_array($user['role'] ?? '', ['admin', 'super_admin']);
}

/**
 * Check if current user is super admin
 * 
 * @return bool
 */
function is_super_admin() {
    $user = get_current_user();
    return $user && ($user['role'] ?? '') === 'super_admin';
}

/**
 * Redirect to a specific URL
 * 
 * @param string $url
 * @param int $status_code
 */
function redirect($url, $status_code = 302) {
    header("Location: $url", true, $status_code);
    exit();
}

/**
 * Redirect back with flash message
 * 
 * @param string $type
 * @param string $message
 * @param string $fallback_url
 */
function redirect_back($type, $message, $fallback_url = 'index.php') {
    set_flash($type, $message);
    $back_url = $_SERVER['HTTP_REFERER'] ?? $fallback_url;
    redirect($back_url);
}

/**
 * Format date for display
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'd/m/Y H:i') {
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Get file size in human readable format
 * 
 * @param int $bytes
 * @param int $precision
 * @return string
 */
function format_file_size($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Generate random password
 * 
 * @param int $length
 * @return string
 */
function generate_password($length = 12) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Log message to file
 * 
 * @param string $message
 * @param string $level
 * @param string $file
 */
function log_message($message, $level = 'INFO', $file = 'app.log') {
    $log_file = LOGS_PATH . '/' . $file;
    $timestamp = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user']['id'] ?? 'guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $log_entry = "[{$timestamp}] [{$level}] [User:{$user_id}] [IP:{$ip}] {$message}" . PHP_EOL;
    
    // Create log directory if it doesn't exist
    $log_dir = dirname($log_file);
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Validate file upload
 * 
 * @param array $file $_FILES array element
 * @param array $allowed_types
 * @param int $max_size
 * @return array [success => bool, message => string]
 */
function validate_file_upload($file, $allowed_types = null, $max_size = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Check file size
    $max_size = $max_size ?? MAX_FILE_SIZE;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File terlalu besar. Maksimal ' . format_file_size($max_size)];
    }
    
    // Check file type
    $allowed_types = $allowed_types ?? ALLOWED_FILE_TYPES;
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    // Check if file is actually uploaded
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'File tidak valid'];
    }
    
    return ['success' => true, 'message' => 'File valid'];
}

/**
 * Upload file to specified directory
 * 
 * @param array $file $_FILES array element
 * @param string $upload_dir
 * @param string $new_name Optional new filename
 * @return array [success => bool, filename => string, message => string]
 */
function upload_file($file, $upload_dir, $new_name = null) {
    // Validate file first
    $validation = validate_file_upload($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate filename
    if ($new_name) {
        $filename = $new_name;
    } else {
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $file_ext;
    }
    
    $target_path = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $target_path,
            'message' => 'File berhasil diupload'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Gagal menyimpan file'
        ];
    }
}

/**
 * Delete file safely
 * 
 * @param string $file_path
 * @return bool
 */
function delete_file($file_path) {
    if (file_exists($file_path) && is_file($file_path)) {
        return unlink($file_path);
    }
    return false;
}

/**
 * Create pagination HTML
 * 
 * @param int $current_page
 * @param int $total_pages
 * @param string $base_url
 * @return string
 */
function create_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $pagination = '<div class="pagination">';
    
    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $pagination .= "<a href=\"{$base_url}&page={$prev_page}\" class=\"prev\">&laquo; Sebelumnya</a>";
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $pagination .= "<span class=\"current\">{$i}</span>";
        } else {
            $pagination .= "<a href=\"{$base_url}&page={$i}\">{$i}</a>";
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $pagination .= "<a href=\"{$base_url}&page={$next_page}\" class=\"next\">Selanjutnya &raquo;</a>";
    }
    
    $pagination .= '</div>';
    
    return $pagination;
}

/**
 * Generate slug from string
 * 
 * @param string $string
 * @return string
 */
function generate_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Truncate text
 * 
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Check if request is AJAX
 * 
 * @return bool
 */
function is_ajax_request() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 * 
 * @param array $data
 * @param int $status_code
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Debug function - only works in debug mode
 * 
 * @param mixed $data
 * @param bool $die
 */
function debug($data, $die = false) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        if ($die) {
            die();
        }
    }
}