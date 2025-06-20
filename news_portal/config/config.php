<?php
// config/config.php - NAPRAWIONA WERSJA
session_start();

define('BASE_URL', 'http://localhost/news_portal/');
define('UPLOAD_PATH', 'uploads/');
define('EXCERPT_LENGTH', 200);

// Funkcje pomocnicze
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isAuthor() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'author';
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function createExcerpt($text, $length = EXCERPT_LENGTH) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// NAPRAWIONY Autoloader dla klas
spl_autoload_register(function ($class_name) {
    // Tablica ścieżek do przeszukania
    $paths = [
        __DIR__ . '/../models/' . $class_name . '.php',
        __DIR__ . '/../classes/' . $class_name . '.php',
        __DIR__ . '/../controllers/' . $class_name . '.php',
        // Dla plików w admin/
        __DIR__ . '/../../models/' . $class_name . '.php',
        __DIR__ . '/../../classes/' . $class_name . '.php',
        __DIR__ . '/../../controllers/' . $class_name . '.php',
        // Relatywne ścieżki
        'models/' . $class_name . '.php',
        '../models/' . $class_name . '.php',
        'classes/' . $class_name . '.php',
        '../classes/' . $class_name . '.php'
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Jeśli nie znaleziono, pokaż błąd debug
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        echo "❌ Nie znaleziono klasy: $class_name<br>";
        echo "Sprawdzono ścieżki:<br>";
        foreach ($paths as $path) {
            echo "- $path " . (file_exists($path) ? "✅" : "❌") . "<br>";
        }
    }
});

// Włącz tryb debug jeśli potrzebny
if (isset($_GET['debug'])) {
    define('DEBUG_MODE', true);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
?>