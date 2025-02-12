-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2024 at 09:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `question_id`, `user_id`, `content`, `time`) VALUES
(54, 217, 60, 'Hello, do you see it?', '2024-08-15 07:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `comment_image`
--

CREATE TABLE `comment_image` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `image_path` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_image`
--

INSERT INTO `comment_image` (`id`, `comment_id`, `image_path`) VALUES
(133, 54, 'uploads/66bdb2345c50a.png'),
(134, 54, 'uploads/66bdb2345d31d.png'),
(135, 54, 'uploads/66bdb2345ddd8.png');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `module_name`) VALUES
(14, 'Web Programming 1'),
(15, 'User Interface Design'),
(16, 'Professional Project Management');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `user_id`, `module_id`, `content`, `time`) VALUES
(216, 57, 14, 'Everyone,\r\n\r\nPlease help me.\r\n\r\nI need help\r\nI need helpI need helpI need help\r\nI need help\r\nI need help\r\nI need helpI need help\r\nI need help\r\nI need help\r\nI need help\r\nI need help\r\nI need help\r\nI need help\r\nI need help\r\nI need help', '2024-08-15 07:41:56'),
(217, 60, 15, 'Everyone,\r\n\r\nDo you think it\'s ok?', '2024-08-15 07:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `question_image`
--

CREATE TABLE `question_image` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_image`
--

INSERT INTO `question_image` (`id`, `question_id`, `image_path`) VALUES
(843, 216, 'uploads/66bdb144e7deb.png'),
(844, 216, 'uploads/66bdb144e8ef7.png'),
(845, 216, 'uploads/66bdb144e9ad0.png'),
(846, 216, 'uploads/66bdb144ebecf.png'),
(847, 216, 'uploads/66bdb144ec9fd.png'),
(848, 217, 'uploads/66bdb19bc03c0.png'),
(849, 217, 'uploads/66bdb19bc26ab.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `name`, `dob`, `email`, `profile_picture`) VALUES
(49, 'nguyentrannhanduc', '123', 'nguyen tran nhan duc', '1970-01-01', '123@gmail.com', 'uploads/66a7038b0f7b7.jpg'),
(57, 'trandangbach', '1', 'Trần Đăng Bách', '1970-01-01', 'tb7938r@gre.ac.uk', 'uploads/66bd83c62cec4.jpg'),
(58, 'blackbigdick', '12345', 'Huỳnh Phi Long', '2004-01-19', 'long84bro1234910@gmail.com', 'uploads/66b05706c2abb.jpg'),
(59, 'trandinhthong', '1', 'Trần Đình Thông', '1970-01-01', '131@gmaiil.com', 'uploads/66b05f2ae095a.jpg'),
(60, 'trankevu', '123', 'Trần Kế Vũ', '1970-01-01', 'tkv@gmail.com', 'uploads/66b4140369aae.png'),
(61, 'mrbeard', '123456', 'Trần Viết Duy', '2004-11-20', 'tranvietduy05@gmail.com', 'uploads/66bc7dc645e0a.jpg'),
(62, 'admin', '1', 'admin', '1970-01-01', '123@gmail.com', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`question_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comment_image`
--
ALTER TABLE `comment_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `question_image`
--
ALTER TABLE `question_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `comment_image`
--
ALTER TABLE `comment_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `question_image`
--
ALTER TABLE `question_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=852;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment_image`
--
ALTER TABLE `comment_image`
  ADD CONSTRAINT `comment_image_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `question_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_image`
--
ALTER TABLE `question_image`
  ADD CONSTRAINT `question_image_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
