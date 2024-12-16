<?php

require_once './lib/Database.php';

?>
<?php
// Tạo đối tượng Database
$db = new Database();

// Lấy ID người dùng từ session
$userId = $_SESSION['manv'] ?? null;

if ($userId) {
  // Truy vấn để lấy đường dẫn ảnh đại diện
  $query = "SELECT * FROM users WHERE manv = :id";
  $params = [':id' => $userId];
  $result = $db->fetch($query, $params);

  // Kiểm tra nếu có kết quả
  if ($result && isset($result['avatar'])) {
    $avatarPath = $result['avatar']; // Lấy đường dẫn ảnh từ DB
  } else {
    $avatarPath = 'uploads/avatars/default-avatar.png'; // Đường dẫn ảnh mặc định
  }
} else {
  die("Không tìm thấy người dùng.");
}


$lang = $result['language_code'] ?? 'en'; // Nếu không có, mặc định là 'en'
?>

<!-- Hiển thị ảnh đại diện -->


<!-- Hiển thị ảnh đại diện -->


<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin cá nhân</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .modal-header {
      background-color: #ff6f00;
      color: white;
    }

    .avatar-wrapper {
      position: relative;
      display: inline-block;
      margin-bottom: 15px;
    }

    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 3px solid #ff6f00;
      object-fit: cover;
      transition: all 0.3s ease;
    }

    .avatar:hover {
      opacity: 0.8;
      cursor: pointer;
    }

    .upload-icon {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #ff6f00;
      color: white;
      border-radius: 50%;
      padding: 5px;
      font-size: 18px;
      cursor: pointer;
    }

    .upload-icon:hover {
      background: #e65b00;
    }

    .dropdown-menu {
      border: 1px solid while;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item img {
      margin-right: 5px;
    }
  </style>
</head>

<body>
  <!-- Button trigger modal -->
  <!-- <button  type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#personalInfoModal">
    Xem thông tin cá nhân
  </button> -->

  <!-- Modal -->
  <div class="modal fade" id="personalInfoModal" tabindex="-1" aria-labelledby="personalInfoModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="personalInfoModalLabel" style="color: white;">Thông tin cá nhân</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="profileForm" method="POST" action="save_profile.php" enctype="multipart/form-data">
            <div class="text-center">
              <div class="avatar-wrapper">
                <img src="<?php echo $avatarPath; ?>" alt="Avatar" id="avatarPreview" class="avatar">
                <label for="avatarUpload" class="upload-icon">
                  <i class="fa fa-camera"></i>
                </label>
                <input type="file" id="avatarUpload" name="avatar" style="display: none;" accept="image/*">
              </div>
            </div>


            <!-- Full Name -->
            <div class="mb-3">
              <label for="fullName" class="form-label"><?= $translations['full_name'] ?></label>
              <input type="text" class="form-control" id="fullName" value="<?= htmlspecialchars($_SESSION['displayName']); ?>" readonly>
            </div>

            <!-- Employee ID -->
            <div class="mb-3">
              <label for="employeeId" class="form-label"><?= $translations['employee_id'] ?></label>
              <input type="text" class="form-control" id="employeeId" value="<?= htmlspecialchars($_SESSION['manv']); ?>" readonly>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label"><?= $translations['email'] ?></label>
              <input type="email" class="form-control" id="email1" value="<?= htmlspecialchars($_SESSION['mail']); ?>" readonly>
            </div>

            <!-- Department -->
            <div class="mb-3">
              <label for="department" class="form-label"><?= $translations['department'] ?></label>
              <input type="text" class="form-control" id="department" value="<?= htmlspecialchars($_SESSION['department']); ?>" readonly>
            </div>

            <!-- Language Selection -->
            <div class="mb-3">
              <label for="language" class="form-label">Chọn ngôn ngữ mặc định:</label>
              <input type="hidden" name="language" id="hiddenLanguageInput" value="<?= $lang; ?>">
              <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <img id="currentFlag" src="img/<?= $lang === 'en' ? 'US' : ($lang === 'vi' ? 'VN' : 'JP'); ?>.png" alt="Flag" style="width: 20px; height: 15px;">
                  <span id="currentLanguage"><?= $lang === 'en' ? 'English (US)' : ($lang === 'vi' ? 'Việt Nam (VI)' : '日本語 (JP)'); ?></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                  <li><a class="dropdown-item" data-lang="en" data-flag="US.png" data-text="English (US)"><img src="img/US.png" alt="US Flag" style="width: 20px; height: 15px;"> English (US)</a></li>
                  <li><a class="dropdown-item" data-lang="jp" data-flag="JP.png" data-text="日本語 (JP)"><img src="img/JP.png" alt="JP Flag" style="width: 20px; height: 15px;"> 日本語 (JP)</a></li>
                  <li><a class="dropdown-item" data-lang="vi" data-flag="VN.png" data-text="Việt Nam (VI)"><img src="img/VN.png" alt="VN Flag" style="width: 20px; height: 15px;"> Việt Nam (VI)</a></li>
                </ul>
              </div>

            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>



          </form>
        </div>

      </div>
    </div>
  </div>
  <script src=".admin/assets/js/core/popper.min.js"></script>
  <script src=".admin/assets/js/core/bootstrap.min.js"></script>

  <script>
    document.getElementById('avatarUpload').addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
  <script>
    document.querySelectorAll('.dropdown-item').forEach(item => {
      item.addEventListener('click', function() {
        // Lấy các giá trị từ data attributes
        const selectedLang = this.getAttribute('data-lang');
        const selectedFlag = this.getAttribute('data-flag');
        const selectedText = this.getAttribute('data-text');

        // Cập nhật giao diện của nút dropdown
        document.getElementById('currentFlag').src = `img/${selectedFlag}`;
        document.getElementById('currentLanguage').textContent = selectedText;

        // Cập nhật ngôn ngữ vào input hidden (nếu cần gửi form)
        document.getElementById('hiddenLanguageInput').value = selectedLang;

        // (Tuỳ chọn) Gửi yêu cầu lưu vào server qua AJAX hoặc form submit
        console.log("Language selected:", selectedLang); // Debug
      });
    });
  </script>
</body>

</html>