<style>
    .modal-xl {
        max-width: 40%;
        /* Tùy chỉnh theo ý muốn */
        margin: 0 auto;
    }

    .col-lg-8 {
        width: 100%;
    }

    @media (max-width: 1556px) {
        .modal-xl {
            max-width: 60% !important;
            margin: 0 auto !important;
        }

        .modal-content {
            padding: 10px;
        }

        .form-label {
            font-size: 14px;
        }

        .btn {
            font-size: 14px;
            padding: 8px 12px;
        }
    }

    @media (max-width: 1060px) {
        .modal-xl {
            max-width: 70% !important;
            margin: 0 auto !important;
        }
    }

    @media (max-width: 895px) {
        .modal-xl {
            max-width: 80% !important;
            margin: 0 auto !important;
        }

        @media (max-width: 495px) {
            .modal-xl {
                max-width: 100% !important;
                margin: 0 auto !important;
            }
        }
    }

    .btn-orange {
        background-color: #ff7f3f;
        /* Màu cam sáng */
        color: white;
        width: 40px;
        /* Kích thước nút */
        height: 40px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .btn-orange:hover {
        background-color: #e56b2b;
        /* Màu cam đậm hơn khi hover */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        transform: scale(1.1);
        /* Hiệu ứng phóng to nhẹ */
    }

    .btn-orange:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 127, 63, 0.5);
    }

    .btn-orange i {
        font-size: 20px;
        /* Kích thước dấu cộng */
        font-weight: bold;
    }
</style>
<button class="btn btn-outline-primary btn-sm" type="button"
    data-bs-toggle="modal"
    data-bs-target="#supportModal">
    <?= $translations['new_request'] ?>
