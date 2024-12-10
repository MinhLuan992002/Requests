<?php

// Kết nối cơ sở dữ liệu
$pdo = new PDO('mysql:host=localhost:3309;dbname=requestsapp', 'root', '');

// Lấy ngôn ngữ mặc định từ cơ sở dữ liệu (nếu người dùng đã đăng nhập)
if (isset($_SESSION['manv'])) {
    $userId = $_SESSION['manv']; // Lấy mã nhân viên từ session
    $stmt = $pdo->prepare("SELECT language_code FROM users WHERE manv = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ngôn ngữ mặc định của người dùng
    $defaultLang = $user ? $user['language_code'] : 'en';
} else {
    $defaultLang = 'en'; // Mặc định là tiếng Anh nếu chưa đăng nhập
}

// Kiểm tra ngôn ngữ từ URL hoặc Session (ưu tiên URL > Session > Mặc định)
if (isset($_GET['lang'])) {
    $currentLang = $_GET['lang']; // Ngôn ngữ được chọn từ URL
    $_SESSION['lang'] = $currentLang; // Lưu vào session
} elseif (isset($_SESSION['lang'])) {
    $currentLang = $_SESSION['lang']; // Ngôn ngữ từ session
} else {
    $currentLang = $defaultLang; // Nếu không có URL hoặc session, dùng mặc định
}

// Cập nhật ngôn ngữ mặc định của người dùng khi họ chọn ngôn ngữ mới
if (isset($_GET['lang']) && isset($_SESSION['manv'])) {
    $stmt = $pdo->prepare("UPDATE users SET language_code = ? WHERE manv = ?");
    $stmt->execute([$currentLang, $userId]);
}

// Lấy tệp ngôn ngữ tương ứng
$langFile = "lang/$currentLang.php";
if (file_exists($langFile)) {
    $translations = include($langFile);
} else {
    $translations = include('lang/en.php'); // Mặc định là tiếng Anh
}

// Nếu cần lấy các bản dịch động từ cơ sở dữ liệu
if ($currentLang != 'en') {
    $stmt = $pdo->prepare("SELECT `key`, `value` FROM translations WHERE language_code = ?");
    $stmt->execute([$currentLang]);
    $translationsFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tạo ánh xạ các bản dịch từ cơ sở dữ liệu
    $dbTranslationMap = [];
    foreach ($translationsFromDb as $translation) {
        $dbTranslationMap[$translation['key']] = $translation['value'];
    }

    // Hợp nhất các bản dịch tĩnh và động
    $translations = array_merge($translations, $dbTranslationMap);
}
?>
