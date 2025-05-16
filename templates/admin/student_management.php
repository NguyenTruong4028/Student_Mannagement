<?php
include __DIR__ . '/../../config/db.php';

//Kiểm tra quyền admin
if(!isset($_SESSION['role'])||$_SESSION['role'] != 'Quản trị viên'){
    header('Location: ../login.php');
    exit();
}
//Search student
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
    $query = "DELETE FROM student_management_sinhvien WHERE ma_sinhvien = '$ma_sinhvien'";
    if ($conn->query($query)) {
        echo "<script>alert('Xóa sinh viên thành công!'); window.location.href='student_management.php';</script>";
    } else {
        echo "<script>alert('Xóa sinh viên thất bại!');</script>";
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
    <style>
        .search-bar form {
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .action-buttons a {
            display: inline-flex;
            align-items: center;
            margin-right: 8px;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .action-buttons .edit-btn {
            background-color: #fef9c3;
            color: #854d0e;
        }
        
        .action-buttons .edit-btn:hover {
            background-color: #fef08a;
        }
        
        .action-buttons .view-btn {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        
        .action-buttons .view-btn:hover {
            background-color: #bae6fd;
        }
        
        .action-buttons .delete-btn {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .action-buttons .delete-btn:hover {
            background-color: #fecaca;
        }
        
        .action-buttons i {
            margin-right: 5px;
            font-size: 12px;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        table {
            border-radius: 8px;
            overflow: hidden;
            border: none;
        }
        
        table th {
            background-color: #0275d8;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            padding: 15px 12px;
            border: none;
        }
        
        table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            border-left: none;
            border-right: none;
            font-size: 15px;
        }
        
        table tr:hover {
            background-color: #f0f9ff;
        }
        
        .add-student-btn {
            background-color: #0275d8;
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .add-student-btn:hover {
            background-color: #0258a8;
            transform: translateY(-2px);
        }
        
        .add-student-btn i {
            margin-right: 8px;
        }
        
        .pagination {
            margin-top: 30px;
        }
        
        .pagination a {
            transition: all 0.3s;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #e0f2fe;
        }
        
        .navigation {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            width: 100%;
            max-width: 1200px;
            display: flex;
            justify-content: flex-end;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-greeting {
            font-weight: 600;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .search-bar form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-bar .form-control,
            .search-bar .form-option {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .action-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .action-buttons a {
                margin-right: 0;
            }
            
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body>
    <div id="manage-student-page" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <h1 class="title">QUẢN LÝ SINH VIÊN</h1>

        <div class="navigation">
            <div class="user-info">
                <span class="user-greeting">Xin chào, Admin</span>
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
                            <i class="fas fa-search"></i> Tìm kiếm
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

            <ul class="pagination">
                <li><a href="#"><i class="fas fa-angle-double-left"></i></a></li>
                <li><a href="#" class="active">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#"><i class="fas fa-angle-double-right"></i></a></li>
            </ul>
        </div>
    </div>

    <script src="../../asset/js/main.js"></script>
</body>

</html>