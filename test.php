<?php include 'inc/header.php'; ?>
<?php include 'config/config.php';
include 'set_language.php';

?>
<?php
// Kết nối cơ sở dữ liệu
try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (!isset($_SESSION['manv']) || !isset($_SESSION['displayName'])) {
    header("Location: login.php");
    exit();
  }

  $employeeId = $_SESSION['manv'];
  $employeeName = $_SESSION['displayName'];
  $stmt = $pdo->query("SELECT id, category_name FROM support_categories WHERE isDeleted = 0 AND isActive = 1 ");
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmt = $pdo->prepare("SELECT level_value, level_name FROM priority_levels WHERE isDeleted = 0 AND isActive = 1 ORDER BY level_value ASC");
  $stmt->execute();
  $priorityLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage(); // Hiển thị lỗi nếu kết nối thất bại
}


// Đóng kết nối PDO
$pdo = null;
?>
<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link
    rel="apple-touch-icon"
    sizes="76x76"
    href="../assets/img/apple-icon.png" />
  <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
  <title>Requests Forms</title>
  <!--     Fonts and icons     -->
  <link
    href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800"
    rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link
    href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css"
    rel="stylesheet" />
  <link
    href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css"
    rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script
    src="https://kit.fontawesome.com/42d5adcbca.js"
    crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href=" css/soft-design-system.css" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->


  <link rel="stylesheet" href="./fontawesome/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script
    defer
    data-site="YOUR_DOMAIN_HERE"
    src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <style>
    #senderSignature {
      font-family: 'Brush Script MT', cursive;
      /* Phông chữ giống chữ ký */
      font-size: 24px;
      /* Kích thước chữ lớn hơn */
      text-align: center;
      /* Căn giữa nội dung */
      color: red;
      /* Màu chữ */
    }
  </style>
</head>


