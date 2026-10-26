
<?php
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "
        SELECT
            users.id,
            users.username,
            users.email,
            role.role_name
        FROM users
        LEFT JOIN role
            ON users.role_id = role.role_id
        ORDER BY users.id DESC
    ";

    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'data' => $users
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'data' => [],
        'error' => 'ไม่สามารถโหลดข้อมูลผู้ใช้งานได้'
    ], JSON_UNESCAPED_UNICODE);
}
