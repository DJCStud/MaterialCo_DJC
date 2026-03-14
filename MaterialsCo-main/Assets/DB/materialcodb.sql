-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2026 at 02:59 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_material` (IN `p_user_id` INT, IN `p_name` VARCHAR(100), IN `p_quantity` INT, IN `p_price` INT, IN `p_model` VARCHAR(50))   BEGIN
    IF p_quantity < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantity cannot be negative.';
    END IF;

    IF p_price < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Price cannot be negative.';
    END IF;

    INSERT INTO inventory (USER_ID, MATERIAL_NAME, QUANTITY, PRICE, MODEL)
    VALUES (p_user_id, p_name, p_quantity, p_price, p_model);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_inventory_summary` (IN `p_user_id` INT)   BEGIN
    SELECT
        i.MATERIAL_ID,
        i.MATERIAL_NAME,
        i.QUANTITY,
        i.PRICE,
        i.MODEL,
        i.DATE_ADDED,
        fn_get_stock_status(i.QUANTITY)        AS STOCK_STATUS,
        fn_get_material_total_value(i.MATERIAL_ID) AS TOTAL_VALUE
    FROM inventory i
    WHERE i.USER_ID = p_user_id
      AND i.IS_ACTIVE = 1
    ORDER BY i.DATE_ADDED DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_reservation_report` (IN `p_user_id` INT)   BEGIN
    SELECT
        r.RESERVATION_ID,
        r.MATERIAL_ID,
        i.MATERIAL_NAME,
        r.QUANTITY,
        i.PRICE,
        (r.QUANTITY * i.PRICE)  AS TOTAL_PRICE,
        r.REQUESTOR,
        r.PURPOSE,
        r.RESERVATION_DATE,
        r.CLAIMING_DATE,
        r.STATUS
    FROM reservation r
    JOIN inventory i ON r.MATERIAL_ID = i.MATERIAL_ID
    WHERE r.USER_ID = p_user_id
      AND r.IS_ACTIVE = 1
    ORDER BY r.RESERVATION_DATE DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_material` (IN `p_material_id` INT, IN `p_name` VARCHAR(100), IN `p_quantity` INT, IN `p_price` INT, IN `p_model` VARCHAR(50))   BEGIN
    IF p_quantity < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantity cannot be negative.';
    END IF;

    IF p_price < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Price cannot be negative.';
    END IF;

    UPDATE inventory
    SET MATERIAL_NAME = p_name,
        QUANTITY      = p_quantity,
        PRICE         = p_price,
        MODEL         = p_model
    WHERE MATERIAL_ID = p_material_id
      AND IS_ACTIVE = 1;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_count_active_members` (`p_organization_id` INT) RETURNS INT(11) READS SQL DATA BEGIN
    DECLARE v_count INT;

    SELECT COUNT(*) INTO v_count
    FROM members
    WHERE ORGANIZATION_ID = p_organization_id
      AND IS_ACTIVE = 1;

    RETURN IFNULL(v_count, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_material_total_value` (`p_material_id` INT) RETURNS DECIMAL(12,2) READS SQL DATA BEGIN
    DECLARE v_total DECIMAL(12,2);

    SELECT (QUANTITY * PRICE) INTO v_total
    FROM inventory
    WHERE MATERIAL_ID = p_material_id
      AND IS_ACTIVE = 1;

    RETURN IFNULL(v_total, 0.00);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_get_stock_status` (`p_quantity` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    IF p_quantity = 0 THEN
        RETURN 'Out of Stock';
    ELSEIF p_quantity <= 5 THEN
        RETURN 'Low Stock';
    ELSE
        RETURN 'In Stock';
    END IF;
END$$

DELIMITER ;

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
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1,
  `DESCRIPTION` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`MATERIAL_ID`, `USER_ID`, `MATERIAL_NAME`, `QUANTITY`, `PRICE`, `SIZE`, `MODEL`, `DATE_ADDED`, `IS_ACTIVE`, `DESCRIPTION`) VALUES
(50, 8, 'Brown Cardboard Box', 9, 100, 'Small', 'N/A', '2026-01-02', 1, NULL),
(51, 8, 'clean', 1, 100, NULL, 'N/A', '2026-01-06', 1, NULL),
(52, 8, 'somethings', 1, 100, NULL, 'N/A', '2026-01-06', 1, NULL),
(53, 9, 'Blasted Bricks', 200, 50, NULL, 'N/A', '2026-02-25', 1, NULL);

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_delete` AFTER UPDATE ON `inventory` FOR EACH ROW BEGIN
    IF NEW.IS_ACTIVE = 0 AND OLD.IS_ACTIVE = 1 THEN
        UPDATE reservation
        SET STATUS = 'CANCELED'
        WHERE MATERIAL_ID = NEW.MATERIAL_ID
          AND UPPER(STATUS) IN ('ON PROCESS', 'RESERVED')
          AND IS_ACTIVE = 1;
    END IF;
