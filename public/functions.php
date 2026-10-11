<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// BASE_URL
define('BASE_URL', '/inventaris/public/');

// Koneksi global
$pdo = Database::getInstance()->getConnection();

// ============================================
// Sanitasi output (anti XSS)
// ============================================
function e($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// ============================================
// Flash Message
// ============================================
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ============================================
// Redirect
// ============================================
function redirect($url) {
    header("Location: $url");
    exit;
}