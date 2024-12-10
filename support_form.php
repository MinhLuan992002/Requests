<?php include 'config/config.php'; 
include './notifications/notifications.php';
?>

<?php
  session_start(); 
// Kết nối cơ sở dữ liệu
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['priority_level']) && !empty($_POST['category']) && !empty($_POST['content'])) {
            $manv = $_SESSION['manv'];
            $priority_level = $_POST['priority_level'];
            $category = $_POST['category'];
            $content = $_POST['content'];
            $sender_name= $_SESSION['displayName'];
            try {
                // Chuẩn bị câu lệnh INSERT
                $stmt = $pdo->prepare("
                    INSERT INTO support_requests (manv, priority_level, category, content,sender_name)
                    VALUES (:manv, :priority_level, :category, :content,:sender_name)
                ");
                
                // Gắn tham số
                $stmt->bindParam(':manv', $manv);
                $stmt->bindParam(':priority_level', $priority_level);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':content', $content);
                $stmt->bindParam(':sender_name', $sender_name);
                // Thực thi câu lệnh
                if ($stmt->execute()) {
                    $request_id = $pdo->lastInsertId();  // Lấy ID của bản ghi vừa được thêm vào
                    echo "Yêu cầu đã được gửi thành công! ID yêu cầu là: " . $request_id;
                    // echo("hi");
                //     echo "<script>
                //     showSuccessNotification('Thông tin đã lưu thành công!');
                // </script>";
                    exit();
                } else {
                    echo "<script>
                    showErrorNotification('Không thể lưu thông tin!');
                </script>";
                }
            } catch (PDOException $e) {
                echo "Lỗi: " . $e->getMessage(); // Bắt lỗi PDO
            }
        } else {
            echo "<script>
            showErrorNotification('Vui lòng điền đầy đủ thông tin!');
        </script>";
        }
    }
} catch (PDOException $e) {
    echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage(); // Hiển thị lỗi nếu kết nối thất bại
}


// Đóng kết nối PDO
$pdo = null;
?>