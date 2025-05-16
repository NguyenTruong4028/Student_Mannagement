<?php

session_start();

function login($conn, $username, $password) {
    $username = $conn->real_escape_string($username);
    $query = "SELECT * FROM taikhoan WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Kiểm tra trạng thái xác minh
        if ($user['is_verified'] == 0) {
            return ['success' => false, 'message' => 'Tài khoản chưa được xác minh. Vui lòng kiểm tra email để xác minh!'];
        }

        // Kiểm tra mật khẩu
         if ($password === $user['password']) {
            // Đăng nhập thành công, lưu thông tin vào session
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'Sinh viên') {
                $_SESSION['ma_sinhvien'] = $user['ma_sinhvien'];
            }
            return ['success' => true];
        }else {
            return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!'];
        }
    } else {
        return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!'];
    }
}
function isLoggedIn() {
    return isset($_SESSION['username']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'Quản trị viên';
}

function logout() {
    session_destroy();
}
?>