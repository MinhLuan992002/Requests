<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/Session.php');
include './set_language.php';
Session::init();

include_once($filepath . '/../lib/Database.php');
include_once($filepath . '/../helpers/Format.php');
spl_autoload_register(function ($class) {
   include_once "classes/" . $class . ".php";
});

$db   = new Database();
$fm   = new Format();
$exam = new Exam();
$user = new User();
$pro  = new Process();
?>
<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
$timeout_duration = 3 * 60 * 60;
if (!isset($_SESSION['username'])) {
   header("Location: login.php");
   exit;
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
   session_unset();
   session_destroy();
   header("Location: login.php");
   exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
?>

<!doctype html>
<html>

<head>
   <title><?= $translations['request_forms'] ?></title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <meta name="description" content="">
   <meta name="author" content="">
   <link rel="apple-touch-icon" sizes="76x76" href="./admin/assets/img/apple-icon.png">
   <link rel="icon" type="image/png" href="./admin/assets/img/favicon.png">
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <script src="js/jquery-3.3.1.min.js"  crossorigin="anonymous"></script>
   <link href='font/css.css' rel='stylesheet'>
   <script src="js/main.js"></script>
   <link rel="stylesheet" href="./css/master.css">
   <link rel="stylesheet" href="./remixicon/remixicon.css">
</head>

<body>


   <!-- <nav class="navbar navbar-dark bg-dark ">
      <div class="navbar-left">
         <img src="./img/logo_company.png" alt="Logo" class="img-fluid mb-1">
         <a style="color: white;" class="navbar-brand" href="#">Armcuff Forms</a>
      </div>
      <?php if (isset($_SESSION['displayName'])): ?>
         <div class="navbar">
            <div class="dropdown" id="dropdown-content">
               <button style="color: white;" class="dropdown__button" id="dropdown-button">
                  <i class="ri-user-3-line dropdown__user"></i>
                  <span style="font-weight: bold; " class="dropdown__name"><?php echo htmlspecialchars($_SESSION['displayName']); ?></span>

                  <div class="dropdown__icons">
                     <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                     <i class="ri-close-line dropdown__close"></i>
                  </div>
               </button>

               <ul class="dropdown__menu" style="  z-index: 2;">
                  <!-- <li class="dropdown__item">
                  <i class="ri-message-3-line dropdown__icon"></i> 
                  <span class="dropdown__name">Messages</span>
               </li> -->

                  <!-- <li class="dropdown__item">
                     <i class="ri-lock-line dropdown__icon"></i>
                     <span class="dropdown__name"><a style="text-decoration: none; color: black;" href="/armcuff/admin">Admin</a></span>
                  </li>
                  <hr>
                  <li class="dropdown__item">
                     <i class="ri-logout-box-line dropdown__icon"></i>
                     <span class="dropdown__name"><a style="text-decoration: none; color: black;" href="logout.php">Logout </a></span>
                  </li>
               </ul>
            </div>
         </div>
      <?php endif; ?>
   </nav> -->

   <script>
      var timeoutDuration = 3 * 60 * 60 * 1000;
      var warningTime = 2 * 60 * 1000;

      setTimeout(function() {
         alert("Bạn sẽ bị đăng xuất do không hoạt động trong một khoảng thời gian dài!.");
      }, timeoutDuration - warningTime);


      document.body.addEventListener('click', resetTimer);
      document.body.addEventListener('keypress', resetTimer);

      function resetTimer() {
         clearTimeout();
         setTimeout(function() {
            alert("Đang đăng xuất do không hoạt động!");
            window.location.href = 'logout.php';
         }, timeoutDuration);
      }
   </script>
   <!-- <script>
      const showDropdown = (content, button) => {
         const dropdownContent = document.getElementById(content),
            dropdownButton = document.getElementById(button)

         dropdownButton.addEventListener('click', () => {

            dropdownContent.classList.toggle('show-dropdown')
         })
      }

      showDropdown('dropdown-content', 'dropdown-button')
   </script> -->