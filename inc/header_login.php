<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Session.php');
Session::init();

include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');
spl_autoload_register(function($class){
	include_once "classes/".$class.".php";
});

$db   = new Database();
$fm   = new Format();
$exam = new Exam();
$user = new User();
$pro  = new Process();

header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
header("Pragma: no-cache"); 
header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'logout') {
  	     Session::destroy();
  	     header("Location:index.php");
  	     exit();
  }
?>
<!doctype html>
<html>
<head>
	<title>Armcuff Forms</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
	<link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <link href='font/css.css' rel='stylesheet'>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div style="margin-left: 219px;">
    <img style="max-width: 65px;" src="./img/logo_company.png" alt="Logo" class="img-fluid mb-1">
    <a class="navbar-brand" href="" style="font-family: 'Kaushan Script', cursive; font-size: 34px;line-height: 1em; margin-left: 20px;">Armcuff Forms</a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">

        </div>
    </div>
</nav>
