<?php
require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>จัดการผู้ใช้งาน</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/users.css">
  
</head>

<body>

    <!-- Sidebar -->
    <?php include __DIR__ . '/include/nav.php'; ?>

    <!-- Main content -->
    <main>
        <div class="container-fluid p-0">
            <div class="table-card">

                <!-- Header -->
                <div class="table-header">
                    <div class="page-header">
                        <div class="eyebrow">
                            <i class="bi bi-people-fill"></i>
                            User management
                        </div>

                        <h1 class="page-title">จัดการผู้ใช้งาน</h1>

                        <p class="page-description">
                            รายชื่อผู้ใช้งานและสิทธิ์ภายในระบบ
                        </p>
                    </div>

              
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="repairTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อ</th>
                                <th>ตำแหน่ง</th>
                                <th>อีเมล</th>
                             
                            </tr>
                        </thead>

                        <tbody>
                          
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <?php include __DIR__ . '/include/footer.php'; ?>
        </div>
    </main>

    <!-- Edit Modal -->
    <div class="modal fade" id="editRepairModal" tabindex="-1" aria-labelledby="editRepairModalLabel"
        aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">

                <div class="modal-header">
                    <h5 class="modal-title" id="editRepairModalLabel">
                        แก้ไขรายการแจ้งซ่อม
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <form id="editRepairForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_name" class="form-label">
                                ชื่ออุปกรณ์
                            </label>

                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">
                                รายละเอียด
                            </label>

                            <textarea name="description" id="edit_description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">
                                สถานะ
                            </label>

                            <select name="status" id="edit_status" class="form-select">
                                <option value="pending">รอดำเนินการ</option>
                                <option value="in_progress">กำลังซ่อม</option>
                                <option value="completed">ซ่อมเสร็จแล้ว</option>
                                <option value="cancelled">ยกเลิก</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            ยกเลิก
                        </button>

                        <button type="submit" class="btn btn-primary" id="saveRepairBtn">
                            บันทึก
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>



    <!-- Page JavaScript -->
    <script>
       
$(document).ready(function () {

    // DataTables AJAX
    const table = $('#repairTable').DataTable({
        processing: true,
        ajax: {
            url: 'user_datatable.php',
            type: 'GET',
            dataSrc: 'data',
            error: function (xhr) {
                console.error('AJAX Error:', xhr.status);
                console.error(xhr.responseText);
            }
        },
        columns: [
            { data: 'id' },
            { data: 'username', defaultContent: '-' },
            { data: 'role_name', defaultContent: '-' },
            { data: 'email', defaultContent: '-' },
          
        ],
       
    });

  


            const modalElement = document.getElementById('editRepairModal');
            const editModal = bootstrap.Modal.getOrCreateInstance(modalElement);

            // เปิด Modal และโหลดข้อมูล
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');

                $('#editRepairForm')[0].reset();
                $('#edit_id').val(id);

                $.ajax({
                    url: 'repair_get.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',

                    success: function (response) {
                        if (response.success) {
                            const data = response.data;

                            $('#edit_id').val(data.id);
                            $('#edit_name').val(data.name);
                            $('#edit_description').val(data.description);
                            $('#edit_status').val(data.status);

                            editModal.show();
                        } else {
                            alert(response.message || 'ไม่พบข้อมูล');
                        }
                    },

                    error: function () {
                        alert('ไม่สามารถดึงข้อมูลได้');
                    }
                });
            });

            // บันทึกข้อมูล
            $('#editRepairForm').on('submit', function (e) {
                e.preventDefault();

                const $saveButton = $('#saveRepairBtn');

                $saveButton.prop('disabled', true).text('กำลังบันทึก...');

                $.ajax({
                    url: 'repair_update.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',

                    success: function (response) {
                        if (response.success) {
                            editModal.hide();

                            // โหลดใหม่หลัง Modal ปิดเรียบร้อย
                            $(modalElement).one('hidden.bs.modal', function () {
                                location.reload();
                            });
                        } else {
                            alert(response.message || 'บันทึกข้อมูลไม่สำเร็จ');
                        }
                    },

                    error: function () {
                        alert('เกิดข้อผิดพลาดในการบันทึก');
                    },

                    complete: function () {
                        $saveButton.prop('disabled', false).text('บันทึก');
                    }
                });
            });

            // ลบข้อมูล
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');

                if (!confirm('ต้องการลบข้อมูลนี้หรือไม่?')) {
                    return;
                }

                $.ajax({
                    url: 'repair_delete.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',

                    success: function (response) {
                        if (response.success) {
                            alert(response.message || 'ลบข้อมูลสำเร็จ');
                            location.reload();
                        } else {
                            alert(response.message || 'ลบข้อมูลไม่สำเร็จ');
                        }
                    },

                    error: function () {
                        alert('เกิดข้อผิดพลาดในการลบ');
                    }
                });
            });
        });
    </script>

</body>

</html>