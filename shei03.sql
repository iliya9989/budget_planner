-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Май 18 2024 г., 23:35
-- Версия сервера: 10.5.19-MariaDB-0+deb11u2
-- Версия PHP: 8.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `shei03`
--

-- --------------------------------------------------------

--
-- Структура таблицы `budgets`
--

CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `budget_name` varchar(255) NOT NULL,
  `budget_balance` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `budget_expenses`
--

CREATE TABLE `budget_expenses` (
  `budget_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `budget_incomes`
--

CREATE TABLE `budget_incomes` (
  `budget_id` int(11) NOT NULL,
  `income_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `expense_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `expense_categories`
--

CREATE TABLE `expense_categories` (
  `expense_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `incomes`
--

CREATE TABLE `incomes` (
  `income_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `income_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `income_categories`
--

CREATE TABLE `income_categories` (
  `income_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
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
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `budget_expenses`
--
ALTER TABLE `budget_expenses`
  ADD PRIMARY KEY (`budget_id`,`expense_id`),
  ADD KEY `expense_id` (`expense_id`);

--
-- Индексы таблицы `budget_incomes`
--
ALTER TABLE `budget_incomes`
  ADD PRIMARY KEY (`budget_id`,`income_id`),
  ADD KEY `income_id` (`income_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Индексы таблицы `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

--
-- Индексы таблицы `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`expense_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`income_id`);

--
-- Индексы таблицы `income_categories`
--
ALTER TABLE `income_categories`
  ADD PRIMARY KEY (`income_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `facebook_id` (`facebook_id`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD UNIQUE KEY `vk_id` (`vk_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `incomes`
--
ALTER TABLE `incomes`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ограничения внешнего ключа таблицы `budget_expenses`
--
ALTER TABLE `budget_expenses`
  ADD CONSTRAINT `budget_expenses_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`budget_id`),
  ADD CONSTRAINT `budget_expenses_ibfk_2` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`);

--
-- Ограничения внешнего ключа таблицы `budget_incomes`
--
ALTER TABLE `budget_incomes`
  ADD CONSTRAINT `budget_incomes_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`budget_id`),
  ADD CONSTRAINT `budget_incomes_ibfk_2` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`income_id`);

--
-- Ограничения внешнего ключа таблицы `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`),
  ADD CONSTRAINT `expense_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Ограничения внешнего ключа таблицы `income_categories`
--
ALTER TABLE `income_categories`
  ADD CONSTRAINT `income_categories_ibfk_1` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`income_id`),
  ADD CONSTRAINT `income_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
