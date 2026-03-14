<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['deleteBtn'])) {

    // Sanitize input
    $ID = filter_var(trim($_POST['reservationId']), FILTER_SANITIZE_NUMBER_INT);

    // Controller
    $items = new ItemsCntrl();

    // Execute registration
    if(!$items->deleteReservation($ID)){
        $_SESSION['InventoryMessage'] = "ERROR DELETING RESERVATION!";
    }
    else{
         $_SESSION['InventoryMessageSuccess'] = " SUCCESSFULLY DELETED!";
    }

   header("Location: ../../Reservation.php");
    exit();

} else {
    header("Location: ../Reservation.php?error=FailedtoAddItem");
    exit();
}