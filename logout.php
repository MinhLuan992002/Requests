<?php
session_start(); // Bắt đầu phiên
session_unset(); // Xóa tất cả các biến phiên
session_destroy(); // Hủy phiên

header("Location: login.php"); // Chuyển hướng về trang đăng nhập
exit; // Dừng thực thi đoạn mã tiếp theo
?>
