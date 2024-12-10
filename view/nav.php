<style>
    .navbar-nav.mx-auto {
    text-align: center;
    flex: 1; /* Đảm bảo chiếm đều khoảng trống */
    justify-content: center; /* Căn giữa */
}

.navbar-nav.ms-auto {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.navbar-nav li {
    margin: 0 10px; /* Khoảng cách giữa các mục menu */
}

.navbar {
    padding: 10px 20px; /* Đảm bảo thanh điều hướng không bị quá dày */
}

</style>
<?php include  'profile.php' ?>
<nav  id="navbarBlur" navbar-scroll="true">
    <nav class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
        <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="#">
                <img src="./img/logo_company.png" style=" width: 30px;" class="navbar-brand-img h-100" alt="main_logo">
                Request Forms
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon mt-2">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </button>
            
            <div class="collapse navbar-collapse" id="navigation">
                
                    <!-- Dropdown Menu cho người dùng -->
                    <ul class="navbar-nav mx-auto">
                        <!-- Mục Home -->
                        <li class="nav-item">
                            <a class="nav-link" href="http://is.ctmatsuyard.com/">
                                <i class="fa fa-home opacity-6 text-dark me-1"></i>
                                <?= $translations['home'] ?>
                            </a>
                        </li>
                        <!-- Dropdown Web của tôi -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="myWebDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-globe opacity-6 text-dark me-1"></i>
                                <?= $translations['my_website'] ?>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="http://192.168.100.9/issue/">
                                    <i class="fa fa-info-circle me-1"></i> <?= $translations['issue'] ?>
                                </a>
                                <a class="dropdown-item" href="http://192.168.100.6/Home">
                                    <i class="fa fa-cogs me-1"></i> <?= $translations['circulation_approval_system'] ?>
                                </a>
                                <a class="dropdown-item" href="http://is.ctmatsuyard.com/">
                                    <i class="fa fa-briefcase me-1"></i> <?= $translations['training'] ?>
                                </a>
                            </div>
                        </li>

                        <!-- Mục Liên hệ -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fa fa-envelope opacity-6 text-dark me-1"></i>
                                <?= $translations['contact'] ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                    
                    <!-- Language Dropdown nằm bên cạnh -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="img/<?= $lang === 'en' ? 'US' : ($lang === 'vi' ? 'VN' : 'JP'); ?>.png" alt="Flag" style="width: 20px; height: 15px;">
                            <?= $lang === 'en' ? '' : ($lang === 'vi' ? '' : ''); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <a class="dropdown-item" href="?lang=en">
                                <img src="img/US.png" alt="US Flag" style="width: 20px; height: 15px;"> English (US)
                            </a>
                            <a class="dropdown-item" href="?lang=jp">
                                <img src="img/JP.png" alt="JP Flag" style="width: 20px; height: 15px;"> 日本語 (JP) <span class="badge badge-secondary">soon</span>
                            </a>
                            <a class="dropdown-item" href="?lang=vi">
                                <img src="img/VN.png" alt="VN Flag" style="width: 20px; height: 15px;"> Việt Nam (VI) <span class="badge badge-secondary">soon</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user-circle opacity-6 text-dark me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['displayName']); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="/armcuff/admin">
                                <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                                <?= $translations['admin']; ?>
                            </a>
                            <a class="dropdown-item" href="#"   data-bs-toggle="modal" data-bs-target="#personalInfoModal">
                                <i class="fa fa-user opacity-6 text-dark me-1"></i>
                                <?= $translations['profile']; ?>
                            </a>
                            <!-- <a class="dropdown-item" href="#">
                                <i class="fas fa-key opacity-6 text-dark me-1"></i>
                                <?= $translations['sign_in']; ?>
                            </a> -->
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt opacity-6 text-dark me-1"></i>
                                <?= $translations['sign_out']; ?>
                            </a>
                        </div>
                    </li>
                    </ul>
            </div>

            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </a>
            </li>
        </div>
    </nav>
</nav>