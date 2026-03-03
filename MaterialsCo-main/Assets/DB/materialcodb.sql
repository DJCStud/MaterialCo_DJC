-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 03:54 PM
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
-- Database: `materialcodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `MATERIAL_ID` int(11) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `MATERIAL_NAME` varchar(100) DEFAULT NULL,
  `QUANTITY` int(11) DEFAULT NULL,
  `PRICE` int(11) DEFAULT NULL,
  `SIZE` varchar(11) DEFAULT NULL,
  `MODEL` varchar(50) DEFAULT NULL,
  `DATE_ADDED` date NOT NULL DEFAULT current_timestamp(),
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`MATERIAL_ID`, `USER_ID`, `MATERIAL_NAME`, `QUANTITY`, `PRICE`, `SIZE`, `MODEL`, `DATE_ADDED`, `IS_ACTIVE`) VALUES
(50, 8, 'Brown Cardboard Box', 9, 100, 'Small', 'N/A', '2026-01-02', 1),
(51, 8, 'clean', 1, 100, NULL, 'N/A', '2026-01-06', 1),
(52, 8, 'somethings', 1, 100, NULL, 'N/A', '2026-01-06', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `RESERVATION_ID` int(11) NOT NULL,
  `MATERIAL_ID` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `QUANTITY` int(11) DEFAULT NULL,
  `REQUESTOR` varchar(255) DEFAULT NULL,
  `PURPOSE` varchar(255) DEFAULT NULL,
  `RESERVATION_DATE` date DEFAULT current_timestamp(),
  `CLAIMING_DATE` date DEFAULT NULL,
  `STATUS` varchar(50) DEFAULT 'On Process',
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`RESERVATION_ID`, `MATERIAL_ID`, `USER_ID`, `QUANTITY`, `REQUESTOR`, `PURPOSE`, `RESERVATION_DATE`, `CLAIMING_DATE`, `STATUS`, `IS_ACTIVE`) VALUES
(27, 50, 8, 12, 'Mr. Slark', 'N/A', '2026-01-02', '2026-01-03', 'Canceled', 1);

--
-- Triggers `reservation`
--
DELIMITER $$
CREATE TRIGGER `trg_reservation_update` BEFORE UPDATE ON `reservation` FOR EACH ROW BEGIN
    DECLARE v_material_name VARCHAR(255);

    -- Get material name
    SELECT MATERIAL_NAME
    INTO v_material_name
    FROM inventory
    WHERE MATERIAL_ID = NEW.MATERIAL_ID;

    -- If status changed to RESERVED, subtract quantity from inventory
    IF NEW.STATUS = 'RESERVED' AND OLD.STATUS <> 'RESERVED' THEN
        UPDATE inventory
        SET QUANTITY = QUANTITY - NEW.QUANTITY
        WHERE MATERIAL_ID = NEW.MATERIAL_ID;

        -- Log the reservation
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
            v_material_name,
            NEW.USER_ID,
            'reservation',
            NEW.RESERVATION_ID,
            NEW.QUANTITY,
            'RESERVE',
            NOW()
        );

    -- If status changed to CANCELED, add quantity back to inventory
    ELSEIF NEW.STATUS = 'CANCELED' AND OLD.STATUS <> 'CANCELED' THEN
        UPDATE inventory
        SET QUANTITY = QUANTITY + NEW.QUANTITY
        WHERE MATERIAL_ID = NEW.MATERIAL_ID;

        -- Log the cancellation
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
            v_material_name,
            NEW.USER_ID,
            'reservation',
            NEW.RESERVATION_ID,
            NEW.QUANTITY,
            'CANCEL',
            NOW()
        );
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stocks_log`
--

CREATE TABLE `stocks_log` (
  `STOCKS_ID` int(11) NOT NULL,
  `MATERIAL_NAME` varchar(100) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL,
  `SOURCE_TABLE` varchar(100) DEFAULT NULL,
  `SOURCE_ID` int(11) DEFAULT NULL,
  `QUANTITY` int(11) NOT NULL,
  `TRANSACTION_TYPE` enum('IN','OUT','RESERVE','RELEASE') NOT NULL,
  `TIME_AND_DATE` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks_log`
--

INSERT INTO `stocks_log` (`STOCKS_ID`, `MATERIAL_NAME`, `USER_ID`, `SOURCE_TABLE`, `SOURCE_ID`, `QUANTITY`, `TRANSACTION_TYPE`, `TIME_AND_DATE`) VALUES
(25, 'Brown Cardboard Box', 8, 'inventory', 50, 12, 'IN', '2026-01-02 21:23:07'),
(26, 'Brown Cardboard Box', 8, 'reservation', 27, 12, 'RESERVE', '2026-01-04 16:02:36'),
(27, 'Brown Cardboard Box', 8, 'reservation', 27, 12, '', '2026-01-04 17:00:09'),
(28, 'clean', 8, 'inventory', 51, 1, 'IN', '2026-01-06 12:27:44'),
(29, 'somethings', 8, 'inventory', 52, 1, 'IN', '2026-01-06 13:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USER_ID` int(11) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USER_ID`, `NAME`, `EMAIL`, `PASSWORD`) VALUES
(8, 'Joseph Mejos', 'Xzilleon2@gmail.com', '$2y$10$oLhAU413PYYlc3FWdQA03OqK3DOtKuq9tepTaEhHiWWcugluyE0tS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`MATERIAL_ID`),
  ADD KEY `fk_inventory_user` (`USER_ID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`RESERVATION_ID`),
  ADD KEY `MATERIAL_ID` (`MATERIAL_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `stocks_log`
--
ALTER TABLE `stocks_log`
  ADD PRIMARY KEY (`STOCKS_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `MATERIAL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `RESERVATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `stocks_log`
--
ALTER TABLE `stocks_log`
  MODIFY `STOCKS_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_user` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`MATERIAL_ID`) REFERENCES `inventory` (`MATERIAL_ID`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
