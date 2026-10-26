
<?php
require 'config/database.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT
            repairs.*,
            users.username
        FROM repairs
        LEFT JOIN users ON repairs.user_id = users.id
        ORDER BY repairs.created_at DESC
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'data' => [],
        'error' => 'ไม่สามารถโหลดรายการแจ้งซ่อมได้'
    ], JSON_UNESCAPED_UNICODE);
}

