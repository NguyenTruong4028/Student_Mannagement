<?php
session_start();
include __DIR__ . '/../../config/db.php';

// Chế độ xem chi tiết
$view_mode = isset($_GET['view']) && $_GET['view'] == 1;

// Lấy thông tin sinh viên nếu sửa/xem
$student = null;
if (isset($_GET['id'])) {
    $ma_sinhvien = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'");
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<script>alert('Sinh viên không tồn tại!'); window.location.href='student_management.php';</script>";
        exit();
    }
}

// Lấy danh sách khoa
$faculties = [];
$faculty_result = $conn->query("SELECT ma_khoa, ten_khoa FROM khoa ORDER BY ten_khoa");
if ($faculty_result && $faculty_result->num_rows > 0) {
    while ($row = $faculty_result->fetch_assoc()) {
        $faculties[] = $row;
    }
}

// Lấy danh sách lớp
$classes = [];
$class_result = $conn->query("SELECT lop FROM lophocphan ORDER BY lop");
if ($class_result && $class_result->num_rows > 0) {
    while ($row = $class_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Xử lý thêm/sửa sinh viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$view_mode) {
    $ma_sinhvien = $conn->real_escape_string($_POST['ma_sinhvien']);
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
    $lop = $conn->real_escape_string($_POST['lop']);
    $ma_khoa = $conn->real_escape_string($_POST['ma_khoa']);
    $nien_khoa = $conn->real_escape_string($_POST['nien_khoa']);
    $trang_thai = $conn->real_escape_string($_POST['trang_thai']);
    $avatar_path = $conn->real_escape_string($_POST['avatar_path']);

    // Kiểm tra giá trị bắt buộc
    if (empty($ma_sinhvien) || empty($ho_ten) || empty($gioi_tinh) || empty($lop) || empty($ma_khoa) || empty($trang_thai)) {
        echo "<script>alert('Vui lòng điền đầy đủ các trường bắt buộc!');</script>";
    } elseif (!in_array($gioi_tinh, ['Nam', 'Nữ'])) {
        echo "<script>alert('Vui lòng chọn giới tính hợp lệ!');</script>";
    } elseif (!in_array($trang_thai, ['Đang học', 'Tốt nghiệp', 'Bảo lưu', 'Thôi học'])) {
        echo "<script>alert('Vui lòng chọn trạng thái hợp lệ!');</script>";
    } else {
        // Kiểm tra ma_khoa
        $check_khoa = $conn->query("SELECT ma_khoa FROM khoa WHERE ma_khoa = '$ma_khoa'");
        if ($check_khoa->num_rows == 0) {
            echo "<script>alert('Mã khoa không tồn tại!');</script>";
        } else {
            // Kiểm tra lop
            $check_lop = $conn->query("SELECT lop FROM lophocphan WHERE lop = '$lop'");
            if ($check_lop->num_rows == 0) {
                echo "<script>alert('Lớp $lop không tồn tại trong hệ thống! Vui lòng kiểm tra lại hoặc thêm lớp mới.'); window.location.href='edit_class.php?lop=$lop&ma_khoa=$ma_khoa';</script>";
            } else {
                if ($student) {
                    // Sửa sinh viên
                    $query = "UPDATE sinhvien SET 
                              ho_ten='$ho_ten', ngay_sinh='$ngay_sinh', gioi_tinh='$gioi_tinh', cmnd_cccd='$cmnd_cccd', 
                              dan_toc='$dan_toc', ton_giao='$ton_giao', email='$email', sdt='$sdt', 
                              dia_chi_hien_tai='$dia_chi_hien_tai', dia_chi_thuong_tru='$dia_chi_thuong_tru', 
                              lop='$lop', ma_khoa='$ma_khoa', nien_khoa='$nien_khoa', trang_thai='$trang_thai', 
                              avatar_path='$avatar_path' 
                              WHERE ma_sinhvien='$ma_sinhvien'";
                    $message = "Cập nhật sinh viên thành công!";
                    $error = "Cập nhật sinh viên thất bại!";
                } else {
                    // Kiểm tra trùng ma_sinhvien
                    $check_query = "SELECT ma_sinhvien FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'";
                    $check_result = $conn->query($check_query);
                    if ($check_result->num_rows > 0) {
                        echo "<script>alert('Mã sinh viên đã tồn tại! Vui lòng chọn mã khác.');</script>";
                    } else {
                        // Thêm sinh viên
                        $query = "INSERT INTO sinhvien (ma_sinhvien, ho_ten, ngay_sinh, gioi_tinh, cmnd_cccd, dan_toc, ton_giao, email, sdt, 
                                  dia_chi_hien_tai, dia_chi_thuong_tru, lop, ma_khoa, nien_khoa, trang_thai, avatar_path) 
                                  VALUES ('$ma_sinhvien', '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$cmnd_cccd', '$dan_toc', '$ton_giao', 
                                          '$email', '$sdt', '$dia_chi_hien_tai', '$dia_chi_thuong_tru', '$lop', '$ma_khoa', '$nien_khoa', 
                                          '$trang_thai', '$avatar_path')";
                        $message = "Thêm sinh viên thành công!";
                        $error = "Thêm sinh viên thất bại!";
                    }
                }

                if (isset($query)) {
                    if ($conn->query($query)) {
                        echo "<script>alert('$message'); window.location.href='student_management.php';</script>";
                    } else {
                        $error_message = $conn->error;
                        echo "<script>alert('$error: " . addslashes($error_message) . "');</script>";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $view_mode ? 'Xem Chi Tiết Sinh Viên' : ($student ? 'Sửa Thông Tin Sinh Viên' : 'Thêm Sinh Viên Mới'); ?></title>
    <link rel="stylesheet" href="../../asset/css/edit_student.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="page-wrapper">
        <!-- Main Content -->
        <div class="main-content">
            <div class="top-navigation">
                <div class="user-profile">
                    <span class="user-welcome">Xin chào, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Khách'; ?></span>
                    <a href="../../logout.php" class="logout-link">Đăng xuất</a>
                </div>
            </div>
            <div class="page-header">
                <h1 class="page-title">QUẢN LÝ SINH VIÊN</h1>
                <p class="subtitle">
                    <?php echo $view_mode ? 'Xem thông tin chi tiết sinh viên' : ($student ? 'Chỉnh sửa thông tin sinh viên' : 'Thêm mới sinh viên vào hệ thống'); ?>
                </p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><?php echo $view_mode ? 'Thông Tin Chi Tiết Sinh Viên' : ($student ? 'Chỉnh Sửa Thông Tin Sinh Viên' : 'Thêm Sinh Viên Mới'); ?></h2>
                </div>

                <form action="edit_student.php<?php echo $student ? '?id=' . $student['ma_sinhvien'] : ''; ?>" method="post">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="ma_sinhvien">Mã số sinh viên</label>
                            <input type="text" id="ma_sinhvien" name="ma_sinhvien"
                                value="<?php echo $student ? htmlspecialchars($student['ma_sinhvien']) : ''; ?>"
                                <?php echo $student || $view_mode ? 'readonly' : 'required'; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="ho_ten">Họ và tên</label>
                            <input type="text" id="ho_ten" name="ho_ten"
                                value="<?php echo $student ? htmlspecialchars($student['ho_ten']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : 'required'; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="ngay_sinh">Ngày sinh</label>
                            <input type="date" id="ngay_sinh" name="ngay_sinh"
                                value="<?php echo $student ? htmlspecialchars($student['ngay_sinh']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="gioi_tinh">Giới tính</label>
                            <select id="gioi_tinh" name="gioi_tinh" <?php echo $view_mode ? 'disabled' : 'required'; ?> class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" <?php echo $student && $student['gioi_tinh'] == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo $student && $student['gioi_tinh'] == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cmnd_cccd">CMND/CCCD</label>
                            <input type="text" id="cmnd_cccd" name="cmnd_cccd"
                                value="<?php echo $student ? htmlspecialchars($student['cmnd_cccd']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                value="<?php echo $student ? htmlspecialchars($student['email']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="sdt">Số điện thoại</label>
                            <input type="text" id="sdt" name="sdt"
                                value="<?php echo $student ? htmlspecialchars($student['sdt']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="dan_toc">Dân tộc</label>
                            <input type="text" id="dan_toc" name="dan_toc"
                                value="<?php echo $student ? htmlspecialchars($student['dan_toc']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="ton_giao">Tôn giáo</label>
                            <input type="text" id="ton_giao" name="ton_giao"
                                value="<?php echo $student ? htmlspecialchars($student['ton_giao']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="lop">Lớp</label>
                            <select id="lop" name="lop" <?php echo $view_mode ? 'disabled' : 'required'; ?> class="form-control">
                                <option value="">-- Chọn lớp --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo htmlspecialchars($class['lop']); ?>"
                                        <?php echo $student && $student['lop'] == $class['lop'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['lop']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ma_khoa">Khoa</label>
                            <select id="ma_khoa" name="ma_khoa" <?php echo $view_mode ? 'disabled' : 'required'; ?> class="form-control">
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($faculties as $faculty): ?>
                                    <option value="<?php echo htmlspecialchars($faculty['ma_khoa']); ?>"
                                        <?php echo $student && $student['ma_khoa'] == $faculty['ma_khoa'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($faculty['ten_khoa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nien_khoa">Niên khóa</label>
                            <input type="text" id="nien_khoa" name="nien_khoa" placeholder="VD: 2020-2024"
                                value="<?php echo $student ? htmlspecialchars($student['nien_khoa']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="trang_thai">Trạng thái</label>
                            <select id="trang_thai" name="trang_thai" <?php echo $view_mode ? 'disabled' : 'required'; ?> class="form-control">
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="Đang học" <?php echo $student && $student['trang_thai'] == 'Đang học' ? 'selected' : ''; ?>>Đang học</option>
                                <option value="Tốt nghiệp" <?php echo $student && $student['trang_thai'] == 'Tốt nghiệp' ? 'selected' : ''; ?>>Tốt nghiệp</option>
                                <option value="Bảo lưu" <?php echo $student && $student['trang_thai'] == 'Bảo lưu' ? 'selected' : ''; ?>>Bảo lưu</option>
                                <option value="Thôi học" <?php echo $student && $student['trang_thai'] == 'Thôi học' ? 'selected' : ''; ?>>Thôi học</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="dia_chi_hien_tai">Địa chỉ hiện tại</label>
                            <textarea id="dia_chi_hien_tai" name="dia_chi_hien_tai" <?php echo $view_mode ? 'readonly' : ''; ?>
                                class="form-control"><?php echo $student ? htmlspecialchars($student['dia_chi_hien_tai']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="dia_chi_thuong_tru">Địa chỉ thường trú</label>
                            <textarea id="dia_chi_thuong_tru" name="dia_chi_thuong_tru" <?php echo $view_mode ? 'readonly' : ''; ?>
                                class="form-control"><?php echo $student ? htmlspecialchars($student['dia_chi_thuong_tru']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="avatar_path">Đường dẫn ảnh đại diện</label>
                            <input type="text" id="avatar_path" name="avatar_path"
                                value="<?php echo $student ? htmlspecialchars($student['avatar_path']) : ''; ?>"
                                <?php echo $view_mode ? 'readonly' : ''; ?> class="form-control">
                        </div>
                    </div>

                    <div class="btn-container">
                        <a href="student_management.php" class="btn btn-secondary">Quay lại</a>
                        <?php if (!$view_mode): ?>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $student ? 'Cập nhật thông tin' : 'Thêm sinh viên'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let valid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            valid = false;
                            field.style.borderColor = '#ef4444';
                        } else {
                            field.style.borderColor = '';
                        }
                    });

                    if (!valid) {
                        event.preventDefault();
                        alert('Vui lòng điền đầy đủ thông tin bắt buộc.');
                    }
                });
            }
        });
    </script>
</body>

</html>