<?php
require_once 'config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('รหัสรายการไม่ถูกต้อง');
}

$stmt = $pdo->prepare(
    "SELECT * FROM repairs WHERE id = :id"
);
$stmt->execute(['id' => $id]);

$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    http_response_code(404);
    exit('ไม่พบคำขอแจ้งซ่อม');
}

function e($value) {
    return htmlspecialchars((string) ($value ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label ">รหัสคำขอ</label>
        <p class="fw-semibold"><?= e($repair['id']) ?></p>
    </div>

    <div class="col-md-6">
        <label class="form-label">สถานะ</label>
        <p class="fw-semibold"><?= e($repair['status']) ?></p>
    </div>

    <div class="col-6">
        <label class="form-label">หัวข้อแจ้งซ่อม</label>
        <p><?= e($repair['title']) ?></p>
    </div>

    <div class="col-6">
        <label class="form-label">รายละเอียดปัญหา</label>
        <p><?= nl2br(e($repair['description'])) ?></p>
    </div>
</div>