<?php
include 'set_language.php';
include './notifications/notifications.php';
include 'config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Nếu bạn sử dụng Composer
$mail = new PHPMailer(true);
// Hàm gửi email
function sendEmail($to, $subject, $body, $mail)
{

    try {
        // Cấu hình gửi mail

        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'it_support@vnmatsuya.com'; // Tài khoản email gửi đi
        $mail->Password = 'support@456'; // Mật khẩu email gửi đi
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Thiết lập thông tin người gửi và người nhận
        $mail->setFrom('it_support@vnmatsuya.com', 'Hệ Thống Hỗ Trợ');
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($to); // Người nhận email

        // Thiết lập nội dung email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Gửi email
        $mail->send();
        // echo ('.');
        // echo "<script>
        //         showSuccessNotification('Email đã được gửi thành công!');
        //       </script>";
    } catch (Exception $e) {
        echo 'Email không gửi được. Lỗi: ' . $mail->ErrorInfo;
    }
}

// Kiểm tra nếu dữ liệu 'request_id' có trong POST không
if (isset($_POST['request_id'])) {
    // Kết nối cơ sở dữ liệu và lấy thông tin yêu cầu
    $requestId = $_POST['request_id']; // ID yêu cầu từ form hoặc cơ sở dữ liệu
    $department = $_POST['department'];
    $displayName = $_POST['displayName'];
    $manv = $_POST['manv'];
    $email = $_POST['mail'];

    // người áp dụng 
    $userId = $_POST['userId'];
    $userEmail = $_POST['userEmail'];
    $dept = $_POST['dept'];
    //
    $priorityLevel = $_POST['priority_level'];
    $category = $_POST['category'];  // Đảm bảo biến này được gán đúng
    $content = $_POST['content']; // Nội dung hỗ trợ
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $timestamp = date("Y-m-d H:i:s");
    $desired_time = $_POST['desired_time'];
    $notes = $_POST['notes'];
    // Lấy thông tin người dùng theo phòng ban
    $stmt = $pdo->prepare("SELECT u.email, u.fullname, ut.ConfigName
                           FROM users u
                           JOIN config ut ON u.UserType = ut.id
                           WHERE u.department = :department and ConfigName='manager' AND u.IsActive = 1 AND u.IsDeleted = 0");
    $stmt->bindParam(':department', $department, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Không tìm thấy thông tin người dùng!";
        exit;
    }
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
                                       signer_manager = :signer_manager, 
                                       notes_manager = :notes_manager,
                                       manager_signed_at = CURRENT_TIMESTAMP

                                   WHERE id = :request_id");

            $stmt->bindParam(':approval_status', $approval_status, PDO::PARAM_STR);
            $stmt->bindParam(':manager_approval', $manager_approval, PDO::PARAM_INT);
            $stmt->bindParam(':signer_manager', $signer_manager, PDO::PARAM_STR);
            $stmt->bindParam(':notes_manager', $notes, PDO::PARAM_STR);
            $stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);

            $stmt->execute();

            echo ".";
            echo "<script>
            showSuccessNotification('Yêu cầu đã được " . ($approval_status == 'Manager Approved' ? 'phê duyệt' : 'từ chối') . " thành công.');
          </script>";
        } catch (PDOException $e) {
            // In ra thông báo lỗi chi tiết
            echo "Có lỗi xảy ra khi xử lý yêu cầu: " . $e->getMessage();
            error_log("Lỗi SQL: " . $e->getMessage());
        }
    }

    $approvalLink = "http://192.168.16.251:8009/requests/final_approver.php?request_id=" . urlencode($requestId);
    $details = "http://192.168.16.251:8009/requests/approval_list.php?lang=en";
    // Truy vấn lấy thông tin yêu cầu từ bảng support_requests
    $query = "SELECT * FROM support_requests WHERE id = :requestId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có dữ liệu yêu cầu

    if ($request) {
        $sql = $pdo->prepare("SELECT u.email, u.fullname, ut.ConfigName
        FROM users u
        JOIN config ut ON u.UserType = ut.id
        WHERE u.department = :department and ConfigName='Admin' AND u.IsActive = 1 AND u.IsDeleted = 0");
        $sql->bindParam(':department', $department, PDO::PARAM_STR);
        $sql->execute();
        $Final = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$Final) {
            echo "Không tìm thấy thông tin người dùng!";
            exit;
        }
        $userEmail = $email;
        $status = $request['approval_status']; // Trạng thái yêu cầu


        // $managerEmail = $request['manager_email']; // Email trưởng phòng (nếu có trường này trong bảng của bạn)
        // $directorEmail = $request['director_email']; // Email giám đốc (nếu có trường này trong bảng của bạn)
        $managerEmail = 'minhluan@vnmatsuya.com';
        $directorEmail = $Final['email'];


        // echo $directorEmail;
        // Xử lý trạng thái phê duyệt
        if ($status === 'Manager Approved' && $managerEmail) {
            // Nội dung email gửi giám đốc
            $subject = 'Notification: Request Approved';
            $body = '

        <p>Mr. Takano,</p>
        <p>Your support request has been approved by the department head. Please check the detailed information.</p> 
         <p>Thank you.</p>
            <div style="width: 100px; font-family: Arial, sans-serif; line-height: 1.4; color: #333; max-width: 150px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; padding: 6px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <h2 style="color: #007bff;  margin-left: px; margin-bottom: 15px; font-size: 20px;">' . $translations['submit_request'] . '</h2>
            
                <!-- Thông tin người thực hiện -->
                <div style="margin-bottom: 15px;">
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['requester_info'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($manv) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['full_name'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($displayName) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($email) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($department) . '</td>
                        </tr>
                    </table>
                </div>
            
                <!-- Thông tin người áp dụng -->
                <div style="margin-bottom: 15px;">
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['applicant_info'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($userId) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($dept) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($userEmail) . '</td>
                        </tr>
                    </table>
                </div>
            
                <!-- Chi tiết yêu cầu -->
                <div>
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['request_details'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['priority_level'] . '</td>
                            <td style="padding: 8px; color: #d9534f; font-weight: bold;">' . htmlspecialchars($priorityLevel) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['support_category'] . '</td>
                            <td style="padding: 10px;">' . htmlspecialchars(is_array($category) ? implode(', ', $category) : $category) . '</td>

                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['content'] . '</td>
                           <td style="padding: 10px;">' . htmlspecialchars(is_array($content) ? implode(', ', $content) : $content) . '</td>

                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['request_time'] . '</td>
                            <td style="padding: 8px;">' . $timestamp . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['desired_time'] . '</td>
                            <td style="padding: 8px;">' .  htmlspecialchars($desired_time) . '</td>
                        </tr>
                                                <tr>
                <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['manager_note'] . '</td>
                <td style="padding: 8px;">' . htmlspecialchars($notes) . '</td>
            </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['request_details'] . '</td>
                            <td style="padding: 8px;"><a style="font-style: italic; color: #007bff;" href="' . htmlspecialchars($approvalLink) . '">' . $translations['access_now'] . '</a></td>
                        </tr>
                    </table>
                </div>
            
                <p style="text-align: center; margin-top: 20px; color: #555; font-size: 14px;">
                    Thank you for your request. We will respond as soon as possible!
                </p>
            </div>
        ';
            if (!empty($request['attachment'])) {
                $filePath = $request['attachment'];
                // echo "Đường dẫn file: $filePath"; // Kiểm tra đường dẫn
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath); // Đính kèm file
                } else {
                    echo "File không tồn tại: $filePath";
                }
            }


            sendEmail($directorEmail, $subject, $body, $mail);
        } elseif ($status === 'Rejected' && $userEmail) {
            // Nội dung email gửi người yêu cầu
            $subject = 'Notification: Request Declined';
            $body = '

                    <p>Hello, wishing you a great day,</p> 
                    <p>Your support request has been declined. Please contact us or visit the link below for more details.</p> 
                    <p>Thank you.</p>
                                       <div style="width: 100px; font-family: Arial, sans-serif; line-height: 1.4; color: #333; max-width: 150px; margin: 0 auto; border: 1px solid #ddd; border-radius: 10px; padding: 6px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <h2 style="color: #007bff;  margin-left: px; margin-bottom: 15px; font-size: 20px;">' . $translations['submit_request'] . '</h2>
            
                <!-- Thông tin người thực hiện -->
                <div style="margin-bottom: 15px;">
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['requester_info'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($manv) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['full_name'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($displayName) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($email) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($department) . '</td>
                        </tr>
                    </table>
                </div>
            
                <!-- Thông tin người áp dụng -->
                <div style="margin-bottom: 15px;">
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['applicant_info'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['employee_id'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($userId) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['department'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($dept) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['email'] . '</td>
                            <td style="padding: 8px;">' . htmlspecialchars($userEmail) . '</td>
                        </tr>
                    </table>
                </div>
            
                <!-- Chi tiết yêu cầu -->
                <div>
                    <h3 style="color: #007bff; margin-bottom: 8px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">' . $translations['request_details'] . '</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold; width: 30%;">' . $translations['priority_level'] . '</td>
                            <td style="padding: 8px; color: #d9534f; font-weight: bold;">' . htmlspecialchars($priorityLevel) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['support_category'] . '</td>
                            <td style="padding: 10px;">' . htmlspecialchars(is_array($category) ? implode(', ', $category) : $category) . '</td>

                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['content'] . '</td>
                           <td style="padding: 10px;">' . htmlspecialchars(is_array($content) ? implode(', ', $content) : $content) . '</td>

                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['request_time'] . '</td>
                            <td style="padding: 8px;">' . $timestamp . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #f7f7f7; font-weight: bold;">' . $translations['desired_time'] . '</td>
                            <td style="padding: 8px;">' .  htmlspecialchars($desired_time) . '</td>
                        </tr>
                                                <tr>
                <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['manager_note'] . '</td>
                <td style="padding: 8px;">' . htmlspecialchars($notes) . '</td>
            </tr>
                        <tr>
                            <td style="padding: 8px; background-color: #ffffff; font-weight: bold;">' . $translations['request_details'] . '</td>
                            <td style="padding: 8px;"><a style="font-style: italic; color: #007bff;" href="' . htmlspecialchars($details) . '">' . $translations['access_now'] . '</a></td>
                        </tr>
                    </table>
                </div>
            
                <p style="text-align: center; margin-top: 20px; color: #555; font-size: 14px;">
                    Thank you for your request. We will respond as soon as possible!
                </p>
            </div>
            ';
            sendEmail($directorEmail, $subject, $body, $mail);
        }
    } else {
        echo 'Không tìm thấy yêu cầu trong cơ sở dữ liệu.';
    }
} else {
    echo 'Không có tham số "request_id" trong yêu cầu.';
}
