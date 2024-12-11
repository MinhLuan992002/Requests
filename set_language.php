<?php


// Kết nối cơ sở dữ liệu
$pdo = new PDO('mysql:host=localhost:3309;dbname=requestsapp', 'root', '');

// Biến ngôn ngữ
$lang = 'en'; // Mặc định là tiếng Anh

// Lấy ngôn ngữ từ cơ sở dữ liệu nếu người dùng đã đăng nhập
if (isset($_SESSION['manv'])) {
    $userId = $_SESSION['manv'];
    $stmt = $pdo->prepare("SELECT language_code FROM users WHERE manv = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && isset($user['language_code'])) {
        $lang = $user['language_code']; // Ngôn ngữ mặc định từ DB
    }
}

// Kiểm tra ngôn ngữ được chọn tạm thời qua URL (chỉ thay đổi phiên làm việc)
if (isset($_GET['lang'])) {
    $lang = $_GET['lang']; // Ưu tiên ngôn ngữ URL
    $_SESSION['lang'] = $lang; // Cập nhật vào session cho tạm thời
}

// Lấy tệp ngôn ngữ tương ứng
$langFile = "lang/$lang.php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    $translations = include('lang/en.php'); // Mặc định là tiếng Anh
}

// Hiển thị ngôn ngữ hiện tại (để kiểm tra)
// echo "Ngôn ngữ hiện tại: $lang";
?>
