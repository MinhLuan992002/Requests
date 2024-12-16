<?php
session_start();
include 'notifications/notifications.php';
include 'config/config.php';
require_once './lib/Database.php';
// Kiểm tra nếu form được gửi qua POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['manv']; // Lấy ID người dùng từ session
    $language = $_POST['language'] ?? 'en'; // Ngôn ngữ mặc định là 'en'
    
    // Xử lý upload avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarTmpPath = $_FILES['avatar']['tmp_name'];
        $avatarName = $_FILES['avatar']['name'];
        $avatarExtension = pathinfo($avatarName, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Kiểm tra định dạng file
        if (in_array(strtolower($avatarExtension), $allowedExtensions)) {
            $avatarNewName = "avatar_$userId." . $avatarExtension; // Đổi tên file
            $uploadDir = 'uploads/avatars/'; // Thư mục lưu trữ avatar
            $avatarUploadPath = $uploadDir . $avatarNewName;

            // Di chuyển file vào thư mục upload
            if (move_uploaded_file($avatarTmpPath, $avatarUploadPath)) {
                $avatarPath = $avatarUploadPath; // Đường dẫn lưu vào DB
            } else {
                die("Không thể tải lên ảnh đại diện.");
            }
        } else {
            die("Định dạng ảnh không hợp lệ.");
        }
    } else {
        $avatarPath = null; // Nếu không upload ảnh
    }

    // Cập nhật dữ liệu vào database
    $db = new Database();
    $query = "UPDATE users SET language_code = :language, avatar = :avatar WHERE manv = :id";
    $params = [
        ':language' => $language,
        ':avatar' => $avatarPath,
        ':id' => $userId,
    ];
    $result = $db->execute($query, $params);

    if ($result) {
        echo('.');
        echo "<script>
                showSuccessNotification('Yêu cầu đã được thực hiện thành công!');
              </script>";
    } else {
        echo "Cập nhật thất bại.";
    }
}
?>
