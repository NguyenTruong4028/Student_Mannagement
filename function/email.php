<?php
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'your_app_password';    // Thay bằng mật khẩu ứng dụng
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Hệ thống Quản lý Sinh viên');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Xác minh Email';
        $mail->Body = "Nhấn <a href='http://localhost/student_management/verify.php?token=$token'>vào đây</a> để xác minh email của bạn.";
        $mail->send();
    } catch (Exception $e) {
        error_log("Không thể gửi email. Lỗi: {$mail->ErrorInfo}");
    }
}

function sendResetPasswordEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com';
        $mail->Password = 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'Hệ thống Quản lý Sinh viên');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Đặt lại Mật khẩu';
        $mail->Body = "Nhấn <a href='http://localhost/student_management/reset_password.php?token=$token'>vào đây</a> để đặt lại mật khẩu của bạn.";
        $mail->send();
    } catch (Exception $e) {
        error_log("Không thể gửi email. Lỗi: {$mail->ErrorInfo}");
    }
}
?>