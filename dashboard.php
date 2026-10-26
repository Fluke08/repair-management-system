<?php

require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT COUNT(*) FROM repairs');
$totalRepairs = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM repairs WHERE status = 'in_progress'");
$inProgress = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM repairs WHERE status = 'completed'");
$completed = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM repairs WHERE status = 'pending'");
$pending = (int) $stmt->fetchColumn();

$sql = "
    SELECT
        repairs.id,
        repairs.status,
        users.username,
        equipment.name AS equipment_name,
        repair_categories.name AS category_name
    FROM repairs
    LEFT JOIN users ON repairs.user_id = users.id
    LEFT JOIN equipment ON repairs.equipment_id = equipment.id
    LEFT JOIN repair_categories ON repairs.category_id = repair_categories.id
    ORDER BY repairs.id DESC
    LIMIT 5
";
$stmt = $pdo->query($sql);
$latestRepairs = $stmt->fetchAll(PDO::FETCH_ASSOC);

function repairStatusLabel(string $status): string
{
    return match ($status) {
        'pending' => 'รอดำเนินการ',
        'in_progress' => 'กำลังดำเนินการ',
        'completed' => 'เสร็จแล้ว',
        default => $status !== '' ? $status : 'ไม่ระบุสถานะ',
    };
}

function repairStatusClass(string $status): string
{
    return match ($status) {
        'pending' => 'status-pending',
        'in_progress' => 'status-progress',
        'completed' => 'status-completed',
        default => 'status-unknown',
    };
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Repair System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
   
</head>
<body>

<?php require_once __DIR__ . '/include/nav.php'; ?>

<main class="main-content">
    <header class="dashboard-header">
        <div>
            <div class="eyebrow">
                <i class="bi bi-grid-1x2-fill"></i>
                Repair management
            </div>
            <h1>Dashboard</h1>
            <p class="welcome-text">
                Welcome back,
                <strong><?= htmlspecialchars($userData['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>
    </header>

    <section class="row g-4 mb-5" aria-label="สรุปสถานะงานซ่อม">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="stat-card">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">รายการทั้งหมด</p>
                        <div class="stat-number"><?= $totalRepairs ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-clipboard-data"></i></div>
                </div>
            </article>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <article class="stat-card pending">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">รอดำเนินการ</p>
                        <div class="stat-number"><?= $pending ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </article>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <article class="stat-card progress">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">กำลังดำเนินการ</p>
                        <div class="stat-number"><?= $inProgress ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-tools"></i></div>
                </div>
            </article>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <article class="stat-card completed">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">ดำเนินการเสร็จแล้ว</p>
                        <div class="stat-number"><?= $completed ?></div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                </div>
            </article>
        </div>
    </section>

    <section class="section-card">
        <div class="section-heading">
            <div>
                <h2>รายการแจ้งซ่อมล่าสุด</h2>
                <p class="section-subtitle">รายการแจ้งซ่อม 5 รายการล่าสุด</p>
            </div>
            <span class="record-count">
                <i class="bi bi-clock-history"></i>
                5 รายการล่าสุด
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-dashboard align-middle">
                <thead>
                    <tr>
                        <th scope="col">รหัส</th>
                        <th scope="col">อุปกรณ์</th>
                        <th scope="col">หมวดหมู่</th>
                        <th scope="col">ผู้แจ้ง</th>
                        <th scope="col">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($latestRepairs)): ?>
                        <?php foreach ($latestRepairs as $row): ?>
                            <?php $status = (string) ($row['status'] ?? ''); ?>
                            <tr>
                                <td><span class="repair-id">#<?= (int) $row['id'] ?></span></td>
                                <td><span class="equipment-name"><?= htmlspecialchars($row['equipment_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="table-muted"><?= htmlspecialchars($row['category_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><span class="table-muted"><?= htmlspecialchars($row['username'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td>
                                    <span class="status <?= htmlspecialchars(repairStatusClass($status), ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars(repairStatusLabel($status), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                ยังไม่มีรายการแจ้งซ่อม
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
