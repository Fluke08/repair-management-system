<?php

require_once __DIR__ . '/config/database.php';

$id = $_POST['id'] ?? 0;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบ ID'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM equipment
    WHERE id = ?
");

$stmt->execute([$id]);

echo json_encode([
    'success' => true,
    'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
]);