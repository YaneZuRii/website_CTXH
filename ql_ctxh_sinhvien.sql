-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2026 at 08:39 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ql_ctxh_sinhvien`
--
CREATE DATABASE IF NOT EXISTS `ql_ctxh_sinhvien` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ql_ctxh_sinhvien`;

-- --------------------------------------------------------

--
-- Table structure for table `hoatdong`
--

DROP TABLE IF EXISTS `hoatdong`;
CREATE TABLE IF NOT EXISTS `hoatdong` (
  `maHoatDong` int(11) NOT NULL AUTO_INCREMENT,
  `tenHoatDong` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `moTa` text COLLATE utf8mb4_unicode_ci,
  `diemCong` int(11) DEFAULT NULL,
  `maQR` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thoiGianBatDau` datetime DEFAULT NULL,
  `thoiGianKetThuc` datetime DEFAULT NULL,
  `trangThai` enum('Mở','Đóng') COLLATE utf8mb4_unicode_ci DEFAULT 'Mở',
  PRIMARY KEY (`maHoatDong`),
  UNIQUE KEY `maQR` (`maQR`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khoa`
--

DROP TABLE IF EXISTS `khoa`;
CREATE TABLE IF NOT EXISTS `khoa` (
  `maKhoa` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenKhoa` varchar(150) CHARACTER SET utf8 NOT NULL,
  `dienThoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maKhoa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lop`
--

DROP TABLE IF EXISTS `lop`;
CREATE TABLE IF NOT EXISTS `lop` (
  `maLop` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenLop` varchar(100) CHARACTER SET utf8 NOT NULL,
  `maKhoa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `khoaHoc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heDaoTao` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `coVanHocTap` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`maLop`),
  KEY `fk_lop_khoa` (`maKhoa`),
  CONSTRAINT `const_lop_khoa` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sinhvien`
--

DROP TABLE IF EXISTS `sinhvien`;
CREATE TABLE IF NOT EXISTS `sinhvien` (
  `maSV` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hoTen` varchar(150) CHARACTER SET utf8 NOT NULL,
  `gioiTinh` enum('Nam','Nữ','Khác') COLLATE utf8mb4_unicode_ci DEFAULT 'Nam',
  `ngaySinh` date DEFAULT NULL,
  `cccd` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diaChi` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `soDienThoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maLop` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngayNhapHoc` date DEFAULT NULL,
  `trangThai` enum('Đang học','Bảo lưu','Đã tốt nghiệp') COLLATE utf8mb4_unicode_ci DEFAULT 'Đang học',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`maSV`),
  UNIQUE KEY `cccd` (`cccd`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_sv_lop` (`maLop`),
  CONSTRAINT `const_sv_lop` FOREIGN KEY (`maLop`) REFERENCES `lop` (`maLop`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `diemrenluyen`
--

DROP TABLE IF EXISTS `diemrenluyen`;
CREATE TABLE IF NOT EXISTS `diemrenluyen` (
  `maDRL` int(11) NOT NULL AUTO_INCREMENT,
  `maSV` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hocKy` int(11) DEFAULT NULL,
  `namHoc` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diemHocTap` int(11) DEFAULT '0',
  `diemKyLuat` int(11) DEFAULT '0',
  `diemCTXH` int(11) DEFAULT '0',
  `tongDiem` int(11) DEFAULT NULL,
  `xepLoai` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`maDRL`),
  KEY `fk_drl_sv` (`maSV`),
  CONSTRAINT `const_drl_sv` FOREIGN KEY (`maSV`) REFERENCES `sinhvien` (`maSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `diemrenluyen`
--
DROP TRIGGER IF EXISTS `trg_TinhDRL`;
DELIMITER $$
CREATE TRIGGER `trg_TinhDRL` BEFORE INSERT ON `diemrenluyen` FOR EACH ROW BEGIN
    SET NEW.tongDiem = NEW.diemHocTap + NEW.diemKyLuat + NEW.diemCTXH;

    SET NEW.xepLoai = CASE
        WHEN NEW.tongDiem >= 90 THEN 'Xuất sắc'
        WHEN NEW.tongDiem >= 80 THEN 'Tốt'
        WHEN NEW.tongDiem >= 65 THEN 'Khá'
        WHEN NEW.tongDiem >= 50 THEN 'Trung bình'
        ELSE 'Yếu'
    END;
END
$$
DELIMITER ;


-- --------------------------------------------------------

--
-- Table structure for table `thamgiahoatdong`
--

DROP TABLE IF EXISTS `thamgiahoatdong`;
CREATE TABLE IF NOT EXISTS `thamgiahoatdong` (
  `maThamGia` int(11) NOT NULL AUTO_INCREMENT,
  `maSV` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maHoatDong` int(11) DEFAULT NULL,
  `thoiGianQuet` datetime DEFAULT CURRENT_TIMESTAMP,
  `ketQua` enum('Thành công','Thất bại') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lyDo` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `diemCong` int(11) DEFAULT '0',
  PRIMARY KEY (`maThamGia`),
  UNIQUE KEY `maSV` (`maSV`,`maHoatDong`),
  KEY `fk_tg_hd` (`maHoatDong`),
  CONSTRAINT `const_tg_sv` FOREIGN KEY (`maSV`) REFERENCES `sinhvien` (`maSV`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `const_tg_hd` FOREIGN KEY (`maHoatDong`) REFERENCES `hoatdong` (`maHoatDong`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `thamgiahoatdong`
--
DROP TRIGGER IF EXISTS `trg_CongDiemCTXH`;
DELIMITER $$
CREATE TRIGGER `trg_CongDiemCTXH` AFTER INSERT ON `thamgiahoatdong` FOR EACH ROW BEGIN
    IF NEW.ketQua = 'Thành công' THEN
        INSERT INTO TongCTXH(maSV, tongDiem)
        VALUES (NEW.maSV, NEW.diemCong)
        ON DUPLICATE KEY UPDATE
            tongDiem = tongDiem + NEW.diemCong;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tongctxh`
--

DROP TABLE IF EXISTS `tongctxh`;
CREATE TABLE IF NOT EXISTS `tongctxh` (
  `maSV` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tongDiem` int(11) DEFAULT '0',
  PRIMARY KEY (`maSV`),
  CONSTRAINT `const_tongctxh_sv` FOREIGN KEY (`maSV`) REFERENCES `sinhvien` (`maSV`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Sinh viên','Quản trị viên','Khoa') COLLATE utf8mb4_unicode_ci DEFAULT 'Sinh viên',
  `trangThai` enum('Hoạt động','Bị khóa') COLLATE utf8mb4_unicode_ci DEFAULT 'Hoạt động',
  `maSV` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maKhoa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `maSV_UNIQUE` (`maSV`),
  UNIQUE KEY `maKhoa_UNIQUE` (`maKhoa`),
  KEY `fk_user_sv` (`maSV`),
  KEY `fk_user_khoa` (`maKhoa`),
  CONSTRAINT `const_user_sv` FOREIGN KEY (`maSV`) REFERENCES `sinhvien` (`maSV`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `const_user_khoa` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
