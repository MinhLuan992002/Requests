<?php 
    include_once ("../lib/Session.php");
    include_once ("../lib/Database.php");
    include_once ("../helpers/Format.php");
	$db  = new Database();
	$fm  = new Format(); 
?>

<!doctype html>
<html>
<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <link href='../font/css.css' rel='stylesheet'>
    <style>
        .navbar-custom {
            padding-left: 20px;
        }
        .navbar-custom .navbar-brand {
            font-family: 'Kaushan Script', cursive;
            font-size: 34px;
            line-height: 1em;
           
        }
        .navbar-custom .navbar-nav {
            margin-left: auto;
        }
        .navbar-custom .nav-item {
            margin-left: 20px;
        }
        .navbar-custom .nav-link {
            color: white;
            font-size: 16px;
        }
        #nav-link {
            color: white;
            font-size: 16px;
        }
        .navbar-custom .nav-link:hover {
            color: #ddd;
        }
    </style>
</head>
<body>


<!-- Navigation --> 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top navbar-custom">
    <div class="container">
        <img style="max-width: 65px;" src="../img/logo_company.png" alt="Logo" class="img-fluid mb-1">
        <a class="navbar-brand" href="">Armcuff Forms</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['fullname'])): ?>
                <li class="nav-item">
                    <span style="color: white;" class="nav-link">Chào, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
                </li>
                <li class="nav-item">
                    <a style="color: white;" class="nav-link" href="logout.php">Đăng xuất</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
