-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2026 at 04:43 AM
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
-- Database: `materialcodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `MATERIAL_ID` int(11) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `ORGANIZATION_ID` int(11) DEFAULT NULL,
  `MATERIAL_NAME` varchar(100) DEFAULT NULL,
  `QUANTITY` int(11) DEFAULT NULL,
  `PRICE` int(11) DEFAULT NULL,
  `SIZE` varchar(11) DEFAULT NULL,
  `MODEL` varchar(50) DEFAULT NULL,
  `DATE_ADDED` date NOT NULL DEFAULT current_timestamp(),
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `DESCRIPTION` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`MATERIAL_ID`, `USER_ID`, `ORGANIZATION_ID`, `MATERIAL_NAME`, `QUANTITY`, `PRICE`, `SIZE`, `MODEL`, `DATE_ADDED`, `IS_ACTIVE`, `DESCRIPTION`) VALUES
(50, 9, 200, 'Brown Cardboard Box', 9, 100, 'Small', 'N/A', '2026-01-02', 1, NULL),
(51, 9, 200, 'clean', 1, 100, NULL, 'N/A', '2026-01-06', 1, NULL),
(52, 9, 200, 'somethings', 1, 100, NULL, 'N/A', '2026-01-06', 1, NULL),
(53, 9, 200, 'Blasted Bricks', 200, 50, NULL, 'N/A', '2026-02-25', 1, NULL),
(200, 9, 200, 'Concrete Hollow Blocks', 90, 50, NULL, 'Industrial', '2026-03-09', 0, NULL),
(201, 9, 200, 'Galvanized Wire', 3, 80, NULL, '16-gauge', '2026-03-09', 1, NULL),
(202, 9, 200, 'Steel Angle Bar', 0, 320, NULL, '2x2x3mm', '2026-03-09', 1, NULL),
(203, 9, 200, 'Portland Cement', 100, 280, NULL, 'Type I', '2026-03-09', 1, NULL),
(204, 9, 200, 'Plywood 3/4 inch', 40, 650, NULL, 'Marine', '2026-03-10', 1, NULL),
(206, 9, 200, 'Fired Bricks', 40, 650, NULL, 'Rustic', '2026-03-15', 1, NULL),
(207, 9, 200, 'Er', 1, 100, NULL, NULL, '2026-03-16', 0, 'N/A'),
(208, 9, 200, 'Glass Pane2', 1, 100, NULL, 'N/A', '2026-07-07', 1, ''),
(209, 9, 200, 'Building Block', 500, 50, NULL, 'N/A', '2026-07-07', 1, NULL),
(212, 9, NULL, 'e', 1, 100, NULL, 'N/A', '2026-07-07', 1, NULL);

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_insert_stocks_log` AFTER INSERT ON `inventory` FOR EACH ROW BEGIN
    IF NEW.QUANTITY > 0 THEN
        INSERT INTO stocks_log (
            MATERIAL_NAME,
            USER_ID,
            SOURCE_TABLE,
            SOURCE_ID,
            QUANTITY,
            TRANSACTION_TYPE,
            TIME_AND_DATE
        )
        VALUES (
            NEW.MATERIAL_NAME,
            NEW.USER_ID,          -- creator of the item
            'inventory',
            NEW.MATERIAL_ID,
            NEW.QUANTITY,
            'IN',
            NOW()
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_inventory_soft_delete` AFTER UPDATE ON `inventory` FOR EACH ROW BEGIN
    IF NEW.IS_ACTIVE = 0 AND OLD.IS_ACTIVE = 1 THEN
        UPDATE reservation
        SET IS_ACTIVE = 0
        WHERE MATERIAL_ID = NEW.MATERIAL_ID
          AND UPPER(STATUS) IN ('ON PROCESS', 'RESERVED')
          AND IS_ACTIVE = 1;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`MATERIAL_ID`),
  ADD KEY `fk_inventory_user` (`USER_ID`),
  ADD KEY `fk_inv_org` (`ORGANIZATION_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `MATERIAL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inv_org` FOREIGN KEY (`ORGANIZATION_ID`) REFERENCES `organizations` (`ORGANIZATION_ID`),
  ADD CONSTRAINT `fk_inventory_user` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
