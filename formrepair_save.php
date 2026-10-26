<?php

session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: formrepair.php");
    exit;
}

$equipment_id = $_POST['equipment_id'] ?? '';
$location     = trim($_POST['location'] ?? '');
$description  = trim($_POST['description'] ?? '');
$priority     = $_POST['priority'] ?? '';


if (
    empty($equipment_id) ||
    empty($location) ||
    empty($description) ||
    empty($priority)
) {
    die("กรุณากรอกข้อมูลให้ครบ");
}

try {

    $pdo->beginTransaction();

    // 1. ตรวจสอบอุปกรณ์
    $stmt = $pdo->prepare("
        SELECT id   
        FROM equipment
        WHERE id = ?
        AND status = 'ready'
        FOR UPDATE
    ");

    $stmt->execute([$equipment_id]);

    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        throw new Exception("อุปกรณ์นี้ไม่สามารถแจ้งซ่อมได้");
    }

  $user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    INSERT INTO repairs
    (
        user_id,
        equipment_id,
        location,
        description,
        priority,
        status,
        created_at
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        'pending',
        NOW()
    )
");

$stmt->execute([
    $user_id,
    $equipment_id,
    $location,
    $description,
    $priority
]);
    // 3. เปลี่ยนสถานะอุปกรณ์
    $stmt = $pdo->prepare("
        UPDATE equipment
        SET status = 'in_progress'
        WHERE id = ?
    ");

    $stmt->execute([$equipment_id]);

    // 4. ยืนยัน Transaction
    $pdo->commit();

    header("Location: formrepair.php?success=1");
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}