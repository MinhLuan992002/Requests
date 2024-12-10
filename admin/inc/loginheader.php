<?php

include_once ("../lib/Session.php");
Session::checkAdminLogin();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: pre-check=0, post-check=0, max-age=0");
header("Pragma: no-cache");
header("Expires: Mon, 6 Dec 1977 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<!doctype html>
<html>
<head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Cache-Control" content="no-cache">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/footer.css">
    <script src="../js/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <link href='../font/css.css' rel='stylesheet'>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div style="margin-left: 219px;">
    <img style="max-width: 65px;" src="../img/logo_company.png" alt="Logo" class="img-fluid mb-1">
    <a class="navbar-brand" href="" style="font-family: 'Kaushan Script', cursive; font-size: 34px;line-height: 1em; margin-left: 20px;">Armcuff Forms</a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <!-- <ul class="navbar-nav ml-auto">

                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="users.php">Manage Users</a></li>
                <li class="nav-item"><a class="nav-link" href="quesadd.php">Add Ques</a></li>
                <li class="nav-item"><a class="nav-link" href="queslist.php">Ques List</a></li>
                <li class="nav-item"><a class="nav-link" href="?action=logout">Logout</a></li>
            </ul> -->
        </div>
    </div>
</nav>


	