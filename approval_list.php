<?php include 'inc/header.php'; ?>
<?php include 'config/config.php';
include 'set_language.php';

?>
<?php
// Kết nối cơ sở dữ liệu
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Truy vấn danh sách yêu cầu
// Giả sử bạn đã có kết nối PDO $pdo
$manv = $_SESSION['manv'];  // Thay thế bằng mã nhân viên hiện tại

// Gọi stored procedure
$sql = $pdo->prepare("CALL requestsapp.GetRequestsByUserType(:manv)");

// Gắn giá trị cho tham số đầu vào
$sql->bindParam(':manv', $manv, PDO::PARAM_STR);

// Thực thi câu lệnh
$sql->execute();
// Lấy kết quả trả về
$requests = $sql->fetchAll(PDO::FETCH_ASSOC);
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (!isset($_SESSION['manv']) || !isset($_SESSION['displayName'])) {
    header("Location: login.php");
    exit();
  }
  $employeeId = $_SESSION['manv'];
  $employeeName = $_SESSION['displayName'];
  $stmt = $pdo->prepare("
  SELECT u.email, ut.ConfigName
  FROM users u
  JOIN config ut ON u.UserType = ut.id
  WHERE u.department = :department and manv= :manv  AND u.IsActive = 1 AND u.IsDeleted = 0
");
  $stmt->execute([
    'department' =>  $_SESSION['department'],
    'manv' =>  $_SESSION['manv']
  ]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
$lang= 'ja';
 

  $stmt = $pdo->query("SELECT level_value, level_name FROM priority_levels WHERE isDeleted = 0 AND isActive = 1 ORDER BY level_value ASC");
  $priorityLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Lấy bản dịch cho các danh mục từ bảng translations
  foreach ($priorityLevels as &$priority) {
      // Tạo key cho priority_name theo định dạng 'priority_camera', 'priority_user', ...
      $key = 'priority_' . strtolower($priority['level_name']); // Đảm bảo key đúng với bảng translations
  

  
      // Lấy bản dịch từ bảng translations
      $stmt = $pdo->prepare("SELECT value FROM translations WHERE `key` = ? AND language_code = ?");
      $stmt->execute([$key,$lang]);
      $translation = $stmt->fetch(PDO::FETCH_ASSOC);
      

  
      // Nếu có bản dịch, thay đổi giá trị level_name thành bản dịch, nếu không giữ nguyên giá trị gốc
      if ($translation) {
          $priority['level_name'] = $translation['value'];
      }}

  
// Lấy danh mục từ bảng support_categories
$stmt = $pdo->query("SELECT id, category_name FROM support_categories WHERE isDeleted = 0 AND isActive = 1");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: kiểm tra dữ liệu danh mục
// echo '<pre>';
// var_dump($categories);
// echo '</pre>';

} catch (PDOException $e) {
  echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage(); // Hiển thị lỗi nếu kết nối thất bại
}


foreach ($categories as &$category) {
    // Tạo key cho category_name theo định dạng 'category_camera', 'category_user', ...
    $key = 'category_' . strtolower($category['category_name']); // Đảm bảo key đúng với bảng translations


    // Lấy bản dịch từ bảng translations
    $stmt = $pdo->prepare("SELECT value FROM translations WHERE `key` = ? AND language_code = ?");
    $stmt->execute([$key,$lang]);
    $translation = $stmt->fetch(PDO::FETCH_ASSOC);
    

    // Nếu có bản dịch, thay đổi giá trị category_name thành bản dịch, nếu không giữ nguyên giá trị gốc
    if ($translation) {
        $category['category_name'] = $translation['value'];
    }
}



// Đóng kết nối PDO
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="apple-touch-icon" sizes="76x76" href="./admin/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="./admin/assets/img/favicon.png">
  <title><?= $translations['request_forms'] ?></title>
  <!--     Fonts and icons     -->
  <!-- <link
    href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800"
    rel="stylesheet" /> -->

  <!-- Nucleo Icons -->
  <link rel="stylesheet" href="./css/nucleo-icons.css">
  <link rel="stylesheet" href="./css/nucleo-svg.css">
  <link id="pagestyle" href=" css/soft-design-system.css" rel="stylesheet" />


  <link rel="stylesheet" href="./fontawesome/css/all.min.css">
  <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
  <script src="./js/jquery-3.3.1.min.js"></script>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: large;
    }

    #senderSignature {
      font-family: 'Brush Script MT', cursive;
      /* Phông chữ giống chữ ký */
      font-size: 9px;
      /* Kích thước chữ lớn hơn */
      text-align: center;
      /* Căn giữa nội dung */
      color: red;
      /* Màu chữ */
    }

    .table tbody tr td {
      white-space: nowrap;
      /* Hạn chế xuống dòng trừ khi được chỉ định */
      /* Ẩn nội dung tràn */
      text-overflow: ellipsis;
      /* Hiển thị "..." khi nội dung bị cắt */
      position: relative;
      /* Đảm bảo dropdown không bị ẩn */
    }

    .table tbody tr td .dropdown {
      white-space: normal !important;
      /* Cho phép xuống dòng cho dropdown */
      overflow: visible !important;
      /* Hiển thị menu đầy đủ */
      position: static;
      /* Không bị ảnh hưởng bởi overflow của ô */
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      /* Đặt dropdown ngay dưới nút */
      left: 0;
      z-index: 1050;
    }

    .table tbody tr td.content-cell {
      white-space: normal;
      /* Cho phép xuống dòng */
      max-width: 200px;
      /* Giới hạn chiều rộng của cột */
      overflow: auto;
      /* Hiển thị thanh cuộn nếu cần */
    }

    /* Loại trừ dropdown */
    .table tbody tr td .dropdown {
      white-space: normal !important;
      overflow: visible !important;
      /* Đảm bảo menu không bị cắt */
    }


    .progress-bar {
      height: 8px;
      /* Giảm chiều cao thanh tiến độ */
      border-radius: 4px;
      /* Làm bo góc thanh tiến độ */
    }

    @media (max-width: 768px) {
      .table tbody tr td {
        white-space: normal;
        /* Cho phép xuống dòng */
        max-width: none;
        /* Gỡ bỏ giới hạn chiều rộng */
        overflow: visible;
        /* Hiển thị toàn bộ nội dung */
      }

      .modal-dialog {
        max-width: 95%;
        margin: 10px auto;
      }

    }
  </style>
