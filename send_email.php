<?php
include './notifications/notifications.php';
include 'config/config.php';
include 'set_language.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Nếu bạn sử dụng Composer

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Thu thập dữ liệu từ form
    $displayName = $_POST['displayName'];
    $manv = $_POST['manv'];
    $email = $_POST['mail'];
    $department = $_POST['department'];
    // người áp dụng 
    $userId = $_POST['userId'];
    $userEmail = $_POST['userEmail'];
    $dept = $_POST['dept'];
    $priorityLevel = $_POST['priority_level'];
    $category = $_POST['category'];  // Đảm bảo biến này được gán đúng
    $content = $_POST['content']; // Nội dung hỗ trợ

    $desired_time = !empty($_POST['desired_time']) ? $_POST['desired_time'] : null;; // Nội dung hỗ trợ
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $timestamp = date("Y-m-d H:i:s");
    $department = $_POST['department'];
    // Kiểm tra và xử lý tệp đính kèm
    $attachmentPath =  $_POST['attachment'] ?? null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = $_FILES['attachment']['name'];
        $fileSize = $_FILES['attachment']['size'];
        $fileType = $_FILES['attachment']['type'];

        // Thư mục nơi bạn muốn lưu tệp (tạo thư mục uploads nếu chưa có)
        $uploadDir = 'uploads/';
        $destination = $uploadDir . $fileName;

        // Kiểm tra loại tệp (chấp nhận hình ảnh và tệp Excel)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($fileType, $allowedTypes)) {
            // Di chuyển tệp vào thư mục đích
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $attachmentPath = $destination; // Lưu đường dẫn tệp vào biến
            } else {
                echo 'Có lỗi khi tải tệp lên.';
                exit;
            }
        } else {
            echo 'Chỉ chấp nhận hình ảnh (JPEG, PNG, GIF) và tệp Excel (XLS, XLSX).';
            exit;
        }
    }

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Thu thập dữ liệu từ form
        $displayName = $_POST['displayName'];
        $manv = $_POST['manv'];
        $email = $_POST['mail'];
        // Người áp dụng
        $userId = $_POST['userId'];
        $userEmail = $_POST['userEmail'];
        $dept = $_POST['dept'];
        $priorityLevel = $_POST['priority_level'];
        $category = $_POST['category'];  // Mảng danh mục
        $content = $_POST['content'];    // Mảng nội dung hỗ trợ

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $timestamp = date("Y-m-d H:i:s");
        // Kiểm tra và xử lý tệp đính kèm

        // Chuyển mảng `category` và `content` thành chuỗi JSON
        // $categoryJson = json_encode($category);  // Mảng danh mục thành chuỗi JSON
        // $contentJson = json_encode($content);    // Mảng nội dung thành chuỗi JSON
        $categoryJson = json_encode($category, JSON_UNESCAPED_UNICODE);
        $contentJson = json_encode($content, JSON_UNESCAPED_UNICODE);

        // Chèn dữ liệu vào bảng yêu cầu hỗ trợ
        $stmt = $pdo->prepare("INSERT INTO support_requests (manv, priority_level, category, content, sender_name, userId, dept, userEmail, desired_time, attachment) 
VALUES (:manv, :priority_level, :category, :content, :sender_name, :userId, :dept, :userEmail, :desired_time, :attachment)");

        // Thực thi câu lệnh với dữ liệu được gán vào các tham số
        $stmt->execute([
            ':manv' => $manv,               // Mã nhân viên
            ':priority_level' => $priorityLevel, // Cấp độ ưu tiên
            ':category' => $categoryJson,   // Lưu mảng danh mục dưới dạng JSON
            ':content' => $contentJson,     // Lưu mảng nội dung dưới dạng JSON
            ':sender_name' => $displayName, // Tên người gửi
            ':userId' => $userId,
            ':dept' => $dept,
            ':userEmail' => $userEmail,    // Lưu đúng email người dùng
            ':desired_time' => $desired_time,
            ':attachment' => $attachmentPath, // Lưu đường dẫn tệp vào cơ sở dữ liệu
        ]);

        // Lấy ID yêu cầu vừa chèn
        $request_id = $pdo->lastInsertId();


        // Tạo link phê duyệt
        $approvalLink = "http://192.168.16.251:8009/requests/approve_request.php?request_id=" . urlencode($request_id);

        // echo "Yêu cầu hỗ trợ đã được gửi thành công!";
    } catch (PDOException $e) {
        echo "Lỗi cơ sở dữ liệu: {$e->getMessage()}";
        exit;
    } catch (Exception $e) {
        echo "Lỗi: {$e->getMessage()}";
        exit;
    }


    // Khởi tạo PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Truy vấn email của trưởng bộ phận hoặc giám đốc từ cơ sở dữ liệu
        $stmt = $pdo->prepare("
            SELECT u.email, ut.ConfigName
            FROM users u
            JOIN config ut ON u.UserType = ut.id
            WHERE u.department = :department AND u.IsActive = 1 AND u.IsDeleted = 0
        ");
        $stmt->execute(['department' => $department]);
        $users = $stmt->fetchAll();

        $managerEmail = null;
        $directorEmail = null;

        // Duyệt qua danh sách người dùng để phân loại trưởng bộ phận và giám đốc
        foreach ($users as $user) {
            if ($user['ConfigName'] === 'manager') {
                $managerEmail = $user['email'];  // Email trưởng bộ phận
            }
            if ($user['ConfigName'] === 'director') {
                $directorEmail = $user['email'];  // Email giám đốc
            }
        }

        // Kiểm tra nếu có trưởng bộ phận và giám đốc để gửi email
        if ($managerEmail) {
            $mail->addAddress($managerEmail, 'Trưởng bộ phận ' . htmlspecialchars($department)); // Email trưởng bộ phận
        }
        if ($directorEmail) {
            $mail->addAddress($directorEmail, 'Giám đốc'); // Email giám đốc
        }

        // Cấu hình SMTP của Outlook
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // SMTP server của Outlook
        $mail->Username = 'it_support@vnmatsuya.com'; // Thay bằng email của bạn
        $mail->Password = 'support@456'; // Mật khẩu email của bạn
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Thiết lập mã hóa UTF-8
        $mail->CharSet = 'UTF-8';

        // Người gửi và người nhận
        $mail->setFrom('it_support@vnmatsuya.com', 'Hệ Thống Hỗ Trợ');

        // Tiêu đề email
        $mail->isHTML(true);
        $mail->Subject = '[' . htmlspecialchars($priorityLevel) . '] Yêu cầu hỗ trợ về: ' .
            (is_array($content) ? htmlspecialchars(implode(', ', $content)) : htmlspecialchars($content));


        // Nội dung email
        $mail->Body = '
        <p>Hello,</p>
<p>The system has just sent a notification request. Please check the detailed information below.</p>
<p>Thank you.</p>

<div style="font-family: Arial, sans-serif; line-height: 1.8; color: #333; max-width: 800px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h2 style="color: #007bff; text-align: center; margin-bottom: 20px;">' . $translations['submit_request'] . '</h2>

    <!-- Thông tin người thực hiện -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #007bff; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['requester_info'] . '</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($manv) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ffffff; font-weight: bold;">' . $translations['full_name'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($displayName) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($email) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($department) . '</td>
            </tr>
        </table>
    </div>

    <!-- Thông tin người áp dụng -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #007bff; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['applicant_info'] . '</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($userId) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($dept) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars($userEmail) . '</td>
            </tr>
        </table>
    </div>

    <!-- Chi tiết yêu cầu -->
    <div>
        <h3 style="color: #007bff; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['request_details'] . '</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['priority_level'] . '</td>
                <td style="padding: 10px; color: #d9534f; font-weight: bold;">' . htmlspecialchars($priorityLevel) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ffffff; font-weight: bold;">' . $translations['support_category'] . '</td>
<td style="padding: 10px;">' . htmlspecialchars(is_array($category) ? implode(', ', $category) : $category) . '</td>

            </tr>
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold;">' . $translations['content'] . '</td>
                <td style="padding: 10px;">' . htmlspecialchars(is_array($content) ? implode(', ', $content) : $content) . '</td>

            </tr>
                <td style="padding: 10px; background-color: #ffffff; font-weight: bold;">' . $translations['request_time'] . '</td>
                <td style="padding: 10px;">' . $timestamp . '</td>
            </tr>
                                    <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['request_time'] . '</td>
                            <td style="padding: 8px;">' . $timestamp . '</td>
                        </tr>
            <tr>
                <td style="padding: 10px; background-color: #f7f7f7; font-weight: bold;">' . $translations['request_details'] . '</td>

                <td style="padding: 8px;"><a style="font-style: italic; color: #007bff;" href="' . htmlspecialchars($approvalLink) . '">' . $translations['access_now'] . '</a></td>
            </tr>
        </table>
    </div>

    <p style="text-align: center; margin-top: 30px; color: #555;">
        Thank you for your request. We will respond as soon as possible!
    </p>
</div>

    ';
        // Gửi email
        if ($attachmentPath) {
            echo ($attachmentPath);
            $mail->addAttachment($attachmentPath); // Đính kèm tệp từ đường dẫn
        }
        $mail->send();
        echo ('.');
        echo "<script>
            showSuccessNotification('Yêu cầu hỗ trợ đã được gửi thành công!');
        </script>";
    } catch (Exception $e) {
        echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
    }
}
