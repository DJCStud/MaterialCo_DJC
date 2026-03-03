<?php
include_once __DIR__ . '/Items.Class.php';

class ItemsCntrl extends Items {

    private $name;
    private $quantity;
    private $price;
    private $model;
    private $id;

    public function __construct($id = "", $name = "", $quantity = "", $price = "", $model = "") {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->model = $model;
        
    }

    // Add Item
    public function addItem() {

        if ($this->checkEmptyFields($this->name, $this->quantity, $this->price, $this->model)) {
            return false;
        }

        return $this->insertItem(
            $this->id,
            $this->name,
            $this->quantity,
            $this->price,
            $this->model,
        );
    }

    // Add Reservation
    public function addReservation($materialID, $userID, $quantity, $requestor, $claimDate, $remarks) {

        if ($this->checkEmptyFields($materialID, $claimDate, $remarks)) {
            return false;
        }

        // Implementation for adding reservation
        return $this->insertReservation(
            $materialID,
            $userID,
            $quantity,
            $requestor,
            $claimDate,
            $remarks
        );
    }

    // Update Item
    public function updateItem() {

        if ($this->checkEmptyFields($this->name, $this->model)) {
            return false;
        }

        return $this->updateItemDB(
            $this->name,
            $this->quantity,
            $this->price,
            $this->model,
            $this->id
        );
    }

    // Update Reservation
    public function updateReservation($reservationID, $quantity, $requestor, $purpose, $claimingDate) {

        return $this->updateReservationDB(
            $reservationID,
            $quantity,
            $requestor,
            $purpose,
            $claimingDate
        );
    }

    // Update Reservation Status
    public function updateReservationStatus($reservationID, $status) {

        return $this->updateReservationStatusDB(
            $reservationID,
            $status
        );
    }

    // Delete Item
    public function deleteItem() {

        return $this->deleteItemDB(
            $this->id
        );

    }

    // Delete Reservation
    public function deleteReservation($ID) {

        return $this->deleteReservationDB(
            $ID
        );

    }

    private function checkEmptyFields($name, $model) {
        return empty($name) || empty($model);
    }
}
