-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2025 at 05:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `updationDate`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '08-05-2020 07:23:45 PM');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(255) NOT NULL,
  `categoryDescription` longtext NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `categoryName`, `categoryDescription`, `creationDate`, `updationDate`) VALUES
(1, 'Hello', 'Hii', '2025-09-27 13:24:40', '');

-- --------------------------------------------------------

--
-- Table structure for table `complaintremark`
--

CREATE TABLE `complaintremark` (
  `id` int(11) NOT NULL,
  `complaintNumber` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remark` mediumtext NOT NULL,
  `remarkDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `complaintremark`
--

INSERT INTO `complaintremark` (`id`, `complaintNumber`, `status`, `remark`, `remarkDate`) VALUES
(1, 1, 'in process', 'Process', '2025-09-27 13:26:34'),
(2, 1, 'closed', 'Done', '2025-09-27 13:27:29'),
(3, 2, 'in process', 'work', '2025-09-28 06:49:17'),
(4, 3, 'in process', 'avv', '2025-09-28 07:49:09'),
(5, 3, 'closed', 'aafvacH', '2025-09-28 07:50:05'),
(6, 2, 'closed', 'goihkcyhjv', '2025-09-29 08:17:33'),
(7, 4, 'closed', 'process', '2025-10-04 05:03:22'),
(8, 7, 'in process', 'Process', '2025-10-04 14:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'hisfkl', 'sngssvs', 'Hii.png', 'draft', 1, '2025-10-04 06:40:23', '2025-10-04 12:48:54'),
(3, 'Hello', 'Today is so good', '', 'published', 1, '2025-10-04 12:48:47', '2025-10-04 12:48:57');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `from_user` int(11) DEFAULT NULL COMMENT 'NULL means from admin/system',
  `to_user` int(11) DEFAULT NULL COMMENT 'NULL means to all users',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('admin_to_user','user_to_admin','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `from_user`, `to_user`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES
(1, NULL, 6, 'Annuncement', 'gjijdfigjdfgfd', 'admin_to_user', 0, '2025-10-04 03:56:28', '2025-10-04 03:56:28'),
(2, NULL, 5, 'Annuncement', 'gjijdfigjdfgfd', 'admin_to_user', 1, '2025-10-04 03:56:28', '2025-10-04 04:57:57'),
(5, NULL, 6, 'tyutyutyutu', 'tyuyt', 'admin_to_user', 0, '2025-10-04 04:00:41', '2025-10-04 04:00:41'),
(6, NULL, 5, 'tyutyutyutu', 'tyuyt', 'admin_to_user', 1, '2025-10-04 04:00:41', '2025-10-04 04:57:57'),
(9, NULL, 6, 'tyutyutyutu', 'tyuyt', 'admin_to_user', 0, '2025-10-04 04:05:16', '2025-10-04 04:05:16'),
(10, NULL, 5, 'tyutyutyutu', 'tyuyt', 'admin_to_user', 1, '2025-10-04 04:05:16', '2025-10-04 04:57:57'),
(12, NULL, 6, 'jijyirjy', 'irejijery', 'admin_to_user', 0, '2025-10-04 04:06:02', '2025-10-04 04:06:02'),
(13, NULL, 5, 'jijyirjy', 'irejijery', 'admin_to_user', 1, '2025-10-04 04:06:02', '2025-10-04 04:57:57'),
(15, NULL, 5, 'kfdksd', 'ksdfkds', 'admin_to_user', 1, '2025-10-04 04:06:46', '2025-10-04 04:57:57'),
(16, NULL, 6, 'hii', 'acnknja', 'admin_to_user', 0, '2025-10-04 06:12:09', '2025-10-04 06:12:09'),
(17, NULL, 5, 'hii', 'acnknja', 'admin_to_user', 1, '2025-10-04 06:12:09', '2025-10-04 06:12:16'),
(24, NULL, 6, 'hjskadh', 'sDHBJFh', 'admin_to_user', 0, '2025-10-04 15:42:08', '2025-10-04 15:42:08'),
(25, NULL, 7, 'hjskadh', 'sDHBJFh', 'admin_to_user', 1, '2025-10-04 15:42:08', '2025-10-04 15:42:25'),
(26, NULL, 5, 'hjskadh', 'sDHBJFh', 'admin_to_user', 0, '2025-10-04 15:42:08', '2025-10-04 15:42:08'),
(27, NULL, 1, 'hjskadh', 'sDHBJFh', 'admin_to_user', 0, '2025-10-04 15:42:08', '2025-10-04 15:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `recipient_phone` varchar(15) NOT NULL,
  `message` text NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `work_id` int(11) DEFAULT NULL,
  `status` enum('sent','failed','pending') DEFAULT 'pending',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sms_logs`
--

INSERT INTO `sms_logs` (`id`, `recipient_phone`, `message`, `worker_id`, `work_id`, `status`, `sent_at`, `response_text`) VALUES
(1, '8159824282', 'NEW WORK ASSIGNED\nTitle: Technical Error\nPlace: bhkj\nPriority: MEDIUM\nDeadline: 2025-10-06\nDetails: ukk...\nPlease check your dashboard for complete details.', 2, 1, 'sent', '2025-09-29 11:29:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `stateName` varchar(255) NOT NULL,
  `stateDescription` tinytext NOT NULL,
  `postingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`id`, `stateName`, `stateDescription`, `postingDate`, `updationDate`) VALUES
(1, 'West Bengal', 'Hoo', '2025-09-27 13:24:55', '');

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL,
  `subcategory` varchar(255) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`id`, `categoryid`, `subcategory`, `creationDate`, `updationDate`) VALUES
(1, 1, 'Fail', '2025-09-27 13:24:48', ''),
(2, 1, 'Leafy Vegetables', '2025-09-27 13:25:32', ''),
(3, 1, 'Leafy Vegetables', '2025-09-27 13:26:20', ''),
(4, 1, 'Fresh Fruit', '2025-09-28 07:05:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblcomplaints`
--

CREATE TABLE `tblcomplaints` (
  `complaintNumber` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `subcategory` varchar(255) NOT NULL,
  `complaintType` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `noc` varchar(255) NOT NULL,
  `complaintDetails` mediumtext NOT NULL,
  `complaintFile` varchar(255) DEFAULT NULL,
  `regDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `lastUpdationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcomplaints`
--

INSERT INTO `tblcomplaints` (`complaintNumber`, `userId`, `category`, `subcategory`, `complaintType`, `state`, `noc`, `complaintDetails`, `complaintFile`, `regDate`, `status`, `lastUpdationDate`) VALUES
(1, 1, 1, '', 'General Query', 'West Bengal', 'Hii', 'GOod', '', '2025-09-27 13:26:06', 'closed', '2025-09-27 13:27:29'),
(2, 1, 1, '', 'General Query', 'West Bengal', 'Hii', 'GOod', '', '2025-09-27 13:26:44', 'closed', '2025-09-29 08:17:33'),
(3, 1, 1, '', 'General Query', 'West Bengal', 'Hiida', 'zXcV', '', '2025-09-28 07:46:02', 'closed', '2025-09-28 07:50:05'),
(4, 5, 1, '', 'Complaint', 'West Bengal', 'Hiida', ';cu:JB?N<CNC ', '', '2025-10-04 05:00:40', 'closed', '2025-10-04 05:03:22'),
(5, 5, 1, '', 'Complaint', 'West Bengal', 'Hiida', 'dadadda', 'Key Terms in Web Development_EN.pdf', '2025-10-04 05:52:26', NULL, '0000-00-00 00:00:00'),
(6, 1, 1, '', 'Emergency', 'West Bengal', 'Hiida', 'tfeagkchjxbnahvdsfbjnk.ml/,;', '', '2025-10-04 12:42:04', NULL, '0000-00-00 00:00:00'),
(7, 7, 1, '', 'General Query', 'West Bengal', 'High bill', 'High bill', '', '2025-10-04 14:35:59', 'in process', '2025-10-04 14:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `tblfeedback`
--

CREATE TABLE `tblfeedback` (
  `id` int(11) NOT NULL,
  `complaintId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `feedbackType` varchar(50) NOT NULL,
  `comments` text DEFAULT NULL,
  `submissionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfeedback`
--

INSERT INTO `tblfeedback` (`id`, `complaintId`, `userId`, `rating`, `feedbackType`, `comments`, `submissionDate`, `isRead`) VALUES
(1, 1, 1, 3, 'Other', 'Good', '2025-09-28 05:37:13', 0),
(2, 3, 1, 3, 'Staff Behavior', 'Goodqef', '2025-09-28 06:26:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `userip` binary(16) NOT NULL,
  `loginTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(1, 0, 'john@gmail.com', 0x3a3a3100000000000000000000000000, '2020-05-08 14:14:43', '', 0),
(2, 1, 'john@gmail.com', 0x3a3a3100000000000000000000000000, '2020-05-08 14:14:50', '08-05-2020 07:44:51 PM', 1),
(3, 1, 'john@gmail.com', 0x3a3a3100000000000000000000000000, '2020-05-08 14:16:30', '', 1),
(4, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-25 02:43:44', '', 1),
(5, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:37:43', '', 1),
(6, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:45:09', '', 1),
(7, 0, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:46:14', '', 0),
(8, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:46:23', '', 1),
(9, 0, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:53:01', '', 0),
(10, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 04:53:16', '', 1),
(11, 0, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 05:09:49', '', 0),
(12, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 05:10:01', '', 1),
(13, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 06:20:32', '27-09-2025 11:56:42 AM', 1),
(14, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 06:46:50', '', 1),
(15, 2, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 07:34:16', '', 1),
(16, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 07:49:23', '27-09-2025 01:19:34 PM', 1),
(17, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 07:51:06', '', 1),
(18, 5, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 07:53:08', '27-09-2025 01:28:25 PM', 1),
(19, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 08:00:33', '', 1),
(20, 6, 'rimibanerjee779@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 08:15:01', '27-09-2025 01:45:24 PM', 1),
(21, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 10:09:21', '27-09-2025 04:27:22 PM', 1),
(22, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 11:03:01', '', 1),
(23, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-27 12:16:18', '', 1),
(24, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-28 06:42:41', '28-09-2025 12:15:56 PM', 1),
(25, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-28 06:49:39', '28-09-2025 12:30:45 PM', 1),
(26, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-28 07:19:25', '', 1),
(27, 0, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-28 11:54:59', '', 0),
(28, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-28 11:55:10', '', 1),
(29, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 08:06:59', '', 1),
(30, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 08:18:04', '', 1),
(31, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 08:57:32', '29-09-2025 04:33:12 PM', 1),
(32, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 11:03:55', '29-09-2025 04:34:00 PM', 1),
(33, 0, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 13:17:10', '', 0),
(34, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 13:17:24', '', 1),
(35, 0, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 16:15:56', '', 0),
(36, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-29 16:16:10', '29-09-2025 09:48:10 PM', 1),
(37, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-09-30 05:41:10', '30-09-2025 11:12:01 AM', 1),
(38, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-03 05:14:59', '03-10-2025 10:45:30 AM', 1),
(39, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 03:55:31', '', 1),
(40, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 04:07:37', '', 1),
(41, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 04:19:05', '04-10-2025 10:13:57 AM', 1),
(42, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 04:45:13', '', 1),
(43, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 06:21:42', '04-10-2025 11:54:06 AM', 1),
(44, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 09:20:52', '', 1),
(45, 1, 'rajibdastopper@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 11:38:20', '04-10-2025 08:24:11 PM', 1),
(46, 7, 'dassupra666@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 14:32:38', '04-10-2025 08:55:44 PM', 1),
(47, 5, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 14:55:29', '04-10-2025 08:47:06 PM', 1),
(48, 5, 'soumya@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 15:17:36', '', 1),
(49, 0, 'dassupra666@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:30', '', 0),
(50, 0, 'Soumya', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:33', '', 0),
(51, 0, 'dassupra666@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:50', '', 0),
(52, 0, 'Soumya', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:51', '', 0),
(53, 0, 'Soumya', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:51', '', 0),
(54, 0, 'Soumya', 0x3a3a3100000000000000000000000000, '2025-10-04 15:39:51', '', 0),
(55, 7, 'dassupra666@gmail.com', 0x3a3a3100000000000000000000000000, '2025-10-04 15:40:38', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `userEmail` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contactNo` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `consumerId` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `regDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `userEmail`, `password`, `contactNo`, `address`, `consumerId`, `status`, `regDate`) VALUES
(1, 'Soumya Das', 'rajibdastopper@gmail.com', 'cc20cc56b9485515925f74c0ccd88dbc', '8159824282', 'Ratulia', '90128190', 1, '2025-09-27 07:48:00'),
(5, 'Rajib Das', 'soumya@gmail.com', 'd7812b94b1962436cd28c7b5004e059e', '1111111111', 'Ratulia', '78638975', 1, '2025-09-27 07:52:49'),
(6, 'Rimi banerjee', 'rimibanerjee779@gmail.com', '5a72f3f4442091cecd8b7518ff252f7c', '7878070872', 'vadodara', '12345', 1, '2025-09-27 08:14:23'),
(7, 'supra', 'dassupra666@gmail.com', '65abfc2373bdd3e1b978007d1c9cb1a4', '9832700176', 'Ratulia', '660', 1, '2025-10-04 14:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_messages`
--

CREATE TABLE `user_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `direction` enum('user_to_admin','admin_to_user') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_messages`
--

INSERT INTO `user_messages` (`id`, `user_id`, `admin_id`, `subject`, `message`, `direction`, `is_read`, `created_at`) VALUES
(2, 1, 1, 'Good', 'ach', 'admin_to_user', 1, '2025-10-04 06:09:04'),
(3, 1, 1, 'Good', 'cs kC', 'admin_to_user', 1, '2025-10-04 08:09:31'),
(4, 5, NULL, 'Soumya', 'Dajascabck  .X ', 'user_to_admin', 1, '2025-10-04 08:33:30'),
(6, 5, NULL, 'cjka/l', 'anl/vk', 'user_to_admin', 1, '2025-10-04 08:43:23'),
(7, 5, 1, 'Re: Soumyaca ', 'acl  v', 'admin_to_user', 1, '2025-10-04 08:43:44'),
(8, 1, 1, 'Re: Soumya', 'c laav avva', 'admin_to_user', 1, '2025-10-04 08:43:55'),
(9, 1, 1, 'Good', 'ahlkca', 'admin_to_user', 1, '2025-10-04 08:59:50'),
(10, 5, 1, 'Good', 'amc/, /aca', 'admin_to_user', 1, '2025-10-04 09:00:23'),
(11, 5, 1, 'Goodca m,c', 'sdc ms//', 'admin_to_user', 1, '2025-10-04 09:09:43'),
(12, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:53:59'),
(13, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:54:29'),
(14, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:55:00'),
(15, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:55:31'),
(16, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:56:02'),
(17, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:56:33'),
(18, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:57:03'),
(19, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:57:35'),
(20, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:58:03'),
(21, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:58:33'),
(22, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:58:40'),
(23, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:59:11'),
(24, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 14:59:42'),
(25, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:00:13'),
(26, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:00:44'),
(27, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:01:15'),
(28, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:01:47'),
(29, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:02:17'),
(30, 5, NULL, 'Hello', 'Good', 'user_to_admin', 1, '2025-10-04 15:14:28'),
(31, 5, NULL, 'Hello', 'Good', 'user_to_admin', 1, '2025-10-04 15:14:59'),
(32, 5, NULL, 'Hello', 'Good', 'user_to_admin', 1, '2025-10-04 15:15:30'),
(34, 5, 1, 'Re: uudoiuqo', 'bjhhaxsb', 'admin_to_user', 1, '2025-10-04 15:16:08'),
(35, 5, NULL, 'uudoiuqo', 'wqu8qpuq', 'user_to_admin', 1, '2025-10-04 15:16:10'),
(36, 7, 1, 'Hii', 'Heeloo', '', 0, '2025-10-04 15:25:41'),
(37, 7, NULL, 'adlhajk', 'sdak.dnsvsjsa', 'user_to_admin', 1, '2025-10-04 15:40:58'),
(38, 7, 1, 'Re: adlhajk', 'bkj', 'admin_to_user', 1, '2025-10-04 15:41:18'),
(39, 7, NULL, 'adlhajk', 'sdak.dnsvsjsa', 'user_to_admin', 0, '2025-10-04 15:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `department` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`id`, `name`, `email`, `password`, `phone`, `department`, `address`, `salary`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Soumya Das', 'rajibdastopper@gmail.com', 'e9335e177b288c7af4af8f1225c3f938', '8159824282', 'HR', 'Ratulia\r\nRatulia High School H.S.', 3256254.00, 'inactive', '2025-09-29 11:27:24', '2025-10-04 13:12:19'),
(3, 'Soumya', 'soumya@gmail.com', 'd7812b94b1962436cd28c7b5004e059e', '8159824282', 'Technical', 'SHIOabkj', 400.00, 'active', '2025-10-04 15:34:23', '2025-10-04 15:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `works`
--

CREATE TABLE `works` (
  `id` int(11) NOT NULL,
  `work_title` varchar(255) NOT NULL,
  `work_description` text NOT NULL,
  `place_address` text NOT NULL,
  `assigned_worker_id` int(11) NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','assigned','in_progress','completed','cancelled') DEFAULT 'pending',
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `works`
--

INSERT INTO `works` (`id`, `work_title`, `work_description`, `place_address`, `assigned_worker_id`, `priority`, `status`, `assigned_date`, `deadline`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 'Technical Error', 'ukk', 'bhkj', 2, 'medium', 'completed', '2025-09-29 11:29:13', '2025-10-06', 'zzs', '2025-09-29 11:29:13', '2025-09-29 11:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `work_updates`
--

CREATE TABLE `work_updates` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `update_type` enum('assignment','progress','completion','issue') DEFAULT 'progress',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_updates`
--

INSERT INTO `work_updates` (`id`, `work_id`, `update_text`, `update_type`, `created_by`, `created_at`) VALUES
(1, 1, 'Work assigned to Soumya Das', 'assignment', 1, '2025-09-29 11:29:13'),
(2, 1, 'Status changed to: in_progress. Notes: jhgjgg', 'progress', 1, '2025-09-29 11:31:43'),
(3, 1, 'Status changed to: completed. Notes: Done', 'progress', 1, '2025-09-29 11:37:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaintremark`
--
ALTER TABLE `complaintremark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_user` (`from_user`),
  ADD KEY `to_user` (`to_user`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  ADD PRIMARY KEY (`complaintNumber`);

--
-- Indexes for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_complaint_feedback` (`complaintId`,`userId`),
  ADD KEY `idx_user` (`userId`),
  ADD KEY `idx_date` (`submissionDate`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userEmail` (`userEmail`),
  ADD UNIQUE KEY `consumerId` (`consumerId`);

--
-- Indexes for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `works`
--
ALTER TABLE `works`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_worker_id` (`assigned_worker_id`);

--
-- Indexes for table `work_updates`
--
ALTER TABLE `work_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_id` (`work_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaintremark`
--
ALTER TABLE `complaintremark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  MODIFY `complaintNumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_messages`
--
ALTER TABLE `user_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `works`
--
ALTER TABLE `works`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `work_updates`
--
ALTER TABLE `work_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`from_user`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`to_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tblfeedback`
--
ALTER TABLE `tblfeedback`
  ADD CONSTRAINT `tblfeedback_ibfk_1` FOREIGN KEY (`complaintId`) REFERENCES `tblcomplaints` (`complaintNumber`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblfeedback_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD CONSTRAINT `user_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_messages_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `works`
--
ALTER TABLE `works`
  ADD CONSTRAINT `works_ibfk_1` FOREIGN KEY (`assigned_worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_updates`
--
ALTER TABLE `work_updates`
  ADD CONSTRAINT `work_updates_ibfk_1` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
