<?php
ob_start();
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../function/auth.php';

// Kiểm tra đăng nhập và vai trò
if (!isLoggedIn()) {
    echo "<script>alert('Vui lòng đăng nhập để xem thông tin!'); window.location.href='../login.php';</script>";
    exit();
}

if ($_SESSION['role'] != 'Sinh viên') {
    echo "<script>alert('Trang này chỉ dành cho sinh viên!'); window.location.href='manage-student.php';</script>";
    exit();
}

if (!isset($_SESSION['ma_sinhvien'])) {
    echo "Lỗi: ma_sinhvien không tồn tại trong session!";
    exit();
}

// Kiểm tra kết nối database
if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu: " . mysqli_connect_error());
}

// Lấy thông tin sinh viên từ database
$ma_sinhvien = $conn->real_escape_string(trim($_SESSION['ma_sinhvien']));
$query = "SELECT * FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'";
$result = $conn->query($query);

if (!$result) {
    echo "Lỗi SQL: " . $conn->error;
    exit();
}

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Không tìm thấy thông tin sinh viên!'); window.location.href='../login.php';</script>";
    exit();
}

// Lấy thông tin khoa
$ma_khoa = $student['ma_khoa'];
$khoa_result = $conn->query("SELECT ten_khoa FROM khoa WHERE ma_khoa = '$ma_khoa'");
$khoa = $khoa_result->num_rows > 0 ? $khoa_result->fetch_assoc()['ten_khoa'] : $ma_khoa;

// Xử lý cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $ho_ten = $conn->real_escape_string($_POST['ho_ten']);
    $ngay_sinh = $conn->real_escape_string($_POST['ngay_sinh']);
    $gioi_tinh = $conn->real_escape_string($_POST['gioi_tinh']);
    $cmnd_cccd = $conn->real_escape_string($_POST['cmnd_cccd']);
    $dan_toc = $conn->real_escape_string($_POST['dan_toc']);
    $ton_giao = $conn->real_escape_string($_POST['ton_giao']);
    $email = $conn->real_escape_string($_POST['email']);
    $sdt = $conn->real_escape_string($_POST['sdt']);
    $dia_chi_hien_tai = $conn->real_escape_string($_POST['dia_chi_hien_tai']);
    $dia_chi_thuong_tru = $conn->real_escape_string($_POST['dia_chi_thuong_tru']);

    $query = "UPDATE sinhvien SET 
              ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', cmnd_cccd='$cmnd_cccd', 
              dan_toc='$dan_toc', ton_giao='$ton_giao', email='$email', sdt='$sdt', 
              dia_chi_hien_tai='$dia_chi_hien_tai', dia_chi_thuong_tru='$dia_chi_thuong_tru' 
              WHERE ma_sinhvien='$ma_sinhvien'";

    if ($conn->query($query) === TRUE) {
        $result = $conn->query("SELECT * FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'");
        $student = $result->fetch_assoc();
        echo "<script>alert('Cập nhật thông tin thành công!');</script>";
    } else {
        echo "<script>alert('Cập nhật thông tin thất bại! Lỗi: " . addslashes($conn->error) . "');</script>";
    }
}

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $conn->real_escape_string($_POST['current_password']);
    $new_password = $conn->real_escape_string($_POST['new_password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Kiểm tra mật khẩu mới và xác nhận mật khẩu
    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu mới và xác nhận mật khẩu không khớp!']);
        exit();
    }

    // Kiểm tra độ dài và định dạng mật khẩu mới
    $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($password_regex, $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt!']);
        exit();
    }

    // Lấy mật khẩu hiện tại từ bảng taikhoan
    $query = "SELECT password FROM taikhoan WHERE ma_sinhvien = '$ma_sinhvien'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($current_password === $user['password']) { // So sánh plain text
            // Cập nhật mật khẩu mới
            $update_query = "UPDATE taikhoan SET password = '$new_password' WHERE ma_sinhvien = '$ma_sinhvien'";
            if ($conn->query($update_query)) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật mật khẩu thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật mật khẩu thất bại! Lỗi: ' . addslashes($conn->error)]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản!']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Thông Tin Cá Nhân</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../../asset/css/profile.css">
