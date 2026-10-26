
<?php

require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT
                id,
                name,
                status,
                description,
                created_at,
                updated_at
            FROM equipment
            ORDER BY id DESC";

    $stmt = $pdo->query($sql);
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'data' => $equipment
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'data' => [],
        'error' => 'ไม่สามารถโหลดข้อมูลอุปกรณ์ได้'
    ], JSON_UNESCAPED_UNICODE);
}
