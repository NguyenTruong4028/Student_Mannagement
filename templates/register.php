<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Đăng Kí</title>
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="register-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        
        <div class="form-container">
            <h2 class="page-title">Đăng kí tài khoản</h2>
            
            <form id="register-form" onsubmit="event.preventDefault(); if(validateForm('register-form')) window.location.href='login.html';">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" class="form-control" placeholder="username" required>
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <!-- Google reCAPTCHA -->
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeN0zcrAAAAAOG8GYukg_ejjNdhkvAmMbb6HL_n"></div>
                    <small style="display: block; margin-top: 5px; color: #6c757d;">Vui lòng xác nhận bạn không phải là robot</small>
                </div>
                
                <button type="submit" class="btn">Đăng kí</button>
            </form>
            
            <div class="link-text">
                Đã có tài khoản? <a href="login.html">Đăng nhập</a>
            </div>
        </div>
    </div>

    <script src="../asset/js/main.js"></script>
</body>
</html>