<body class="g-sidenav-show  bg-gray-100">
  <?php include './view/slidenav.php' ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include './view/nav.php' ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4" style="margin-top: 19px;">
      <div class="row" style="margin-bottom: 30px;">
        <header>
          <div class="page-header min-vh-85">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-8">
                  <div class="card shadow-lg mt-5">
                    <!-- Tiêu đề -->
                    <div
                      class="card-header text-center bg-gradient-primary text-white ">
                      <h3 style="color: white;" class=" mb-0 ">Gửi Yêu Cầu Hỗ Trợ</h3>
                      <small id="requestTime" class="d-block mt-1">
                        Thời gian yêu cầu: <strong id="currentTime"></strong>
                      </small>
                    </div>

                    <!-- Nội dung -->
                    <div class="card-body">
                      <!-- Chữ ký -->
                      <div class="row mb-4">

                        <div class="col-md-4 text-center">
                          <label for="directorSignature" class="form-label">Chữ ký Giám đốc</label>
                          <div
                            class="border bg-light p-3"
                            id="directorSignature"
                            style="height: 80px; border-radius: 10px"></div>
                        </div>
                        <div class="col-md-4 text-center">
                          <label for="managerSignature" class="form-label">Chữ ký Trưởng bộ phận</label>
                          <div
                            class="border bg-light p-3"
                            id="managerSignature"
                            style="height: 80px; border-radius: 10px"></div>
                        </div>
                        <div class="col-md-4 text-center">
                          <label for="senderSignature" class="form-label">Chữ ký Người gửi</label>
                          <div
                            class="border bg-light p-3"
                            id="senderSignature"
                            style="height: 80px; border-radius: 10px; font-family: 'Brush Script MT', cursive; font-size: 24px;">
                            <?php echo htmlspecialchars($_SESSION['displayName']); ?>
                          </div>
                        </div>
                      </div>
                      <form id="support-form" method="post" action="send_email.php" autocomplete="off">

                        <div class="row  mb-3">
                          <!-- Thông tin người yêu cầu -->
                          <div class="col-md-5">
                            <h5 class="text-primary mb-3">Thông tin người yêu cầu</h5>
                            <p>Họ tên: <?php echo htmlspecialchars($_SESSION['displayName']); ?></p>
                            <p>Mã nhân viên: <?php echo htmlspecialchars($_SESSION['manv']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($_SESSION['mail']); ?></p>
                            <p>Bộ phận: <?php echo htmlspecialchars($_SESSION['department']); ?></p>
                            <!-- Trường ẩn để gửi thông tin người yêu cầu -->
                            <input type="hidden" name="displayName" value="<?= htmlspecialchars($_SESSION['displayName']) ?>">
                            <input type="hidden" name="manv" value="<?= htmlspecialchars($_SESSION['manv']) ?>">
                            <input type="hidden" name="mail" value="<?= htmlspecialchars($_SESSION['mail']) ?>">
                            <input type="hidden" name="department" value="<?= htmlspecialchars($_SESSION['department']) ?>">
                          </div>

                          <!-- Cấp độ ưu tiên -->
                          <div class="col-md-7">
                            <div class="row align-items-center ">
                              <!-- Cấp độ ưu tiên -->
                              <div class="col-md-6 mb-3">
                                <label for="priority_level" class="form-label text-primary fw-bold">Cấp độ ưu tiên</label>
                                <select
                                  class="form-select shadow-sm"
                                  id="priority_level"
                                  name="priority_level"
                                  required>
                                  <option value="" selected>Chọn cấp độ ưu tiên:</option>
                                  <?php foreach ($priorityLevels as $priority): ?>
                                    <option value="<?php echo htmlspecialchars($priority['level_name']); ?>">
                                      <?= htmlspecialchars($priority['level_name']) ?>
                                    </option>
                                  <?php endforeach; ?>
                                </select>
                              </div>

                              <!-- Thời gian mong muốn -->
                              <div class="col-md-6 mb-3">
                                <label for="desired_time" class="form-label text-primary fw-bold">Thời gian mong muốn:</label>
                                <input
                                  type="date"
                                  id="desired_time"
                                  name="desired_time"
                                  class="form-control shadow-sm" />
                              </div>
                            </div>
                          </div>



                          <!-- Thông tin người gửi -->
                          <h5 class="text-primary mb-3">Thông tin người áp dụng</h5>

                          <div class="row">
                            <div class="col-md-3 mb-3">
                              <label for="manv" class="form-label">Mã nhân viên</label>
                              <input
                                type="text"
                                class="form-control"
                                id="manv"
                                name="userId"
                                placeholder="Nhập Mã nhân viên"
                                onkeyup="fetchEmployeeInfo()" />
                            </div>
                            <div class="col-md-3 mb-3">
                              <label for="dept" class="form-label">Bộ phận</label>
                              <input
                                type="text"
                                class="form-control"
                                id="dept"
                                name="dept"
                                placeholder="Bộ phận" />
                            </div>
                            <div class="col-md-6 mb-3">
                              <label for="email" class="form-label">Email</label>
                              <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="userEmail"
                                placeholder="Email" />
                            </div>
                          </div>



                          <!-- Nội dung yêu cầu -->
                          <h5 class="text-primary mb-3">Nội dung yêu cầu</h5>
                          <div class="col-md-3 mb-3">
                            <label for="category" class="form-label">Danh mục hỗ trợ</label>
                            <select
                              class="form-select"
                              id="category"
                              name="category"
                              required
                              onchange="updateSupportFields()">
                              <option value="" selected>Chọn danh mục</option>
                              <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) . '. ' . $category['category_name'] ?>"> <!-- Kết hợp id và name -->
                                  <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>


                          <div class="col-md-9 mb-3">
                            <label for="supportContent" class="form-label">Chi tiết hỗ trợ</label>
                            <div
                              id="supportContent">
                              <p style="font-style: italic;" class="btn outline-primary">
                                Vui lòng chọn danh mục để hiển thị nội dung hỗ trợ.
                              </p>
                            </div>
                          </div>

                          <div class="text-center">
                            <button type="submit" class="btn btn-primary mt-3">
                              Gửi yêu cầu
                            </button>
                          </div>
                      </form>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </header>

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
        <script>
          // Hiển thị thời gian hiện tại
          function displayCurrentTime() {
            const now = new Date();
            const formattedTime =
              now.toLocaleDateString("vi-VN") +
              " " +
              now.toLocaleTimeString("vi-VN");
            document.getElementById("currentTime").textContent = formattedTime;
          }
          displayCurrentTime();

          // Hàm cập nhật nội dung hỗ trợ dựa trên danh mục
          function updateSupportFields() {
            const categoryId = document.getElementById('category').value;
            alert(categoryId);
            const supportContent = document.getElementById('supportContent');

            if (!categoryId) {
              supportContent.innerHTML = `<p class="text-muted">Vui lòng chọn danh mục để hiển thị nội dung hỗ trợ.</p>`;
              return;
            }

            fetch(`get_category_details.php?category_id='${categoryId}'`)
              .then(response => response.json())
              .then(data => {
                if (data.error) {
                  supportContent.innerHTML = `<p class="text-danger">Lỗi: ${data.error}</p>`;
                  return;
                }

                let content = '';
                switch (data.field_type) {
                  case 'textarea':
                    content = `
                            <input class="form-control" name="content" rows="4" placeholder="${data.placeholder || ''}" required></input>
                        `;
                    break;
                  case 'input':
                    content = `
                            <input type="text" class="form-control" name="content" placeholder="${data.placeholder || ''}" required />
                        `;
                    break;
                  case 'select':
                    // const options = JSON.parse(data.options || '[]');
                    content = `
                <input type="text" class="form-control" name="content" placeholder="${data.placeholder || ''}" required />
    
                        `;
                    break;
                  default:
                    content = `<p class="text-muted">Không có nội dung hỗ trợ cho danh mục này.</p>`;
                }

                supportContent.innerHTML = content;
              })
              .catch(error => {
                supportContent.innerHTML = `<p class="text-danger">Lỗi khi tải nội dung hỗ trợ.</p>`;
                console.error(error);
              });
          }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      </div>
      <!-- <div class="row">
        <div class="col-md-7 mt-4">
          <div class="card">
            <div class="card-header pb-0 px-3">
              <h6 class="mb-0">Billing Information</h6>
            </div>
            <div class="card-body pt-4 p-3">
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Oliver Liam</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Lucas Harper</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Stone Tech Zone</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">lucas@stone-tech.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                  <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">Ethan James</h6>
                    <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Fiber Notion</span></span>
                    <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">ethan@fiber.com</span></span>
                    <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                  </div>
                  <div class="ms-auto text-end">
                    <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-5 mt-4">
          <div class="card h-100 mb-4">
            <div class="card-header pb-0 px-3">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="mb-0">Your Transaction's</h6>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                  <i class="far fa-calendar-alt me-2"></i>
                  <small>23 - 30 March 2020</small>
                </div>
              </div>
            </div>
            <div class="card-body pt-4 p-3">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                      <span class="text-xs">27 March 2020, at 12:30 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                    - $ 2,500
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Apple</h6>
                      <span class="text-xs">27 March 2020, at 04:30 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 2,000
                  </div>
                </li>
              </ul>
              <h6 class="text-uppercase text-body text-xs font-weight-bolder my-3">Yesterday</h6>
              <ul class="list-group">
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Stripe</h6>
                      <span class="text-xs">26 March 2020, at 13:45 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 750
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">HubSpot</h6>
                      <span class="text-xs">26 March 2020, at 12:30 PM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 1,000
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Creative Tim</h6>
                      <span class="text-xs">26 March 2020, at 08:30 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                    + $ 2,500
                  </div>
                </li>
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <button class="btn btn-icon-only btn-rounded btn-outline-dark mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-exclamation"></i></button>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">Webflow</h6>
                      <span class="text-xs">26 March 2020, at 05:00 AM</span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                    Pending
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div> -->
      <?php include './view/footer.php' ?>
    </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="fa fa-cog py-2"> </i>
    </a>
    <div class="card shadow-lg ">
      <div class="card-header pb-0 pt-3 ">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Request Forms</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="fa fa-close"></i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn btn-primary w-100 px-3 mb-2 active" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn btn-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3">
          <h6 class="mb-0">Navbar Fixed</h6>
        </div>
        <div class="form-check form-switch ps-0">
          <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/license/soft-ui-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/soft-ui-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Soft%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/soft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
        </div>
      </div>
    </div>
  </div>
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
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="./js/soft-ui-dashboard.min.js"></script>
</body>

