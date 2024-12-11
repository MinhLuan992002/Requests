<?php
include 'config/config.php';

if (isset($_GET['category_name'])) {
    $categoryName = $_GET['category_name'];

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy chi tiết danh mục theo tên
        $stmt = $pdo->prepare("SELECT field_type, options, placeholder FROM support_categories WHERE category_name = :name");
        $stmt->execute([':name' => $categoryName]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra kết quả
        if ($category) {
            header('Content-Type: application/json');
            echo json_encode($category);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Danh mục không tồn tại']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
} else {
    echo json_encode(['error' => 'Thiếu category_name']);
}
