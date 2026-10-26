<?php require_once __DIR__ . '/../config/database.php';
$currentPage = basename($_SERVER['PHP_SELF']);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = 'ผู้ใช้งาน';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare(
        "SELECT username FROM users WHERE id = ?"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $username = $user['username'];
    }
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare(
        "SELECT role_id FROM users WHERE id = ?"
    );

    $stmt->execute([$_SESSION['user_id']]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $checkrole = $role['role_id'];
    }
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile Navbar -->
<nav class="mobile-navbar">
    <button class="menu-btn" id="menuBtn" type="button" aria-label="เปิดเมนู" aria-controls="sidebar"
        aria-expanded="false">
        <i class="bi bi-list"></i>
    </button>

    <a href="dashboard.php" class="mobile-brand">
        <span class="brand-icon"><i class="bi bi-tools"></i></span>
        <span class="mobile-title">ระบบแจ้งซ่อมอุปกรณ์</span>
    </a>
</nav>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <header class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <span class="sidebar-logo"><i class="bi bi-tools"></i></span>
            <span class="brand-copy">
                <span class="brand-name">ระบบแจ้งซ่อม</span>
                <span class="brand-caption">REPAIR MANAGEMENT</span>
            </span>
        </a>
    </header>

    <div class="sidebar-section-label">เมนูหลัก</div>

    <nav class="sidebar-menu" aria-label="เมนูหลัก">
        <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i>
            <span>แดชบอร์ด</span>
        </a>

        <a href="formrepair.php" class="nav-link <?= $currentPage === 'formrepair.php' ? 'active' : '' ?>">
            <i class="bi bi-plus-square"></i>
            <span>แจ้งซ่อม</span>
        </a>
<?php if ($checkrole == 1): ?>
        <a href="listrepair.php" class="nav-link <?= $currentPage === 'listrepair.php' ? 'active' : '' ?>">
            <i class="bi bi-clipboard-check"></i>
            <span>รายการแจ้งซ่อม</span>
        </a>
<?php endif; ?>
        <a href="inventory.php" class="nav-link <?= $currentPage === 'inventory.php' ? 'active' : '' ?>">
            <i class="bi bi-person-lines-fill"></i>
            <span>รายการแจ้งซ่อมของฉัน</span>
        </a>

<?php if ($checkrole == 1): ?>
    <a href="equipment.php"
       class="nav-link <?= $currentPage === 'equipment.php' ? 'active' : '' ?>">
        <i class="bi bi-pc-display"></i>
        <span>รายการอุปกรณ์</span>
    </a>
<?php endif; ?>
        <a href="users.php" class="nav-link <?= $currentPage === 'users.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>ผู้ใช้งาน</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="account-note">เริ่มต้นใช้งานระบบ</div>
            <a href="login.php" class="account-btn account-btn-primary">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>เข้าสู่ระบบ</span>
            </a>
            <a href="register.php" class="account-btn account-btn-secondary">
                <i class="bi bi-person-plus"></i>
                <span>สมัครสมาชิก</span>
            </a>
        <?php else: ?>
            <div class="account-card">
                <span class="account-avatar"><i class="bi bi-person"></i></span>
                <span class="account-copy">
                    <span class="account-name">
                        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="account-status"><span class="status-dot"></span> เข้าสู่ระบบแล้ว</span>
                </span>
            </div>
            <a href="logout.php" class="account-btn account-btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>ออกจากระบบ</span>
            </a>
        <?php endif; ?>
    </div>
</aside>

