
<?php

require_once __DIR__ . '/../config/database.php';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare('SELECT role_id FROM users WHERE id = ?');
$stmt->execute([$userId]);

$roleId = $stmt->fetchColumn();

if ((int) $roleId !== 1) {
    header('Location: dashboard.php');
    exit;
}
