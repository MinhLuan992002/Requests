<?php
// Kết nối cơ sở dữ liệu
include 'config/config.php'; // Kết nối cơ sở dữ liệu
include './notifications/notifications.php';
if (isset($_GET['request_id'])) {
    $requestId = intval($_GET['request_id']);

    // Truy vấn SQL cập nhật
    $sql = "UPDATE support_requests SET isDeleted = 1, isActive = 0 WHERE id = :request_id";

    // Chuẩn bị và thực thi truy vấn
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['request_id' => $requestId]);

    // Chuyển hướng sau khi xử lý
    echo('.');
    echo "<script>
    showSuccessNotification('Bạn đã xoá thành công!');
    setTimeout(function() {
        window.location.href = 'approval_list.php';
    }, 2000); // Chuyển hướng sau 2 giây
  </script>";
exit();
} else {
echo "<script>
    showErrorNotification('Có lỗi xảy ra!');
    setTimeout(function() {
        window.location.href = 'approval_list.php';
    }, 2000); // Chuyển hướng sau 2 giây
  </script>";
exit();
}
?>