</head>


<body class="g-sidenav-show  bg-gray-100">
  <!-- <?php include './view/slidenav.php' ?> -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include './view/nav.php' ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4" style="margin-top: 59px;">
      <div class="row" style="padding-bottom: 30px;">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6 class="mb-0"><?= $translations['request_list'] ?></h6>
              <?php include './view/modal_request.php' ?>

            </div>
            <div class="table-responsive p-0">
              <table class="table align-items-center justify-content-center mb-0" style=" overflow: hidden; ">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 col-1 "> <?= $translations['employee_id'] ?></th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 col-2"> <?= $translations['sender_name'] ?></th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2  "><?= $translations['category'] ?></th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $translations['content'] ?></th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 "><?= $translations['approval_status'] ?></th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $translations['completion'] ?></th> <!-- Thanh tiến độ -->
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2"><?= $translations['action'] ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($requests as $request): ?>
                    <?php
                    // Tính phần trăm completion dựa trên approval_status
                    $completionPercent = 0; // Mặc định 0%
                    $progressClass = ''; // Lớp CSS cho thanh tiến độ
                    switch (strtolower($request['approval_status'])) {
                      case 'pending':
                        $completionPercent = 0;
                        $progressClass = 'bg-gradient-warning'; // Màu vàng
                        break;
                      case 'manager approved':
                        $completionPercent = 50;
                        $progressClass = 'bg-gradient-info'; // Màu xanh dương
                        break;
                      case 'final approved':
                        $completionPercent = 75;
                        $progressClass = 'bg-gradient-primary'; // Màu xanh
                        break;
                      case 'completed':
                        $completionPercent = 100;
                        $progressClass = 'bg-gradient-success'; // Màu xanh lá
                        break;
                      default:
                        $completionPercent = 0; // Trạng thái khác mặc định 0%
                        $progressClass = 'bg-gradient-warning'; // Màu vàng
                        break;
                    }
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($request['manv']); ?></td>
                      <td><?= htmlspecialchars($request['sender_name']); ?></td>
                      <?php
                      $category = json_decode($request['category'], true);
                      $content = json_decode($request['content'], true);
                      ?>
                      <td>
                        <?php if (is_array($category)): ?>
                          <?= implode('<br>', array_map('htmlspecialchars', $category)); ?>
                        <?php else: ?>
                          <?= htmlspecialchars($request['category'] ?? 'Không có dữ liệu'); ?>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (is_array($content)): ?>
                          <?= implode('<br>', array_map('htmlspecialchars', $content)); ?>
                        <?php else: ?>
                          <?= htmlspecialchars($request['content'] ?? 'Không có dữ liệu'); ?>
                        <?php endif; ?>
                      </td>

                      <td>
                        <span class="text-xs font-weight-bold">
                          <?= htmlspecialchars($request['approval_status']); ?>
                        </span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold"><?= $completionPercent; ?>%</span>
                          <div class="progress w-100">
                            <div class="progress-bar <?= $progressClass; ?>" role="progressbar" aria-valuenow="<?= $completionPercent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $completionPercent; ?>%;"></div>
                          </div>
                        </div>
                      </td>

                      <td class="align-middle text-center">
                        <div class="dropdown">
                          <button class="btn btn-link text-secondary mb-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-h text-xs"></i> <!-- Dấu ba chấm cho dropdown -->
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                              <a
                                data-bs-toggle="modal"
                                data-bs-target="#supportModal_Completed_<?= $request['id'] ?>"
                                class="dropdown-item text-info"
                                href="#"
                                onclick="updateUrlWithRequestId(<?= $request['id'] ?>)">
                                <i class="fa fa-eye text-xs"></i> <?= $translations['view'] ?>
                              </a>
                            </li>
                            <!-- Nút mở modal -->
                            <form id="requestForm" method="get" action="">
                              <input type="hidden" id="request_id_input" name="request_id" value="">
                            </form>
                            <script>
                              // Hàm kiểm tra và mở modal nếu có request_id trong URL
                              function checkAndOpenModal() {
                                const urlParams = new URLSearchParams(window.location.search);
                                const requestId = urlParams.get('request_id'); // Lấy request_id từ URL
                                // Nếu có request_id trong URL, mở modal tương ứng
                                if (requestId) {
                                  const modalTarget = document.querySelector(`#supportModal_Completed_${requestId}`);
                                  if (modalTarget) {
                                    const modal = new bootstrap.Modal(modalTarget);
                                    modal.show(); // Hiển thị modal tương ứng
                                  }
                                }
                              }
                              // Khi trang được tải hoặc URL thay đổi (popstate), kiểm tra và mở modal
                              document.addEventListener('DOMContentLoaded', function() {
                                checkAndOpenModal(); // Kiểm tra và mở modal khi trang được tải

                                // Kiểm tra khi URL thay đổi (popstate)
                                window.addEventListener('popstate', function() {
                                  checkAndOpenModal();
                                });
                              });
                              // Cập nhật URL với request_id và reload trang
                              function updateUrlWithRequestId(requestId) {
                                const url = new URL(window.location.href);
                                url.searchParams.set('request_id', requestId); // Thêm hoặc cập nhật tham số request_id trong URL
                                window.location.href = url.toString(); // Reload trang với URL mới (sẽ có request_id trong URL)
                              }
                            </script>
                            <?php if (in_array($result['ConfigName'], ['Admin', 'Superadmin'])): ?>
                              <li>
                                <a class="dropdown-item text-danger"
                                  href="delete_request.php?request_id=<?= $request['id']; ?>"
                                  onclick="return confirm('Bạn có chắc muốn xoá yêu cầu này?')">
                                  <i class="fa fa-trash text-xs"></i> <?= $translations['delete'] ?>
                                </a>
                              </li>
                            <?php endif; ?>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php include './view/footer.php' ?>
    </div>
  </main>
  <?php include './view/modal_completed.php' ?>
  <!--   Core JS Files   -->
  <script src="./admin/assets/js/core/popper.min.js"></script>
  <script src="./admin/assets/js/core/bootstrap.min.js"></script>
  <script src="./admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script>
    function fetchEmployeeInfo() {
      const manv = document.getElementById('manv').value;

      // Nếu mã nhân viên không có giá trị, xóa thông tin bộ phận và email
      if (!manv) {
        document.getElementById('dept').value = '';
        document.getElementById('email').value = '';
        return;
      }

      // Gửi yêu cầu AJAX tới server
      fetch('fetch_employee.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `manv=${encodeURIComponent(manv)}`,
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById('dept').value = data.department;
            document.getElementById('email').value = data.email;
          } else {
            document.getElementById('dept').value = '';
            document.getElementById('email').value = '';
          }
        })
        .catch((error) => console.error('Error fetching employee info:', error));
    }
  </script>
  <!-- Script -->

  <script src="./admin/assets/js/core/popper.min.js"></script>
  <script src="./admin/assets/js/core/bootstrap.min.js"></script>
  <script src="./admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./admin/assets/js/plugins/smooth-scrollbar.min.js"></script>

  <script src="./js/bootstrap.bundle.min.js"></script>


  <script src="./js/soft-ui-dashboard.min.js"></script>
</body>

</html>