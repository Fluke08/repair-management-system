<?php

require_once __DIR__ . '/config/database.php';;

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT *
    FROM equipment
    WHERE id = ?
");

$stmt->execute([$id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {

    echo json_encode([
        'success' => true,
        'data' => $row
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบข้อมูล'
    ]);

}