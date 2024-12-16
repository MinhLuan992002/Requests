<?php
// Kiểm tra nếu có ID yêu cầu
if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Kết nối đến cơ sở dữ liệu
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy thông tin yêu cầu dựa trên request_id
        $stmt = $pdo->prepare("SELECT u.*, sr.*,sr.attachment as sr_attachment, sr.created_at as  sr_created_at FROM support_requests sr 
                               JOIN users u ON sr.manv = u.manv  
                               WHERE sr.id = :request_id 
                               AND u.IsActive = 1 AND u.IsDeleted = 0");

        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->execute();
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            echo "Yêu cầu không tồn tại!";
            exit;
        }
    } catch (PDOException $e) {
        echo "Lỗi cơ sở dữ liệu: {$e->getMessage()}";
        exit;
    }
}

?>
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
</style>
<div class="modal fade" id="supportModal_Completed_<?= $request['id'] ?>" tabindex="-1" aria-labelledby="modalLabel_<?= $request['id'] ?>" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div>
                <div
                    class="card-header text-center bg-gradient-primary text-white " style="height: 100px; border-radius: 10px;">
                    <h3 style="color: white; padding-top: 10px;" class=" mb-0 "><?= $translations['submit_request'] ?></h3>
                    <small id="requestTime" class="d-block mt-1">
                        <?= $translations['request_time'] ?> <strong><?= htmlspecialchars($request['sr_created_at']) ?></strong>
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nội dung yêu cầu -->
                <div class="page-header ">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div >
                            <!-- Tiêu đề -->


                            <!-- Nội dung -->
                            <div class="card-body">
                                <!-- Chữ ký -->
                                <div class="row mb-4">

                                    <div class="col-md-4 text-center">
                                        <label for="directorSignature" class="form-label"><?= $translations['final_approval'] ?></label>
                                        <div
                                            class="border bg-light p-3"
                                            id="directorSignature"
                                            style="color: red; height: 80px; border-radius: 10px; font-family: 'Brush Script MT', cursive; font-size: 24px;">
                                            <?= htmlspecialchars($request['signer_director'] ?? '') ?>

                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <label for="managerSignature" class="form-label"><?= $translations['department_head'] ?></label>
                                        <div
                                            class="border bg-light p-3"
                                            id="managerSignature"
                                            style=" color: red; height: 80px; border-radius: 10px; font-family: 'Brush Script MT', cursive; font-size: 24px;">
                                            <?= htmlspecialchars($request['signer_manager'] ?? '') ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <label for="senderSignature" class="form-label"><?= $translations['sender'] ?></label>
                                        <div
                                            class="border bg-light p-3"
                                            id="senderSignature"
                                            style="height: 80px; border-radius: 10px; font-family: 'Brush Script MT', cursive; font-size: 24px;">
                                            <?= htmlspecialchars($request['sender_name']) ?>
                                        </div>
                                    </div>
                                </div>
                                <form id="support-form" method="post" action="update_status.php" autocomplete="off">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request_id) ?>">
                                    <input type="hidden" name="department" value="<?= htmlspecialchars($request['department']) ?>">
                                    <div class="row  mb-3">
                                        <!-- Thông tin người yêu cầu -->
                                        <div class="col-md-5">
                                            <h5 class="text-primary mb-3"><?= $translations['requester_info'] ?></h5>
                                            <p><?= $translations['full_name'] ?> <?= htmlspecialchars($request['sender_name']) ?></p>
                                            <p><?= $translations['employee_id'] ?> <?= htmlspecialchars($request['manv']) ?></p>
                                            <p><?= $translations['email'] ?><?= htmlspecialchars($request['email']) ?></p>
                                            <p><?= $translations['department'] ?> <?= htmlspecialchars($request['department']) ?></p>
                                            <!-- Trường ẩn để gửi thông tin người yêu cầu -->
                                            <input type="hidden" name="displayName" value="<?= htmlspecialchars($request['sender_name']) ?>">
                                            <input type="hidden" name="manv" value="<?= htmlspecialchars($request['manv']) ?>">
                                            <input type="hidden" name="mail" value="<?= htmlspecialchars($request['email']) ?>">
                                            <input type="hidden" name="department" value="<?= htmlspecialchars($request['department']) ?>">
                                        </div>

                                        <!-- Cấp độ ưu tiên -->
                                        <div class="col-md-7">
                                            <div class="row align-items-center ">
                                                <!-- Cấp độ ưu tiên -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="priority_level" class="form-label text-primary fw-bold"><?= $translations['priority_level'] ?></label>
                                                    <input type="text" name="priority_level" class="form-control" value="<?= htmlspecialchars($request['priority_level']) ?>" readonly />
                                                </div>

                                                <!-- Thời gian mong muốn -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="desired_time" class="form-label text-primary fw-bold"><?= $translations['desired_time'] ?></label>
                                                    <input type="date" name="desired_time" class="form-control" value="<?= htmlspecialchars($request['desired_time']) ?>" readonly />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thông tin người gửi -->
                                        <h5 class="text-primary mb-3"><?= $translations['applicant_info'] ?></h5>


                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="manv" class="form-label"><?= $translations['employee_id'] ?></label>
                                                <input type="text" name="userId" class="form-control" value="<?= htmlspecialchars($request['userId']) ?>" readonly />
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="dept" class="form-label"><?= $translations['department'] ?></label>
                                                <input type="text" name="dept" class="form-control" value="<?= htmlspecialchars($request['dept']) ?>" readonly />
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label"><?= $translations['email'] ?></label>
                                                <input type="text" name="userEmail" class="form-control" value="<?= htmlspecialchars($request['userEmail']) ?>" readonly />
                                            </div>

                                        </div>

                                        <!-- Nội dung yêu cầu -->
                                        <h5 class="text-primary mb-3"><?= $translations['request_content'] ?></h5>
                                        <div class="col-md-3 mb-3">
                                            <label for="category" class="form-label"><?= $translations['support_category'] ?></label>
                                            <?php foreach (json_decode($request['category'], true) as $cat): ?>
                                                <input type="text" name="category[]" class="form-control mb-2" value="<?= htmlspecialchars($cat) ?>" readonly />
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="col-md-9">
                                            <label for="content" class="form-label"><?= $translations['content'] ?></label>
                                            <?php foreach (json_decode($request['content'], true) as $cont): ?>
                                                <textarea name="content[]" class="form-control mb-2" rows="1" readonly><?= htmlspecialchars($cont) ?></textarea>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="content" class="form-label"><?= $translations['manager_note'] ?></label>

                                            <textarea name="note" class="form-control mb-2" rows="2" readonly><?= htmlspecialchars($request['notes_manager'] ?? '') ?></textarea>

                                        </div>
                                        <div class="col-md-12">
                                            <label for="content" class="form-label"><?= $translations['final_approver_note'] ?></label>

                                            <textarea name="note" class="form-control mb-2" rows="2" readonly><?= htmlspecialchars($request['notes_final'] ?? '') ?></textarea>

                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <?php if (!empty($request['sr_attachment'])): ?>
                                                <label for="attachment" class="form-label"><?= $translations['attachment'] ?> (Optional)</label>
                                                <p>
                                                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $request['sr_attachment'])): ?>
                                                        <!-- Nếu là hình ảnh -->
                                                        <img
                                                            src="<?= htmlspecialchars($request['sr_attachment']) ?>"
                                                            alt="sr_attachment"
                                                            style="max-width: 40%; height: auto; cursor: pointer;"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#imageModal">
                                                    <?php else: ?>
                                                        <!-- Nếu là file khác -->
                                                        <a href="<?= htmlspecialchars($request['sr_attachment']) ?>" target="_blank">
                                                            <?= htmlspecialchars(basename($request['sr_attachment'])) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

            

                                    </div>


                                    <?php
                                    $approval_status = $request['approval_status'] ?? ''; // Lấy trạng thái phê duyệt từ cơ sở dữ liệu

                                    if ($approval_status !== 'Completed'): ?>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary mt-3">
                                                <?= $translations['complete'] ?>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center text-success"><?= $translations['request_processed'] ?></p>
                                    <?php endif; ?>

                                    <!-- Input ẩn để lưu trạng thái approval_status -->





                            </div>

                            <!-- Nút gửi -->
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <!-- <button type="button" class="btn btn-primary">Lưu</button> -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.querySelector('#supportModal_Completed_<?= $request['id'] ?>');
        const modal = new bootstrap.Modal(modalElement);

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