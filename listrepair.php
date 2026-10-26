
<?php
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/include/authadmin.php';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการแจ้งซ่อม</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

     <link rel="stylesheet" href="assets/css/listrepair.css">     

</head>

<body>

    <!-- Sidebar -->
    <?php require_once __DIR__ . '/include/nav.php'; ?>

    <!-- Main content -->
    <main>
        <div class="container-fluid p-0">
            <div class="table-card">

                <!-- Header -->
                <div class="table-header">
                    <div>
                            <h3 class="page-title mb-1">
                            รายการแจ้งซ่อมทั้งหมด 
                        </h3>

                        <div class="page-description">
                            รายการแจ้งซ่อมทั้งหมดในระบบ
                        </div>
                    </div>

                  
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="repairTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ผู้แจ้ง</th>
                                <th class="text-center">ความต้องการ</th>
                                <th>รายละเอียด</th>
                                <th>สถานที่</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <?php require_once __DIR__ . '/include/footer.php'; ?>
        </div>
    </main>

    <!-- Repair detail modal -->
    <div class="modal fade" id="repairDetailModal" tabindex="-1"
         aria-labelledby="repairDetailModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="repairDetailModalLabel">
                        รายละเอียดคำขอแจ้งซ่อม
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>
                </div>

                <div class="modal-body" id="repairDetailContent">
                    กำลังโหลดข้อมูล...
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        ปิด
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page JavaScript -->
    <script>
        $(function () {
            // ป้องกันข้อความจากฐานข้อมูลแทรก HTML
            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            // รีเฟรช DataTable โดยคงหน้าปัจจุบัน
                    function reloadRepairTable() {
                        $('#repairTable').DataTable().ajax.reload(null, false);
                    }

                    // แสดง SweetAlert ข้อผิดพลาด
                    function showError(title, message) {
                        Swal.fire({
                            icon: 'error',
                            title: title,
                            text: message,
                            background: '#1e1e2d',
                            color: '#f9fafb',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#4f8cff'
                        });
                    }

                    // DataTable
                    const table = $('#repairTable').DataTable({
                        processing: true,

                        ajax: {
                            url: 'listrepair_datatable.php',
                            type: 'GET',
                            dataSrc: 'data'
                        },

                        columns: [
                            {
                                data: 'id',
                                render: function (data, type) {
                                    return type === 'display'
                                        ? escapeHtml(data)
                                        : data;
                                }
                            },
                            {
                                data: 'username',
                                render: function (data, type) {
                                    if (type !== 'display') return data ?? '';

                                    return '<strong>' + escapeHtml(data) + '</strong>';
                                }
                            },
                            {
                                data: 'priority',
                                className: 'text-center',
                                render: function (data, type) {
                                    if (type !== 'display') return data ?? '';

                                    return escapeHtml(data);
                                }
                            },
                            {
                                data: 'description',
                                render: function (data, type) {
                                    const description = data ?? '';

                                    if (type !== 'display') return description;

                                    const shortText = description.length > 50
                                        ? description.substring(0, 50) + '...'
                                        : description;

                                    return '<span title="' + escapeHtml(description) + '">' +
                                        escapeHtml(shortText) +
                                        '</span>';
                                }
                            },
                            {
                                data: 'location',
                                render: function (data, type) {
                                    return type === 'display'
                                        ? escapeHtml(data)
                                        : (data ?? '');
                                }
                            },
                            {
                                data: 'status',
                                render: function (status, type) {
                                    if (type !== 'display') return status ?? '';

                                    const statuses = {
                                        pending:
                                            '<span class="badge bg-warning text-dark status-badge">รอดำเนินการ</span>',
                                        in_progress:
                                            '<span class="badge bg-primary status-badge">กำลังซ่อม</span>',
                                        completed:
                                            '<span class="badge bg-success status-badge">ซ่อมเสร็จแล้ว</span>',
                                        cancelled:
                                            '<span class="badge bg-danger status-badge">ยกเลิก</span>'
                                    };

                                    return statuses[status] || escapeHtml(status);
                                }
                            },
                            {
                                data: 'created_at',
                                render: function (data, type) {
                                    if (!data) return '';

                                    if (type !== 'display') return data;

                                    const date = new Date(data.replace(' ', 'T'));

                                    if (isNaN(date.getTime())) {
                                        return escapeHtml(data);
                                    }

                                    const pad = number => String(number).padStart(2, '0');

                                    return pad(date.getDate()) + '/' +
                                        pad(date.getMonth() + 1) + '/' +
                                        date.getFullYear() + ' ' +
                                        pad(date.getHours()) + ':' +
                                        pad(date.getMinutes());
                                }
                            },
                            {
                                data: null,
                                orderable: false,
                                searchable: false,

                                render: function (data, type, row) {
                                    if (type !== 'display') return '';

                                    const id = Number(row.id);

                                    if (!Number.isSafeInteger(id) || id < 1) {
                                        return '';
                                    }

                                    let actionButton = '';

                                    if (row.status === 'in_progress') {
                                        actionButton = `
                                            <button type="button"
                                                    class="btn btn-sm btn-success btn-complete-repair"
                                                    data-id="${id}">
                                                <i class="bi bi-check-circle"></i>
                                                ซ่อมเสร็จ
                                            </button>`;
                                    } else if (row.status === 'completed') {
                                        actionButton = `
                                            <button type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    disabled>
                                                <i class="bi bi-check-circle-fill"></i>
                                                COMPLETED
                                            </button>`;
                                    } else {
                                        actionButton = `
                                            <button type="button"
                                                    class="btn btn-sm btn-primary btn-progress-repair"
                                                    data-id="${id}">
                                                <i class="bi bi-tools"></i>
                                                PROGRESS
                                            </button>`;
                                    }

                                    return `
                                        <div class="action-buttons">
                                            <button type="button"
                                                    class="btn btn-sm btn-info view-repair"
                                                    data-id="${id}">
                                              VIEW
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-repair"
                                                    data-id="${id}">
                                            
                                                DELETE
                                            </button>

                                            ${actionButton}
                                        </div>`;
                                }
                            }
                        ],

                        pageLength: 10,

                        lengthMenu: [
                            [10, 25, 50, 100],
                            [10, 25, 50, 100]
                        ],

                        order: [[6, 'desc']],

                  
                    });

            const modalElement = document.getElementById('repairDetailModal');
            const detailModal = bootstrap.Modal.getOrCreateInstance(modalElement);

            // ดูรายละเอียดแจ้งซ่อม
            $(document).on('click', '.view-repair', function (e) {
                e.preventDefault();

                const id = Number($(this).data('id'));

                if (!Number.isSafeInteger(id) || id < 1) {
                    showError('ข้อมูลไม่ถูกต้อง', 'ไม่พบรหัสรายการแจ้งซ่อม');
                    return;
                }

                $('#repairDetailContent').text('กำลังโหลดข้อมูล...');
                detailModal.show();

                $.ajax({
                    url: 'listrepair_detail.php',
                    method: 'GET',
                    data: { id: id },
                    dataType: 'html',

                    success: function (html) {
                        $('#repairDetailContent').html(html);
                    },

                    error: function (xhr) {
                        console.error('Detail request error:', xhr.status, xhr.responseText);

                        $('#repairDetailContent').text(
                            'ไม่สามารถโหลดรายละเอียดได้ (HTTP ' + xhr.status + ')'
                        );
                    }
                });
            });

            // เริ่มดำเนินการซ่อม
            $(document).on('click', '.btn-progress-repair', function () {
                const id = Number($(this).data('id'));

                if (!Number.isSafeInteger(id) || id < 1) return;

                Swal.fire({
                    title: 'ยืนยันการซ่อม?',
                    text: 'ต้องการเริ่มดำเนินการซ่อมอุปกรณ์ใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    background: '#1e1e2d',
                    color: '#f9fafb'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: 'listrepair_progress.php',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json',

                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'เริ่มดำเนินการซ่อมแล้ว!',
                                    text: response.message || 'อัปเดตสถานะเรียบร้อย',
                                    icon: 'success',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#198754',
                                    background: '#1e1e2d',
                                    color: '#f9fafb'
                                }).then(reloadRepairTable);
                            } else {
                                showError(
                                    'ไม่สำเร็จ',
                                    response.message || 'ไม่สามารถดำเนินการได้'
                                );
                            }
                        },

                        error: function (xhr) {
                            console.error('Progress request error:', xhr.responseText);
                            showError('เกิดข้อผิดพลาด', 'ไม่สามารถเริ่มซ่อมได้ กรุณาลองใหม่');
                        }
                    });
                });
            });

            // ยืนยันซ่อมเสร็จ
            $(document).on('click', '.btn-complete-repair', function () {
                const id = Number($(this).data('id'));

                if (!Number.isSafeInteger(id) || id < 1) return;

                Swal.fire({
                    title: 'ยืนยันการซ่อมเสร็จ?',
                    text: 'คุณต้องการยืนยันว่าอุปกรณ์ซ่อมเสร็จแล้วใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    background: '#1e1e2d',
                    color: '#f9fafb'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: 'listrepair_success.php',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json',

                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'ซ่อมเสร็จแล้ว!',
                                    text: response.message || 'อัปเดตสถานะเรียบร้อย',
                                    icon: 'success',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#198754',
                                    background: '#1e1e2d',
                                    color: '#f9fafb'
                                }).then(reloadRepairTable);
                            } else {
                                showError(
                                    'ไม่สำเร็จ',
                                    response.message || 'ไม่สามารถดำเนินการได้'
                                );
                            }
                        },

                        error: function (xhr) {
                            console.error('Complete request error:', xhr.responseText);
                            showError('เกิดข้อผิดพลาด', 'ไม่สามารถอัปเดตสถานะได้ กรุณาลองใหม่');
                        }
                    });
                });
            });

            // ลบรายการแจ้งซ่อม
            $(document).on('click', '.btn-delete-repair', function () {
                const id = Number($(this).data('id'));

                if (!Number.isSafeInteger(id) || id < 1) return;

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: 'คุณต้องการลบรายการแจ้งซ่อมนี้หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยันการลบ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    background: '#1e1e2d',
                    color: '#f9fafb',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'swal-delete-confirm',
                        cancelButton: 'swal-delete-cancel',
                        popup: 'repair-swal-card'
                    }
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: 'listrepair_delete.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { id: id },

                        success: function (response) {
                            if (response.success) {
                                reloadRepairTable();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบสำเร็จ!',
                                    text: response.message || 'ลบรายการเรียบร้อยแล้ว',
                                    background: '#1e1e2d',
                                    color: '#f9fafb',
                                    showConfirmButton: false,
                                    timer: 1800,
                                    timerProgressBar: true,
                                    customClass: {
                                        popup: 'repair-swal-card'
                                    }
                                });
                            } else {
                                showError(
                                    'ลบไม่สำเร็จ',
                                    response.message || 'ไม่สามารถลบรายการได้'
                                );
                            }
                        },

                        error: function (xhr, status, error) {
                            console.error('Delete HTTP status:', xhr.status);
                            console.error('AJAX status:', status);
                            console.error('Error:', error);
                            console.error('Response:', xhr.responseText);

                            showError(
                                'เกิดข้อผิดพลาด',
                                'ไม่สามารถลบรายการได้ กรุณาตรวจสอบ Console'
                            );
                        }
                    });
                });
            });
        });ำ
    </script>

</body>
</html>
