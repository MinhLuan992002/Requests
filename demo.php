<?php
include 'config/config.php';
include 'set_language.php';

// Ngôn ngữ cần dịch, lấy từ URL hoặc session
$language = 'vi';

// Lấy tất cả các mức độ ưu tiên
$stmt = $pdo->query("SELECT level_value, level_name, id FROM priority_levels WHERE isDeleted = 0 AND isActive = 1 ORDER BY level_value ASC");
$priorityLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy tất cả bản dịch cho các key
$keys = [];
foreach ($priorityLevels as $priority) {
    $keys[] = 'priority_' . strtolower($priority['level_name']);
}

// Lấy bản dịch từ bảng translations cho tất cả các key
$placeholders = implode(',', array_fill(0, count($keys), '?'));
$stmt = $pdo->prepare("SELECT `key`, value FROM translations WHERE `key` IN ($placeholders) AND language_code = ?");
$stmt->execute(array_merge($keys, [$language]));
$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tạo mảng dịch từ kết quả
$translationMap = [];
foreach ($translations as $translation) {
    $translationMap[$translation['key']] = $translation['value'];
}

// Debug: Kiểm tra mảng translations và translationMap
echo '<pre>';
var_dump($translations);
echo '</pre>';
echo '<pre>';
var_dump($translationMap);
echo '</pre>';

// Cập nhật lại tên mức độ ưu tiên với bản dịch nếu có
// Giả sử bạn đã có $translationMap chứa các key và value dịch

foreach ($priorityLevels as &$priority) {  // Lưu ý dấu & để tham chiếu trực tiếp phần tử trong mảng
  // Tạo key cho bảng translations, ví dụ: 'priority_urgent', 'priority_high'...
  $key = 'priority_' . strtolower($priority['level_name']);
  
  // Debug: In key và kiểm tra xem có bản dịch hay không
  echo 'Checking key: ' . $key . '<br>';

  // Kiểm tra xem trong translationMap có key này không
  if (isset($translationMap[$key])) {
      // In ra giá trị ban đầu và giá trị đã thay đổi (nếu có bản dịch)
      echo 'Changing level_name from ' . $priority['level_name'] . ' to ' . $translationMap[$key] . '<br>';
      
      // Cập nhật giá trị level_name với bản dịch
      $priority['level_name'] = $translationMap[$key];
  } else {
      // Nếu không có bản dịch, giữ nguyên giá trị cũ
      echo 'No translation found for ' . $key . '. Keeping original: ' . $priority['level_name'] . '<br>';
  }
}






// Debug: Kiểm tra lại mảng priorityLevels sau khi thay đổi
echo '<pre>';
var_dump($priorityLevels);
echo '</pre>';


// Bây giờ $priorityLevels đã chứa tên danh mục đã được dịch theo ngôn ngữ đã chọn.
?>

<!-- HTML để hiển thị dropdown -->
<select class="form-select" name="priority[]" required>
    <option value="" selected><?= $translations['select_priority'] ?></option> <!-- Dịch "Select priority" nếu cần -->
    <?php foreach ($priorityLevels as &$priority): ?>
        <option value="<?= htmlspecialchars($priority['id']) ?>">
            <?= htmlspecialchars($priority['level_name']) ?>
        </option>
        <?php echo 'Debug: ' . $priority['level_name'] . '<br>'; // Debug để kiểm tra giá trị ?>
    <?php endforeach; ?>
</select>
