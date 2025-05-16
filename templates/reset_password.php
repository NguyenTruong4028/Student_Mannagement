<?php
ob_start();
session_start();
include_once '../config/db.php';

if (!isset($_GET['token'])) {
    $_SESSION['error'] = 'Liên kết không hợp lệ!';
    header('Location: ../templates/forgotpassword.php');
    exit();
}

$token = $conn->real_escape_string(trim($_GET['token']));

// Kiểm tra token
$query = "SELECT ma_sinhvien FROM taikhoan WHERE reset_token = '$token'";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    $_SESSION['error'] = 'Liên kết không hợp lệ!';
    header('Location: ../templates/forgotpassword.php');
    exit();
}

$user = $result->fetch_assoc();
$ma_sinhvien = $user['ma_sinhvien'];

// Xử lý đặt lại mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $conn->real_escape_string(trim($_POST['new_password']));
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));

    // Kiểm tra mật khẩu
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $_SESSION['error'] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt!';
    } else {
        // Cập nhật mật khẩu mới 
        $update_query = "UPDATE taikhoan SET password = '$new_password', reset_token = NULL WHERE ma_sinhvien = '$ma_sinhvien'";
        if ($conn->query($update_query)) {
            $_SESSION['success'] = 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.';
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['error'] = 'Lỗi khi đặt lại mật khẩu: ' . $conn->error;
        }
    }
    header('Location: reset_password.php?token=' . urlencode($token));
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body>
    <div id="reset-password-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
        <div class="form-container">
            <h2 class="page-title">Đặt lại mật khẩu</h2>
            
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

            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="form-group">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới" required>
                    <small style="color: #6c757d; margin-top: 5px; display: block;">
                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.
                    </small>
                </div>
                <div class="form-group">
                    <label>Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                </div>
                <button type="submit" class="btn">Cập nhật mật khẩu</button>
                <a href="login.php" class="btn">Quay lại</a>
            </form>
        </div>
    </div>
</body>
</html>