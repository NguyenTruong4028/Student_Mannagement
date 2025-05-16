
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Cơ sở dữ liệu: `student_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `ma_khoa` varchar(50) NOT NULL,
  `ten_khoa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`ma_khoa`, `ten_khoa`) VALUES
('CNTT', 'Công nghệ Thông tin'),
('KT', 'Kinh tế'),
('XD', 'Xây dựng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lophocphan`
--

CREATE TABLE `lophocphan` (
  `lop` varchar(50) NOT NULL,
  `ten_lop` varchar(100) NOT NULL,
  `soluongsv` int(11) DEFAULT 0,
  `ma_khoa` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lophocphan`
--

INSERT INTO `lophocphan` (`lop`, `ten_lop`, `soluongsv`, `ma_khoa`) VALUES
('CNTT01', 'Lớp Công nghệ Thông tin 01', 40, NULL),
('KT02', 'Lớp Kinh tế 02', 35, NULL),
('XD03', 'Lớp Xây dựng 03', 30, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sinhvien`
--

CREATE TABLE `sinhvien` (
  `ma_sinhvien` varchar(10) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ') NOT NULL,
  `cmnd_cccd` varchar(20) DEFAULT NULL,
  `dan_toc` varchar(50) DEFAULT NULL,
  `ton_giao` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `dia_chi_hien_tai` text DEFAULT NULL,
  `dia_chi_thuong_tru` text DEFAULT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `ma_khoa` varchar(50) DEFAULT NULL,
  `nien_khoa` varchar(20) DEFAULT NULL,
  `trang_thai` enum('Đang học','Tốt nghiệp','Bảo lưu','Thôi học') DEFAULT 'Đang học',
  `avatar_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sinhvien`
--

INSERT INTO `sinhvien` (`ma_sinhvien`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `cmnd_cccd`, `dan_toc`, `ton_giao`, `email`, `sdt`, `dia_chi_hien_tai`, `dia_chi_thuong_tru`, `lop`, `ma_khoa`, `nien_khoa`, `trang_thai`, `avatar_path`) VALUES
('SV001', 'Nguyễn Văn An', '2002-05-15', 'Nam', '123456789012', 'Kinh', 'Phật giáo', 'an.nguyen@example.com', '0901234567', '123 Đường Láng, Hà Nội', '456 Đường Láng, Hà Nội', 'CNTT01', 'CNTT', '2020-2024', 'Đang học', '/uploads/avatars/sv001.jpg'),
('SV002', 'Trần Thị Bình', '2003-08-22', 'Nữ', '987654321098', 'Kinh', 'Không', 'binh.tran@example.com', '0912345678', '456 Lê Lợi, TP.HCM', '789 Lê Lợi, TP.HCM', 'KT02', 'KT', '2021-2025', 'Đang học', '/uploads/avatars/sv002.jpg'),
('SV003', 'Lê Minh Cường', '2001-12-10', 'Nam', '456789123456', 'Tày', 'Thiên Chúa giáo', 'cuong.le@example.com', '0923456789', '789 Trần Phú, Đà Nẵng', '123 Trần Phú, Đà Nẵng', 'XD03', 'XD', '2019-2023', 'Tốt nghiệp', '/uploads/avatars/sv003.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `username` varchar(50) NOT NULL,
  `ma_sinhvien` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Sinh viên','Quản trị viên') DEFAULT 'Sinh viên',
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`username`, `ma_sinhvien`, `password`, `role`, `is_verified`, `verification_token`, `reset_token`, `created_at`) VALUES
('admin1', NULL, 'admin123', 'Quản trị viên', 1, NULL, NULL, '2025-05-13 08:57:00'),
('SV001', 'SV001', 'sinhvien123', 'Sinh viên', 1, NULL, NULL, '2025-05-13 08:57:00'),
('SV002', 'SV002', '$2y$10$examplehashedpassword2', 'Sinh viên', 0, 'token123456789', NULL, '2025-05-13 08:57:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`ma_khoa`);

--
-- Chỉ mục cho bảng `lophocphan`
--
ALTER TABLE `lophocphan`
  ADD PRIMARY KEY (`lop`),
  ADD KEY `ma_khoa` (`ma_khoa`);

--
-- Chỉ mục cho bảng `sinhvien`
--
ALTER TABLE `sinhvien`
  ADD PRIMARY KEY (`ma_sinhvien`),
  ADD UNIQUE KEY `cmnd_cccd` (`cmnd_cccd`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `lop` (`lop`),
  ADD KEY `ma_khoa` (`ma_khoa`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`username`),
  ADD KEY `ma_sinhvien` (`ma_sinhvien`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `lophocphan`
--
ALTER TABLE `lophocphan`
  ADD CONSTRAINT `lophocphan_ibfk_1` FOREIGN KEY (`ma_khoa`) REFERENCES `khoa` (`ma_khoa`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `sinhvien`
--
ALTER TABLE `sinhvien`
  ADD CONSTRAINT `sinhvien_ibfk_1` FOREIGN KEY (`lop`) REFERENCES `lophocphan` (`lop`) ON DELETE SET NULL,
  ADD CONSTRAINT `sinhvien_ibfk_2` FOREIGN KEY (`ma_khoa`) REFERENCES `khoa` (`ma_khoa`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `taikhoan_ibfk_1` FOREIGN KEY (`ma_sinhvien`) REFERENCES `sinhvien` (`ma_sinhvien`) ON DELETE CASCADE;
COMMIT;