</head>
<body>
    <h1 class="title">QUẢN LÝ SINH VIÊN</h1>
    
    <div class="navigation">
        <div class="user-info">
            <span>Xin chào, <?php echo htmlspecialchars($student['ho_ten']); ?></span>
            <a href="../../logout.php" style="color: #dc3545; margin-left: 10px;">Đăng xuất</a>
        </div>
    </div>
    
    <div class="container">
        <h2 class="page-title">Thông Tin Cá Nhân</h2>
        
        <div class="profile-container">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <div class="profile-avatar-placeholder">
                        <span style="font-size: 80px;">👤</span>
                    </div>
                    <?php if ($student['avatar_path']): ?>
                        <img src="<?php echo htmlspecialchars($student['avatar_path']); ?>" alt="Avatar" style="max-width: 100%; border-radius: 50%;">
                    <?php endif; ?>
                </div>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; margin-bottom: 5px;"><?php echo htmlspecialchars($student['ho_ten']); ?></h3>
                    <p style="color: #6c757d; font-size: 14px;">Sinh viên</p>
                    <p style="color: #0275d8; font-size: 14px; font-weight: 500;"><?php echo htmlspecialchars($student['ma_sinhvien']); ?></p>
                </div>
                
                <div class="file-upload">
                    <button class="btn" style="width: 100%; font-size: 14px;">Thay đổi ảnh đại diện</button>
                    <input type="file" id="avatar-upload" accept="image/*">
                </div>
                
                <ul class="profile-menu" style="margin-top: 20px;">
                    <li><a href="#thong-tin" class="active" onclick="showTab('thong-tin')">Thông tin cá nhân</a></li>
                    <li><a href="#mat-khau" onclick="showTab('mat-khau')">Đổi mật khẩu</a></li>
                </ul>
            </div>
            
            <div class="profile-content">
                <div class="tab-content">
                    <div id="thong-tin" class="active">
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true'): ?>
                            <div class="card">
                                <h3 class="card-title">Chỉnh sửa thông tin</h3>
                                <form method="POST" action="profile.php" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_profile">
                                    <div class="form-group">
                                        <label for="ho_ten">Họ và tên:</label>
                                        <input type="text" id="ho_ten" name="ho_ten" class="form-control" value="<?php echo htmlspecialchars($student['ho_ten']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ngay_sinh">Ngày sinh:</label>
                                        <input type="date" id="ngay_sinh" name="ngay_sinh" class="form-control" value="<?php echo htmlspecialchars($student['ngay_sinh']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="gioi_tinh">Giới tính:</label>
                                        <select id="gioi_tinh" name="gioi_tinh" class="form-control" required>
                                            <option value="Nam" <?php echo $student['gioi_tinh'] == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                            <option value="Nữ" <?php echo $student['gioi_tinh'] == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cmnd_cccd">CMND/CCCD:</label>
                                        <input type="text" id="cmnd_cccd" name="cmnd_cccd" class="form-control" value="<?php echo htmlspecialchars($student['cmnd_cccd']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="dan_toc">Dân tộc:</label>
                                        <input type="text" id="dan_toc" name="dan_toc" class="form-control" value="<?php echo htmlspecialchars($student['dan_toc']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="ton_giao">Tôn giáo:</label>
                                        <input type="text" id="ton_giao" name="ton_giao" class="form-control" value="<?php echo htmlspecialchars($student['ton_giao']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="sdt">Số điện thoại:</label>
                                        <input type="text" id="sdt" name="sdt" class="form-control" value="<?php echo htmlspecialchars($student['sdt']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="dia_chi_hien_tai">Địa chỉ hiện tại:</label>
                                        <textarea id="dia_chi_hien_tai" name="dia_chi_hien_tai" class="form-control"><?php echo htmlspecialchars($student['dia_chi_hien_tai']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="dia_chi_thuong_tru">Địa chỉ thường trú:</label>
                                        <textarea id="dia_chi_thuong_tru" name="dia_chi_thuong_tru" class="form-control"><?php echo htmlspecialchars($student['dia_chi_thuong_tru']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                                    <a href="profile.php" class="btn btn-secondary">Hủy</a>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="card">
                                <h3 class="card-title">Thông tin chung</h3>
                                <div class="info-group">
                                    <div class="info-label">Họ và tên:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['ho_ten']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Ngày sinh:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['ngay_sinh']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Giới tính:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['gioi_tinh']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">CMND/CCCD:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['cmnd_cccd']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Dân tộc:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['dan_toc']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Tôn giáo:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['ton_giao']); ?></div>
                                </div>
                            </div>
                            <div class="card">
                                <h3 class="card-title">Thông tin liên hệ</h3>
                                <div class="info-group">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['email']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Số điện thoại:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['sdt']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Địa chỉ hiện tại:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['dia_chi_hien_tai']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Địa chỉ thường trú:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['dia_chi_thuong_tru']); ?></div>
                                </div>
                            </div>
                            <div class="card">
                                <h3 class="card-title">Thông tin học tập</h3>
                                <div class="info-group">
                                    <div class="info-label">Mã số sinh viên:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['ma_sinhvien']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Lớp:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['lop']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Khoa:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($khoa); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Niên khóa:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($student['nien_khoa']); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="info-label">Trạng thái:</div>
                                    <div class="info-value"><span class="badge badge-success"><?php echo htmlspecialchars($student['trang_thai']); ?></span></div>
                                </div>
                            </div>
                            <a href="profile.php?edit=true" class="btn btn-success">Chỉnh sửa thông tin</a>
                        <?php endif; ?>
                    </div>
                    <div id="mat-khau">
                        <div class="card">
                            <h3 class="card-title">Đổi mật khẩu</h3>
                            <form id="change-password-form" method="POST">
                                <input type="hidden" name="action" value="change_password">
                                <div class="form-group">
                                    <label for="current-password">Mật khẩu hiện tại</label>
                                    <input type="password" id="current-password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                                <div class="form-group">
                                    <label for="new-password">Mật khẩu mới</label>
                                    <input type="password" id="new-password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới" required>
                                    <small style="color: #6c757d; margin-top: 5px; display: block;">
                                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password">Xác nhận mật khẩu mới</label>
                                    <input type="password" id="confirm-password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                                </div>
                                <button type="submit" class="btn btn-success">Cập nhật mật khẩu</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content > div').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            
            document.querySelectorAll('.profile-menu a').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.profile-menu a[href="#${tabId}"]`).classList.add('active');
        }
        
        document.getElementById('change-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch('profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Đã xảy ra lỗi khi cập nhật mật khẩu!');
            });
        });
        
        document.getElementById('avatar-upload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatar = document.querySelector('.profile-avatar');
                    const placeholder = avatar.querySelector('.profile-avatar-placeholder');
                    if (placeholder) placeholder.remove();
                    
                    let img = avatar.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        avatar.appendChild(img);
                    }
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '50%';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>