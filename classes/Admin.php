<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/Database.php');
include_once($filepath . '/../lib/Session.php');
include_once($filepath . '/../helpers/Format.php');

class Admin
{
    private $db;
    private $fm;

    public function __construct()
    {
        $this->db = new Database();
        $this->fm = new Format();
    }

    public function getAdminData($data)
    {
        $adminUser = $data['adminUser'];
        $adminPass = $data['adminPass'];
    
        // Kiểm tra xem tên đăng nhập hoặc mật khẩu có rỗng không
        if (empty($adminUser) || empty($adminPass)) {
            return "<span class='alert alert-danger'>Tên đăng nhập hoặc mật khẩu không được để trống</span>";
        }
    
        try {
            // Truy vấn để lấy thông tin user từ database và lấy thêm bộ phận từ bảng department
            $query = "SELECT users.*, department.name as department_name 
                      FROM users 
                      LEFT JOIN department ON users.department_id = department.id 
                      WHERE username = :username 
                      AND users.IsDeleted = 0 
                      AND users.IsActive = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $adminUser, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "<span class='alert alert-danger'>Lỗi truy vấn: " . $e->getMessage() . "</span>";
        }
    
        // Kiểm tra kết quả và mật khẩu
        if ($result && password_verify($adminPass, $result['password'])) {
            if ($result['Code'] == 'Admin') {  // Kiểm tra quyền truy cập admin (Code = Admin)
                
                // Khởi tạo session nếu chưa khởi tạo
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
    
                // Đặt các thông tin cần thiết vào session
                $_SESSION['adminLogin'] = true;
                $_SESSION['username'] = $result['username'];
                $_SESSION['fullname'] = $result['fullname'];
                $_SESSION['admin_id'] = $result['id'];
                $_SESSION['department'] = $result['department_name'];
                $_SESSION['Code'] = $result['Code'];
    
                // Chuyển hướng tới trang admin
                header('Location: index.php');
                exit();
            } else {
                return "<span class='alert alert-danger'>Bạn không có quyền truy cập admin</span>";
            }
        } else {
            return "<span class='alert alert-danger'>Tên đăng nhập hoặc mật khẩu không chính xác</span>";
        }
    }

    // Phương thức kiểm tra quyền truy cập
    public function authorize($requiredCode, $department_id = null) {
        // Kiểm tra xem người dùng có quyền truy cập không dựa trên giá trị Code
        if ($_SESSION['Code'] == $requiredCode) {
            if ($department_id !== null && $_SESSION['department_id'] != $department_id) {
                return false; // Người dùng không thuộc bộ phận này
            }
            return true; // Có quyền
        }
        return false; // Không có quyền
    }

    // Phương thức thay đổi mật khẩu
    public function changePassword($data) {
        $adminUser = $data['adminUser'];
        $currentPass = $data['currentPass'];
        $newPass = $data['newPass'];
    
        // Kiểm tra các trường nhập có bị rỗng hay không
        if (empty($adminUser) || empty($currentPass) || empty($newPass)) {
            return "<span class='alert alert-danger'>Vui lòng điền đầy đủ thông tin</span>";
        }
    
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $adminUser, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "<span class='alert alert-danger'>Lỗi truy vấn: " . $e->getMessage() . "</span>";
        }
        if ($result && password_verify($currentPass, $result['password'])) {
            try {
                $newPasswordHash = password_hash($newPass, PASSWORD_BCRYPT);
                $updateQuery = "UPDATE users SET password = :newPass WHERE username = :username";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bindValue(':newPass', $newPasswordHash, PDO::PARAM_STR);
                $updateStmt->bindValue(':username', $adminUser, PDO::PARAM_STR);
                $updateStmt->execute();
            } catch (PDOException $e) {
                return "<span class='alert alert-danger'>Lỗi cập nhật mật khẩu: " . $e->getMessage() . "</span>";
            }
    
            return "<span class='success'>Đổi mật khẩu thành công!</span>";
        } else {
            return "<span class='alert alert-danger'>Mật khẩu hiện tại không chính xác</span>";
        }
    }
}
