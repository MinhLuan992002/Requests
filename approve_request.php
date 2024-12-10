<?php
include 'set_language.php';
include 'config/config.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có ID yêu cầu
if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Kết nối đến cơ sở dữ liệu
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy thông tin yêu cầu dựa trên request_id
        $stmt = $pdo->prepare("SELECT u.*, sr.*,sr.attachment as sr_attachment  , sr.created_at as  sr_created_at FROM support_requests sr 
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
} else {
    echo "Không có ID yêu cầu!";
    exit;
}

// Kiểm tra và lấy thông tin phòng ban
if (!isset($request['department'])) {
    echo "Không tìm thấy thông tin phòng ban!";
    exit;
}
$department = $request['department'];

// Lấy thông tin người dùng theo phòng ban
// Kiểm tra giá trị department

// Truy vấn cơ sở dữ liệu
$stmt = $pdo->prepare("SELECT u.email, u.fullname, ut.ConfigName
                       FROM users u
                       JOIN config ut ON u.UserType = ut.id
                       WHERE u.department = :department AND u.IsActive = 1 AND u.IsDeleted = 0");
$stmt->bindParam(':department', $department, PDO::PARAM_STR);
$stmt->execute();

// Kiểm tra lỗi truy vấn
if ($stmt->errorCode() != '00000') {
    echo "Lỗi truy vấn: " . implode(", ", $stmt->errorInfo());
    exit;
}

// Lấy kết quả
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra nếu không tìm thấy người dùng
if (!$user) {
    echo "Không tìm thấy thông tin người dùng với department: " . htmlspecialchars($department);
    exit;
}


// Xử lý khi gửi form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approval_status'])) {
        $approval_status = $_POST['approval_status'];

        // Kiểm tra giá trị hợp lệ của approval_status
        $valid_statuses = ['Pending', 'Manager Approved', 'Director Approved', 'Completed', 'Rejected'];
        if (!in_array($approval_status, $valid_statuses)) {
            echo "Trạng thái phê duyệt không hợp lệ!";
            exit;
        }

        // Xác định trạng thái phê duyệt của quản lý
        $manager_approval = ($approval_status == 'Manager Approved') ? 1 : 0;
        $signer_manager = ($approval_status == 'Manager Approved') ? $user['fullname'] : NULL;
    } else {
        echo "Trạng thái phê duyệt không hợp lệ!";
        exit;
    }

    // Cập nhật trạng thái phê duyệt và manager_approval
    try {
        $stmt = $pdo->prepare("UPDATE support_requests 
                               SET approval_status = :approval_status, 
                                   manager_approval = :manager_approval, 
                                   signer_manager = :signer_manager 
                               WHERE id = :request_id");

        $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);
        $stmt->bindParam(':manager_approval', $manager_approval, PDO::PARAM_INT);
        $stmt->bindParam(':signer_manager', $signer_manager, PDO::PARAM_STR);
        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);

        $stmt->execute();

        echo "Yêu cầu đã được " . ($approval_status == 'Manager Approved' ? 'phê duyệt' : 'từ chối') . " thành công.";
    } catch (PDOException $e) {
        // In ra thông báo lỗi chi tiết
        echo "Có lỗi xảy ra khi xử lý yêu cầu: " . $e->getMessage();
        error_log("Lỗi SQL: " . $e->getMessage());
    }
}

?>




<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link
        rel="apple-touch-icon"
        sizes="76x76"
        href="../assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <title>Requests Forms</title>
    <!--     Fonts and icons     -->
    <link
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800"
        rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link
        href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css"
        rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        rel="stylesheet" />
    <link
        href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css"
        rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <!-- <script
        src="https://kit.fontawesome.com/42d5adcbca.js"
        crossorigin="anonymous"></script> -->
    <!-- CSS Files -->
    <link id="pagestyle" href=" css/soft-design-system.css" rel="stylesheet" />
    <!-- Nepcha Analytics (nepcha.com) -->
    <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
    <script
        defer
        data-site="YOUR_DOMAIN_HERE"
        src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <style>
        #senderSignature {
            font-family: 'Brush Script MT', cursive;
            /* Phông chữ giống chữ ký */
            font-size: 24px;
            /* Kích thước chữ lớn hơn */
            text-align: center;
            /* Căn giữa nội dung */
            color: red;
            /* Màu chữ */
        }
    </style>
</head>

