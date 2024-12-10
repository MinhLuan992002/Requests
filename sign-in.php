<?php include 'inc/header_login.php'; ?>
<link rel="stylesheet" href="./admin/css/main_login.css">
<style>
    
    body {
        margin: 0;
        font-family: Arial, sans-serif; /* Hoặc bất kỳ phông chữ nào bạn muốn */
        /* background: url('./img/hinhnen.png') no-repeat center center fixed;
        background-size: cover;  */
    }

    h1.display-4 {
        font-weight: 900;
        color: #3498db;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    p.lead {
        font-size: 1.2rem;
        color: #95a5a6;
        font-weight: bold;
    }

    /* CSS for mobile responsiveness */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        h1.display-4 {
            font-size: 2rem;
        }

        p.lead {
            font-size: 1rem;
        }

        input[type="text"],
        input[type="password"],
        button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            font-size: 1rem;
        }
    }

    /* Additional styles */
    .social-container img {
        width: 100px; /* Adjust size as needed */
        margin-bottom: 20px;
    }

    button {
        background-color: #3498db;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #2980b9;
    }
</style>

<div style="margin-top: 29px;" class="text-center mb-4">
    <h1 class="display-4" style="font-family: 'Kaushan Script', cursive;">Armcuff Forms</h1>
    <p class="lead">Welcome to the Training Management System</p>
</div>

<div style="margin-top: 59px;" class="container" id="container">
    <div class="text-center mb-4">

        <form action="login.php" method="post">
            <div class="social-container">
                <img src="./img/logo_company.png" alt="Logo Company">
            </div>
            <span>Nhập thông tin để đổi mật khẩu</span>
            <input type="text" name="adminUser" placeholder="Tên đăng nhập" required />
            <input type="password" name="currentPass" placeholder="Mật khẩu hiện tại" required />
            <input type="password" name="newPass" placeholder="Mật khẩu mới" required />
            <button type="submit" name="changePassword" value="ChangePassword">Đổi mật khẩu</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form action="getlogin.php" method="post">
            <h3 style="color: #285DAA; text-transform: uppercase; font-weight: bold;">Đăng Nhập</h3>
            <div class="social-container">
                <img src="./img/logo_company.png" alt="Logo Company">
            </div>
            <span>Đăng nhập bằng tài khoản của bạn</span>
            <input type="text" id="username" name="username" placeholder="Username" required />
            <input type="password" id="password" name="password" placeholder="Password" required />
            <button type="submit" value="login">Đăng Nhập</button>
        </form>
    </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Xin Chào!</h1>
                <p>Để duy trì kết nối với chúng tôi vui lòng đăng nhập bằng thông tin cá nhân của bạn</p>
                <button class="ghost" id="signIn">Đăng nhập</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Xin chào!</h1>
                <p>Hãy nhập thông tin tài khoản của bạn để đổi mật khẩu</p>
                <button class="ghost" id="signUp">Đổi mật khẩu</button>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add('right-panel-active');
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove('right-panel-active');
    });
</script>
