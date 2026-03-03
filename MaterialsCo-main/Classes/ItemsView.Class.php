<?php
include_once __DIR__ . '/ItemsCntrl.Class.php';

class ItemsView extends ItemsCntrl{

    // View all Inventory Items
    public function viewInventory($userID){
        return $this->getInventory($userID);
    }

    // View all Reservation Items
    public function viewReservations($userID){
        return $this->getReservations($userID);
    }

    // View all Stocks
    public function viewStocks($userID){
        return $this->getStocks($userID);
    }

    // View Low Stock Count
    public function viewLowStockCount($userID){
        return $this->countLowStocks($userID);
    }

    // View Out of Stock Count
    public function viewOutOfStockCount($userID){
        return $this->countOutOfStocks($userID);
    }

    // View Pending Count
    public function viewPendingCount($userID){
        return $this->countPending($userID);
    }

    // View Reserved Count
    public function viewReservedCount($userID){
        return $this->countReserved($userID);
    }
    
}