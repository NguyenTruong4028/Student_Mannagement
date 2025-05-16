<?php
include_once '../config/db.php';
include_once '../function/auth.php';
include_once '../function/recaptcha.php';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Kiểm tra reCAPTCHA
    if (empty($recaptcha_response)) {
        $error = "Vui lòng xác nhận bạn không phải là robot!";
    } elseif (!verifyRecaptcha($recaptcha_response)) {
        $error = "Xác minh reCAPTCHA thất bại! Kiểm tra log để biết chi tiết.";
    } else {
        // Kiểm tra thông tin đăng nhập
        $login_result = login($conn, $username, $password);
        if (is_array($login_result) && isset($login_result['success']) && $login_result['success']) {
            if ($_SESSION['role'] == 'Quản trị viên') {
                header("Location: ../templates/admin/student_management.php");
            } else {
                header("Location: ../templates/students/profile.php");
            }
            exit();
        } else {
            $error = is_array($login_result) && isset($login_result['message']) ? $login_result['message'] : 'Đã xảy ra lỗi khi đăng nhập!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Đăng Nhập</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="login-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        
        <div class="form-container">
            <h2 class="page-title">Login</h2>
            
            <?php if (isset($error)): ?>
                <div style="color: red; margin-bottom: 15px; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form id="login-form" method="POST" action="login.php">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>
                
                <div class="options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="forgotpassword.php">Quên mật khẩu?</a>
                </div>
                
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeN0zcrAAAAAOG8GYukg_ejjNdhkvAmMbb6HL_n" data-theme="light" data-size="normal"></div>
                    <small style="display: block; margin-top: 5px; color: #6c757d;">Vui lòng xác nhận bạn không phải là robot</small>
                </div>
                
                <button type="submit" class="btn">Đăng nhập</button>
            </form>
            
            <div class="link-text">
                Chưa có tài khoản? <a href="register.php">Đăng kí</a>
            </div>
        </div>
    </div>

    <script src="../../asset/js/main.js"></script>
</body>
</html>