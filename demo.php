<?php
include 'config/config.php';
include 'set_language.php';

// Ngôn ngữ cần dịch, lấy từ session hoặc URL
$language = $_SESSION['language'] ?? 'en';

// 1. Lấy dữ liệu từ bảng priority_levels
$stmt = $pdo->prepare("SELECT level_value, level_name FROM priority_levels WHERE isDeleted = 0 AND isActive = 1 ORDER BY level_value ASC");
$stmt->execute();
$priorityLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Lấy tất cả bản dịch một lần từ bảng translations
$stmt = $pdo->prepare("SELECT `key`, `value` FROM translations WHERE language_code = ?");
$stmt->execute([$language]);
$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tạo map từ key -> value cho bản dịch
$translationMap = [];
foreach ($translations as $translation) {
    $translationMap[$translation['key']] = $translation['value'];
}

// 3. Dịch level_name theo key
foreach ($priorityLevels as &$priority) {
    $key = 'priority_' . $priority['level_value']; // Tạo key
    if (isset($translationMap[$key])) {
        $priority['level_name'] = $translationMap[$key]; // Cập nhật nếu có bản dịch
    }
}
unset($priority); // Xóa tham chiếu

// 4. Hiển thị HTML
?>

<select class="form-select" name="priority[]" required>
    <option value="" selected>Select Priority</option>
    <?php foreach ($priorityLevels as $priority): ?>
        <option value="<?= htmlspecialchars($priority['level_value']) ?>">
            <?= htmlspecialchars($priority['level_name']) ?>
        </option>
    <?php endforeach; ?>
</select>