<style>
    /* ===== Repair system sidebar ===== */
    :root {
        --sidebar-bg: #151a23;
        --sidebar-border: #272e3a;
        --sidebar-text: #c5ccd8;
        --sidebar-muted: #8994a6;
        --sidebar-accent: #4f8cff;
        --sidebar-width: 250px;
    }

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        width: var(--sidebar-width);
        height: 100vh;
        height: 100dvh;
        padding: 18px 14px 14px;
        color: #fff;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        overflow-x: hidden;
        overflow-y: auto;
        transition: transform .25s ease;
    }

    .sidebar-header {
        padding: 4px 7px 22px;
        border-bottom: 1px solid var(--sidebar-border);
        margin-bottom: 22px;
    }

    .sidebar-brand,
    .mobile-brand {
        display: flex;
        align-items: center;
        gap: 11px;
        color: inherit;
        text-decoration: none;
    }

    .sidebar-brand:hover,
    .mobile-brand:hover {
        color: inherit;
    }

    .sidebar-logo,
    .brand-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        color: #fff;
        background: linear-gradient(145deg, #3978f6, #2455c7);
        box-shadow: 0 5px 16px rgba(37, 99, 235, .22);
        font-size: 20px;
    }

    .brand-copy,
    .account-copy {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .brand-name {
        color: #f5f7fb;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .1px;
    }

    .brand-caption {
        margin-top: 4px;
        color: var(--sidebar-muted);
        font-size: 9px;
        letter-spacing: 1.05px;
    }

    .sidebar-section-label {
        padding: 0 11px;
        margin-bottom: 10px;
        color: #778397;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.2px;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .sidebar .nav-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 45px;
        padding: 11px 12px !important;
        margin: 0 !important;
        border-radius: 10px;
        color: var(--sidebar-text);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: color .18s ease, background .18s ease, transform .18s ease;
    }

    .sidebar .nav-link>i {
        width: 20px;
        flex: 0 0 20px;
        color: #8996aa;
        font-size: 17px;
        text-align: center;
    }

    .sidebar .nav-link:hover {
        color: #fff;
        background: #202735;
        transform: translateX(2px);
    }

    .sidebar .nav-link:hover>i {
        color: #b8d1ff;
    }

    .sidebar .nav-link.active {
        color: #fff;
        background: linear-gradient(100deg, rgba(59, 130, 246, .23), rgba(59, 130, 246, .09));
        box-shadow: inset 0 0 0 1px rgba(96, 165, 250, .16);
    }

    .sidebar .nav-link.active>i {
        color: #77aaff;
    }

    .sidebar .nav-link.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 10px;
        bottom: 10px;
        width: 3px;
        border-radius: 0 4px 4px 0;
        background: var(--sidebar-accent);
    }

    .sidebar-bottom {
        position: static;
        width: 100%;
        margin-top: auto;
        padding: 18px 2px 0;
        border-top: 1px solid var(--sidebar-border);
        margin-top: 24px;
    }

    .account-note {
        margin: 0 8px 10px;
        color: var(--sidebar-muted);
        font-size: 11px;
    }

    .account-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        min-height: 39px;
        padding: 9px 12px;
        margin-top: 7px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: background .18s ease, border-color .18s ease;
    }

    .account-btn-primary {
        color: #fff;
        background: #326fe5;
    }

    .account-btn-primary:hover {
        color: #fff;
        background: #245bc7;
    }

    .account-btn-secondary {
        color: #d4dbe6;
        border-color: #343d4c;
        background: transparent;
    }

    .account-btn-secondary:hover {
        color: #fff;
        border-color: #566277;
        background: #202735;
    }

    .account-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border: 1px solid #2c3442;
        border-radius: 11px;
        background: #1b222e;
    }

    .account-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #2b3d5b;
        color: #a9c9ff;
        font-size: 17px;
    }

    .account-name {
        color: #edf1f7;
        font-size: 12px;
        font-weight: 600;
    }

    .account-status {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
        color: #98a4b6;
        font-size: 10px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
    }

    /* ===== Mobile top bar ===== */
    .mobile-navbar {
        display: none;
    }

    .sidebar-overlay {
        display: none;
    }

    @media (max-width: 991.98px) {
        .mobile-navbar {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 1040;
            display: flex;
            align-items: center;
            gap: 12px;
            height: 62px;
            padding: 0 18px;
            color: #f8fafc;
            background: rgba(21, 26, 35, .96);
            border-bottom: 1px solid var(--sidebar-border);
            box-shadow: 0 5px 20px rgba(0, 0, 0, .12);
            backdrop-filter: blur(12px);
        }

        .menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid #343d4c;
            border-radius: 10px;
            color: #e8edf5;
            background: #202735;
            font-size: 22px;
            cursor: pointer;
            transition: background .18s ease;
        }

        .menu-btn:hover {
            background: #2b3545;
        }

        .mobile-brand {
            min-width: 0;
            gap: 9px;
        }

        .mobile-brand .brand-icon {
            flex-basis: 34px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            font-size: 17px;
        }

        .mobile-title {
            overflow: hidden;
            color: #f5f7fb;
            font-size: 14px;
            font-weight: 650;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar {
            width: min(var(--sidebar-width), 86vw);
            border-radius: 0;
            transform: translateX(-102%);
            box-shadow: none;
        }

        .sidebar.active {
            transform: translateX(0);
            box-shadow: 15px 0 40px rgba(0, 0, 0, .25);
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 1045;
            display: block;
            visibility: hidden;
            background: rgba(4, 8, 15, .62);
            opacity: 0;
            transition: opacity .25s ease, visibility .25s ease;
        }

        .sidebar-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        main {
            margin-left: 0 !important;
            padding-top: 78px !important;
        }
    }

    @media (max-width: 575.98px) {
        .mobile-navbar {
            height: 56px;
            padding: 0 12px;
            gap: 9px;
        }

        .mobile-title {
            font-size: 13px;
        }

        main {
            padding-top: 70px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .sidebar,
        .sidebar .nav-link,
        .menu-btn,
        .sidebar-overlay {
            transition: none;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuBtn = document.getElementById("menuBtn");
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");

        if (!menuBtn || !sidebar || !overlay) return;

        function openSidebar() {
            sidebar.classList.add("active");
            overlay.classList.add("active");
            menuBtn.setAttribute("aria-expanded", "true");
            document.body.style.overflow = "hidden";
        }

        function closeSidebar() {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
            menuBtn.setAttribute("aria-expanded", "false");
            document.body.style.overflow = "";
        }

        menuBtn.addEventListener("click", function () {
            if (sidebar.classList.contains("active")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener("click", closeSidebar);

        sidebar.querySelectorAll(".nav-link").forEach(function (link) {
            link.addEventListener("click", function () {
                if (window.innerWidth <= 991) closeSidebar();
            });
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") closeSidebar();
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 991) closeSidebar();
        });
    });
</script>