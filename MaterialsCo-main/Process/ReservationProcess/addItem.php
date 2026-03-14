<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['reserveBtn'])) {

    // Sanitize input
    $materialID = filter_var(trim($_POST['material_id']), FILTER_SANITIZE_NUMBER_INT);
    $userID = $_SESSION['USER_ID'];
    $quantity = filter_var(trim($_POST['quantity']), FILTER_SANITIZE_NUMBER_INT);
    $claimDate = $_POST['claimDate'];
    $requestor = filter_var(trim($_POST['requestor']), FILTER_SANITIZE_SPECIAL_CHARS);
    $remarks = filter_var(trim($_POST['remarks']), FILTER_SANITIZE_SPECIAL_CHARS);

    // Controller
    $items = new ItemsCntrl();

    // Execute registration
    if(!$items->addReservation($materialID, $userID, $quantity, $requestor, $remarks, $claimDate)){
        $_SESSION['ReservationMessage'] = "ERROR RESERVING MATERIALS!";
    }
    else{
        $_SESSION['ReservationMessageSuccess'] = "RESERVATION SUCCESSFUL!";
    }

    // Redict after processing
    //header("Location: ../../Reservation.php");
    header("Location: ../../Reservation.php?materialID=$materialID&userID=$userID&quantity=$quantity&size=$size&claimDate=$claimDate&requestor=$requestor&remarks=$remarks");
    exit();

} else {
    header("Location: ../Reservation.php?error=FailedtoAddItem");
    exit();
}