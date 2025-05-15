<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="reset-password-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        
        <div class="form-container">
            <h2 class="page-title">Đặt lại mật khẩu</h2>
            
            <form id="reset-password-form" onsubmit="event.preventDefault(); if(validateForm('reset-password-form')) window.location.href='login.html';">
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <div class="form-group">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn">Đặt lại mật khẩu</button>
            </form>
            
            <div class="link-text">
                <a href="login.html">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>

    <script src="../asset/js/main.js"></script>
</body>
</html>