<?php

require_once __DIR__ . '/config/database.php';

$id = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$status = $_POST['status'] ?? '';

$stmt = $pdo->prepare("
    UPDATE equipment
    SET
        name = ?,
        description = ?,
        status = ?,
        updated_at = NOW()
    WHERE id = ?
");

$stmt->execute([
    $name,
    $description,
    $status,
    $id
]);

echo json_encode([
    'success' => true,
    'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว'
]);
