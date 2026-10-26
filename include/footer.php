
<footer class="app-footer py-3 my-4">
    <div class="footer-divider mb-3"></div>

    <p class="text-center footer-text mb-0">
        <i class="bi bi-tools me-2"></i>
        &copy; <?= date('Y') ?> ระบบแจ้งซ่อมอุปกรณ์
        <span class="footer-subtitle">| Repair Management System</span>
    </p>
</footer>

<style>
    
.app-footer {
    padding: 16px 20px;
}

.footer-divider {
    width: 100%;
    height: 1px;
    background: #383330;
}

.footer-text {
    color: #9ca3af;
    font-size: 13px;
}

.footer-text i {
    color: #60a5fa;
}

.footer-subtitle {
    color: #6b7280;
    font-size: 12px;
}

@media (max-width: 576px) {
    .footer-subtitle {
        display: block;
        margin-top: 5px;
    }
}

</style>