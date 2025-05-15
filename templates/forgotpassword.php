<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Quên Mật Khẩu</title>
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="forgot-password-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        
        <div class="form-container">
            <h2 class="page-title">Quên mật khẩu</h2>
            
            <div class="alert alert-success">
                Hướng dẫn đặt lại mật khẩu đã được gửi vào email của bạn.
            </div>
            
            <form id="forgot-password-form" onsubmit="event.preventDefault(); if(validateForm('forgot-password-form')) window.location.href='reset-password.html';">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                
                <!-- Google reCAPTCHA -->
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeN0zcrAAAAAOG8GYukg_ejjNdhkvAmMbb6HL_n"></div>
                    <small style="display: block; margin-top: 5px; color: #6c757d;">Vui lòng xác nhận bạn không phải là robot</small>
                </div>
                
                <button type="submit" class="btn">Gửi yêu cầu</button>
            </form>
            
            <div class="link-text">
                <a href="login.html">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>

    <script src="../asset/js/main.js"></script>
</body>
</html>