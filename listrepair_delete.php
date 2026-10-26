
<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method ไม่ถูกต้อง'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบ ID หรือ ID ไม่ถูกต้อง'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    // ตรวจสอบรายการและสถานะก่อนลบ
    $sql = "SELECT status FROM repairs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $repair = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$repair) {
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบรายการที่ต้องการลบ'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ถ้ากำลังดำเนินการ ห้ามลบ
    if ($repair['status'] === 'in_progress') {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'อยู่ระหว่างดำเนินการ ไม่สามารถลบได้'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ถ้าซ่อมเสร็จแล้ว ห้ามลบ
    if ($repair['status'] === 'completed') {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'ซ่อมอุปกรณ์เรียบร้อย ไม่สามารถลบได้'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // สถานะอื่นสามารถลบได้
    $sql = "DELETE FROM repairs WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'ลบรายการแจ้งซ่อมสำเร็จ'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบรายการที่ต้องการลบ'
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'
    ], JSON_UNESCAPED_UNICODE);
}

exit;
