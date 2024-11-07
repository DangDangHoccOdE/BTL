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
-- Cấu trúc bảng cho bảng `movies`
--

CREATE TABLE `movies` (
  `mid` int(10) NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `genre` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `rdate` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `runtime` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decription` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `viewers` int(10) DEFAULT 1,
  `imgpath` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `videopath` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`mid`, `name`, `genre`, `rdate`, `runtime`, `decription`, `viewers`, `imgpath`, `videopath`, `price`) VALUES
(1, 'Siêu Thú Cuồng Nộ', 'Viễn tưởng', '2018', '120', 'animals', 8, 'Poster-phim-sieu-thu-cuong-no.jpg', 'RAMPAGE Trailer.mp4', 10000),
(2, 'Black Panther', 'Viễn tưởng', '2017', '140', 'Vương quốc Wakanda, quê hương của Black Panther/ T\'Challa hiện ra qua lời kể của một nhân chứng sống sót trở về. Đây là quốc gia khá khép kín và sở hữu lượng Vibranium lớn nhất trên thế giới. Black Panther - người cầm quyền của Wakanda sở hữu bộ áo giáp chống đạn và móng vuốt sắc nhọn, anh được miêu tả là “người tốt với trái tim nhân hậu”. Nhưng cũng chính vì những đức tính tốt này mà Black Panther gặp khó khăn khi kế thừa ngai vàng sau khi vua cha băng hà. Đối mặt với sự phản bội và hiểm nguy, vị vua trẻ phải tập hợp các đồng minh và phát huy toàn bộ sức mạnh của Black Panther để đánh bại kẻ thù, đem lại an bình cho nhân dân của mình.', 32, 'Black_Panther_Wakanda_Forever_poster.jpg', 'Black Panther Teaser Trailer [HD].mp4', 15000),
(3, 'spiderman homecoming', 'fiction', '2018', '110', 'super hero movie', 6, 'spider-man-homecoming.jpg', 'Spider-Man Homecoming.mp4', 10000),
(4, 'jumanji', 'adventure', '2017', '130', '4 kids stuck in video game', 11, 'jumanji2017.jpg', 'JUMANJI 17.mp4', 20000),
(5, 'the conjuring', 'horror', '2013', '120', 'ghost house', 4, 'the_conjuring.jpg', 'The Conjuring.mp4', 19000),
(6, 'the conjuring 2', 'horror', '2015', '115', 'cursed family', 3, 'conjuring2.jpg', 'The Conjuring 2.mp4', 15000),
(7, 'Avengers: Infinity War', 'fiction', '2018', '123', 'collaboration of all marvel characters', 4, 'infinity_war.jpg', 'Avengers Infinity War.mp4', 25000),
(8, 'Sứ Giả Của Chúa', 'Horror', '2020', '92', 'djfsdf', 12, 'the-devine-fury.jpg', '243315062_195377862708493_7722592316589441431_n.mp', 17000),
(9, 'Kẻ Ăn Hồn', 'Kinh dị', '2023', '105', 'Trong vùng đất ẩn chứa hàng loạt bí ẩn và cái chết đầy u tối của Làng Địa Ngục, xoay quanh câu chuyện huyền bí về 5 mạng đổi bình Rượu Sọ Người, tức là loại rượu ma thuật cổ xưa nhất. Thập Nương, người phụ nữ mặc chiếc áo đỏ nổi bật, giữ trong tay bí mật của loại rượu này - sức mạnh không thể tin được. Bộ phim Kẻ Ăn Hồn không chỉ là câu chuyện kinh dị về ma thuật và cái chết, mà còn là hành trình khám phá văn hóa Việt qua các biểu tượng như bầy rối nước, thủy đình hay bài vè. Những chi tiết đặc trưng của làng Địa Ngục như bà Vạn lái đò chở hồn ma, mồ hôi máu và đom đóm câu hồn sẽ khiến khán giả không thể rời mắt khỏi màn ảnh. Với viễn cảnh u ám của ngôi làng này, cuộc phiêu lưu để khám phá loại cổ thuật kỳ diệu - Rượu Sọ Người - sẽ mang lại cho người xem những trải nghiệm không thể quên trong thế giới ma quỷ huyền bí.', 2, 'kẻ_ăn_hồn.jpg', 'A, Mày khinh bô mày đấy à.mp4', 10000),
(10, 'Avengers: Endgame', 'Viễn tưởng', '2019', '181', 'Sau những sự kiện tàn khốc của Avengers: Infinity War (2018), vũ trụ đang bị hủy hoại. Với sự trợ giúp của các đồng minh còn lại, Avengers đã lắp ráp một lần nữa để đảo ngược hành động của Thanos và khôi phục lại sự cân bằng cho vũ trụ.', 5, 'Avengers_Endgame_bia_teaser.jpg', '243315062_195377862708493_7722592316589441431_n.mp', 25000),
(11, 'Deadpool & Wolverine', 'Hành động', '2024', '128', 'Một Wade Wilson vô hồn đang vật lộn với cuộc sống thường dân với những ngày tháng của anh như một lính đánh thuê có đạo đức, Deadpool, đã qua. Nhưng khi quê hương của anh phải đối mặt với một mối đe dọa hiện hữu, Wade phải miễn cưỡng mặc lại bộ đồ với một Wolverine còn miễn cưỡng hơn.', 2, 'Deadpool_&_Wolverine_poster.jpg', 'Screenrecorder-2024-02-27-12-35-03-483.mp4', 14000),
(12, 'Venom 3: Kèo cuối', 'Hành động', '2024', '109', 'sdkjfkdsfds', 2, 'Venom_The_Last_Dance_Poster.jpg', 'Video - Facebook.mp4', 16000);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`mid`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `mid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