END
$$
DELIMITER ;
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
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `MEMBER_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `ORGANIZATION_ID` int(11) NOT NULL,
  `REMARKS` varchar(255) NOT NULL,
  `DATE_JOINED` datetime NOT NULL DEFAULT current_timestamp(),
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `ORGANIZATION_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `ADDRESS` text DEFAULT NULL,
  `TYPE` varchar(100) DEFAULT NULL,
  `CREATED_AT` datetime DEFAULT current_timestamp(),
  `IS_ACTIVE` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`ORGANIZATION_ID`, `USER_ID`, `NAME`, `ADDRESS`, `TYPE`, `CREATED_AT`, `IS_ACTIVE`) VALUES
(1, 9, 'Yes', '123', 'School', '2026-03-09 09:50:58', 1);

--
-- Triggers `organizations`
--
DELIMITER $$
CREATE TRIGGER `trg_organization_delete` AFTER UPDATE ON `organizations` FOR EACH ROW BEGIN
    IF NEW.IS_ACTIVE = 0 AND OLD.IS_ACTIVE = 1 THEN
        UPDATE members
        SET IS_ACTIVE = 0
        WHERE ORGANIZATION_ID = NEW.ORGANIZATION_ID
          AND IS_ACTIVE = 1;
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
CREATE TRIGGER `trg_before_reservation_insert` BEFORE INSERT ON `reservation` FOR EACH ROW BEGIN
    DECLARE v_available INT;

    SELECT QUANTITY INTO v_available
    FROM inventory
    WHERE MATERIAL_ID = NEW.MATERIAL_ID
      AND IS_ACTIVE = 1;

    IF v_available IS NULL OR NEW.QUANTITY > v_available THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Reservation quantity exceeds available stock.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_reservation_claimed` BEFORE UPDATE ON `reservation` FOR EACH ROW BEGIN
    DECLARE v_material_name VARCHAR(255);

    IF UPPER(NEW.STATUS) = 'CLAIMED' AND UPPER(OLD.STATUS) <> 'CLAIMED' THEN

        SELECT MATERIAL_NAME INTO v_material_name
        FROM inventory
        WHERE MATERIAL_ID = NEW.MATERIAL_ID;

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
            'OUT',
            NOW()
        );

    END IF;
END
$$
DELIMITER ;
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
(29, 'somethings', 8, 'inventory', 52, 1, 'IN', '2026-01-06 13:29:13'),
(30, 'Blasted Bricks', 9, 'inventory', 53, 200, 'IN', '2026-02-25 01:14:58');

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
(8, 'Joseph Mejos', 'Xzilleon2@gmail.com', '$2y$10$oLhAU413PYYlc3FWdQA03OqK3DOtKuq9tepTaEhHiWWcugluyE0tS'),
(9, 'dan', 'd@gmail.com', '$2y$10$jpsauq4lCk3.lKEknd47weBDYdBbmOvPMerrWKVwCsPKYmHQVlF0K');

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
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`MEMBER_ID`),
  ADD KEY `USER_ID` (`USER_ID`),
  ADD KEY `ORGANIZATION_ID` (`ORGANIZATION_ID`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`ORGANIZATION_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

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
  MODIFY `MATERIAL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `MEMBER_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `ORGANIZATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `RESERVATION_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `stocks_log`
--
ALTER TABLE `stocks_log`
  MODIFY `STOCKS_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_user` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`),
  ADD CONSTRAINT `members_ibfk_2` FOREIGN KEY (`ORGANIZATION_ID`) REFERENCES `organizations` (`ORGANIZATION_ID`);

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`USER_ID`);

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
