<?php
ob_start();
session_start();
include_once '../config/db.php';
include_once '../function/recaptcha.php';
require '../vendor/PHPMailer-master/src/PHPMailer.php';
require '../vendor/PHPMailer-master/src/SMTP.php';
require '../vendor/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    $_SESSION['error'] = 'Lỗi kết nối cơ sở dữ liệu!';
    header('Location: ../templates/forgotpassword.php');
    exit();
}

// Hàm gửi email đặt lại mật khẩu
function sendResetPasswordEmail($email, $token) {
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
        $mail->Subject = 'Đặt lại mật khẩu';
        $resetLink = "http://localhost:8888/EXCERCISEW1/templates/reset_password.php?token=$token";
        $mail->Body = "Chào bạn,<br>Xin vui lòng nhấp vào liên kết sau để đặt lại mật khẩu: <a href='$resetLink'>$resetLink</a>";
        $mail->AltBody = "Chào bạn, xin vui lòng truy cập liên kết sau để đặt lại mật khẩu: $resetLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi email: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    if (empty($recaptcha_response) || !verifyRecaptcha($recaptcha_response)) {
        $_SESSION['error'] = 'Xác minh reCAPTCHA thất bại!';
        header('Location: ../templates/forgotpassword.php');
        exit();
    }

    $email = $conn->real_escape_string(trim($_POST['email']));
    error_log("Email nhập: $email");

    // Kiểm tra email tồn tại trong bảng sinhvien và liên kết với taikhoan
    $query = "SELECT t.ma_sinhvien 
              FROM sinhvien s 
              JOIN taikhoan t ON s.ma_sinhvien = t.ma_sinhvien 
              WHERE s.email = '$email'";
    error_log("Truy vấn: $query");
    $result = $conn->query($query);
    error_log("Số hàng: " . $result->num_rows);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $ma_sinhvien = $user['ma_sinhvien'];

        // Tạo token đặt lại mật khẩu
        $reset_token = bin2hex(random_bytes(50));

        // Lưu token vào cơ sở dữ liệu
        $update_query = "UPDATE taikhoan SET reset_token = '$reset_token' WHERE ma_sinhvien = '$ma_sinhvien'";
        if ($conn->query($update_query)) {
            // Gửi email đặt lại mật khẩu
            if (sendResetPasswordEmail($email, $reset_token)) {
                $_SESSION['success'] = 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn!';
            } else {
                $_SESSION['error'] = 'Gửi email thất bại. Vui lòng liên hệ quản trị viên.';
            }
        } else {
            $_SESSION['error'] = 'Lỗi khi lưu token: ' . $conn->error;
        }
    } else {
        $_SESSION['error'] = 'Email không tồn tại trong hệ thống!';
    }

    header('Location: ../templates/forgotpassword.php');
    exit();
}
?>