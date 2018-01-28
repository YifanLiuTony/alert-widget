-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 21, 2017 at 12:34 AM
-- Server version: 5.6.35
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `tracy_alert_widget`
--

-- --------------------------------------------------------

--
-- Table structure for table `ALERT_DETAIL`
--

CREATE TABLE `ALERT_DETAIL` (
  `id` int(4) NOT NULL,
  `uid` int(4) NOT NULL,
  `vendor` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount_due` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ref_num` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `orginal_due_date` date NOT NULL,
  `memo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_done` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `USER_INFO`
--

CREATE TABLE `USER_INFO` (
  `id` int(4) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='User information';

--
-- Dumping data for table `USER_INFO`
--

INSERT INTO `USER_INFO` (`id`, `email`, `password`, `name`, `creation_timestamp`, `is_active`) VALUES
(10000003, 'tony@gmail.com', 'f78f12194279e2fbf8c129a5c0ac848c', 'Tony', '2017-10-15 00:29:41', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ALERT_DETAIL`
--
ALTER TABLE `ALERT_DETAIL`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`,`ref_num`),
  ADD KEY `ref_num` (`ref_num`);

--
-- Indexes for table `USER_INFO`
--
ALTER TABLE `USER_INFO`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ALERT_DETAIL`
--
ALTER TABLE `ALERT_DETAIL`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9773;
--
-- AUTO_INCREMENT for table `USER_INFO`
--
ALTER TABLE `USER_INFO`
  MODIFY `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000004;
DELIMITER $$
