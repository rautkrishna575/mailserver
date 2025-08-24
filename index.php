<?php
// /index.php
session_start();

require_once 'config.php';
require_once 'src/Database.php';


$pdo = Database::getInstance()->getConnection();

$loggedIn = false;
$currentUser = null;

if (isset($_SESSION['user_id'])) {

    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user) {
        $loggedIn = true;
        $currentUser = $user;
    } else {
        session_destroy();
    }
}

if ($loggedIn) {
    include 'view/app_layout.php';
} else {
    include 'view/auth_layout.php';
}
