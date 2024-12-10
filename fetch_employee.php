<?php
include 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manv'])) {
    $manv = trim($_POST['manv']);

    try {
        // Kết nối cơ sở dữ liệu
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Truy vấn thông tin nhân viên
        $stmt = $pdo->prepare("SELECT department, email FROM users WHERE manv = :manv AND IsActive = 1 AND IsDeleted = 0");
        $stmt->execute([':manv' => $manv]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['success' => true, 'department' => $result['department'], 'email' => $result['email']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhân viên']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}
