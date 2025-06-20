<?php
// logout.php
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Wyloguj użytkownika
$user->logout();

// Przekieruj na stronę logowania z informacją
redirect('login.php?logout=1');
?>