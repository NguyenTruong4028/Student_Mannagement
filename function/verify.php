<?php
ob_start();
session_start();
include_once '../config/db.php';

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $query = "SELECT ma_sinhvien FROM taikhoan WHERE verification_token = '$token' AND is_verified = 0";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $query = "UPDATE taikhoan SET is_verified = 1, verification_token = NULL WHERE verification_token = '$token'";
        if ($conn->query($query)) {
            $_SESSION['success'] = 'Tài khoản của bạn đã được xác minh! Vui lòng đăng nhập.';
        } else {
            $_SESSION['error'] = 'Xác minh tài khoản thất bại: ' . $conn->error;
        }
    } else {
        $_SESSION['error'] = 'Liên kết không hợp lệ hoặc tài khoản đã được xác minh!';
    }
} else {
    $_SESSION['error'] = 'Không tìm thấy token xác minh!';
}

header('Location: ../templates/login.php');
exit();
?>