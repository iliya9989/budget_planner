-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2024 at 02:58 AM
-- Server version: 10.5.19-MariaDB-0+deb11u2
-- PHP Version: 8.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shei03`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
                           `budget_id` int(11) NOT NULL,
                           `user_id` int(11) NOT NULL,
                           `budget_name` varchar(255) NOT NULL,
                           `budget_balance` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`budget_id`, `user_id`, `budget_name`, `budget_balance`) VALUES
                                                                                    (1, 1, 'TEST 1', 0),
                                                                                    (2, 1, 'ыфвафыва', 0),
                                                                                    (3, 1, 'фывафыва', 0);

-- --------------------------------------------------------

--
-- Table structure for table `budget_expenses`
--

CREATE TABLE `budget_expenses` (
                                   `budget_id` int(11) NOT NULL,
                                   `expense_id` int(11) NOT NULL,
                                   `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_expenses`
--

INSERT INTO `budget_expenses` (`budget_id`, `expense_id`, `user_id`) VALUES
                                                                         (1, 1, NULL),
                                                                         (2, 2, NULL),
                                                                         (3, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `budget_incomes`
--

CREATE TABLE `budget_incomes` (
                                  `budget_id` int(11) NOT NULL,
                                  `income_id` int(11) NOT NULL,
                                  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_incomes`
--

INSERT INTO `budget_incomes` (`budget_id`, `income_id`, `user_id`) VALUES
                                                                       (1, 1, NULL),
                                                                       (2, 2, NULL),
                                                                       (3, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
                              `category_id` int(11) NOT NULL,
                              `category_name` varchar(255) NOT NULL,
                              `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `user_id`) VALUES
                                                                         (1, 'test', NULL),
                                                                         (2, 'test 2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
                            `expense_id` int(11) NOT NULL,
                            `cost` int(11) NOT NULL,
                            `expense_name` varchar(255) NOT NULL,
                            `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `cost`, `expense_name`, `user_id`) VALUES
                                                                             (1, 1, 'test expense', NULL),
                                                                             (2, 1, 'фыва', NULL),
                                                                             (3, 1, 'фывафыва', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
                                      `expense_id` int(11) NOT NULL,
                                      `category_id` int(11) NOT NULL,
                                      `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`expense_id`, `category_id`, `user_id`) VALUES
    (2, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
                           `income_id` int(11) NOT NULL,
                           `amount` int(11) NOT NULL,
                           `income_name` varchar(255) NOT NULL,
                           `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`income_id`, `amount`, `income_name`, `user_id`) VALUES
                                                                            (1, 1, 'test income', NULL),
                                                                            (2, 1, 'фывафыва', NULL),
                                                                            (3, 1, 'фыва', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `income_categories`
--

CREATE TABLE `income_categories` (
                                     `income_id` int(11) NOT NULL,
                                     `category_id` int(11) NOT NULL,
                                     `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income_categories`
--

INSERT INTO `income_categories` (`income_id`, `category_id`, `user_id`) VALUES
    (2, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
                         `user_id` int(11) NOT NULL,
                         `username` varchar(255) NOT NULL,
                         `email` varchar(255) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `facebook_id` varchar(255) DEFAULT NULL,
                         `google_id` varchar(255) DEFAULT NULL,
                         `vk_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `facebook_id`, `google_id`, `vk_id`) VALUES
    (1, 'Pushok', 'iliya9989@gmail.com', '$2y$10$/Do7RYE4vUpLIpWzo70E5.zWb7a5ueFeCk7vs/izCWtYs.3KUZAcO', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
    ADD PRIMARY KEY (`budget_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_expenses`
--
ALTER TABLE `budget_expenses`
    ADD PRIMARY KEY (`budget_id`,`expense_id`),
    ADD KEY `expense_id` (`expense_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budget_incomes`
--
ALTER TABLE `budget_incomes`
    ADD PRIMARY KEY (`budget_id`,`income_id`),
    ADD KEY `income_id` (`income_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
    ADD PRIMARY KEY (`category_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
    ADD PRIMARY KEY (`expense_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
    ADD PRIMARY KEY (`expense_id`,`category_id`),
    ADD KEY `category_id` (`category_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
    ADD PRIMARY KEY (`income_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `income_categories`
--
ALTER TABLE `income_categories`
    ADD PRIMARY KEY (`income_id`,`category_id`),
    ADD KEY `category_id` (`category_id`),
    ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `username` (`username`),
    ADD UNIQUE KEY `email` (`email`),
    ADD UNIQUE KEY `facebook_id` (`facebook_id`),
    ADD UNIQUE KEY `google_id` (`google_id`),
    ADD UNIQUE KEY `vk_id` (`vk_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
    MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
    MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
    MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
    MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
    ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `budget_expenses`
--
ALTER TABLE `budget_expenses`
    ADD CONSTRAINT `budget_expenses_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`budget_id`),
    ADD CONSTRAINT `budget_expenses_ibfk_2` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`),
    ADD CONSTRAINT `budget_expenses_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `budget_incomes`
--
ALTER TABLE `budget_incomes`
    ADD CONSTRAINT `budget_incomes_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`budget_id`),
    ADD CONSTRAINT `budget_incomes_ibfk_2` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`income_id`),
    ADD CONSTRAINT `budget_incomes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
    ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
    ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
    ADD CONSTRAINT `expense_categories_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`),
    ADD CONSTRAINT `expense_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
    ADD CONSTRAINT `expense_categories_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
    ADD CONSTRAINT `incomes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `income_categories`
--
ALTER TABLE `income_categories`
    ADD CONSTRAINT `income_categories_ibfk_1` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`income_id`),
    ADD CONSTRAINT `income_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
    ADD CONSTRAINT `income_categories_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
