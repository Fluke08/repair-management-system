<?php
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';

$stmt = $pdo->prepare("
    SELECT id, name
    FROM equipment
    WHERE status = 'ready'
    ORDER BY name ASC
");
$stmt->execute();
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมอุปกรณ์</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --page-bg: #101318;
            --panel-bg: #151a23;
            --panel-inner: #1b222e;
            --panel-border: #2c3442;
            --text-main: #f5f7fb;
            --text-muted: #98a4b6;
            --accent: #4f8cff;
        }

        * { box-sizing: border-box; }

        html, body {
            width: 100%;
            min-height: 100%;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            color: var(--text-main);
            background: var(--page-bg);
        }

        main {
            min-height: 100vh;
            margin-left: 250px;
            padding: 32px;
            transition: padding .2s ease;
        }

        .page-wrap {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
        }

        .page-heading {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 24px;
        }

        .page-heading-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 48px;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(96, 165, 250, .2);
            border-radius: 14px;
            color: #8dbaff;
            background: rgba(59, 130, 246, .13);
            font-size: 22px;
        }

        .page-title {
            margin: 1px 0 6px;
            color: var(--text-main);
            font-size: 25px;
            font-weight: 700;
            letter-spacing: -.35px;
        }

        .page-description {
            margin: 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .repair-card {
            overflow: hidden;
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            background: var(--panel-bg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, .12);
        }

        .card-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 21px 26px;
            border-bottom: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, .015);
        }

        .card-heading i {
            color: #83b2ff;
            font-size: 18px;
        }

        .card-heading h2 {
            margin: 0;
            color: var(--text-main);
            font-size: 15px;
            font-weight: 650;
        }

        .card-heading p {
            margin: 4px 0 0;
            color: var(--text-muted);
            font-size: 12px;
        }

        .repair-form {
            padding: 26px;
        }

        .form-section {
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--panel-border);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 21px;
            color: #e9edf5;
            font-size: 14px;
            font-weight: 650;
        }

        .section-title i {
            color: #80adfa;
            font-size: 17px;
        }

        .form-label {
            margin-bottom: 8px;
            color: #dbe1eb;
            font-size: 12px;
            font-weight: 600;
        }

        .required { color: #fb7185; }

        .form-control,
        .form-select {
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #394252;
            border-radius: 9px;
            color: var(--text-main);
            background-color: #11161e;
            font-size: 13px;
            box-shadow: none;
        }

        .form-control::placeholder { color: #727e90; }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            color: var(--text-main);
            background-color: #11161e;
            box-shadow: 0 0 0 3px rgba(79, 140, 255, .13);
        }

        .form-select option {
            color: var(--text-main);
            background: var(--panel-bg);
        }

        textarea.form-control {
            min-height: 135px;
            resize: vertical;
        }

        .field-hint {
            margin-top: 7px;
            color: #7f8b9d;
            font-size: 11px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .form-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 18px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-submit {
            border: 1px solid #4f8cff;
            color: #fff;
            background: #3476ed;
        }

        .btn-submit:hover,
        .btn-submit:focus {
            border-color: #6aa0ff;
            color: #fff;
            background: #2864d6;
        }

        .btn-cancel {
            border: 1px solid #394252;
            color: #d0d7e2;
            background: #202735;
        }

        .btn-cancel:hover {
            border-color: #566277;
            color: #fff;
            background: #293242;
        }

        .toast-container { z-index: 2000; }

        .success-toast {
            min-width: 300px;
            border: 1px solid #2c5946;
            border-radius: 13px;
            color: var(--text-main);
            background: #17251f;
        }

        .toast-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            color: #4ade80;
            background: rgba(34, 197, 94, .13);
            font-size: 21px;
        }

        @media (max-width: 991.98px) {
            main {
                margin-left: 0 !important;
                padding: 86px 24px 28px !important;
            }
        }

        @media (max-width: 767.98px) {
            main { padding: 78px 14px 22px !important; }
            .page-heading { gap: 11px; margin-bottom: 18px; }
            .page-heading-icon { flex-basis: 42px; width: 42px; height: 42px; border-radius: 12px; font-size: 19px; }
            .page-title { font-size: 21px; }
            .page-description { font-size: 12px; }
            .card-heading { padding: 18px; }
            .repair-form { padding: 19px; }
            .form-section { padding-bottom: 20px; margin-bottom: 20px; }
            .form-actions { flex-direction: column-reverse; }
            .form-actions .btn { width: 100%; }
        }

        @media (max-width: 575.98px) {
            main { padding: 70px 9px 16px !important; }
            .page-title { font-size: 19px; }
            .repair-card { border-radius: 12px; }
            .repair-form { padding: 15px; }
            .card-heading { padding: 16px 15px; }
            .toast-container { padding: 12px !important; }
            .success-toast { min-width: 0; width: calc(100vw - 24px); }
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/include/nav.php'; ?>

    <main>
        <div class="page-wrap">
            <header class="page-heading">
                <div class="page-heading-icon">
                    <i class="bi bi-wrench-adjustable-circle"></i>
                </div>
                <div>
                    <h1 class="page-title">แจ้งซ่อมอุปกรณ์</h1>
                    <p class="page-description">กรอกข้อมูลอุปกรณ์และรายละเอียดปัญหาที่พบ เพื่อส่งคำขอซ่อม</p>
                </div>
            </header>

            <section class="repair-card">
                <div class="card-heading">
                    <i class="bi bi-clipboard2-plus"></i>
                    <div>
                        <h2>ข้อมูลการแจ้งซ่อม</h2>
                        <p>กรุณากรอกข้อมูลที่มีเครื่องหมาย <span class="required">*</span> ให้ครบถ้วน</p>
                    </div>
                </div>

                <form class="repair-form" action="formrepair_save.php" method="POST">
                    <div class="form-section">
                        <div class="section-title">
                            <i class="bi bi-info-circle"></i>
                            รายละเอียดปัญหา
                        </div>

                        <div class="mb-4">
                            <label for="equipment_id" class="form-label">
                                ชื่ออุปกรณ์ <span class="required">*</span>
                            </label>
                            <select name="equipment_id" id="equipment_id" class="form-select" required>
                                <option value="">-- เลือกอุปกรณ์ --</option>
                                <?php foreach ($equipments as $equipment): ?>
                                    <option value="<?= (int) $equipment['id'] ?>">
                                        <?= htmlspecialchars($equipment['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="location" class="form-label">
                                สถานที่ / ตำแหน่ง <span class="required">*</span>
                            </label>
                            <input type="text" name="location" id="location" class="form-control"
                                   placeholder="เช่น ห้อง IT ชั้น 2" maxlength="255" required>
                            <div class="field-hint">ระบุห้อง ชั้น หรือบริเวณที่พบปัญหา</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                รายละเอียดปัญหา <span class="required">*</span>
                            </label>
                            <textarea name="description" id="description" class="form-control"
                                      placeholder="อธิบายอาการเสียหรือปัญหาที่พบให้ชัดเจน" required></textarea>
                        </div>

                        <div class="mb-0">
                            <label for="priority" class="form-label">
                                ความเร่งด่วน <span class="required">*</span>
                            </label>
                            <select name="priority" id="priority" class="form-select" required>
                                <option value="">-- เลือกระดับความเร่งด่วน --</option>
                                <option value="low">ต่ำ</option>
                                <option value="medium">ปานกลาง</option>
                                <option value="high">สูง</option>
                                <option value="urgent">เร่งด่วน</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="dashboard.php" class="btn btn-cancel">
                            <i class="bi bi-x-lg"></i> ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-send"></i> ส่งแจ้งซ่อม
                        </button>
                    </div>
                </form>
            </section>

            <?php require __DIR__ . '/include/footer.php'; ?>
        </div>
    </main>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast success-toast shadow-lg" role="status"
             aria-live="polite" aria-atomic="true">
            <div class="toast-body p-3">
                <div class="d-flex align-items-center">
                    <div class="toast-icon me-3">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">บันทึกสำเร็จ</div>
                        <div class="small mt-1" style="color:#9eb8aa;">แจ้งซ่อมเรียบร้อยแล้ว</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-2"
                            data-bs-dismiss="toast" aria-label="ปิด"></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get("success") === "1") {
            const toastElement = document.getElementById("successToast");

            if (toastElement && window.bootstrap) {
                const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                toast.show();
            }

            // Keep other query parameters intact while removing success=1.
            urlParams.delete("success");
            const queryString = urlParams.toString();
            const cleanUrl = window.location.pathname + (queryString ? "?" + queryString : "") + window.location.hash;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    });
    </script>
</body>
</html>