</button>
<div
    class="modal fade"
    id="supportModal"
    tabindex="-1"
    aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-xl"> <!-- Đổi modal-lg thành modal-xl -->
        <div class="modal-content">
            <div>
                <div
                    class="card-header text-center bg-gradient-primary text-white ">
                    <h3 style="color: white;" class=" mb-0 "><?= $translations['submit_request'] ?></h3>
                    <small id="requestTime" class="d-block mt-1">
                        <?= $translations['request_time'] ?> <strong id="currentTime"></strong>
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div>
                <!-- Nội dung form không đổi -->
                <div class="container-fluid py-4" style="margin: 0px;padding: 0px;">
                    <div class="row">
                        <header>
                            <div class="page-header ">
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="card shadow-lg ">
                                                <!-- Tiêu đề -->
                                                <!-- <div
                              class="card-header text-center bg-gradient-primary text-white ">
                              <h3 style="color: white;" class=" mb-0 ">Gửi Yêu Cầu Hỗ Trợ</h3>
                              <small id="requestTime" class="d-block mt-1">
                                Thời gian yêu cầu: <strong id="currentTime"></strong>
                              </small>
                            </div> -->

                                                <!-- Nội dung -->
                                                <div class="card-body">
                                                    <!-- Chữ ký -->
                                                    <div class="row mb-4">

                                                        <div class="col-md-4 text-center">
                                                            <label for="directorSignature" class="form-label"><?= $translations['final_approval'] ?></label>
                                                            <div
                                                                class="border bg-light p-3"
                                                                id="directorSignature"
                                                                style="height: 80px; border-radius: 10px"></div>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <label for="managerSignature" class="form-label"><?= $translations['department_head'] ?></label>
                                                            <div
                                                                class="border bg-light p-3"
                                                                id="managerSignature"
                                                                style="height: 80px; border-radius: 10px"></div>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <label for="senderSignature" class="form-label"><?= $translations['sender'] ?></label>
                                                            <div
                                                                class="border bg-light p-3"
                                                                id="senderSignature"
                                                                style="height: 80px; border-radius: 10px; font-family: 'Brush Script MT', cursive; font-size: 24px;">
                                                                <?php echo htmlspecialchars($_SESSION['displayName']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form id="support-form" method="post" action="send_email.php" enctype="multipart/form-data" autocomplete="off">

                                                        <div class="row  mb-3">
                                                            <!-- Thông tin người yêu cầu -->
                                                            <div class="col-md-5">
                                                                <h5 class="text-primary mb-3"><?= $translations['requester_info'] ?></h5>
                                                                <p><?= $translations['full_name'] ?> <?php echo htmlspecialchars($_SESSION['displayName']); ?></p>
                                                                <p><?= $translations['employee_id'] ?> <?php echo htmlspecialchars($_SESSION['manv']); ?></p>
                                                                <p><?= $translations['email'] ?> <?php echo htmlspecialchars($_SESSION['mail']); ?></p>
                                                                <p><?= $translations['department'] ?> <?php echo htmlspecialchars($_SESSION['department']); ?></p>
                                                                <!-- Trường ẩn để gửi thông tin người yêu cầu -->
                                                                <input type="hidden" name="displayName" value="<?= htmlspecialchars($_SESSION['displayName']) ?>">
                                                                <input type="hidden" name="manv" value="<?= htmlspecialchars($_SESSION['manv']) ?>">
                                                                <input type="hidden" name="mail" value="<?= htmlspecialchars($_SESSION['mail']) ?>">
                                                                <input type="hidden" name="department" value="<?= htmlspecialchars($_SESSION['department']) ?>">
                                                            </div>

                                                            <!-- Cấp độ ưu tiên -->
                                                            <div class="col-md-7">
                                                                <div class="row align-items-center ">
                                                                    <!-- Cấp độ ưu tiên -->
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="priority_level" class="form-label text-primary fw-bold"><?= $translations['priority_level'] ?></label>
                                                                        <select
                                                                            class="form-select shadow-sm"
                                                                            id="priority_level"
                                                                            name="priority_level"
                                                                            required>
                                                                            <option value="" selected><?= $translations['priority_level'] ?></option>
                                                                            <?php foreach ($priorityLevels as &$priority): ?>
                                                                                <option value="<?php echo htmlspecialchars($priority['level_name']); ?>">
                                                                                    <?= htmlspecialchars($priority['level_name']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Thời gian mong muốn -->
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="desired_time" class="form-label text-primary fw-bold"><?= $translations['desired_time'] ?></label>
                                                                        <input value=""
                                                                            type="date"
                                                                            id="desired_time"
                                                                            name="desired_time"
                                                                            class="form-control shadow-sm" />
                                                                    </div>
                                                                </div>
                                                            </div>



                                                            <!-- Thông tin người gửi -->
                                                            <h5 class="text-primary mb-3"><?= $translations['applicant_info'] ?></h5>

                                                            <div class="row">
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="manv" class="form-label"><?= $translations['employee_id'] ?></label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        id="manv"
                                                                        name="userId"
                                                                        placeholder="<?= $translations['employee_id'] ?>"
                                                                        onkeyup="fetchEmployeeInfo()" />
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="dept" class="form-label"><?= $translations['department'] ?></label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        id="dept"
                                                                        name="dept"
                                                                        placeholder="<?= $translations['department'] ?>" />
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="email" class="form-label"><?= $translations['email'] ?></label>
                                                                    <input
                                                                        type="email"
                                                                        class="form-control"
                                                                        id="email"
                                                                        name="userEmail"
                                                                        placeholder="<?= $translations['email'] ?>" />
                                                                </div>
                                                            </div>



                                                            <!-- Nội dung yêu cầu -->
                                                            <h5 class="text-primary mb-3"><?= $translations['request_content'] ?></h5>

                                                            <div id="request-container">
                                                                <!-- Dòng đầu tiên -->
                                                                <div class="row request-row">
                                                                    <div class="col-md-3 mb-3">
                                                                        <label for="category" class="form-label"><?= $translations['support_category'] ?></label>
                                                                        <select class="form-select" name="category[]" required onchange="updateSupportFields(this)">
    <option value="" selected><?= $translations['support_category'] ?? 'Danh mục hỗ trợ' ?></option>
    <?php foreach ($categories as &$category): ?>
        <option value="<?= htmlspecialchars($category['id']) . '. ' . $category['category_name'] ?>">
            <?= htmlspecialchars($category['category_name']) ?>
        </option>
    <?php endforeach; ?>
</select>



                                                                    </div>
                                                                    <div class="col-md-7 mb-3">
                                                                        <label for="supportContent" class="form-label"><?= $translations['support_details'] ?></label>
                                                                        <div class="support-content">
                                                                            <p style="font-style: italic;" class="btn outline-primary"><?= $translations['please_select_category'] ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-md-1 mb-3 d-flex align-items-center">
            <i style="font-size: 35px; margin-top: 37px;" class="fa-regular fa-square-minus text-primary" onclick="removeRequestRow(this)"></i>
        </div> -->
                                                                    <div class="col-md-1 mb-3">
                                                                        <i style="font-size: 35px; margin-top: 37px;" class="fa-regular fa-square-plus text-primary" onclick="addRequestRow()"></i>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                            <script>
                                                                // Hàm thêm dòng mới
                                                                function addRequestRow() {
                                                                    const container = document.getElementById('request-container');

                                                                    // Tạo dòng mới
                                                                    const newRow = document.createElement('div');
                                                                    newRow.classList.add('row', 'request-row');

                                                                    // Nội dung của dòng mới
                                                                    newRow.innerHTML = `
            <div class="col-md-3 mb-3">
                <label for="category" class="form-label"><?= $translations['support_category'] ?></label>
                <select
                    class="form-select"
                    name="category[]"
                    required
                    onchange="updateSupportFields(this)">
                    <option value="" selected><?= $translations['support_category'] ?></option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) . '. ' . $category['category_name'] ?>">
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-7 mb-3">
                <label for="supportContent" class="form-label"><?= $translations['support_details'] ?></label>
                <div class="support-content">
                    <p style="font-style: italic;" class="btn outline-primary"><?= $translations['please_select_category'] ?></p>
                </div>
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-center">
  <i style="font-size: 35px; margin-top: 37px;" class="fa-regular fa-square-minus text-primary" onclick="removeRequestRow(this)"></i>
            </div>
        `;

                                                                    // Thêm dòng vào container
                                                                    container.appendChild(newRow);
                                                                }

                                                                // Hàm xóa dòng
                                                                function removeRequestRow(button) {
                                                                    const row = button.closest('.row');
                                                                    row.remove();
                                                                }

                                                                // Hàm cập nhật nội dung hỗ trợ dựa trên danh mục
                                                                function updateSupportFields(selectElement) {
                                                                    const categoryId = selectElement.value;
                                                                    const supportContent = selectElement.closest('.request-row').querySelector('.support-content');

                                                                    if (!categoryId) {
                                                                        supportContent.innerHTML = `<p class="text-muted"><?= $translations['please_select_category'] ?></p>`;
                                                                        return;
                                                                    }

                                                                    fetch(`get_category_details.php?category_id=${categoryId}`)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            if (data.error) {
                                                                                supportContent.innerHTML = `<p class="text-danger">Lỗi: ${data.error}</p>`;
                                                                                return;
                                                                            }

                                                                            let content = '';
                                                                            switch (data.field_type) {
                                                                                case 'textarea':
                                                                                    content = `
                            <textarea class="form-control" name="content[]" rows="4" placeholder="<?= $translations['enter_content_details'] ?>" required></textarea>
                        `;
                                                                                    break;
                                                                                case 'input':
                                                                                    content = `
                            <input type="text" class="form-control" name="content[]" placeholder="<?= $translations['enter_content_details'] ?>" required />
                        `;
                                                                                    break;
                                                                                case 'select':
                                                                                    content = `
                            <select class="form-select" name="content[]" required>
                                ${data.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                            </select>
                        `;
                                                                                    break;
                                                                                default:
                                                                                    content = `<p class="text-muted">Không có nội dung hỗ trợ cho danh mục này.</p>`;
                                                                            }

                                                                            supportContent.innerHTML = content;
                                                                        })
                                                                        .catch(error => {
                                                                            supportContent.innerHTML = `<p class="text-danger">Lỗi khi tải nội dung hỗ trợ.</p>`;
                                                                            console.error(error);
                                                                        });
                                                                }
                                                            </script>

                                                            <!-- <div class="col-md-2 mb-3">
                                                                <i style="font-size: 35px;" data-bs-toggle="collapse"
                                                                    href="#optionalSection"
                                                                    role="button"
                                                                    aria-expanded="false"
                                                                    aria-controls="optionalSection" class="fa-regular fa-square-plus text-primary"></i>
                                                            </div> -->


                                                            <div class="collapse mt-3" id="optionalSection">



                                                                <div class="mb-3">
                                                                    <label for="notes" class="form-label text-primary fw-bold"><?= $translations['notes'] ?></label>
                                                                    <textarea
                                                                        id="notes"
                                                                        name="notes"
                                                                        rows="4"
                                                                        class="form-control shadow-sm"
                                                                        placeholder="Nhập ghi chú tại đây..."></textarea>
                                                                </div>
                                                            </div>

                                                            <form id="support-form" method="post" action="send_email.php" enctype="multipart/form-data" autocomplete="off">
                                                                <div class="col-md-12 mb-3">
                                                                    <label for="attachment" class="form-label"><?= $translations['attachment'] ?></label>
                                                                    <input
                                                                        type="file"
                                                                        class="form-control"
                                                                        id="attachment"
                                                                        name="attachment"
                                                                        accept="image/*, .xls, .xlsx" /> <!-- Chấp nhận hình ảnh và tệp Excel -->
                                                                </div>

                                                                <div class="col-md-12 mb-3">


                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-primary mt-3">
                                                                            <?= $translations['support_category'] ?>
                                                                        </button>
                                                                    </div>
                                                            </form>
                                                        </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </header>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"><?= $translations['close'] ?></button>
                <!-- <button
                    type="submit"
                    class="btn btn-primary"
                    form="support-form"><?= $translations['submit_request'] ?></button> -->
            </div>
        </div>
    </div>
</div>
<script>
    function displayCurrentTime() {
        const now = new Date();
        const formattedTime =
            now.toLocaleDateString("vi-VN") +
            " " +
            now.toLocaleTimeString("vi-VN");
        document.getElementById("currentTime").textContent = formattedTime;
    }
    displayCurrentTime();
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.querySelector('#supportModal<?= $request['id'] ?>');
        const modal = new bootstrap.Modal(modalElement);

        // Lắng nghe sự kiện click vào backdrop và đóng modal nếu click vào ngoài modal
        modalElement.addEventListener('click', function(event) {
            if (event.target === modalElement) {
                modal.hide(); // Đóng modal nếu nhấn vào backdrop
            }
        });

        // Mở modal khi request_id có trong URL
        const urlParams = new URLSearchParams(window.location.search);
        const requestId = urlParams.get('request_id');

        if (requestId) {
            modal.show();
        }
    });
</script>