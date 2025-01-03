-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 23, 2024 lúc 08:55 AM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_movies`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking`
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE IF NOT EXISTS `booking` (
  `bookingid` int NOT NULL AUTO_INCREMENT,
  `theaterid` int NOT NULL,
  `bookingdate` date NOT NULL,
  `person` varchar(50) NOT NULL,
  `userid` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`bookingid`),
  KEY `FK_booking_users` (`userid`),
  KEY `FK_booking` (`theaterid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `booking`
--

INSERT INTO `booking` (`bookingid`, `theaterid`, `bookingdate`, `person`, `userid`, `status`) VALUES
(1, 1, '2024-12-25', '2', 2, 1),
(2, 1, '2024-12-23', '1', 4, 0),
(3, 1, '2024-12-25', '5', 3, 0),
(5, 12, '2024-12-24', '2', 2, 0),
(6, 12, '2024-12-23', '1', 3, 0),
(7, 12, '2024-12-25', '2', 5, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `catid` int NOT NULL AUTO_INCREMENT,
  `catname` varchar(50) NOT NULL,
  PRIMARY KEY (`catid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`catid`, `catname`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Animated'),
(4, 'Comedy'),
(5, 'Drama'),
(6, 'Family'),
(7, 'Fantasy'),
(8, 'Historical'),
(9, 'Horror'),
(10, 'Musical'),
(11, 'Romance'),
(12, 'Science fiction'),
(13, 'Thriller'),
(14, 'Western'),
(15, 'Biographical'),
(16, 'Psychology'),
(17, 'Flutter'),
(18, 'Myth'),
(19, 'bbb'),
(20, 'aa');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category_movie`
--

DROP TABLE IF EXISTS `category_movie`;
CREATE TABLE IF NOT EXISTS `category_movie` (
  `movieid` int NOT NULL,
  `catid` int NOT NULL,
  PRIMARY KEY (`movieid`,`catid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `category_movie`
--

INSERT INTO `category_movie` (`movieid`, `catid`) VALUES
(1, 4),
(1, 15),
(1, 16),
(2, 4),
(2, 16),
(3, 1),
(3, 17),
(4, 1),
(4, 2),
(4, 3),
(4, 18),
(5, 4),
(5, 11),
(6, 9),
(7, 9),
(7, 11),
(7, 13),
(8, 9),
(8, 13),
(9, 1),
(9, 2),
(9, 3),
(9, 11),
(9, 18),
(10, 4),
(10, 9),
(10, 13),
(11, 9),
(12, 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `classid` int NOT NULL AUTO_INCREMENT,
  `classname` int NOT NULL,
  PRIMARY KEY (`classid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `movieid` int NOT NULL,
  `user_ip` varchar(45) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`movieid`,`user_ip`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `likes`
--

INSERT INTO `likes` (`id`, `movieid`, `user_ip`, `created_at`) VALUES
(1, 12, '::1', '2024-12-23 07:47:37'),
(2, 9, '::1', '2024-12-23 08:06:34'),
(3, 8, '::1', '2024-12-23 08:13:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
--

DROP TABLE IF EXISTS `movies`;
CREATE TABLE IF NOT EXISTS `movies` (
  `movieid` int NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` longtext,
  `releasedate` date NOT NULL,
  `image` varchar(1000) NOT NULL,
  `trailer` varchar(1000) NOT NULL,
  `catid` int NOT NULL,
  `likes` int DEFAULT '0',
  PRIMARY KEY (`movieid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`movieid`, `title`, `description`, `releasedate`, `image`, `trailer`, `catid`, `likes`) VALUES
(1, 'CÔNG TỬ BẠC LIÊU', 'Lấy cảm hứng từ giai thoại nổi tiếng của nhân vật được mệnh danh là thiên hạ đệ nhất chơi ngông, Công Tử Bạc Liêu là bộ phim tâm lý hài hước, lấy bối cảnh Nam Kỳ Lục Tỉnh xưa của Việt Nam. BA HƠN - Con trai được thương yêu hết mực của ông Hội đồng Lịnh vốn là chủ ngân hàng đầu tiên tại Việt Nam, sau khi du học Pháp về đã sử dụng cả gia sản của mình vào những trò vui tiêu khiển, ăn chơi trác tán – nên được người dân gọi bằng cái tên Công Tử Bạc Liêu.', '2024-12-25', 'poster_cong_tu_bac_lieu.jpg', 'CONG_TU_BAC_LIEU _ OFFICIAL_TRAILER.mp4', 4, 0),
(2, 'CHỊ DÂU', 'Chuyện bắt đầu khi bà Nhị - con dâu cả của gia đình quyết định nhân dịp đám giỗ của mẹ chồng, tụ họp cả bốn chị em gái - con ruột trong nhà lại để thông báo chuyện sẽ tự bỏ tiền túi ra sửa sang căn nhà từ đường cũ kỹ trước khi bão về. Vấn đề này khiến cho nội bộ gia đình bắt đầu có những lục đục, chị dâu và các em chồng cũng xảy ra mâu thuẫn, bất hoà. Dần dà những sự thật đằng sau việc \"bằng mặt mà không bằng lòng\" giữa các chị em cũng dần được hé lộ, những bí mật, nỗi đau sâu thẳm nhất trong mỗi cá nhân cũng dần được bóc tách. Liệu sợi dây liên kết vốn đã mong manh giữa các chị em có bị cắt đứt và liệu “căn nhà” vốn đã dột nát ấy có còn nguyên vẹn sau cơn bão lớn?', '2024-12-25', 'poster_chi_dau.jpg', 'CHI_DAU_TRAILER.mp4', 0, 0),
(3, 'KRAVEN - THỢ SĂN THỦ LĨNH', 'Kraven the Hunter là câu chuyện đầy khốc liệt và hoành tráng về sự hình thành của một trong những phản diện biểu tượng nhất của Marvel - kẻ thù truyền kiếp của Spiderman. Aaron Taylor-Johnson đảm nhận vai Kraven, một người đàn ông có người cha mafia vô cùng tàn nhẫn, Nikolai Kravinoff (Russell Crowe) - người đã đưa anh vào con đường báo thù với những hệ quả tàn khốc. Điều này thúc đẩy anh không chỉ trở thành thợ săn vĩ đại nhất thế giới, mà còn là một trong những nhân vật đáng sợ nhất.', '2024-12-24', 'poster_tho_san_thu_linh.jpg', 'KRAVEN_THO_SAN_THU_LINH_TRAILER.mp4', 0, 0),
(4, 'HÀNH TRÌNH CỦA MOANA 2', '“Hành Trình của Moana 2” là màn tái hợp của Moana và Maui sau 3 năm, trở lại trong chuyến phiêu lưu cùng với những thành viên mới. Theo tiếng gọi của tổ tiên, Moana sẽ tham gia cuộc hành trình đến những vùng biển xa xôi của Châu Đại Dương và sẽ đi tới vùng biển nguy hiểm, đã mất tích từ lâu. Cùng chờ đón cuộc phiêu lưu của Moana đầy chông gai sắp tới nhé.', '2024-12-25', 'poster_hanh_trinh_cua_moana_2.jpg', 'HANH_TRINH_CUA_MOANA_2_TRAILER.mp4', 0, 0),
(5, 'YÊU EM KHÔNG CẦN LỜI NÓI', 'Phim điện ảnh Yêu Em Không Cần Lời Nói (Hear me: Our summer) ngập tràn hơi thở thanh xuân và chữa lành với sự tham gia của Hong Kyung, Roh Yoon Seo và Kim Min Ju Phim được remake lại từ Nghe Nói (Hear me) của Đài Loan được sản xuất năm 2009.', '2024-12-24', 'poster_yeu_em_khong_can_loi_noi.jpg', 'YEU_EM_KHONG_CAN_LOI_NOI_TRAILER.mp4', 0, 0),
(6, 'LINH MIÊU', 'Linh Miêu: Quỷ Nhập Tràng lấy cảm hứng từ truyền thuyết dân gian về “quỷ nhập tràng” để xây dựng cốt truyện. Phim lồng ghép những nét văn hóa đặc trưng của Huế như nghệ thuật khảm sành - một văn hóa đặc sắc của thời nhà Nguyễn, đề cập đến các vấn đề về giai cấp và quan điểm trọng nam khinh nữ. Đặc biệt, hình ảnh rước kiệu thây ma và những hình nhân giấy không chỉ biểu trưng cho tai ương hay điềm dữ mà còn là hiện thân của nghiệp quả.', '2024-12-23', 'poster_linh_mieu.jpg', 'LINH_MIEU_TRAILER.mp4', 0, 0),
(7, 'GIA ĐÌNH HOÀN HẢO', 'Jae-wan là một luật sư chuyên bào chữa thành công cho những vụ án giết người. Em trai Jae-wan là một bác sĩ lương tri, luôn ưu tiên và đặt bệnh nhân lên trên lợi ích của chính mình. Bất ngờ, một sự việc nghiêm trọng giữa hai người con của hai anh em đã diễn ra và đặt ra cho họ một bài toán lương tâm về hướng giải quyết.', '2024-12-23', 'poster_gia_dinh_hoan_hao.jpg', 'GIA_DINH_HOAN_HAO_TRAILER.mp4', 0, 0),
(8, 'NGÀI QUỶ', 'Một bác sĩ nghi ngờ rằng cái chết kỳ lạ của cô con gái vừa được cấy ghép tim là do buổi trừ tà quái dị gây ra, những âm thanh rên rỉ bên tai khiến người đàn ông tin rằng con gái của mình chưa hề chết. Sau 3 ngày khâm liệm, vị bác sĩ cùng cha xứ quyết tâm tìm ra uẩn khúc về con quỷ ẩn mình trong cơ thể cô bé và đưa cô trở về từ cõi chết.', '2024-12-23', 'poster_ngai_quy.jpg', 'NGAI_QUY_TRAILER.mp4', 0, 0),
(9, 'CHÚA TỂ CỦA NHỮNG CHIẾC NHẪN: CUỘC CHIẾN CỦA ROHIR', 'Lấy bối cảnh 183 năm trước những sự kiện trong bộ ba phim gốc, “Chúa Tể Của Những Chiếc Nhẫn: Cuộc Chiến Của Rohirrim\" kể về số phận của Gia tộc của Helm Hammerhand, vị vua huyền thoại của Rohan. Cuộc tấn công bất ngờ của Wulf, lãnh chúa xảo trá và tàn nhẫn của tộc Dunlending, nhằm báo thù cho cái chết của cha hắn, đã buộc Helm và thần dân của ngài phải chống cự trong pháo đài cổ Hornburg - một thành trì vững chãi sau này được biết đến với tên gọi Helm\'s Deep. Tình thế ngày càng tuyệt vọng, Héra, con gái của Helm, phải dốc hết sức dẫn dắt cuộc chiến chống lại kẻ địch nguy hiểm, quyết tâm tiêu diệt bọn chúng.', '2024-12-25', 'poster_cuoc_chien_cua_rohirrim.jpg', 'CHUA_TE_CUA_NHUNG_CHIEC_NHAN_TRAILER.mp4', 0, 0),
(10, 'GÁI NGỐ GẶP MA LẦY', 'Một nhóm bạn tình cờ tìm thấy một cuốn băng video bí ẩn cũ từ năm 1998 trong tủ của phòng phát thanh của trường. Họ phát hiện ra nếu chiến thắng trò chơi trốn tìm với con ma bị nguyền rủa vào đêm kỷ niệm ngày thành lập trường, họ sẽ nhận được điểm cao trong kỳ thi tuyển sinh đại học. Bần cùng nên làm liều, họ thực hiện các nghi thức như trong cuốn băng bí ẩn. Thế nhưng những hiện tượng ngộ - lạ và ối giồi ôi bắt đầu xảy ra với tất cả bọn họ. Truyền thuyết học đường giờ đây không còn là câu chuyện hư cấu mà đã trở thành tấn hài kịch bao trùm lấy họ.', '2024-12-23', 'poster_gai_ngo_gap_ma_lay.jpg', 'GAI_NGO_GAP_MA_LAY_TRAILER.mp4', 0, 0),
(11, 'CHIẾN ĐỊA TỬ THI', 'Chiến Địa Tử Thi lấy bối cảnh miền Nam Thái Lan trong một cuộc xâm lược ít được biết đến của quân đội Nhật Bản thời kỳ Thế chiến 2. Mek (Nonkul) là một hạ sĩ quan trong quân đội Thái Lan mang tình yêu lớn với đất nước, sẵn sàng hy sinh thân mình vì đại cuộc. Ngược lại, người em trai Mok (Awat Rattanaphinta) là một chàng trai trẻ thích tự do, không bao giờ muốn trở thành một người lính như cha và anh trai mình. Đối với Mok, việc tham gia chiến tranh giống như vứt bỏ mạng sống một cách vô ích. Tuy nhiên, Mok không may bị nhiễm bệnh và biến thành một xác sống đói ăn, điên loạn tấn công con người. Cùng lúc đó, Mek nhận lệnh gia nhập một đơn vị bí ẩn của Nhật Bản để truy lùng những người bị nhiễm bệnh, anh nhận ra người em trai Mok nằm trong danh sách mục tiêu. Khi đứng giữa tình thân và sự an nguy của đất nước, Mek sẽ đưa ra lựa chọn như thế nào?', '2024-12-23', 'poster_chien_dia_tu_thi.jpg', 'CHIEN_DIA_TU_THI_TRAILER.mp4', 0, 0),
(12, 'XÍCH: TRÓI HỒN YỂM XÁC', 'Mỗi ngôi trường đều có những lời đồn và truyền thuyết đáng sợ riêng. Học viện Đức Dục vốn từng bị phát xít Nhật chiếm đóng lại càng thu hút nhiều tò mò khi có lời đồn nơi đây tồn tại hàng trăm oán hồn thường khóc lóc inh ỏi mỗi khi đêm xuống.', '2024-12-23', 'poster_xich.jpg', 'XICH_TROI_HON_DOAT_XAC_TRAILER.mp4', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theater`
--

DROP TABLE IF EXISTS `theater`;
CREATE TABLE IF NOT EXISTS `theater` (
  `theaterid` int NOT NULL AUTO_INCREMENT,
  `theater_name` varchar(100) NOT NULL,
  `duration` int DEFAULT NULL,
  `date` date NOT NULL,
  `price` int NOT NULL,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `movieid` int NOT NULL,
  PRIMARY KEY (`theaterid`),
  KEY `FK_theater` (`movieid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `theater`
--

INSERT INTO `theater` (`theaterid`, `theater_name`, `duration`, `date`, `price`, `location`, `movieid`) VALUES
(1, 'CGV Sư Vạn Hạnh', 113, '2024-12-25', 70000, '0', 1),
(2, 'CGV Satra Củ Chi', 81, '2024-12-25', 70000, '0', 12),
(3, 'CGV Satra Củ Chi', 105, '2024-11-29', 70000, '0', 11),
(4, 'CGV Sư Vạn Hạnh', 91, '2024-12-13', 70000, '0', 10),
(5, 'CGV Vivo City', 135, '2024-12-13', 80000, '0', 9),
(6, 'CGV Pearl Plaza', 94, '2024-12-13', 80000, '0', 8),
(7, 'CGV Lý Chính Thắng', 104, '2024-12-13', 80000, '0', 7),
(8, 'CGV Satra Củ Chi', 109, '2024-11-22', 80000, '0', 6),
(9, 'CGV Vincom Thủ Đức', 106, '2024-12-20', 70000, '0', 5),
(10, 'CGV Crescent Mall', 99, '2024-12-04', 70000, '0', 4),
(11, 'CGV Menas Mall (CGV CT Plaza)', 127, '2024-12-13', 70000, '0', 3),
(12, 'CGV Aeon Tân Phú', 100, '2024-12-20', 70000, '0', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roteype` int NOT NULL,
  `remember_token` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`userid`, `name`, `email`, `password`, `roteype`, `remember_token`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$6r.FhPcMuFQ4JtsvPq5qF.JyS7wccE21evK/k0nsfySnaWA5YA/lq', 1, NULL),
(2, 'user1', 'user1@gmail.com', '$2y$10$1uQOB1jD6xOr9zXhS.9PSuRzIgo4Srl3uVCMN7onTPNa9Yy5r7ora', 2, 'cf6515abcd2eecca91ddce1d66638aec'),
(3, 'user2', 'user2@gmail.com', '$2y$10$9NSIRLavTxYg1gQRuZD75OBf2Lub45SyYlwUW0YE5A3pFPs4RhyY2', 2, NULL),
(4, 'user3', 'user3@gmail.com', '$2y$10$13PwuL/xU9MhvPHgbITGr.UW7/sEBY.G2oIl55g8PO1YmKCQk2MNa', 2, NULL),
(5, 'user4', 'user4@gmail.com', '$2y$10$f22Kl5pGKVrB2RKGrihfquLqAc6FqpG7JN2Vmls24xQ3C1qQtPLoK', 2, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