<body class="contact-us">
    <header>
        <div class="page-header min-vh-85">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-lg mt-5">
                            <!-- Tiêu đề -->
                            <div
                                class="card-header text-center bg-gradient-primary text-white">
                                <h3 style="color: white;" class=" mb-0 "><?= $translations['submit_request'] ?></h3>
                                <small id="requestTime" class="d-block mt-1">
                                    <?= $translations['request_time'] ?> <strong><?= htmlspecialchars($request['sr_created_at']) ?></strong>
                                </small>
                            </div>

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
                                <form id="support-form" method="post" action="approval_handler.php" autocomplete="off">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request_id) ?>">
                                    <input type="hidden" name="department" value="<?= htmlspecialchars($request['department']) ?>">
                                    <div class="row  mb-3">
                                        <!-- Thông tin người yêu cầu -->
                                        <div class="col-md-5">
                                            <h5 class="text-primary mb-3"><?= $translations['requester_info'] ?></h5>
                                            <p><?= $translations['full_name'] ?> <?= htmlspecialchars($request['sender_name']) ?></p>
                                            <p><?= $translations['employee_id'] ?><?= htmlspecialchars($request['manv']) ?></p>
                                            <p><?= $translations['email'] ?> <?= htmlspecialchars($request['email']) ?></p>
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
                                                <label for="fullName" class="form-label"><?= $translations['employee_id'] ?></label>
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



                                    </div>
                                    <div class="col-md-12 mb-3">


                                        <?php if (!empty($request['sr_attachment'])): ?>
                                            <label for="attachment" class="form-label"><?= $translations['attachment'] ?> (Optional)</label>
                                            <!-- Hiển thị file đã tải lên -->
                                            <p>
                                                <!-- <strong><?= $translations['uploaded_file'] ?></strong> -->
                                                <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $request['sr_attachment'])): ?>
                                                    <!-- Nếu là hình ảnh -->
                                                    <img src="<?= htmlspecialchars($request['sr_attachment']) ?>" alt="sr_attachment" style="max-width: 100px; height: auto;">
                                                <?php else: ?>
                                                    <!-- Nếu là file Excel -->
                                                    <a href="<?= htmlspecialchars($request['sr_attachment']) ?>" target="_blank">
                                                        <?= htmlspecialchars(basename($request['sr_attachment'])) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Input để tải lên file mới -->
                                    </div>



                            </div>

                            <div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true" data-bs-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="supportModalLabel"><?= $translations['manager_note'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="requestDetails" class="form-label"><?= $translations['manager_note'] ?></label>
                                                <textarea class="form-control" id="notes" name="notes" rows="5" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $translations['decline'] ?></button>
                                            <!-- Nút submit -->
                                            <button type="submit" id="modalSubmitButton" class="btn btn-primary"><?= $translations['send'] ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút gửi -->
                            <!-- <div class="text-center mt-4">
        <button type="button" class="btn btn-success" onclick="setApprovalStatus('Manager Approved')">
            <i class="fas fa-check"></i> Đồng ý
        </button>
        <button type="button" class="btn btn-danger" onclick="setApprovalStatus('Rejected')">
            <i class="fas fa-times"></i> Từ chối
        </button>
    </div> -->
                            <?php
                            $approval_status = $request['approval_status'] ?? ''; // Lấy trạng thái phê duyệt từ cơ sở dữ liệu

                            if ($approval_status !== 'Manager Approved' && $approval_status !== 'Final Approved' && $approval_status !== 'Completed'    && $approval_status !== 'Rejected'): ?>
                                <div class="text-center">
                                    <button data-bs-toggle="modal"
                                        data-bs-target="#supportModal" type="submit" name="approval_status" value="Manager Approved" onclick="setApprovalStatus('Manager Approved')" class="btn btn-success">
                                        <i class="fas fa-check"></i> <?= $translations['agree'] ?>
                                    </button>
                                    <button data-bs-toggle="modal"
                                        data-bs-target="#supportModal" type="submit" name="approval_status" value="Rejected" onclick="setApprovalStatus('Rejected')" class="btn btn-danger">
                                        <i class="fas fa-times"></i> <?= $translations['agree'] ?>
                                    </button>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-success"><?= $translations['request_processed'] ?></p>
                            <?php endif; ?>


                            <!-- Input ẩn để lưu trạng thái approval_status -->
                            <input type="hidden" name="approval_status" id="approvalStatus">
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </header>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setApprovalStatus(status) {
        // Gán trạng thái vào input ẩn
        document.getElementById('approvalStatus').value = status;

        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('supportModal'));
        modal.show();
    }
</script>

</html>