</html>


<script>
    // Hiển thị thời gian hiện tại
    function displayCurrentTime() {
      const now = new Date();
      const formattedTime =
        now.toLocaleDateString("vi-VN") +
        " " +
        now.toLocaleTimeString("vi-VN");
      document.getElementById("currentTime").textContent = formattedTime;
    }
    displayCurrentTime();

    // Hàm cập nhật nội dung hỗ trợ dựa trên danh mục
    function updateSupportFields() {
      const categoryId = document.getElementById('category').value;
      const supportContent = document.getElementById('supportContent');

      if (!categoryId) {
        supportContent.innerHTML = `<p class="text-muted">Vui lòng chọn danh mục để hiển thị nội dung hỗ trợ.</p>`;
        return;
      }

      fetch(`get_category_details.php?category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            supportContent.innerHTML = `<p class="text-danger">Lỗi: ${data.error}</p>`;
            return;
          }

          let content = '';
          switch (data.field_type) {
            case 'textarea':
              content = `
                            <input class="form-control" name="content" rows="4" placeholder="${data.placeholder || ''}" required></input>
                        `;
              break;
            case 'input':
              content = `
                            <input type="text" class="form-control" name="content" placeholder="${data.placeholder || ''}" required />
                        `;
              break;
            case 'select':
              // const options = JSON.parse(data.options || '[]');
              content = `
                    <input type="text" class="form-control" name="content" placeholder="${data.placeholder || ''}" required />
    
                        `;
              break;
            default:
              content = `<p class="text-muted">Không có nội dung hỗ trợ cho danh mục này.</p>`;
          }

          supportContent.innerHTML = content;
        })
        .catch(error => {
          supportContent.innerHTML = `<p class="text-danger">Lỗi khi tải nội dung hỗ trợ.</p>`;
          console.error(error);
        });
    }
  </script>



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
echo "Ngôn ngữ hiện tại: $lang";
?>
