<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    // Sanitize input
    $reservationID = filter_var(trim($_POST['reservationId']), FILTER_SANITIZE_NUMBER_INT);

    if(isset($_POST['reservedBtn'])){
        $status = "Reserved";
    }
    else if(isset($_POST['claimedBtn'])){
        $status = "Claimed";
    }
    else if(isset($_POST['cancelBtn'])){
        $status = "Canceled";
    }
    else{
        $_SESSION['ReservationMessage'] = "NO ACTION SELECTED!";
        header("Location: ../../Reservation.php");
        exit();
    }

    // Controller
    $items = new ItemsCntrl();

    // Execute registration
    if(!$items->updateReservationStatus($reservationID, $status)){
        $_SESSION['ReservationMessage'] = "ERROR UPDATING RESERVATION!";
    }
    else{
         $_SESSION['ReservationMessageSuccess'] = "UPDATE SUCCESSFUL!";
    }

   header("Location: ../../Reservation.php");
    exit();

} else {
    header("Location: ../Reservation.php?error=FailedtoAddItem");
    exit();
}