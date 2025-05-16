<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../config/db.php';
include_once '../function/auth.php';
include_once '../function/recaptcha.php';
require '../vendor/PHPMailer-master/src/PHPMailer.php';
require '../vendor/PHPMailer-master/src/SMTP.php';
require '../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Hàm gửi email xác minh
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hongtruong.0909.04@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'ngzdbsrsybgchfkm'; // Thay bằng mật khẩu ứng dụng
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('hongtruong.0909.04@gmail.com', 'Quản Lý Sinh Viên');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Xác minh tài khoản';
        $verifyLink = "http://localhost:8888/EXCERCISEW1/function/verify.php?token=$token";
        $mail->Body = "Chào bạn,<br>Xin vui lòng nhấp vào liên kết sau để xác minh tài khoản: <a href='$verifyLink'>$verifyLink</a>";
        $mail->AltBody = "Chào bạn, xin vui lòng truy cập liên kết sau để xác minh tài khoản: $verifyLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi email: " . $mail->ErrorInfo);
        return false;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Form submitted!"; // Debug

    $ho_ten = $conn->real_escape_string(trim($_POST['ho_ten']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $ma_sinhvien = $conn->real_escape_string(trim($_POST['ma_sinhvien']));
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $conn->real_escape_string(trim($_POST['password'])); 
    $confirm_password = $conn->real_escape_string(trim($_POST['confirm_password']));
    $lop = $conn->real_escape_string(trim($_POST['lop']));
    $ma_khoa = $conn->real_escape_string(trim($_POST['ma_khoa']));

    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Mật khẩu và xác nhận mật khẩu không khớp!';
    } elseif ($password === '') {
        $_SESSION['error'] = 'Mật khẩu không được để trống!';
    } elseif (empty($lop) || empty($ma_khoa)) {
        $_SESSION['error'] = 'Vui lòng chọn lớp và khoa!';
    } else {
        // Check if username or ma_sinhvien already exists
        $check_query = "SELECT username, ma_sinhvien FROM taikhoan WHERE username = '$username' OR ma_sinhvien = '$ma_sinhvien'";
        $check_result = $conn->query($check_query);
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mã sinh viên đã tồn tại!';
        } else {
            // Generate verification token
            $verification_token = bin2hex(random_bytes(50));

            // Insert into sinhvien with lop and ma_khoa
            $sinhvien_query = "INSERT INTO sinhvien (ma_sinhvien, ho_ten, email, lop, ma_khoa, trang_thai) VALUES ('$ma_sinhvien', '$ho_ten', '$email', '$lop', '$ma_khoa', 'Đang học')";
            if ($conn->query($sinhvien_query)) {
                // Insert into taikhoan with plain text password
                $taikhoan_query = "INSERT INTO taikhoan (username, ma_sinhvien, password, role, is_verified, verification_token) VALUES ('$username', '$ma_sinhvien', '$password', 'Sinh viên', 0, '$verification_token')";
                if ($conn->query($taikhoan_query)) {
                    // Send verification email
                    if (sendVerificationEmail($email, $verification_token)) {
                        $_SESSION['success'] = 'Đăng kí thành công! Vui lòng kiểm tra email để xác minh tài khoản.';
                    } else {
                        $_SESSION['error'] = 'Đăng kí thành công nhưng gửi email xác minh thất bại. Vui lòng liên hệ quản trị viên.';
                    }
                    header('Location: ../templates/login.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Đăng kí thất bại (taikhoan): ' . $conn->error;
                    error_log("taikhoan insert error: " . $conn->error);
                    // Roll back sinhvien entry
                    $conn->query("DELETE FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'");
                }
            } else {
                $_SESSION['error'] = 'Đăng kí thất bại (sinhvien): ' . $conn->error;
                error_log("sinhvien insert error: " . $conn->error);
            }
        }
    }

    if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
        header('Location: register.php');
        exit();
    }
}
?>

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

            <form id="register-form" action="register.php" method="POST">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="ho_ten" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label>Mã sinh viên</label>
                    <input type="text" name="ma_sinhvien" class="form-control" placeholder="SVXXX"  required>
                </div>
                <div class="form-group">
                    <label>Khoa</label>
                    <select name="ma_khoa" class="form-control" required>
                        <option value="">Chọn khoa</option>
                        <?php
                        $khoa_query = "SELECT ma_khoa, ten_khoa FROM khoa";
                        $khoa_result = $conn->query($khoa_query);
                        while ($row = $khoa_result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['ma_khoa']) . '">' . htmlspecialchars($row['ten_khoa']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lớp</label>
                    <select name="lop" class="form-control" required>
                        <option value="">Chọn lớp</option>
                        <?php
                        $lophocphan_query = "SELECT lop, ten_lop FROM lophocphan";
                        $lophocphan_result = $conn->query($lophocphan_query);
                        while ($row = $lophocphan_result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['lop']) . '">' . htmlspecialchars($row['ten_lop']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" placeholder="username" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LeN0zcrAAAAAOG8GYukg_ejjNdhkvAmMbb6HL_n"></div>
                    <small style="display: block; margin-top: 5px; color: #6c757d;">Vui lòng xác nhận bạn không phải là robot</small>
                </div>
                <button type="submit" class="btn">Đăng kí</button>
            </form>
            <div class="link-text">
                Đã có tài khoản? <a href="login.php">Đăng nhập</a>
            </div>
        </div>
    </div>
</body>
</html>