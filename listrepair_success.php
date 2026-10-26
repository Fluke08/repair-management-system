
<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'รหัสรายการไม่ถูกต้อง'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, status FROM repairs WHERE id = :id"
    );
    $stmt->execute(['id' => $id]);

    $repair = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$repair) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบคำขอแจ้งซ่อม'
        ]);
        exit;
    }


// 1. ดึง equipment_id จากรายการแจ้งซ่อม
$stmt = $pdo->prepare(
    "SELECT equipment_id FROM repairs WHERE id = :id"
);
$stmt->execute(['id' => $id]);

$idequip = $stmt->fetchColumn();

// 2. อัปเดตสถานะอุปกรณ์
if ($idequip !== false && $idequip !== null) {
    $stmt = $pdo->prepare(
        "UPDATE equipment SET status = :status WHERE id = :idequip"
    );

    $stmt->execute([
        'status' => 'ready',
        'idequip' => $idequip
    ]);
}


    $update = $pdo->prepare(
        "UPDATE repairs SET status = :status WHERE id = :id"
    );

    $update->execute([
        'status' => 'completed',
        'id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'ยืนยันการซ่อมอุปกรณ์เรียบร้อยแล้ว'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
    ]);
}
