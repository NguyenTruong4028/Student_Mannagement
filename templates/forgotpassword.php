<?php
ob_start();
include_once '../config/db.php';
include_once '../function/auth.php';
include_once '../function/recaptcha.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="forgot-password-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        <div class="form-container">
            <h2 class="page-title">Khôi phục mật khẩu</h2>
            
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p style="color: green;">' . htmlspecialchars($_SESSION['success']) . '</p>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="../function/email.php" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeN0zcrAAAAAOG8GYukg_ejjNdhkvAmMbb6HL_n"></div>
                    <small style="display: block; margin-top: 5px; color: #6c757d; text-align:center;">Vui lòng xác nhận bạn không phải là robot</small>
                </div>
                <button type="submit" class="btn">Gửi liên kết</button>
                <a href="login.php" class="btn">Quay lại</a>
            </form>
        </div>
    </div>
</body>
</html>