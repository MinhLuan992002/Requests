<?php
include 'config/config.php';

if (isset($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);

    try {
        // Kết nối cơ sở dữ liệu
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
            DB_USER, 
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy chi tiết danh mục
        $stmt = $pdo->prepare("SELECT field_type, options, placeholder FROM support_categories WHERE id = :id");
        $stmt->execute([':id' => $categoryId]);
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
        // Xử lý lỗi cơ sở dữ liệu
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Đã xảy ra lỗi, vui lòng thử lại sau.']);
    }
} else {
    echo json_encode(['error' => 'Thiếu category_id']);
}
