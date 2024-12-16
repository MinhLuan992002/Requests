<?php


// Kết nối cơ sở dữ liệu
$pdo = new PDO('mysql:host=localhost:3309;dbname=requestsapp', 'root', '');

// Danh sách ngôn ngữ hỗ trợ với thông tin lá cờ và tên
$supportedLangs = [
    'en' => ['name' => 'English (US)', 'flag' => 'US.png'],
    'vi' => ['name' => 'Việt Nam (VI)', 'flag' => 'VN.png'],
    'jp' => ['name' => '日本語 (JP)', 'flag' => 'JP.png']
];

// Ngôn ngữ mặc định
$lang = 'en'; // Mặc định là tiếng Anh

// Lấy ngôn ngữ từ profile người dùng nếu đã đăng nhập
if (isset($_SESSION['manv'])) {
    $userId = $_SESSION['manv'];
    $stmt = $pdo->prepare("SELECT language_code FROM users WHERE manv = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && array_key_exists($user['language_code'], $supportedLangs)) {
        $lang = $user['language_code']; // Ngôn ngữ từ profile
    }
}

// Kiểm tra ngôn ngữ được chọn qua giao diện (URL)
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $supportedLangs)) {
    $lang = $_GET['lang']; // Ngôn ngữ từ URL
    $_SESSION['lang'] = $lang; // Lưu tạm vào session
}

// Nếu có session lưu ngôn ngữ, ưu tiên session
if (isset($_SESSION['lang']) && array_key_exists($_SESSION['lang'], $supportedLangs)) {
    $lang = $_SESSION['lang'];
}

// Lấy thông tin cờ và tên ngôn ngữ hiện tại
$currentFlag = $supportedLangs[$lang]['flag'];
$currentLangName = $supportedLangs[$lang]['name'];

// Lấy tệp ngôn ngữ tương ứng
$langFile = "lang/$lang.php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    $translations = include('lang/en.php'); // Mặc định tiếng Anh
}
?>
