<?php
require_once './lib/Database.php';
include 'notifications/notifications.php';
include 'set_language.php';
session_start(); // Bắt đầu phiên


// Thông tin LDAP server
$ldap_host = "ldap://ctmatsuyard.com";
$ldap_port = 389;
$ldap_dn = "OU=Staff,DC=ctmatsuyard,DC=com";

// Lấy dữ liệu từ form đăng nhập
$username = $_POST['username'];
$password = $_POST['password'];
$isDeleted = 0;
$isActive = 1;
$manv = '';
$code = 'Employee';
$userType = 'Employee';

// Kết nối đến LDAP server
$ldap_conn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ldap_conn) {
    // Chuẩn bị thông tin đăng nhập với tên domain
    $ldap_user = "$username@ctmatsuyard.com";

    // Xác thực với LDAP
    $ldap_bind = @ldap_bind($ldap_conn, $ldap_user, $password);

    if ($ldap_bind) {
        echo "LDAP bind successful!";
    } else {
        
        if (isset($_GET['lang'])) {
            $lang = $_GET['lang'];
        } else {
            $lang = 'en'; // Ngôn ngữ mặc định
        }
        
        $_SESSION['error_message'] = $translations['auth_failed'];
        
        // Chuyển hướng về trang login với tham số `lang`
        header("Location: login.php?lang=" . $lang);
        exit(); // Dừng thực thi mã sau khi chuyển hướng// Dừng thực thi mã sau khi chuyển hướng
    }
    

    if ($ldap_bind) {
        // Tìm kiếm `sAMAccountName`, `displayName`, và `description` từ LDAP
        $filter = "(|(sAMAccountName=$username)(displayName=$username))";
        $attributes = ["sAMAccountName", "displayName", "description","mail"];
        $result = ldap_search($ldap_conn, $ldap_dn, $filter, $attributes);
    
        if ($result) {
            $entries = ldap_get_entries($ldap_conn, $result);
    
            if ($entries["count"] > 0) {
                $manv = isset($entries[0]["samaccountname"][0]) ? $entries[0]["samaccountname"][0] : 'Không có mã nhân viên';
                $displayName = isset($entries[0]["displayname"][0]) ? $entries[0]["displayname"][0] : 'Không có tên hiển thị';
                $description = isset($entries[0]["description"][0]) ? $entries[0]["description"][0] : 'Không có miêu tả';
                $mail = isset($entries[0]["mail"][0]) ? $entries[0]["mail"][0] : 'Không có miêu tả';
                // Hiển thị thông báo chào mừng với thông tin chi tiết
                echo "Xác thực thành công. Chào mừng, " . htmlspecialchars($displayName) . "!<br>";
                echo "Miêu tả: " . htmlspecialchars($description) . "<br>";

                $_SESSION['username'] = $username;
                $_SESSION['displayName'] = $displayName; 
                $_SESSION['manv'] = $manv; 
                $_SESSION['department'] = $description; 
                $_SESSION['mail'] = $mail; 
                $_SESSION['code'] = $code; 
                try {
                    $db = new Database('localhost:3309', 'requestsapp', 'root', '');
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $procedureName = 'sp_insert_update_user_master';
                    $params = [$manv, $displayName, $username, $hashed_password, $isDeleted, $isActive, $code, $userType,$description,$mail];
                    $result = $db->call($procedureName, $params);
    
                    if ($result > 0) {
                        // Chuyển hướng đến trang index
                        if (isset($_GET['lang'])) {
                            $lang = $_GET['lang'];
                        } else {
                            $lang = 'en'; // Ngôn ngữ mặc định
                        }
                        
                        header("Location: approval_list.php?lang=" . $lang);
                        exit;
                    } else {
                        echo "<script>
                            showErrorNotification('Không thể lưu thông tin người dùng!');
                        </script>";
                    }
    
                } catch (PDOException $e) {
                    echo "Lỗi kết nối đến cơ sở dữ liệu: " . $e->getMessage();
                }
            } else {
                echo "Không tìm thấy thông tin người dùng.";
            }
        } else {
            echo "Lỗi khi tìm kiếm trong LDAP.";
        }
    }
     else {
        echo "<script>
        showErrorNotification('Xác thực thất bại. Vui lòng kiểm tra tên đăng nhập và mật khẩu.');

    </script>";
    }

    ldap_close($ldap_conn);
} else {
    echo "Không thể kết nối đến LDAP server.";
}

?>
