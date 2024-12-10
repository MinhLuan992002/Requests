<?php
include 'config/config.php'; // Kết nối cơ sở dữ liệu
include './notifications/notifications.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $requestId = intval($_POST['request_id']);

    // Cập nhật trạng thái approval_status thành Completed
    $sql = "UPDATE support_requests SET approval_status = 'Completed', updated_at = NOW() WHERE id = :request_id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute(['request_id' => $requestId])) {
        echo('.');
        echo "<script>
        showSuccessNotification('Trạng thái của bạn đã câp nhập thành công!');
        setTimeout(function() {
            window.location.href = 'approval_list.php';
        }, 3000); // Chuyển hướng sau 2 giây
      </script>";
    } else {
        echo('.');
        echo "<script>
        showSuccessNotification('Trạng thái của bạn đã câp nhập thành công!');
        setTimeout(function() {
            window.location.href = 'approval_list.php';
        }, 3000); // Chuyển hướng sau 2 giây
      </script>";
    }
} else {
    header('Location: approval_list.php');
    exit();
}
?>
