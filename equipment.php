<?php

require_once __DIR__ . '/include/auth.php';
require_once __DIR__ . '/config/database.php';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>รายการอุปกรณ์</title>

       <!-- CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.3.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  
    <link rel="stylesheet" href="assets/css/equipment.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
 


</head>

<!-- Edit Repair Modal -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>


<body>


    <!-- Sidebar -->
    <?php require('include/nav.php'); ?>


    <!-- Main Content -->
    <main>

        <div class="container-fluid p-0">

            <div class="table-card">

                <!-- Header -->
                <div class="table-header d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h3 class="page-title mb-1">
                            รายการอุปกรณ์
                        </h3>

                        <div class="page-description">
                            รายการอุปกรณ์ทั้งหมดในระบบ
                        </div>
                    </div>


                  

                </div>


                <!-- Table -->
                <div class="table-responsive">

            
<table id="repairTable" class="table table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>ชื่ออุปกรณ์</th>
            <th>สถานะ</th>
            <th>รายละเอียด</th>
            <th>สร้างเมื่อ</th>
            <th>อัปเดตเมื่อ</th>
           
        </tr>
    </thead>
    <tbody></tbody>
</table>



                </div>

            </div>


            <!-- Footer -->
            <?php require('include/footer.php'); ?>

        </div>

    </main>

    <div class="modal fade" id="editRepairModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content bg-dark text-white">

            <div class="modal-header">

                <h5 class="modal-title">
                    แก้ไขรายการแจ้งซ่อม
                </h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                </button>

            </div>


            <form id="editRepairForm">

                <div class="modal-body">

                    <!-- ID -->
                    <input type="hidden" name="id" id="edit_id">


                    <!-- ชื่ออุปกรณ์ -->
                    <div class="mb-3">

                        <label class="form-label">
                            ชื่ออุปกรณ์
                        </label>

                        <input type="text" name="name" id="edit_name" class="form-control">

                    </div>


                    <!-- รายละเอียด -->
                    <div class="mb-3">

                        <label class="form-label">
                            รายละเอียด
                        </label>

                        <textarea name="description" id="edit_description" class="form-control" rows="4"></textarea>

                    </div>


                    <!-- สถานะ -->
                    <div class="mb-3">

                        <label class="form-label">
                            สถานะ
                        </label>

                        <select name="status" id="edit_status" class="form-select">

                            <option value="pending">
                                รอดำเนินการ
                            </option>

                            <option value="in_progress">
                                กำลังซ่อม
                            </option>

                            <option value="completed">
                                ซ่อมเสร็จแล้ว
                            </option>

                            <option value="cancelled">
                                ยกเลิก
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        ยกเลิก

                    </button>

                    <button type="submit" class="btn btn-primary">

                        บันทึก

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


    <!-- DataTables -->
    <script>

$(document).ready(function () {

    $('#repairTable').DataTable({
        processing: true,
        ajax: {
            url: 'equipment_datatable.php',
            type: 'GET',
            dataSrc: 'data'
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']],
        columns: [
            {
                data: 'id'
            },
            {
                data: 'name',
                render: function (data) {
                    return $('<div>').text(data ?? '').html();
                }
            },
            {
                data: 'status',
                render: function (data) {
                    const statuses = {
                        pending: {
                            text: 'รอดำเนินการ',
                            class: 'bg-warning text-dark'
                        },
                        in_progress: {
                            text: 'กำลังซ่อม',
                            class: 'bg-primary'
                        },
                        completed: {
                            text: 'ซ่อมเสร็จแล้ว',
                            class: 'bg-success'
                        },
                        cancelled: {
                            text: 'ยกเลิก',
                            class: 'bg-danger'
                        }
                    };

                    const status = statuses[data];

                    if (!status) {
                        return $('<span>')
                            .addClass('badge bg-secondary status-badge')
                            .text(data ?? 'ไม่ระบุ')
                            .prop('outerHTML');
                    }

                    return $('<span>')
                        .addClass('badge status-badge ' + status.class)
                        .text(status.text)
                        .prop('outerHTML');
                }
            },
            {
                data: 'description',
                render: function (data) {
                    const description = data ?? '';
                    const shortDescription =
                        description.length > 50
                            ? description.substring(0, 50) + '...'
                            : description;

                    return $('<span>')
                        .attr('title', description)
                        .text(shortDescription)
                        .prop('outerHTML');
                }
            },
            {
                data: 'created_at',
                render: function (data) {
                    return formatDate(data);
                }
            },
            {
                data: 'updated_at',
                render: function (data) {
                    return formatDate(data);
                }
            },
            
        ],
      
    });

    function formatDate(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(value.replace(' ', 'T'));

        if (Number.isNaN(date.getTime())) {
            return '-';
        }

        return new Intl.DateTimeFormat('th-TH', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(date);
    }

});



        $(document).on('click', '.edit-btn', function () {

            let id = $(this).data('id');

            // ล้างข้อมูลก่อน
            $('#edit_id').val('');
            $('#edit_name').val('');
            $('#edit_description').val('');
            $('#edit_status').val('');

            // ดึงข้อมูล
            $.ajax({

                url: 'repair_get.php',

                type: 'GET',

                data: {
                    id: id
                },

                dataType: 'json',

                success: function (response) {

                    if (response.success) {

                        let data = response.data;

                        $('#edit_id').val(data.id);
                        $('#edit_name').val(data.name);
                        $('#edit_description').val(data.description);
                        $('#edit_status').val(data.status);

                    } else {

                        alert(response.message);

                    }

                },

                error: function () {

                    alert('ไม่สามารถดึงข้อมูลได้');

                }

            });

        });


        // =========================
        // บันทึกข้อมูล
        // =========================

        $('#editRepairForm').on('submit', function (e) {



            e.preventDefault();

            $.ajax({

                url: 'repair_update.php',

                type: 'POST',

                data: $(this).serialize(),

                dataType: 'json',

                success: function (response) {

                    if (response.success) {

                        alert(response.message);

                        // ปิด Modal
                        $('#editRepairModal').modal('hide');

                        // รีโหลดหน้า
                        location.reload();

                    } else {

                        alert(response.message);

                    }

                },

                error: function () {

                    alert('เกิดข้อผิดพลาดในการบันทึก');

                }

            });

        });
        $(document).on('click', '.delete-btn', function () {

    let id = $(this).data('id');

    if (!confirm('ต้องการลบข้อมูลนี้หรือไม่?')) {
        return;
    }

    $.ajax({
        url: 'repair_delete.php',
        type: 'POST',
        data: {
            id: id
        },
        dataType: 'json',

        success: function(response) {

            if (response.success) {
                alert(response.message);

                // โหลดข้อมูลใหม่
                location.reload();

            } else {
                alert(response.message);
            }

        },

        error: function() {
            alert('เกิดข้อผิดพลาดในการลบ');
        }
    });

}); 
                                           
    </script>



</body>



</html>