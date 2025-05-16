<?php
session_start();
include __DIR__ . '/../../config/db.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Quản trị viên') {
    header("Location: ../../login.php");
    exit();
}

// Search student
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$class = isset($_GET['class']) ? $conn->real_escape_string($_GET['class']) : '';
$ma_khoa = isset($_GET['ma_khoa']) ? $conn->real_escape_string($_GET['ma_khoa']) : '';

$query = "SELECT sv.*, k.ten_khoa 
          FROM sinhvien sv 
          LEFT JOIN khoa k ON sv.ma_khoa = k.ma_khoa 
          WHERE 1=1";
if ($search) {
    $query .= " AND (sv.ma_sinhvien LIKE '%$search%' OR sv.ho_ten LIKE '%$search%')";
}
if ($class) {
    $query .= " AND sv.lop = '$class'";
}
if ($ma_khoa) {
    $query .= " AND sv.ma_khoa = '$ma_khoa'";
}

$result = $conn->query($query);
$students = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $result->free();
} else {
    echo "<p style='color: red;'>Lỗi truy vấn sinh viên: " . $conn->error . "</p>";
}

// Delete student
if (isset($_GET['delete'])) {
    $ma_sinhvien = $conn->real_escape_string($_GET['delete']);
    $query = "DELETE FROM sinhvien WHERE ma_sinhvien = '$ma_sinhvien'";
    if ($conn->query($query)) {
        echo "<script>alert('Xóa sinh viên thành công!'); window.location.href='student_management.php';</script>";
    } else {
        echo "<script>alert('Xóa sinh viên thất bại: " . addslashes($conn->error) . "');</script>";
    }
}

// Lấy danh sách lớp và khoa
$classes = $conn->query("SELECT DISTINCT lop FROM sinhvien ORDER BY lop");
$khoas = $conn->query("SELECT ma_khoa, ten_khoa FROM khoa ORDER BY ten_khoa");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sinh Viên - Danh Sách Sinh Viên</title>
    <link rel="stylesheet" href="../../asset/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div id="manage-student-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>

        <div class="navigation">
            <div class="user-info">
                <span class="user-welcome">Xin chào, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Khách'); ?></span>
                <a href="../../logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>

        <div class="main-container">
            <h2 class="page-title">Danh sách sinh viên</h2>

            <div class="search-container">
                <div class="search-bar">
                    <form action="student_management.php" method="get">
                        <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, MSSV..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <select name="class" class="form-option">
                            <option value="">Tất cả lớp</option>
                            <?php while ($row = $classes->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['lop']); ?>" <?php echo $class == $row['lop'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['lop']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <select name="ma_khoa" class="form-option">
                            <option value="">Tất cả khoa</option>
                            <?php while ($row = $khoas->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['ma_khoa']); ?>" <?php echo $ma_khoa == $row['ma_khoa'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['ten_khoa']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button class="btn" style="width: auto;" type="submit">
                            <i class="fas fa-search"></i> 
                        </button>
                    </form>
                </div>

                <button class="btn add-student-btn" onclick="window.location.href='edit_student.php'">
                    <i class="fas fa-user-plus"></i> Thêm sinh viên mới
                </button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>MSSV</th>
                            <th>Họ và tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Lớp</th>
                            <th>Khoa</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['ma_sinhvien']); ?></td>
                                <td><?php echo htmlspecialchars($student['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($student['ngay_sinh']); ?></td>
                                <td><?php echo htmlspecialchars($student['gioi_tinh']); ?></td>
                                <td><?php echo htmlspecialchars($student['lop']); ?></td>
                                <td><?php echo htmlspecialchars($student['ten_khoa']); ?></td>
                                <td class="action-buttons">
                                    <a href="edit_student.php?id=<?php echo $student['ma_sinhvien']; ?>&view=1" class="view-btn">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="edit_student.php?id=<?php echo $student['ma_sinhvien']; ?>" class="edit-btn">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="student_management.php?delete=<?php echo $student['ma_sinhvien']; ?>" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?')" 
                                       class="delete-btn">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">Không tìm thấy sinh viên nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>