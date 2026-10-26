<?php

require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>รายการแจ้งซ่อม</title>

    <!-- CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.3.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- CSS ของหน้า -->
    <link rel="stylesheet" href="assets/css/inventory.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>



</head>

<body>
    
 
  <!-- Sidebar -->
<?php require_once __DIR__ . '/include/nav.php'; ?>

<!-- Main Content -->
<main>
    <div class="container-fluid p-0">
        <div class="table-card">

            <!-- Header -->
            <div class="table-header">
                <div>
                    <h3 class="page-title mb-1">
                        รายการแจ้งซ่อมของฉัน
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
    </div>

    <!-- Footer -->
    <?php require_once __DIR__ . '/include/footer.php'; ?>
</main>
    
    <div class="modal fade" id="repairDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดคำขอแจ้งซ่อม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="repairDetailContent">
                    กำลังโหลดข้อมูล...
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        ปิด
                    </button>
                </div>

            </div>
        </div>
    </div>







    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>
  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {

            // ป้องกันข้อความจากฐานข้อมูลแทรก HTML
            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            const table = $('#repairTable').DataTable({
                processing: true,

                ajax: {
                    url: 'inventory_datatable.php',
                    type: 'GET',
                    dataSrc: 'data'
                },

                columns: [
                    {
                        data: 'id'
                    },
                    {
                        data: 'username',
                        render: function (data) {
                            return '<strong>' + escapeHtml(data) + '</strong>';
                        }
                    },
                    {
                        data: 'priority',
                        className: 'text-center'
                    },
                    {
                        data: 'description',
                        render: function (data) {
                            const description = data ?? '';
                            const shortText = description.length > 50
                                ? description.substring(0, 50) + '...'
                                : description;

                            return '<span title="' + escapeHtml(description) + '">' +
                                escapeHtml(shortText) + '</span>';
                        }
                    },
                    {
                        data: 'location'
                    },
                    {
                        data: 'status',
                        render: function (status) {
                            const statuses = {
                                pending: '<span class="badge bg-warning text-dark status-badge">รอดำเนินการ</span>',
                                in_progress: '<span class="badge bg-primary status-badge">กำลังซ่อม</span>',
                                completed: '<span class="badge bg-success status-badge">ซ่อมเสร็จแล้ว</span>',
                                cancelled: '<span class="badge bg-danger status-badge">ยกเลิก</span>'
                            };

                            return statuses[status] || '';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function (data) {
                            if (!data) return '';

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
                            const id = Number(row.id);

                            let actionButton = '';

                      

                            return `
                        <div class="action-buttons">
                            <button type="button"
                                class="btn btn-sm btn-info view-repair"
                                data-id="${id}">
                                VIEW
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

                order: [[6, 'desc']]
            });

        });

        // เปิด Modal และโหลดรายละเอียดด้วย AJAX
        $(document).on('click', '.view-repair', function (e) {


            e.preventDefault();

            const id = $(this).data('id');
            const modalElement = document.getElementById('repairDetailModal');

            $('#repairDetailContent').text('กำลังโหลดข้อมูล...');

            bootstrap.Modal.getOrCreateInstance(modalElement).show();

            $.ajax({
                url: 'listrepair_detail.php',
                method: 'GET',
                data: { id: id },
                dataType: 'html',

                success: function (html) {
                    $('#repairDetailContent').html(html);
                },

                error: function (xhr) {
                    console.log('HTTP Status:', xhr.status);
                    console.log('Response:', xhr.responseText);

                    $('#repairDetailContent').text(
                        'เกิดข้อผิดพลาด: ' + xhr.status
                    );
                }
            });
        });



 
$(document).on('click', '.btn-progress-repair', function () {
    const id = $(this).data('id');

    // ยืนยันก่อนดำเนินการ
    Swal.fire({
        title: 'ยืนยันการซ่อม?',
        text: 'ต้องการเริ่มดำเนินการซ่อมอุปกรณ์ใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: 'listrepair_progress.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json',

            success: function (response) {
                if (response.success) {

                    // แสดง Card แจ้งผลสำเร็จ
                    Swal.fire({
                        title: 'เริ่มดำเนินการซ่อมแล้ว!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#198754'
                    }).then(() => {

                        // รีเฟรช DataTable โดยไม่โหลดหน้าใหม่
                        $('#repairTable')
                            .DataTable()
                            .ajax.reload(null, false);
                    });

                } else {
                    Swal.fire({
                        title: 'ไม่สำเร็จ',
                        text: response.message || 'ไม่สามารถดำเนินการได้',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },

            error: function (xhr) {
                console.log(xhr.responseText);

                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    });
});



 
$(document).on('click', '.btn-complete-repair', function () {
    const id = $(this).data('id');

    // ยืนยันก่อนดำเนินการ
    Swal.fire({
        title: 'ยืนยันการซ่อมเสร็จ?',
        text: 'คุณต้องการยืนยันว่าอุปกรณ์ซ่อมเสร็จแล้วใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: 'listrepair_success.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json',

            success: function (response) {
                if (response.success) {
                    // แสดงผลสำเร็จ
                    Swal.fire({
                        title: 'ซ่อมเสร็จแล้ว!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#198754'
                    }).then(() => {
                        // รีเฟรช DataTable โดยไม่โหลดหน้าใหม่
                        $('#repairTable')
                            .DataTable()
                            .ajax.reload(null, false);
                    });

                } else {
                    Swal.fire({
                        title: 'ไม่สำเร็จ',
                        text: response.message || 'ไม่สามารถดำเนินการได้',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }
            },

            error: function (xhr) {
                console.log(xhr.responseText);

                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'กรุณาลองใหม่อีกครั้ง',
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    });
});

$(document).on('click', '.btn-delete-repair', function () {
    const id = $(this).data('id');

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
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'listrepair_delete.php',
            type: 'POST',
            dataType: 'json',
            data: { id: id },

            success: function (response) {
                if (response.success) {
                    // โหลด DataTable ใหม่โดยไม่กลับไปหน้าแรก
                    $('#repairTable').DataTable().ajax.reload(null, false);

                    // Card แจ้งลบสำเร็จ
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: response.message,
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
                    Swal.fire({
                        icon: 'error',
                        title: 'ลบไม่สำเร็จ',
                        text: response.message || 'ไม่สามารถลบรายการได้',
                        background: '#1e1e2d',
                        color: '#f9fafb',
                        confirmButtonText: 'ตกลง',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'swal-delete-confirm',
                            popup: 'repair-swal-card'
                        }
                    });
                }
            },

          error: function (xhr, status, error) {
    console.log('HTTP Status:', xhr.status);
    console.log('AJAX Status:', status);
    console.log('Error:', error);
    console.log('Response:', xhr.responseText);

    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ตรวจสอบ Console เพื่อดู Error จริง',
        background: '#1e1e2d',
        color: '#f9fafb'
    });
}
        });
    });
});

    </script>



</body>

</html>