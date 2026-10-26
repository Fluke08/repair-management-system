
<?php

session_start();
require 'config/database.php';
header('Content-Type: application/json; charset=utf-8');

$userId = $_SESSION['user_id'];
$sql = "
    SELECT repairs.*, users.username
    FROM repairs
    LEFT JOIN users ON repairs.user_id = users.id
    WHERE repairs.user_id = ?
    ORDER BY repairs.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'data' => $data
], JSON_UNESCAPED_UNICODE);
