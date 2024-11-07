-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 07, 2024 lúc 07:26 AM
-- Phiên bản máy phục vụ: 10.4.25-MariaDB
-- Phiên bản PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `users`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user1`
--

CREATE TABLE `user1` (
  `id` int(100) NOT NULL,
  `username` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `passwd` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mid` int(11) DEFAULT NULL,
  `role` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `user1`
--

INSERT INTO `user1` (`id`, `username`, `passwd`, `name`, `phone`, `email`, `DOB`, `mid`, `role`) VALUES
(1, 'shubhamb756@gmail.com', '1234', 'shubham belgaonkar', '8692849041', 'shubhamb756@gmail.com', '25/04/1998', 7, 'user'),
(4, 'soubik@gmail.com', '1234', 'soubik bera', '8622849041', 'soubik@gmail.com', '16/10/1995', 3, 'user'),
(5, 'niru@gmail.com', '1234', 'niru lal', '1234287564', 'niru@gmail.com', '16/09/1996', NULL, 'user'),
(6, 'email@gmail.com', '12345', 'ngo van gia huy', '0704164622', 'email@gmail.com', '19/10/2004', 5, 'admin');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `user1`
--
ALTER TABLE `user1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `mid` (`mid`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `user1`
--
ALTER TABLE `user1`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `user1`
--
ALTER TABLE `user1`
  ADD CONSTRAINT `user1_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `movies` (`mid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
