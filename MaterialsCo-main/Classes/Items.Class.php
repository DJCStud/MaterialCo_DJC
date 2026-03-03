<?php
include_once __DIR__ . '/Dbh.Class.php';

class Items extends Dbh {

    // Count low stock products
    protected function countLowStocks($userID){
        
        $lowStockLimit = 5;

        $query = "
            SELECT COUNT(*) AS low_stock_count
            FROM inventory
            WHERE USER_ID = ?
            AND QUANTITY <= ?
            AND IS_ACTIVE = 1
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID, $lowStockLimit])) {
            return 0;
        }

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['low_stock_count'];
    }

    // Count out of stock products
    protected function countOutOfStocks($userID){
        
        $StockLimit = 0;

        $query = "
            SELECT COUNT(*) AS out_of_stock_count
            FROM inventory
            WHERE USER_ID = ?
            AND QUANTITY = ?
            AND IS_ACTIVE = 1
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID, $StockLimit])) {
            return 0;
        }

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['out_of_stock_count'];
    }

    // Count pending products
    protected function countPending($userID){
        
        $query = "
            SELECT COUNT(*) AS pending_count
            FROM reservation
            WHERE USER_ID = ?
            AND STATUS = 'On Process'
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID])) {
            return 0;
        }

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
    }

    // Count reserved products
    protected function countReserved($userID){

        $query = "
            SELECT COUNT(*) AS reserved_count
            FROM reservation
            WHERE USER_ID = ?
            AND STATUS = 'Reserved'
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID])) {
            return 0;
        }

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['reserved_count'];
    }

    // Get all items from inventory table
    protected function getInventory($userID) {
        $query = "SELECT * FROM inventory WHERE USER_ID = ? && IS_ACTIVE = 1";
        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID])) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all items from reservation table
    protected function getReservations($userID) {
        $query = "SELECT * FROM reservation WHERE USER_ID = ? && IS_ACTIVE = 1";
        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID])) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all stocks from stocks table
    protected function getStocks($userID) {
        $query = "SELECT * FROM stocks_log WHERE USER_ID = ?";
        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID])) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insert new item to inventory
    protected function insertItem($userID, $materialName, $quantity, $price, $model) {

        $query = "
            INSERT INTO inventory (USER_ID, MATERIAL_NAME, QUANTITY, PRICE, MODEL)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$userID, $materialName, $quantity, $price, $model])) {
            return false;
        }

        return true;
    }

    // Insert new reservation
    protected function insertReservation($materialID, $userID, $quantity, $requestor, $remarks, $claimDate) {

        $query = "
            INSERT INTO reservation (MATERIAL_ID, USER_ID, QUANTITY, REQUESTOR, PURPOSE, CLAIMING_DATE)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$materialID, $userID, $quantity, $requestor, $remarks, $claimDate])) {
            return false;
        }

        return true;
    }

    // Update existing item in inventory
    protected function updateItemDB($materialName, $quantity, $price, $model, $ID) {

        $query = "
            UPDATE inventory
            SET MATERIAL_NAME = ?, QUANTITY = ?, PRICE = ?, MODEL = ?
            WHERE MATERIAL_ID = ?
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$materialName, $quantity, $price, $model, $ID])) {
            return false;
        }

        return true;
    }

    // Update existing reservation
    protected function updateReservationDB($reservationID, $quantity, $requestor, $purpose, $claimingDate) {

        $query = "
            UPDATE reservation
            SET QUANTITY = ?, REQUESTOR = ?, PURPOSE = ?, CLAIMING_DATE = ?
            WHERE RESERVATION_ID = ?
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$quantity, $requestor, $purpose, $claimingDate, $reservationID])) {
            return false;
        }

        return true;
    }

    // Update Reservation Status
    protected function updateReservationStatusDB($reservationID, $status) {

        $query = "
            UPDATE reservation
            SET STATUS = ?
            WHERE RESERVATION_ID = ?
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$status, $reservationID])) {
            return false;
        }

        return true;

    }
        

    // Delete existing item in inventory
    protected function deleteItemDB($ID) {

        $query = "
            UPDATE inventory
            SET IS_ACTIVE = 0
            WHERE MATERIAL_ID = ?
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$ID])) {
            return false;
        }

        return true;
    }

    // Delete existing reservation in inventory
    protected function deleteReservationDB($ID) {

        $query = "
            UPDATE reservation
            SET IS_ACTIVE = 0
            WHERE RESERVATION_ID = ?
        ";

        $stmt = $this->connection()->prepare($query);

        if (!$stmt->execute([$ID])) {
            return false;
        }

        return true;
    }
}
